<html>
<head>
    <title>Error</title>
</head>
<body>
<h2>Произошла ошибка!</h2>
<? if (isset($logId) && $logId) : ?>

    <p>
        <small><?= $logId ?></small>
    </p>

<? endif; ?>
</body>
</html>