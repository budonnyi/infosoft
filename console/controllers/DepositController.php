<?php

namespace console\controllers;

use common\models\Deposits;
use common\models\Wallets;
use Yii;
use yii\helpers\ArrayHelper;

Class AlertController extends \yii\console\Controller
{
    public function actionIncrease()
    {
        $deposits = Deposits::find()->where(['!=', 'active', Deposits::STATUS_CLOSE])->all();

        foreach ($deposits as $depositItem) {
            $depositItem->accrue_times++;
            $depositItem->duration += 60;
            if($depositItem->accrue_times == 10) {
                $depositItem->active = Deposits::STATUS_CLOSE;
            }
            $depositItem->save(false);

            $walletModel = Wallets::findOne(['id' => $depositItem->wallet_id]);
            $walletModel->ballance += round($depositItem->invested * $depositItem->percent / 100, 2);
            $walletModel->save(false);
        }

        exit(0);
    }
}
