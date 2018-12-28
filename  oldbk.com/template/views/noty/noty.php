<script>
    new Noty({
        text: "<?= isset($msg) ? $msg : '+' ?>",
        layout: "bottomRight",
        theme: "bootstrap-v4",
        type: "<?= isset($type) ? $type : 'success' ?>",
        timeout: <?= isset($time) ? $time : 5000 ?>
    }).show();
</script>