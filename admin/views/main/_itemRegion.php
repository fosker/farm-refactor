<?php
?>

<div class="row">
    <div class="col-md-5">
        <b><?= $model->name; ?></b>
    </div>
    <?php if ($model->userCount): ?>
        <div class="col-md-3">
            <button type="button" class="btn btn-info more-cities">+</button>
        </div>
    <?php endif; ?>
    <div class="col-md-4"><i><?= $model->userCount ?></i></div>
</div>
<div class="row cities" style="display: none">
    <?php foreach ($model->citiesCount as $city): ?>
        <?php if ($city->userCount): ?>
            <div class="row">
                <div class="col-md-8">
                    <p class="city" data-key="<?= $city->id?>"><?= $city->name; ?></p>
                </div>
                <div class="col-md-4">
                    <i><?= $city->userCount ?></i>
                </div>
            </div>
            <br>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<br>
