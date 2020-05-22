<?php
echo '<h1></h1>';
/* @var $this yii\web\View */

/**
 * @var $model \app\models\Analytics
 */

use yii\helpers\Html;

$this->title = 'Ошибка -'.Yii::$app->name;
$this->params['wide'] = true;
$updateApi = Html::a('Обновить данные', '/analytics/update', ['class' => 'btn btn-danger']);
$updateFiles = Html::a('Загрузить файлы', '/analytics/files', ['class' => 'btn btn-primary']);
?>

<main class="mx-lg-5" style="padding-left: 0">
    <div class="container-fluid mt-3">
        <!-- Heading -->
        <div class="card mb-4 wow fadeIn">

            <!--Card content-->
            <div class="card-body d-sm-flex justify-content-between">
                <h1>Ошибки в получении данных</h1>
                <?=$updateFiles?>
                <?=$updateApi?>
            </div>
            <?php
            foreach ($model->errors as $key => $errors) {
                echo '<ul>';
                echo '<h2>'.$key.'</h2>';
                foreach ($errors as $error) {
                    echo '<li>'.$error.'</li>';
                }
                echo '</ul>';
            }?>
        </div>
    </div>
</main>