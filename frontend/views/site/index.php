<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <?php if (!Yii::$app->user->isGuest) { ?>
            <h1>Баланс пользователя: <?= round($wallet->ballance ?? 0, 2) ?> единиц</h1>

            <p><a class="btn btn-lg btn-success" href="<?= \yii\helpers\Url::to(['site/enter']) ?>">Пополнить</a></p>
            <p><a class="btn btn-lg btn-success" href="<?= \yii\helpers\Url::to(['site/deposit']) ?>">На депозит</a></p>

            <p><a class="btn btn-lg btn-success" href="<?= \yii\helpers\Url::to(['site/increase']) ?>">Ручной крон ;)</a></p>
        <?php } ?>

    </div>

</div>
