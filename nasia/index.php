<?php
define('PATH_ROOT', __DIR__);
define('URL_ROOT', 'http://banhangonline88.com/nasia/');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once PATH_ROOT . DS . 'config.php';

require_once PATH_ROOT . DS . 'libraries/core.php';
$core = core::getInstance();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- favicon -->
    <link href="favicon.png" rel=icon>


    <!-- font-awesome -->
    <link href="css/font-awesome.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery-ui.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="js/jqueryui-ruler/css/jquery.ui.ruler.css" rel="stylesheet">

    <!-- owl carousal -->
    <link href="css/owl.carousel.css" rel="stylesheet">

    <!-- Style CSS -->
    <link href="css/style.less" rel="stylesheet/less" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <![endif]-->
    <!-- #main-wrapper -->
    <?php include PATH_ROOT . DS . 'js_script.php'; ?>
</head>
<body>
<div class="a4"></div>
<div id="main-wrapper">
    <!-- Page Preloader -->
    <div id="preloader">
        <div id="status">
            <div class="status-mes"></div>
        </div>
    </div>

    <header class="header">
        <div class="container">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Home</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="#activity">Biểu đồ dạng thô <span
                                        class="sr-only">(current)</span></a>
                            </li>
                            <li><a href="#skills">Biểu đồ tinh chỉnh</a></li>
                            <li><a href="#produts">Biểu đồ theo thời gian thực</a></li>
                            <li><a href="#contact">Biều thời gian thực đã tinh chỉnh </a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">Language <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/cuongnd/">Vietnamese</a></li>
                                    <li><a href="/cuongnd/en.php">English</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
    </header>
    <!-- .header-->


    <section class="section-wrapper section-experience">
        <div class="container">
            <h3>Chọn giải tiêu hao nhiên liệu chuẩn</h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">From time</span>
                                <input class="template_date_from" readonly value="2016-06-23 11:30 PM"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">To time</span>
                                <input class="template_date_to" readonly value="2016-06-23 11:30 PM"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">From time</span>
                                <input class="date" value="2016-06-23 11:30 PM"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">To time</span>
                                <input class="date" value="2016-06-23 11:30 PM"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <button  class="btn btn-primary check">Check</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="graphs">

                    </div>
                </div>
            </div>
        </div>
        <!-- .container -->
    </section>
    <section class="section-wrapper section-experience">
        <div class="container">
            <h3>loai bo xung nhieu</h3>
        </div>
        <!-- .container -->
    </section>

</div>


</body>
</html>