<?php

/*
 * This file is part of the Cordillera framework.
 *
 * (c) Robert Adrián Díaz <rad8329@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Made with love in Medellín
 */

use cordillera\base\Cordillera;
use cordillera\middlewares\Layout;

/* @var string $content */
/* @var \cordillera\middlewares\Layout $this */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->getProperty('title', 'Cordillera') ?></title>
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-flash.css"/>
    <link rel="stylesheet" type="text/css" href="<?= Cordillera::app()->request->base_url ?>media/css/cordillera.css">
    <link rel="stylesheet" type="text/css" href="<?= Cordillera::app()->request->base_url ?>media/css/custom.css">
    <script src="//code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?= Cordillera::app()->request->base_url ?>media/js/bootstrap-confirmation.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js" type="text/javascript"></script>
    <?= $this->publishRegisteredFiles() ?>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-main-navbar-collapse">
                <span class="sr-only"><?= Cordillera::app()->lang->translate('Toggle navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= Cordillera::app()->request->home ?>">Cordillera framework</a>
        </div>
		<div class="collapse navbar-collapse" id="bs-main-navbar-collapse">
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
<?php if (Cordillera::app()->session->get('flash.success')): ?>
    <div class="container">
        <div class="alert alert-info" role="alert">
            <?= Cordillera::app()->session->get('flash.success') ?>
        </div>
    </div>
<?php endif ?>
<?php if (Cordillera::app()->session->get('flash.error')): ?>
    <div class="container">
        <div class="alert alert-danger" role="alert">
            <?= Cordillera::app()->session->get('flash.error') ?>
        </div>
    </div>
<?php endif ?>
<?= $content ?>
<script src="<?= Cordillera::app()->request->base_url ?>media/js/cordillera.js" type="text/javascript"></script>
<?= $this->publishRegisteredFiles(Layout::END_SCOPE) ?>
</body>
</html>
