<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string|null $address
 * @property string $phone
 * @property string $created_at
 * @property string $updated_at
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
            [['name', 'username', 'email', 'phone'], 'required'],
            [['address'], 'string'],
            // [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['username'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 25],
            ['email', 'email'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // 'id' => 'ID',
            'name' => 'Name',
            'username' => 'Username',
            'email' => 'Email',
            'address' => 'Address',
            'phone' => 'Phone',
            // 'created_at' => 'Created At',
            // 'updated_at' => 'Updated At',
        ];
    }
}
