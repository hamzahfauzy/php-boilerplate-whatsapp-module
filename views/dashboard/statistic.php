<div class="row">
    <div class="col-12">
        <h1>Whatsapp Statistic</h1>
    </div>
    <?php foreach($data as $key => $value): ?>
    <div class="col-12 col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
            <h5 class="card-title text-uppercase text-muted mb-0"><?=strtoupper($key)?></h5>
            <span class="h2 font-weight-bold mb-0"><?=$value?></span>
            </div>
        </div>
    </div>
    <?php endforeach ?>
</div>