<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Главная -'.Yii::$app->name;
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Аналитика!</h1>

        <p class="d-sm-flex justify-content-between">
            <a class="btn btn-success" href="/analytics">Посмотреть</a>
            <?= Html::a('Загрузить файлы', '/analytics/files', ['class' => 'btn btn-primary mb-auto']) ?>
            <?= Html::a('Обновить данные', '/analytics/update', ['class' => 'btn btn-danger mb-auto']) ?>
        </p>
    </div>
</div>
