<?php
?>

</br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-4">
            <b><?=$model->name;?></b>
        </div>
        <?php if($model->userCount):?>
            <div class="col-md-3"><button type="button" class="btn btn-info more-cities">подробнее</button></div>
        <?php endif;?>
        <div class="col-md-2"><i><?=$model->userCount?></i></div>
    </div>
</div>
<div class="row cities" style="display: none">
    <div class="col-md-12">
        <ul style="list-style: none">
            <?php foreach($model->cities as $city):?>
                <?php if($city->userCount):?>
                    <li>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <?=$city->name;?>
                            </div>
                        <div>
                            <div class="col-md-4"><i><?=$city->userCount?></i></div>
                        </div>
                            <br>
                    </li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
    </div>
</div>
</br>
