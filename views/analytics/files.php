<?php

/**
 * @var $model \app\models\UserFiles
 */

use yii\widgets\ActiveForm;
$this->title = 'Загрузка файлов -'.Yii::$app->name;
$form = ActiveForm::begin([
    'id' => 'userFiles-form',
    'method' => 'POST',
    'action' => Yii::$app->homeUrl.'analytics/files',
    'fieldConfig' => [
        'template' => "{input}\n{label}\n{hint}\n{error}",
    ],
    'options' => ['class' => '']
]);
?>
    <p class="h4 mb-4 text-center">Загрузить файлы</p>
    <div class="input-group  mb-5">
        <div class="custom-file">
            <?=$form->field($model, 'yearFile', ['options' => ['tag' => false,]])
                ->fileInput(['required'=>true, 'class'=>'custom-file-input', 'lang' => 'ru'])
                ->label('Выгрузка за год', ['class' => 'custom-file-label']); ?>
        </div>
    </div>
    <div class="input-group mb-5">
        <div class="custom-file">
            <?=$form->field($model, 'monthFile', ['options' => ['tag' => false,]])
                ->fileInput(['required'=>true, 'class'=>'custom-file-input', 'lang' => 'ru'])
                ->label('Выгрузка за месяц', ['class' => 'custom-file-label']); ?>
        </div>
    </div>
    <div class="input-group mb-5">
        <div class="custom-file">
            <?=$form->field($model, 'deficitFile', ['options' => ['tag' => false,]])
                ->fileInput(['required'=>true, 'class'=>'custom-file-input', 'lang' => 'ru'])
                ->label('Дефицит', ['class' => 'custom-file-label']); ?>
        </div>
    </div>
    <p>
        Для расчета себестоимости заполните и загрузите
        <a href=<?= Yii::$app->homeUrl.'analytics/template-download' ?>>файл</a>
    </p><br>
    <div class="input-group mb-5">
        <div class="custom-file">
            <?=$form->field($model, 'costFile', ['options' => ['tag' => false,]])
                ->fileInput(['required'=>false, 'class'=>'custom-file-input', 'lang' => 'ru'])
                ->label('Себестоимость', ['class' => 'custom-file-label']); ?>
        </div>
    </div>

    <!-- Sign in button -->
    <div class="text-center">
        <button class="btn btn-info my-4" type="submit">Загрузить</button>
    </div>

<?php ActiveForm::end(); ?>