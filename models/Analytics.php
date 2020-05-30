<?php


namespace app\models;


use yii\helpers\FileHelper;
use yii\httpclient\Client;
use yii\base\Model;

class Analytics extends Model
{
    public $apiKey = 'Yzc4Y2JmNDUtNTMwNi00MjdjLWJiYWMtZTYxZjRmYTExMDI4';
    public $fileTable = 'goods.csv';
//    public $apiKey = 'c78cbf45-5306-427c-bbac-e61f4fa11028';
    public $sales;
    public $orders;
    public $incomes;
    public $tables = [
        'orders',
        'incomes',
        'sales'
    ];
    public $revHeads;
    public $heads = [
        'brand' => 0,
        'subject' => 1,
        'season' => 2,
        'collection' => 3,
        'name' => 4,
        'ownerID' => 5,
        'wbID' => 6,
        'barCode' => 7,
        'size' => 8,
        'contract' => 9,
        'storage' => 10,
        'incomesCount' => 11,
        'incomesCost' => 12,
        'ordersCount' => 13,
        'ordersCost' => 14,
        'salesCount' => 15,
        'salesCost' => 16,
        'balanceCount' => 17,
    ];

    public $goods = [];

    public function __construct($config = [])
    {
//        $this->revHeads = array_flip($this->heads);
        parent::__construct($config);
    }

    private function extractExcel(string $filename) : bool {
        $extractPath = $filename.'dir';
        FileHelper::createDirectory($extractPath, 0755);
        $zip = new \ZipArchive();
        if ($zip->open($filename) === TRUE) {
            $zip->extractTo($extractPath.'/');
            $zip->close();
            \Yii::info('Успешно распакован файл '. $filename, 'info');
            return true;
        }
        \Yii::error('Ошибка распаковки файла '. $filename, 'error');

        return false;
    }

    /**
     * https://habr.com/ru/post/140352/
     * @param $filename String xlsx file
     * @return array|bool
     */
    private function getExcel($filename) {
        if (!$this->extractExcel($filename)) {
            return false;
        }

        $pathD = $filename.'dir'.'/xl/worksheets/';
        $pathS = $filename.'dir'.'/xl/';

        // собираем строковые значения ячеек. т.к. в файлах листов только числовые значения
        $sharedStringsArr = [];
        if (file_exists($pathS . 'sharedStrings.xml')) {
            $xml = simplexml_load_file($pathS . 'sharedStrings.xml');
            foreach ($xml->children() as $item) {
                $sharedStringsArr[] = (string)$item->t;
            }
        }

        $handle = @opendir($pathD);
        $out = array();
        while ($file = @readdir($handle)) {
            //проходим по всем файлам из директории /xl/worksheets/
            if ($file != "." && $file != ".." && $file != '_rels') {
                $xml = simplexml_load_file($pathD . $file);
                // по каждой строке
                $row = 0;
                foreach ($xml->sheetData->row as $item) {
                    $out[$file][$row] = array();
                    // по каждой ячейке
                    $cell = 0;
                    foreach ($item as $child) {
                        $attr = $child->attributes();
                        $value = isset($child->v) ? (string)$child->v : false;
                        $keyN = preg_replace('/\d/', '', $attr['r']);
                        $out[$file][$row][$keyN] = isset($attr['t']) ? ($sharedStringsArr[$value] ?? (string)$child->is->t) : $value;
                    }
                    $row++;
                }
            }
        }

        return $out;
    }

    private function saveToDB ($table, $data, $simple = false) {
        if (empty($data)) {
            return false;
        }
        $userFiles = 'files/'.\Yii::$app->user->id;
        if (!is_dir($userFiles)) {
            mkdir($userFiles, 0755, true);
        }
        if ($simple) {
            $outPut = json_encode($data);
        } else {
            $outPut = implode(';', array_keys($data[0])).PHP_EOL;
            foreach ($data as $item) {
                $outPut .= implode(';', $item).PHP_EOL;
            }
        }

        return file_put_contents($userFiles.'/'.$table, $outPut);
    }

    public function loadFromDB() {
        $userFiles = 'files/'.\Yii::$app->user->id;
        if (
            !file_exists($userFiles.'/monthFile.json')
            || !file_exists($userFiles.'/yearFile.json')
            || !file_exists($userFiles.'/deficitFile.json')
        ) {
            $this->addError('Данные', 'Отсутствуют файлы с данными.');
            return false;
        }
        $monthRawSales = json_decode(file_get_contents($userFiles.'/monthFile.json'), true);
        $yearRawSales = json_decode(file_get_contents($userFiles.'/yearFile.json'), true);
        $deficitRaw = json_decode(file_get_contents($userFiles.'/deficitFile.json'), true);

        $costRaw = file_exists($userFiles.'/costFile.json')
            ? json_decode(file_get_contents($userFiles.'/costFile.json'), true)
            : [];

        $rawData['monthSales'] = $this->parseSales($monthRawSales['sheet1.xml']);
        $rawData['yearSales'] = $this->parseSales($yearRawSales['sheet1.xml']);
        $rawData['deficit'] = $this->parseDeficit($deficitRaw['sheet1.xml']);
        $rawData['cost'] = $this->parseCost($costRaw['sheet1.xml'] ?? []);

        /**
         * @var $goods WBGood[]
         */
        $goods = [];
        foreach (['month', 'year'] as $period) {
            $method = 'add'.ucfirst($period).'Data';
            foreach ($rawData[$period.'Sales'] as $assocGood) {
                $goodKey = trim($assocGood['barCode']);
                if (!array_key_exists($goodKey, $goods)) {
                    $goods[$goodKey] = new WBGood($assocGood);
                }
                $goods[$goodKey]->addPeriodData($assocGood, $period);
            }
        }

        foreach ($rawData['deficit'] as $assocGood) {
            $goodKey = trim($assocGood['Баркод (не заполнять, колонка для информации)']);
            if (!array_key_exists($goodKey, $goods)) {
                $goods[$goodKey] = new WBGood($assocGood);
            }
            $goods[$goodKey]->addDeficit($assocGood);
        }

        foreach ($goods as &$good) {
            $good->setSelfCost($rawData['cost'][$good->ownerID] ?? null);
        }

        return $goods;
    }

    private function parseSales($rawData) {
        $result = [];
        $this->revHeads = array_flip($this->heads);
        array_shift($rawData);
        array_shift($rawData);
        $headsLength = count($this->revHeads);
        foreach ($rawData as $value) {
            $dataLength = count($value);
            if ($headsLength > $dataLength) {
                $result[] = array_combine(
                    $this->revHeads, array_pad(
                        $value,($headsLength - $dataLength), 0
                    )
                );
            } elseif ($headsLength < $dataLength) {
                $revHeads = array_flip(array_pad(
                    $this->heads,($dataLength - $headsLength), rand(0,100)
                ));
                $result[] = array_combine($revHeads, $value);
            } else {
                $result[] = array_combine($this->revHeads, $value);
            }
        }

        return $result;
    }

    private function parseDeficit($rawData) {
        if (empty($rawData)) {
            return [];
        }

        $result = [];
        $heads = array_shift($rawData);
        foreach ($rawData as $value) {
            if (empty($value['C']) || ($value['C'] == '')) {
                continue;
            }
            $result[] = array_combine($heads, $value);
        }

        return $result;
    }

    private function parseCost($rawData) {
        if (empty($rawData)) {
            return [];
        }

        while (!is_numeric(str_replace(',', '.', $rawData[0]['B']))) {
            array_shift($rawData);
        }
        $result = [];
        foreach ($rawData as $value) {
            $selfCost = array_slice($value, 0, 2);
            if (empty($selfCost['A'])) {
                continue;
            }
            $result[strtoupper(trim($selfCost['A']))] = floatval(str_replace(',', '.', $selfCost['B']));
        }

        return $result;
    }

    private function updateFromApi()
    {
        return [];
    }

    private function updateFromFiles() {
        $userFiles = 'files/'.\Yii::$app->user->id;
        foreach (UserFiles::FILENAMES as $file) {
            $filename = $userFiles.'/'.$file.'.xlsx';
            $rawData = $this->getExcel($filename);
            if ($rawData) {
                $this->saveToDB($file.'.json', $rawData, true);
            } elseif ($file != 'costFile') {
                $this->addError('Данные', 'Отсутствует файл с данными '.$file);
                return false;
            }
        }

        return true;
    }

    public function update($api = false)  {
        return ($api ? $this->updateFromApi() : $this->updateFromFiles());
    }
}