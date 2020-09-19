<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property int $wallet_id
 * @property int|null $deposit_id
 * @property float|null $amount
 * @property int $created_at
 */
class Transactions extends \yii\db\ActiveRecord
{
    const SCENARIO_DEPOSIT = 'deposit';
    const SCENARIO_DEFAULT = 'default';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions';
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
            [['amount'], 'required'],
            [['amount'], 'number', 'min' => 1, 'max' => 100, 'message' => 'Неверный диапазон', 'on' => self::SCENARIO_DEPOSIT],
            [['user_id', 'wallet_id', 'deposit_id', 'created_at'], 'integer'],
            [['amount'], 'number', 'min' => 0],
            [['type'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'user_id' => 'User ID',
            'wallet_id' => 'Wallet ID',
            'deposit_id' => 'Deposit ID',
            'amount' => 'Amount',
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

    public function getDeposits()
    {
        return $this->hasOne(Deposits::className(), ['id' => 'deposit_id']);
    }
}
