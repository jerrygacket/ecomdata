<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%rbac}}`.
 */
class m200318_123219_create_rbac_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $componentRbac = Yii::$app->rbac;
        $componentRbac->generateRbac();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $componentRbac = Yii::$app->rbac;
        $componentRbac->cleanRbac();
    }
}
