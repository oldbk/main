<?php
use components\Component\Slim\Middleware\ClientScript\ClientScript;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="StyleSheet" href="/newstyle_loc.css" type="text/css">
    <?php foreach ($app->clientScript->getCssFiles() as $cssFile): ?>
        <link rel="StyleSheet" href="<?= $cssFile ?>" type="text/css">
    <?php endforeach; ?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <?php foreach ($app->clientScript->getJsFiles(ClientScript::JS_POSITION_BEGIN) as $jsFile): ?>
        <script src="<?= $jsFile; ?>"></script>
    <?php endforeach; ?>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-warning {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc;
        }
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
    </style>
    <script>document.domain = 'oldbk.com';</script>
</head>
<body>
<?= $content; ?>

</body>
</html>