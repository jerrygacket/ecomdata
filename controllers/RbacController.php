<?php


namespace app\controllers;


use app\components\RbacComponent;
use yii\web\Controller;

class RbacController extends Controller
{
    public function actionGen(){
        /** @var RbacComponent $rbac */
        $rbac=\Yii::$app->rbac;
        $rbac->generateRbac();
    }
}