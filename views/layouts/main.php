<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="grey lighten-3">
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-light white scrolling-navbar',
        ],
    ]);
    $menuItems = [
        '<li class="nav-item">'
        .Html::a('<i class="fas fa-home"></i>', [Yii::$app->homeUrl],
            [
                'class' => 'nav-link waves-effect waves-light',
                'title' => 'На главную',
                'data-toggle' => 'tooltip',
            ])
        . '</li>'
    ];
    if (Yii::$app->user->isGuest) {
        echo '';
    } else {
        if (Yii::$app->user->identity->username == 'admin') {
//            $menuItems[] = '<li class="nav-item">'
//                .Html::a('<i class="fas fa-cogs"></i>', ['/site/about'],
//                    [
//                        'class' => 'nav-link waves-effect waves-light',
//                        'title' => 'Настройки',
//                        'data-toggle' => 'tooltip',
//                    ])
//                . '</li>';
//            $menuItems[] = '<li class="nav-item">'
//                .Html::a('<i class="fas fa-users"></i>', ['/users/index'],
//                    [
//                        'class' => 'nav-link waves-effect waves-light',
//                        'title' => 'Пользователи',
//                        'data-toggle' => 'tooltip',
//                    ])
//                . '</li>';
            $menuItems[] = '<li class="nav-item">'
                .Html::a('<i class="fas fa-envelope"></i>', ['/analytics/index'],
                    [
                        'class' => 'nav-link waves-effect waves-light',
                        'title' => 'Аналитика',
                        'data-toggle' => 'tooltip',
                    ])
                . '</li>';
        }


        $menuItems[] = '<li class="nav-item">'
            .Html::a(
                '('. Yii::$app->user->identity->username .')'.'<i class="fas fa-sign-out-alt"></i>',
                ['/auth/logout'],
                [
                    'class' => 'nav-link waves-effect waves-light',
                    'title' => 'Выход',
                    'data-toggle' => 'tooltip',
                ])
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ml-auto'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container<?= isset($this->params['wide']) && $this->params['wide'] ? '-fluid' : '' ?> pt-5">
        <?= $content ?>
    </div>
</div>

<footer class="page-footer font-small blue mt-3">
    <div class="footer-copyright text-center py-3">
        &copy; 1dplab <?= '2020'.(date('Y') == '2020' ? '' : ' - '.date('Y')) ?>
    </div>
</footer>

<?php $this->endBody() ?>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
</body>
</html>
<?php $this->endPage() ?>
