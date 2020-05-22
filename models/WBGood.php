<?php


namespace app\models;


use yii\base\Model;

class WBGood extends Model
{
    public $brand;
    public $subject;
    public $season;
    public $collection;
    public $name;
    public $ownerID;
    public $wbID;
    public $barCode;
    public $contract;

    public $salesStorages = [
        'Новосибирск',
        'Хабаровск',
        'Краснодар',
        'Екатеринбург',
        'Санкт-Петербург',
    ];

    public $image;
    public $deficit = [];
    public $storage = [];
    public $sizes = [];

    public $yearPercentTotals = 0;
    public $yearOrderTotals = 0;
    public $yearSaleTotals = 0;
    public $yearBalanceTotals = 0;
    public $yearIncomes = 0;
    public $yearProfit = 0;
    public $yearTotalCost = 0;

    public $monthPercentTotals = 0;
    public $monthOrderTotals = 0;
    public $monthSaleTotals = 0;
    public $monthBalanceTotals = 0;
    public $monthIncomes = 0;
    public $monthProfit = 0;
    public $monthTotalCost = 0;

    public $deficitTotals = 0;
    public $wishListTotals = 0;

    public $income = 0;
    public $profit = 0;
    public $selfCost = 0;
    public $totalCost = 0;
    public $status = '';
    public $statuses = [
        'norm' => '<span class="badge badge-success">рабочий - не дефицит</span>',
        'hit' => '<span class="badge badge-danger">хит - дефицит</span>',
        'deprecated' => '<span class="badge badge-light">снят с производства</span>',
        'empty' => '',
    ];

    public $orderCount = 0;
    public $orderCost = 0;

    public function __construct($assocGood)
    {
        $this->brand = trim($assocGood['brand'] ?? '');
        $this->subject = trim($assocGood['subject'] ?? '');
        $this->season = trim($assocGood['season'] ?? '');
        $this->collection = trim($assocGood['collection'] ?? '');
        $this->name = trim($assocGood['name'] ?? '');
        $this->ownerID = trim($assocGood['ownerID'] ?? '');
        $this->wbID = trim($assocGood['wbID'] ?? '');
        $this->barCode = trim($assocGood['barCode'] ?? '');
        $this->contract = trim($assocGood['contract'] ?? '');

        $tmp = substr($this->wbID, 0, 4);
        $this->image = 'https://img1.wbstatic.net/big/new/'.$tmp.'0000/'.$this->wbID.'-1.jpg';

        return parent::__construct();
    }

    public function addPeriodData($assocGood, $period) {
        if (trim($assocGood['ownerID']) != $this->ownerID) {
            return false;
        }
        $size = trim($assocGood['size']);
        $storageId = trim($assocGood['storage']);
        $moneys = array_slice($assocGood, 11, 7);

        foreach ($moneys as $key => $money) {
            $this->sizes[$period][$size]['storage'][$storageId][$key] = $assocGood[$key];
        }

        return true;
    }

    public function addMonthData($assocGood) {
        if (trim($assocGood['ownerID']) != $this->ownerID) {
            return false;
        }
        $size = trim($assocGood['size']);
        $storageId = trim($assocGood['storage']);
        $moneys = array_slice($assocGood, 7, 7);
        foreach ($moneys as $key => $money) {
            $this->sizes['month'][$size]['storage'][$storageId][$key] = $assocGood[$key];
        }

        return true;
    }

    public function addDeficit($data) {
        if (trim($data['Артикул Цвета']) != $this->ownerID) {
            return false;
        }
        $size = $data['Размер'];
        $storage = array_slice($data, 16, 6);

        $this->deficit[$size]['storage'] = array_combine($this->salesStorages, $storage);
        $this->deficit[$size]['storage']['Подольск'] = $data['Общий дефицит'] - array_sum($storage);
        $this->deficit[$size]['wishList'] = intval($data['Запросов в листе ожидания']);

        return true;
    }

    public function calcSales($period = 'year') {
        $percent = $period.'PercentTotals';
        $sales = $period.'SaleTotals';
        $orders = $period.'OrderTotals';
        $balance = $period.'BalanceTotals';
        $incomes = $period.'Incomes';
        $profit = $period.'Profit';
        $totalCost = $period.'TotalCost';
        if (empty($this->sizes[$period])) {
            $this->sizes[$period] = [];
        }
        foreach ($this->sizes[$period] as &$size) {
            $size['incomesTotals'] = 0;
            $size['incomesCost'] = 0;
            $size['ordersTotals'] = 0;
            $size['ordersCost'] = 0;
            $size['salesTotals'] = 0;
            $size['salesCost'] = 0;
            $size['balanceTotals'] = 0;
            $size['balanceTooltip'] = '';
            $size['ordersTooltip'] = '';
            $size['salesTooltip'] = '';
            foreach ($size['storage'] as $name => $storage) {
                $size['incomesTotals'] += $storage['incomesCount'];
                $size['incomesCost'] += $storage['incomesCost'];
                $size['ordersTotals'] += $storage['ordersCount'];
                $size['ordersCost'] += $storage['ordersCost'];
                $size['salesTotals'] += $storage['salesCount'];
                $size['salesCost'] += $storage['salesCost'];
                $size['balanceTotals'] += $storage['balanceCount'];
                $size['balanceTooltip'] .= $name.': '.$storage['balanceCount'].'<br>';
                $size['ordersTooltip'] .= $name.': '.($storage['ordersCount'] ?? '').'<br>';
                $size['salesTooltip'] .= $name.': '.($storage['salesCount'] ?? '').'<br>';
            }
            $size['percentTotals'] = round($size['ordersTotals'] > 0 ? $size['salesTotals'] / $size['ordersTotals'] * 100 : 0);
            $size['incomes'] = $size['salesCost']; //Выручка/вознаграждение;
            $size['profit'] = round(
                    ($size['salesTotals'] > 0 ? $size['salesCost']/$size['salesTotals'] : 0) //Вознаграждение
                    - ($size['salesTotals'] > 0 ? ($size['ordersTotals']*33/$size['salesTotals']) : 0) //логистика
                    - ($size['incomesTotals'] > 0 ? ($size['incomesCost']/$size['incomesTotals'] * 0.02) : 0) //квартальный платеж
                    - $this->selfCost //себестоимость
                ) * $size['salesTotals'];
            $this->$incomes += $size['incomes'];
            $this->$profit += $size['profit'];
            $this->$orders += $size['ordersTotals'];
            $this->$sales += $size['salesTotals'];
            $this->$balance += $size['balanceTotals'];
        }
        $this->$totalCost = ($this->$orders - $this->$sales + $this->$balance) * $this->selfCost;
        $this->$percent = round($this->$orders > 0 ? $this->$sales / $this->$orders * 100 : 0);
    }

    public function calcDeficit() {

        foreach ($this->deficit as &$size) {
            $size['deficit'] = empty($size) ? 0 : $size['deficit'] = array_sum($size['storage']);
            $size['tooltip'] = '';
            foreach($size['storage'] as $key => $value) {
                $size['tooltip'] .= $key.': '.$value.'<br>';
            }
            $this->deficitTotals += $size['deficit'];
            if (!is_numeric($size['wishList'])) {
                echo '<pre>';
                print_r($this);
                echo '</pre>';
                \Yii::$app->end(0);
            }
            $this->wishListTotals += $size['wishList'] ?? 0;
        }
        if (($this->deficitTotals + $this->wishListTotals) > 0) {
            $this->status = $this->statuses['hit'];
        } elseif ($this->status == '') {
            $this->status = $this->statuses['norm'];
        }
    }

    public function setSelfCost(array $data) {
        if (trim($data['артикул']) != $this->ownerID) {
            return false;
        }

        $this->selfCost = floatval(trim($data['себестоимость'] ?? 0));

        return true;
    }

    public function setStatus(array $costs) {
        $status = trim($costs[$this->barCode]['Снят с производства'] ?? '0');
        $this->status = $this->statuses[$status == '1' ? 'deprecated' : 'empty'];
    }

    public function calcOrder() {
        switch ($this->status) {
            case '<span class="badge badge-danger">хит - дефицит</span>':
                $this->orderCount = round($this->deficitTotals * 0.3);
                break;
            case '<span class="badge badge-danger">рабочий - не дефицит</span>':
                $this->orderCount = round($this->deficitTotals * 0.1);
                break;
        }
        $this->orderCount = ($this->orderCount == 0 ? 1 : $this->orderCount);
        $this->orderCost = $this->orderCount * $this->selfCost;
    }

//    public function calcIncome() {
//        foreach ($this->sizes['month'] as $key => $size) {
//            $this->income += ($size['saleTotals'] > 0 ? $size['saleCost']/$size['saleTotals'] : 0); //Выручка/вознаграждение
//        }
//    }

//    public function calcProfit() {
//        foreach ($this->sizes['month'] as $key => $size) {
//            $this->profit += (
//                ($size['saleTotals'] > 0 ? $size['saleCost']/$size['saleTotals'] : 0) //Вознаграждение
//                - ($size['saleTotals'] > 0 ? ($size['orderTotals']*33/$size['saleTotals']) : 0) //логистика
//                - ($this->sizes['year'][$key]['incomeTotals'] > 0 ? ($this->sizes['year'][$key]['incomeCost']/$this->sizes['year'][$key]['incomeTotals'] * 0.02) : 0) //квартальный платеж
//                - $this->selfCost //себестоимость
//            ) * $size['saleTotals'];
//        }
//    }
}