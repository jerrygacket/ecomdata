<?php
namespace app\components;

use app\rules\ViewOwnerStatsRule;
use app\rules\ViewOwnerProfileRule;
use yii\base\Component;

class RbacComponent extends Component
{
    /**
     * @return \yii\rbac\ManagerInterface
     */
    public function getAuthManager(){
        return \Yii::$app->authManager;
    }

    public function generateRbac(){
        $authManager=$this->getAuthManager();
        /** удаляем все правила */
        $authManager->removeAll();
        $admin = $authManager->createRole('admin');
        $user = $authManager->createRole('user');
        $authManager->add($admin);
        $authManager->add($user);

        $createStat = $authManager->createPermission('createStat');
        $createStat->description='Создание статистики';

        $createUser = $authManager->createPermission('createUser');
        $createUser->description='Создание/редактирование пользователя';

        $viewAllStat = $authManager->createPermission('viewAllStats');
        $viewAllStat->description='Просмотр любых статистик';

        $viewAllProfiles = $authManager->createPermission('viewAllProfiles');
        $viewAllProfiles->description='Просмотр любых профилей';

        $viewOwnerStat = $authManager->createPermission('viewOwnerStat');
        $viewOwnerStat->description='Просмотр только своих статистик';

        $viewOwnerProfile=$authManager->createPermission('viewOwnerProfile');
        $viewOwnerProfile->description='Просмотр только своего профиля';

        $authManager->add($createStat);
        $authManager->add($createUser);
        $authManager->add($viewAllStat);
        $authManager->add($viewAllProfiles);
        $authManager->add($viewOwnerStat);
        $authManager->add($viewOwnerProfile);

        $authManager->addChild($user,$createStat);
        $authManager->addChild($user,$viewOwnerStat);
        $authManager->addChild($user,$viewOwnerProfile);

        $authManager->addChild($admin,$user);
        $authManager->addChild($admin,$createUser);
        $authManager->addChild($admin,$viewAllStat);
        $authManager->addChild($admin,$viewAllProfiles);

        $authManager->assign($admin,1);
        $authManager->assign($user,2);
    }

    public function canCreateStat(){
        return \Yii::$app->user->can('createStat');
    }

    public function canCreateUser(){
        return \Yii::$app->user->can('createUser');
    }

    public function canViewStat($stat = null){
        if(\Yii::$app->user->can('viewAllStat')){
            return true;
        }
        if(\Yii::$app->user->can('viewOwnerStat',['Stat'=>$stat])){
            return true;
        }
        return false;
    }

    public function canViewOwnerProfile($profile){
        if(\Yii::$app->user->can('viewOwnerProfile',['profile'=>$profile])){
            return true;
        }
        return false;
    }

    public function viewAllProfiles(){
        if(\Yii::$app->user->can('viewAllProfiles')){
            return true;
        }

        return false;
    }
}