<?php

/* @var string $content Content of buffer */
/* @var \cordillera\middlewares\Layout $this */

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->getProperty('title', 'Error') ?></title>
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css"
          href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= app()->request->base_url ?>media/css/normalize.css">
    <link rel="stylesheet" type="text/css"
          href="<?= app()->request->base_url ?>media/css/cordillera.css">
    <script src="//code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>
</head>
<body class="error">
<h1><?= $this->getProperty('title', 'Error') ?></h1>

<div class="alert-danger alert fade in">
    <i class="fa fa-exclamation-triangle"></i>&nbsp;<?= $content ?>
</div>
</body>
</html>
