<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "deposits".
 *
 * @property int $id
 * @property int $user_id
 * @property int $wallet_id
 * @property float|null $invested
 * @property float|null $percent
 * @property int|null $active
 * @property int|null $duration
 * @property int|null $accrue_times
 * @property int $created_at
 */
class Deposits extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_CLOSE = 0;
    const DEPOSIT_PERCENT = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deposits';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'wallet_id', 'created_at'], 'required'],
            [['user_id', 'wallet_id', 'active', 'duration', 'accrue_times', 'created_at'], 'integer'],
            [['invested', 'percent'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'wallet_id' => 'Wallet ID',
            'invested' => 'Invested',
            'percent' => 'Percent',
            'active' => 'Active',
            'duration' => 'Duration',
            'accrue_times' => 'Accrue Times',
            'created_at' => 'Created At',
        ];
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getWallets()
    {
        return $this->hasOne(Wallets::className(), ['id' => 'wallet_id']);
    }
}
