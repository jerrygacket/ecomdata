<?php

/* @var $this yii\web\View */

/**
 * @var $model \app\models\Analytics
 */

use yii\helpers\Html;

$this->title = 'Аналитика -'.Yii::$app->name;
$this->params['wide'] = true;
$updateApi = Html::a('Обновить данные', '/analytics/update', ['class' => 'btn btn-danger mb-auto']);
$updateFiles = Html::a('Загрузить файлы', '/analytics/files', ['class' => 'btn btn-primary mb-auto']);
?>

<main class="mx-lg-5" style="padding-left: 0">
    <div class="container-fluid mt-3">
        <!-- Heading -->
        <div class="card mb-4 wow fadeIn">

            <!--Card content-->
            <div class="card-body d-sm-flex justify-content-between">
                <h1>Статистика Wildberries</h1>
                <p class="card-text">
                    <?php
                    $userFiles = 'files/'.\Yii::$app->user->id;
                    foreach (\app\models\UserFiles::FILENAMES as $key => $file) {
                        $filename = $userFiles.'/'.$file.'.xlsx';
                        echo $key.(file_exists($filename)
                                ? ' <span class="green-text">загружен</span>'
                                : ' <span class="red-text">отсутствует</span>').'<br>';
                    }
                    ?>
                </p>
                <p class="card-text">
                    Выручка: год - <?=$common['yearTotalIncomes']?>, месяц - <?=$common['monthTotalIncomes']?><br>
                    Прибыль: год - <?=$common['yearTotalProfit']?>, месяц - <?=$common['monthTotalProfit']?><br>
                    Себестоимость: год - <?=$common['yearTotalCost']?>, месяц - <?=$common['monthTotalCost']?>
                </p>
                <?=$updateFiles?>
                <?=$updateApi?>
            </div>
            <div class="card-body d-sm-flex justify-content-between">
                <p></p>
            </div>

        </div>
        <!-- Heading -->
        <?php
        if (array_key_exists('Данные', $model->errors)) {
            foreach ($model->errors['Данные'] as $error) {
                echo '<p>'.$error.'</p>';
            }
        }
        ?>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <?php foreach ($brands as $key => $brand) {
                if (!$brand)
                    continue;
            ?>
            <li class="nav-item">
                <a class="nav-link <?=$key == 0 ? 'active' : ''?>" id="brand<?=$key?>-tab" data-toggle="tab" href="#brand<?=$key?>" role="tab" aria-controls="brand<?=$key?>"
                        aria-selected="<?=$key == 0 ? 'true' : 'false'?>"><?=$brand?></a>
            </li>
            <?php } ?>
        </ul>

        <div class="tab-content" id="myTabContent">
            <?php foreach ($brands as $key => $brand) {
                if (!$brand)
                    continue;
            ?>
            <div class="tab-pane fade <?=$key == 0 ? 'show active' : ''?>" id="brand<?=$key?>" role="tabpanel" aria-labelledby="brand<?=$key?>-tab">
                <!--Card-->
                <div class="card">

                    <!--Card content-->
                    <div class="card-body">
                        <p>
                            Выручка: год - <?=$common['brandIncomes']['year'][$brand]?>, месяц - <?=$common['brandIncomes']['month'][$brand]?><br>
                            Прибыль: год - <?=$common['brandProfit']['year'][$brand]?>, месяц - <?=$common['brandProfit']['month'][$brand]?><br>
                            Себестоимость: год - <?=$common['brandCost']['year'][$brand]?>, месяц - <?=$common['brandCost']['month'][$brand]?>
                        </p>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col">Товар</th>
                                <th scope="col">Продано за год</th>
                                <th scope="col">Продано за месяц</th>
                                <th scope="col">Дефицит</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $good) {
                                if ($good->brand != $brand) {
                                    continue;
                                } ?>
                            <tr>
                                <td width="32%">
                                    <p>
                                        <a data-fancybox="gallery" href="<?=$good->image?>" style="float: left;margin-right: 5px"><img height="200px" src="<?=$good->image?>" alt=""></a>
                                        <?=$good->brand?> <?=$good->ownerID?><br>
                                        <?=$good->name?><br>
                                        <?=$good->status?><br>
                                        Себестоимость на складе: <?=$good->selfCost * $good->yearBalanceTotals?> руб.
                                    </p>
                                    <table>
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Год</th>
                                            <th>Месяц</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Выручка</td>
                                            <td><?=$good->yearIncomes?></td>
                                            <td><?=$good->monthIncomes?></td>
                                        </tr>
                                        <tr>
                                            <td>Прибыль</td>
                                            <td><?=$good->yearProfit?></td>
                                            <td><?=$good->monthProfit?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Заказано</th>
                                            <th>Продано</th>
                                            <th>Выкуп</th>
                                            <th>Выручка</th>
                                            <th>Прибыль</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($good->sizes['year'] as $size) {?>
                                        <tr>
                                            <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($size['ordersTooltip'])?>"><?=$size['ordersTotals']?> шт.</a></td>
                                            <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($size['salesTooltip'])?>"><?=$size['salesTotals']?> шт.</a></td>
                                            <td><?=$size['percentTotals']?> %</td>
                                            <td><?=$size['incomes']?></td>
                                            <td><?=$size['profit']?></td>
                                        </tr>
                                        <?php } ?>
                                        </tbody>
                                        <!--                        <tfoot>-->
                                        <!--                        <tr>-->
                                        <!--                            <th><?=$good->yearOrderTotals?> шт.</th>-->
                                        <!--                            <th><?=$good->yearSaleTotals?> шт.</th>-->
                                        <!--                            <th><?=$good->yearPercentTotals?> %</th>-->
                                        <!--                        </tr>-->
                                        <!--                        </tfoot>-->
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Заказано</th>
                                            <th>Продано</th>
                                            <th>Выкуп</th>
                                            <th>Выручка</th>
                                            <th>Прибыль</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($good->sizes['month'] as $size) {?>
                                        <tr>
                                            <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($size['ordersTooltip'])?>"><?=$size['ordersTotals']?> шт.</a></td>
                                            <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($size['salesTooltip'])?>"><?=$size['salesTotals']?> шт.</a></td>
                                            <td><?=$size['percentTotals']?> %</td>
                                            <td><?=$size['incomes']?></td>
                                            <td><?=$size['profit']?></td>
                                        </tr>
                                        <?php } ?>
                                        </tbody>
                                        <!--                        <tfoot>-->
                                        <!--                        <tr>-->
                                        <!--                            <th><?=$good->monthOrderTotals?> шт.</th>-->
                                        <!--                            <th><?=$good->monthSaleTotals?> шт.</th>-->
                                        <!--                            <th><?=$good->monthPercentTotals?> %</th>-->
                                        <!--                        </tr>-->
                                        <!--                        </tfoot>-->
                                    </table>
                                </td>
                                <td>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Наличие</th>
                                            <th>Дефицит</th>
                                            <th>Ожидание</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($good->deficit)) { ?>
                                            <?php foreach ($good->sizes['year'] as $size) {?>
                                                <tr>
                                                    <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($size['balanceTooltip'] ?? '')?>"><?=$size['balanceTotals'] ?? 0?></a></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php foreach ($good->deficit as $key => $deficit) {?>
                                            <tr>
                                                <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($good->sizes['year'][$key]['balanceTooltip'] ?? '')?>"><?=$good->sizes['year'][$key]['balanceTotals'] ?? 0?></a></td>
                                                <td><a style="color: #0d47a1;text-decoration: underline;text-decoration-style: dotted;" href="#" data-toggle="tooltip" data-html="true" title="<?=trim($deficit['tooltip'])?>"><?=$deficit['deficit'] ?? 0?></a></td>
                                                <td><?=$deficit['wishList'] ?? 0?></td>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                        <!--                        <tfoot>-->
                                        <!--                        <tr>-->
                                        <!--                            <th><?=$good->monthBalanceTotals?> шт.</th>-->
                                        <!--                            <th><?=$good->deficitTotals?> шт.</th>-->
                                        <!--                            <th><?=$good->wishListTotals?> шт.</th>-->
                                        <!--                        </tr>-->
                                        <!--                        </tfoot>-->
                                    </table>
                                </td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</main>