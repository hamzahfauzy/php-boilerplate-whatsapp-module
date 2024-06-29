<?php get_header() ?>

<div class="card mb-3">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0"><?=$device->name?> - <?=$device->phone ?? 'No Phone Number'?></p>
    </div>
    <div class="card-body text-center">
        <?php if($device->status == 'NOT CONNECTED'): ?>
        <?php if($device->qrcode): ?>
        <img src="<?=$device->qrcode?>" alt="" width="300px" height="300px">
        <?php else: ?>
            <h4>Cannot get qrcode. Please refresh this page</h4>
        <?php endif ?>
        <?php else: ?>
        <h4>Device is Connected. <a href="<?=routeTo('whatsapp/devices/logout', ['id' => $device->id]) ?>">Logout</a></h4>
        <?php endif ?>
    </div>
</div>

<?php if($device->status == 'NOT CONNECTED'): ?>
<script>
    setTimeout(function(){
        window.location.reload()
    }, 5000)
</script>
<?php endif ?>

<?php get_footer() ?>
