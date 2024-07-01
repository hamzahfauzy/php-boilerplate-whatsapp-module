<?php get_header() ?>

<?php foreach($documentations as $documentation): ?>
<div class="card mb-3">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0"><?=$documentation['title']?></p>
    </div>
    <div class="card-body">
        <?php foreach($documentation['items'] as $item): ?>
        <div>
            <h4><?=$item['title']?></h4>
            Endpoint : <?=$item['url']?><br>
            Method : <?=$item['method']?><br>
            Header : <?=$item['header']?><br>
            Param : <br><?=implode('<br>',$item['param'])?><br><br>
            <?php if($item['method'] == 'POST'): ?>
            Body : <br>
            <?php foreach($item['body'] as $field => $note): ?>
            <?=$field?> : <?= $note ?><br>
            <?php endforeach ?>
            <br><br>
            <?php endif ?>
        </div>
        <?php endforeach ?>
    </div>
</div>

<?php endforeach ?>

<?php get_footer() ?>
