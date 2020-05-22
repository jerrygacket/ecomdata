<?php


namespace app\rules;


use yii\helpers\ArrayHelper;
use yii\log\Logger;
use yii\rbac\Item;
use yii\rbac\Rule;

class ViewOwnerStatsRule extends Rule
{
    public $name='viewOwnerStats';

    /**
     * Executes the rule.
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $stats = ArrayHelper::getValue($params,'stats');
        if(!$stats){
            return false;
        }
        \Yii::getLogger()->log($stats->user_id.' '.\Yii::$app->user->getIdentity()->id,Logger::LEVEL_WARNING);
        return $activity->user_id==\Yii::$app->user->getIdentity()->id;
    }
}