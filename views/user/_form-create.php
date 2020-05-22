<?php



/* @var $this \yii\web\View */
/* @var $model \app\models\LoginForm|\app\models\Users|null|\yii\db\ActiveRecord */

$form = \yii\bootstrap4\ActiveForm::begin([
    'id' => 'user-create-form',
    'method' => 'POST',
    'action' => Yii::$app->homeUrl.'user/create',
    'options' => ['class' => '']
]);
echo $form->field($model,'id')->hiddenInput()->label(false);
?>

<?=$form->field($model, 'username', ['enableClientValidation'=>false,
    'enableAjaxValidation'=>true])
    ->textInput(['required'=>true]); ?>

<?=$form->field($model, 'email', ['enableClientValidation'=>false,
    'enableAjaxValidation'=>true])
    ->textInput(['required'=>true]); ?>

<?=$form->field($model, 'password', ['enableClientValidation'=>false,
    'enableAjaxValidation'=>true])
    ->passwordInput(['required'=>true]); ?>
    <div>
        <!-- Remember me -->
        <div class="custom-control custom-checkbox">
            <?= $form->field($model, 'active', ['options' => ['tag' => false,]])
                ->checkbox([
                    'checked' => true,
                    'template' => '{input}{label}',
                    'class' => 'custom-control-input'
                ], false)
                ->label('Активный пользователь', ['class' => 'custom-control-label']); ?>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-success" type="submit">Сохранить</button>
        <?= \yii\helpers\Html::a('Отмена',['/user/index'], ['class' => 'btn btn-danger'])?>
    </div>
<?php \yii\bootstrap4\ActiveForm::end(); ?>