<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Deposits */

$this->title = 'Create Deposits';
$this->params['breadcrumbs'][] = ['label' => 'Deposits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deposits-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
