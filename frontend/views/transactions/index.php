<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Transactions';
?>

<div class="transactions-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'type',
            'user_id',
            'wallet_id',
            'deposit_id',
            'amount',
            [
                'attribute' => 'created_at',
                'value' => function ($data) {
                    return date('d-m-Y H:i', $data->created_at);
                }
            ],
        ],
    ]); ?>

</div>
