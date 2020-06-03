<?php


namespace app\controllers;


use app\base\BaseController;
use app\models\Analytics;
use app\models\UserFiles;
use app\models\Users;
use yii\base\Model;
use yii\bootstrap4\ActiveForm;
use yii\web\Controller;
use yii\web\UploadedFile;

class AnalyticsController extends BaseController
{
    public function actionIndex() {
        if (!$this->rBac->canViewStat()) {
            return $this->goHome();
        }

        $model = new Analytics();
        $goods = $model->loadFromDB();
        if (!$goods) {
            return $this->render('error', ['model' => $model]);
        }
        $brands = array_unique(array_column($goods, 'brand'));
        $deprecatedYear = 0;
        $deprecatedMonth = 0;
        $brandCost = [
            'year' => [],
            'month' => [],
        ];
        $brandIncomes = [
            'year' => [],
            'month' => [],
        ];
        $brandProfit = [
            'year' => [],
            'month' => [],
        ];
        $brandCostDeprecated = [
            'year' => [],
            'month' => [],
        ];
        $totalBrandOrders = [];
        foreach ($brands as $brand) {
            if (!$brand) {
                unset($brand);
                continue;
            }
            $brandCost['year'][$brand] = 0;
            $brandCost['month'][$brand] = 0;
            $brandIncomes['year'][$brand] = 0;
            $brandIncomes['month'][$brand] = 0;
            $brandProfit['year'][$brand] = 0;
            $brandProfit['month'][$brand] = 0;
            $brandCostDeprecated['year'][$brand] = 0;
            $brandCostDeprecated['month'][$brand] = 0;
            $totalBrandOrders[$brand] = 0;
        }

        $orders = [];
        foreach ($goods as $key => &$good) {
            $brand = $good->brand;
            if (!$brand)
                continue;
            $good->calcSales('year');
            $good->calcSales('month');
            $good->calcDeficit();
            if ($good->status == '<span class="badge badge-light">снят с производства</span>') {
                $deprecatedYear += $good->yearTotalCost;
                $deprecatedMonth += $good->monthTotalCost;
                $brandCostDeprecated['year'][$brand] += $good->yearTotalCost;
                $brandCostDeprecated['month'][$brand] += $good->monthTotalCost;
            }
            $brandCost['year'][$brand] += $good->yearTotalCost;
            $brandCost['month'][$brand] += $good->monthTotalCost;
            $brandIncomes['year'][$brand] += $good->yearIncomes;
            $brandIncomes['month'][$brand] += $good->monthIncomes;
            $brandProfit['year'][$brand] += $good->yearProfit;
            $brandProfit['month'][$brand] += $good->monthProfit;

            $good->calcOrder();
            if ($good->status == '<span class="badge badge-danger">хит - дефицит</span>') {
                $orders[$key] = $good;
                $totalBrandOrders[$brand] += $good->orderCost;
            }
        }
        $tmp = array_column($goods, 'monthProfit');
        array_multisort($tmp, SORT_DESC, $goods);
//$goods = array_slice($goods, 0, 10);
//        file_put_contents('debug', print_r($goods,true));

        return $this->render('index', [
            'model' => $model,
            'items' => $goods,
            'brands' => $brands,
            'common' => [
                'monthTotalCost' => array_sum(array_column($goods, 'monthTotalCost')),
                'yearTotalCost' => array_sum(array_column($goods, 'yearTotalCost')),
                'yearTotalIncomes' => array_sum(array_column($goods, 'yearIncomes')),
                'monthTotalIncomes' => array_sum(array_column($goods, 'monthIncomes')),
                'yearTotalProfit' => array_sum(array_column($goods, 'yearProfit')),
                'monthTotalProfit' => array_sum(array_column($goods, 'monthProfit')),
                'deprecatedYear' => $deprecatedYear,
                'deprecatedMonth' => $deprecatedMonth,
                'brandCost' => $brandCost,
                'brandIncomes' => $brandIncomes,
                'brandProfit' => $brandProfit,
                'brandCostDeprecated' => $brandCostDeprecated,
                'orderCost' => $totalBrandOrders,
            ],]);
    }

    public function actionUpdate() {
        if (!$this->rBac->canCreateStat()) {
            return $this->goHome();
        }

        $model = new Analytics();
        if ($model->update()) {
            return $this->redirect('index');
        }

        return $this->render('error', ['model' => $model]);
    }

    /**
     * @return array|string|\yii\web\Response
     * @var $model UserFiles
     */
    public function actionFiles() {
        if (!$this->rBac->canCreateStat()) {
            return $this->goHome();
        }

        $model = new UserFiles();
        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post())) {
                if (\Yii::$app->request->isAjax) {
                    \Yii::$app->response->format=Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                foreach ($model::FILENAMES as $propFile) {
                    $model->$propFile = UploadedFile::getInstance($model, $propFile);
                    if ($model->$propFile) {
                        $model->upload($model->$propFile, $propFile);
                    }
                }
            }
            $analytics = new Analytics();
            if ($analytics->update()) {
                return $this->redirect('index');
            }
        }

        return $this->render('files', ['model' => $model]);
    }

    public function actionTemplateDownload() {
        $file = 'files/себестоимость-шаблон.xlsx';

        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
    }
}