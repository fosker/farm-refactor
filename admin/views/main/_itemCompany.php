<?php
?>

<div class="row">
    <div class="col-md-5">
        <b class="compan"><?= $model->title; ?></b>
    </div>
    <?php if ($model->userCount): ?>
        <div class="col-md-3">
            <button type="button" class="btn btn-info more-pharmacies">+</button>
        </div>
    <?php endif; ?>
    <div class="col-md-4"><i><?= $model->userCount ?></i></div>
</div>
<div class="row pharmacies" style="display: none">
    <?php foreach ($model->pharmaciesCount as $pharmacy): ?>
        <?php if ($pharmacy->userCount): ?>
            <div class="row">
                <div class="col-md-8">
                    <p class="pharmacy" data-key="<?= $pharmacy->id ?>"><?= $pharmacy->name . ' (' . $pharmacy->address . ')'; ?></p>
                </div>
                <div class="col-md-4">
                    <i><?= $pharmacy->userCount ?></i>
                </div>
            </div>
            <br>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<br>