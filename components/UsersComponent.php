<?php


namespace app\components;


use app\base\BaseComponent;
use app\models\Users;

class UsersComponent extends BaseComponent
{
    /**
     * @param $model Users
     * @return bool
     */
    public function saveUser(&$model):bool{
        if ($model->id == '' || $model->id != \Yii::$app->user->id) {
            return false;
        }

        $currentUser = Users::findOne(\Yii::$app->user->id);

        if ($model->newPassword)

        if (!$currentUser->save()) {
            $model->getErrors();
            return false;
        }

        return true;
    }

    /**
     * @param $model Users
     * @return bool
     */
    public function createUser(&$model):bool{
        $model->setRegistrationScenario();
        $model->passwordHash = $this->hashPassword($model->password);
        $model->authKey = $this->generateAuthKey();
        $model->active = 1;
        if($model->save()){
            return true;
        }

        return false;
    }

    /**
     * @param $model Users
     * @return bool
     */
    public function updateUser(&$model):bool{
        $model->passwordHash = $model->newPassword ? $this->hashPassword($model->newPassword) : $model->passwordHash;
        //$model->authKey = $this->generateAuthKey();
        //$model->active = 1;

        return $model->save();
    }

    private function generateAuthKey(){
        return \Yii::$app->security->generateRandomString();
    }

    private function hashPassword($password){
        return \Yii::$app->security->generatePasswordHash($password);
    }
}