<?php
?>

</br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-4">
            <b><?=$model->title;?></b>
        </div>
        <?php if($model->userCount):?>
            <div class="col-md-3"><button type="button" class="btn btn-info more-pharmacies">+</button></div>
        <?php endif;?>
        <div class="col-md-2"><i><?=$model->userCount?></i></div>
    </div>
</div>
<div class="row pharmacies" style="display: none">
    <div class="col-md-12">
        <ul style="list-style: none">
            <?php foreach($model->pharmaciesCount as $pharmacy):?>
                <?php if($pharmacy->userCount):?>
                    <li>
                        <br>
                        <div class="row">
                            <div class="col-md-8">
                                <?=$pharmacy->name . ' ('.$pharmacy->address.')';?>
                            </div>
                        <div>
                            <div class="col-md-2"><i>
                                    <?=$pharmacy->userCount?></i>
                            </div>
                        </div>
                            <br>
                    </li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
    </div>
</div>
</br>
