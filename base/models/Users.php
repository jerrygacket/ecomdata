<?php

namespace app\base\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property int|null $active
 * @property string|null $passwordHash
 * @property string|null $token
 * @property string|null $authKey
 * @property string $created_on
 * @property string $updated_on
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['active'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['username'], 'string', 'max' => 128],
            [['email'], 'string', 'max' => 1024],
            [['passwordHash', 'token', 'authKey'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'active' => Yii::t('app', 'Active'),
            'passwordHash' => Yii::t('app', 'Password Hash'),
            'token' => Yii::t('app', 'Token'),
            'authKey' => Yii::t('app', 'Auth Key'),
            'created_on' => Yii::t('app', 'Created On'),
            'updated_on' => Yii::t('app', 'Updated On'),
        ];
    }
}
