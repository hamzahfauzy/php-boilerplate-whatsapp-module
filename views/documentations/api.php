<?php get_header() ?>

<div class="card mb-3">
    <div class="card-header d-flex flex-grow-1 align-items-center">
        <p class="h4 m-0">API Key</p>
    </div>
    <div class="card-body">
        <div class="input-group is-invalid">
            <div class="input-group-prepend">
                <span class="input-group-text" style="cursor:pointer;" id="validatedInputGroupPrepend" onclick="copyApiKey()"><i class="fas fa-copy fa-fw"></i> Copy</span>
            </div>
            <input type="text" id="apikey" class="form-control" readonly value="<?= (new Core\JwtAuth)->generate([
                'user_id' => auth()->id
            ]) ?>">
        </div>
    </div>
</div>
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

<script>
function copyApiKey() {
  // Get the text field
  var copyText = document.getElementById("apikey");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

   // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);

  // Alert the copied text
  alert("Copied the text: " + copyText.value);
}
</script>

<?php get_footer() ?>
