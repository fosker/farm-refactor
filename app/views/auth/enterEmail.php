<?php
use yii\helpers\Html;
?>
<div class="auth-reset">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 enter-email">
                <h2 class="text-center"><?= Html::encode($this->title); ?></h2>

                <?= Html::beginForm([''], 'post') ?>
                <div class="form-group <?= $error ? 'has-error' : '';?>">
                    <label class="control-label" for="email">Введите почтовый ящик</label>
                    <div class="input-group"><span class="input-group-addon"><b>@</b></span><?= Html::input('email', 'email', '', ['class'=>'form-control', 'id'=>'email']); ?></div>
                    <div class="help-block"><?=$error ?></div>
                </div>

                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>

                <?php Html::endForm(); ?>
            </div>
        </div>
</div>
