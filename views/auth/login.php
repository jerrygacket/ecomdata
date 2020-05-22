<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \app\models\Users */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход -'.Yii::$app->name;
//$this->params['breadcrumbs'][] = $this->title;
?>
<section class="mt-3">
    <div class="row">
        <div class="col-md-4 offset-md-4 col-12">
            <!-- Card -->
            <div class="card">
                <!-- Card body -->
                <div class="card-body">
                    <?= $this->render('_login-form', ['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
</section>
