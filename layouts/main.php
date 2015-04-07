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

use \cordillera\base\Application;
use \cordillera\helpers\Url;

/** @var string $content Content of buffer */
/** @var \cordillera\middlewares\Layout $this */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->getProperty("title", "Cordillera") ?></title>
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css"
          href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo Application::getRequest()->base_url ?>/media/css/normalize.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo Application::getRequest()->base_url ?>/media/css/cordillera.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Application::getRequest()->base_url ?>/media/css/custom.css">
    <script src="//code.jquery.com/jquery-2.1.3.min.js" type="text/javascript"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo Application::getRequest()->base_url ?>/media/js/cordillera.js" type="text/javascript"></script>
    <?php echo $this->publishRegisteredFiles() ?>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only"><?php echo Application::getLang()->translate('Toggle navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo Application::getRequest()->home ?>">Cordillera framework</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li id="link_1">
                    <a href="<?php echo cordillera\helpers\Url::relative("examples/routes/index") ?>">Google MAPS API</a>
                </li>
                <?php if (!Application::getAuth()->id): ?>
                    <li id="link_2">
                        <a href="<?php echo Url::relative("examples/site/login") ?>">
                            <?php echo Application::getLang()->translate('Login') ?>
                        </a>
                    </li>
                <?php else: ?>
                    <li id="link_3">
                        <a href="<?php echo Url::relative("examples/site/logout") ?>">
                            <?php echo Application::getLang()->translate(
                                'Logout (%s)', [Application::getAuth()->data['username']]
                            ) ?>
                        </a>
                    </li>
                <?php endif ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
<?php if (Application::getSession()->get("flash.success")): ?>
    <div class="container">
        <div class="alert alert-info" role="alert">
            <?php echo Application::getSession()->get("flash.success") ?>
        </div>
    </div>
<?php endif ?>
<?php if (Application::getSession()->get("flash.error")): ?>
    <div class="container">
        <div class="alert alert-danger" role="alert">
            <?php echo Application::getSession()->get("flash.error") ?>
        </div>
    </div>
<?php endif ?>
<?php echo $content ?>
</body>
</html>