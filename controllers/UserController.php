<?php


namespace app\controllers;


use app\base\BaseController;
use app\components\AuthComponent;
use app\components\UsersComponent;
use app\models\Users;
use yii\bootstrap4\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\web\Response;


class UserController extends BaseController
{
    public function actionIndex() {
        if (!$this->rbac->viewAllProfiles()) {
            return $this->redirect('/site/forbidden');
        }
        $model = new Users();
        $model->load(\Yii::$app->request->queryParams);
        $query = $model::find()->andWhere(['active' => '1']);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
            'sort' => [
                'defaultOrder' => [
                    'id'=>SORT_ASC
                ]
            ]
        ]);

        return $this->render('index', [
            'provider' => $provider,
        ]);
    }

    public function actionDelete() {
        if (!$this->rbac->canCreateUser()) {
            return $this->redirect('/site/forbidden');
        }

        $model = Users::findOne(['id' => \Yii::$app->request->queryParams['id'] ?? '']);
        if ($model && $model->username != 'admin') {
            $model->active = 0;
            if ($model->save()) {
                return $this->redirect('index');
            } else {
                print_r($model->errors);
                \Yii::$app->end(0);
            }
        }

        return $this->redirect('index');
    }


    public function actionCreate() {
        if (!$this->rbac->canCreateUser()) {
            return $this->redirect('/site/forbidden');
        }

        $model = new Users();
        if (\Yii::$app->request->isPost) {
            if ($model->load(\Yii::$app->request->post())) {
                if (\Yii::$app->request->isAjax) {
                    \Yii::$app->response->format=Response::FORMAT_JSON;
                    return ActiveForm::validate($model);
                }
                $component = \Yii::createObject(['class' => UsersComponent::class, 'nameClass' => Users::class]);
                if ($component->createUser($model)) {
                    $authManager = $this->rbac->getAuthManager();
                    $authManager->assign($authManager->getRole('user'),$model->id);
                    return $this->redirect('index');
                } else {
                    print_r($model->errors);
                    \Yii::$app->end(0);
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate() {
        if (!$this->rbac->canCreateUser()) {
            return $this->redirect('/site/forbidden');
        }
        $model = Users::find()->andWhere(['id' => \Yii::$app->request->queryParams['id'] ?? ''])->one() ?? new Users();
        if (\Yii::$app->request->isPost) {
            $model = Users::find()->andWhere(['id' => \Yii::$app->request->post('id') ?? ''])->one();
            if (\Yii::$app->request->isAjax) {
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            $model->active = intval(\Yii::$app->request->post('active') == 'on');
            $model->newPassword = \Yii::$app->request->post('newPassword', false);
            $component = \Yii::createObject(['class' => UsersComponent::class, 'nameClass' => Users::class]);
            if ($component->updateUser($model)) {
                return $this->redirect('index');
            }
        }

        return $this->render('update', ['model' => $model]);
    }
}