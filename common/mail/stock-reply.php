<?php
?>
<?php if ($reply->imagePath) : ?>
    <h4><b>����</b></h4>
    <img src="<?= $reply->imagePath ?>" alt="����" style="width:90%">
<?php endif; ?>
<?php if ($reply->comment) : ?>
    <h4><b>�����������</b></h4>
    <p><?= $reply->comment ?></p>
<?php endif; ?>
