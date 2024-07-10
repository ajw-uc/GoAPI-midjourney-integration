
<div id="app" class="container">
    <?= view('navbar') ?>
    <div class="pt-5">
        <div class="text-center">
            <h1 class="display-1"><?= $code ?></h1>
            <?= $text ?>
        </div>
    </div>
</div>

<script>
    document.title = '<?= $code.' '.$text ?>';
</script>