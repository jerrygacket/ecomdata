<?php

use yii\db\Migration;

/**
 * Class m200318_111946_insertData_users_table
 */
class m200318_111946_insertData_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('users',[
            'id'=>1,
            'email'=>'admin@test.ru',
            'username'=>'admin',
            'passwordHash'=>\Yii::$app->security->generatePasswordHash('123456'),
        ]);
        $this->insert('users',[
            'id'=>2,
            'username'=>'user',
            'email'=>'user@test.ru',
            'passwordHash'=>\Yii::$app->security->generatePasswordHash('123456'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('users');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_111946_insertData_users_table cannot be reverted.\n";

        return false;
    }
    */
}
