<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Deposits';
?>

<div class="deposits-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'user_id',
            'wallet_id',
            'invested',
            'percent',
            [
                'attribute' => 'active',
                'value' => function ($data) {
                    return $data->active ? 'Активный' : 'Закрыт';
                }
            ],

            'duration',
            'accrue_times',
            [
                'attribute' => 'created_at',
                'value' => function ($data) {
                    return date('d-m-Y H:i', $data->created_at);
                }
            ],
        ],
    ]); ?>

</div>
