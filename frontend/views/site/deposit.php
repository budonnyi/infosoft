<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Transactions */
/* @var $form ActiveForm */

$this->title = 'Положить на депозит';

?>
<div class="site-enter">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'amount') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Пополнить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-enter -->
