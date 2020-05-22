<?php



/* @var $this \yii\web\View */
/* @var $model \app\models\LoginForm|\app\models\Users|null|\yii\db\ActiveRecord */

$form = \yii\bootstrap4\ActiveForm::begin([
    'id' => 'user-update-form',
    'method' => 'POST',
    'action' => Yii::$app->homeUrl.'user/update',
    'options' => ['class' => '']
]);

echo \yii\bootstrap4\Html::hiddenInput('id', $model->id);
?>
<?=$form->field($model, 'email', ['enableClientValidation'=>false,
    'enableAjaxValidation'=>true])
    ->textInput(['required'=>true]); ?>

    <div class="form-group field-users-newPassword validating">
        <label for="newPassword" class="active">Новый пароль</label>
        <input type="text" id="newPassword" class="form-control is-valid" name="newPassword" value="" aria-invalid="false">
        <div class="invalid-feedback"></div>
    </div>
    <div>
        <!-- Remember me -->
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="active" name="active" <?=$model->active ? 'checked' : ''?>>
            <label class="custom-control-label" for="active">Активный пользователь</label>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-success" type="submit">Сохранить</button>
        <?= \yii\helpers\Html::a('Отмена',['/user/index'], ['class' => 'btn btn-danger'])?>
    </div>
<?php \yii\bootstrap4\ActiveForm::end(); ?>