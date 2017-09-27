<?php
define('PATH_ROOT', __DIR__);
define('URL_ROOT','http://banhangonline88.com/cuongnd/');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once PATH_ROOT.DS.'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title >MyProfile - Nguyễn Đình Cường</title>

    <!-- favicon -->
    <link href="favicon.png" rel=icon>


    <!-- font-awesome -->
    <link href="css/font-awesome.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery-ui.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">

    <!-- owl carousal -->
    <link href="css/owl.carousel.css" rel="stylesheet">

    <!-- Style CSS -->
    <link href="css/style.less" rel="stylesheet/less" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
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
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand"  href="#">Home</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#activity">Activity <span class="sr-only">(current)</span></a></li>
                        <li><a href="#skills">Skill</a></li>
                        <li><a href="#product">Portfolio</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Language <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/cuongnd/">Vietnamese</a></li>
                                <li><a href="/cuongnd/en.php">English</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <div class="row">
            <div class="col-md-3">
                <div class="profile-img">
                    <img src="img/cuong.jpg" class="img-responsive" alt=""/>
                </div>
                <!-- Profile Image -->
            </div>
            <div class="col-md-9">
                <div class="name-wrapper">
                    <h1 class="name">Nguyen Dinh Cuong</h1>
                    <div>Web programmer - <a class="" href="/cuongnd/cv_en_cuong_nd_stander.pdf"> Download my CV</a></div>
                    <h3>Link about me : <a href="http://banhangonline88.com/cuongnd/en.php">http://banhangonline88.com/cuongnd/en.php</a></h3>
                </div>
                <p>
                    As we all know, programming is not dry, it's as boring as people think it's a miniature world you can explore. In any career to succeed you also need to work hard and take time for it, take care of it like a child. But with the information technology in general and the programming profession in particular, more and more need. Why so ? Simply because this is a job not quite easy, the pressure is very high if you do not have a proper view, love and passion will be difficult to follow this career.
                </p>
                <p class="hide">
                    Tôi có 7 năm kinh nghiệm trang việc thiết kê website thương mại điện tử Tôi Thành thạo các công cụ thiết kế website và ngôn ngữ lập trình website
                    hiểu biết rõ ưu nhược điểm của từng hệ thống mã nguồn mở
                </p>

                <div class="row">
                    <div class="col-md-2">
                        <div class="personal-details">
                            <strong>07/02/1983</strong>
                            <small>Birthday</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="personal-details">
                            <strong>7</strong>
                            <small>years of experience</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="personal-details">
                            <strong>0966 742 999</strong>
                            <small>Mobile</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="personal-details">
                            <strong>nguyen.dinh.cuong1</strong>
                            <small>Skype</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="personal-details">
                            <strong>Reading comprehension English, basic communication</strong>
                            <small>Language</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>
<!-- .header-->


<section id="activity" class="expertise-wrapper section-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="section-title">
                    <h2>What am i doing</h2>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Design template joomla template</h3>

                            <div class="description">
                                Joomla is my special strength, I can customize and build templates joomla easily.

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Build module, component joomla</h3>

                            <div class="description">
                                With more than 7 years of experience in rescuing and learning the joomla system including reading the library code and customizing it helped me partly understand the structure of a large system.

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Writing plugin jquery</h3>

                            <div class="description">
                                Jquery is great, especially with the server language we can build for our customers. The frontend is really friendly and easy.
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Write Android application</h3>

                            <div class="description">
                                With the age of mobile technology, using java to write Android applications for the user is a must for a programmer like me.
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Writing application windows</h3>

                            <div class="description">
                                As we all know the profession is not dry, boring as people think it is a miniature that you can satisfy discovery. Wherever you need to work, you have to work hard and make time for it, take care of it like a child. But with the information, information and programming jobs will be used to be better. Why so? Simple but because of a job is a simple job, apply to high if you have a look of the signature, say and say you can not work to work.

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Equipped with some soft skills</h3>

                            <div class="description">
                                In addition to the knowledge to serve the job, the soft skills, such as communication, presentation ... I also often compensate for myself
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- .row -->


    </div>
</section>
<!-- .expertise-wrapper -->
    <?php
    $lang="en";
    ?>
    <?php include PATH_ROOT.DS.'skill.php';?>
<!-- .skills-wrapper -->

<section class="section-wrapper section-experience">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="section-title"><h2>Kinh nghiệm làm việc</h2></div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="content-item">
                            <small>2015 - Now</small>
                            <h3>Web programmer</h3>
                            <h4>Asianventure company</h4>
                            <h4>Specialize about system booking tour,hotel,transfer</h4>

                            <p>110 Ba trieu treet</p>
                        </div>

                        <!-- .experience-item -->
                    </div>
                    <div class="col-md-6">
                        <div class="content-item">
                            <small>2013 - 2015</small>
                            <h3>Web programmer</h3>
                            <h4>Joombooking.com</h4>
                            <h4>Specialize about system booking tour,hotel,transfer</h4>
                        </div>
                        <!-- .experience-item -->
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="content-item">
                            <small>2009 - 2013</small>
                            <h3>Web Developer</h3>

                            <p>Study and work in some, places Joomlart company specializing in template joomla</p>
                        </div>
                        <!-- .experience-item -->
                    </div>
                </div>
            </div>
            <!--.row-->
        </div>
    </div>
    <!-- .container -->
</section>
<!-- .section-experience -->

<section class="section-education section-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="section-title"><h2>Degree</h2></div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="content-item">
                            <h3>Information and Library Center - Posts and Telecommunications Institute of Technology(ILC - PTIT)</h3>
                            <h4>Faculty of Information Technology</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- .experience-item -->
                        <div class="content-item">
                            <h3>By MCITP network administrator</h3>

                        </div>
                        <!-- .experience-item -->
                    </div>
                </div>
                <!--.row-->
            </div>

        </div>
        <!--.row-->
    </div>
    <!-- .container -->
</section>
<!-- .section-education -->


<!-- .section-profile -->

<section id="product" class="section-wrapper portfolio-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="section-title">
                    <h2>Some my products</h2>
                    <span>Please click thumb image to play video</span>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="About banhangonline88.com" href="http://www.youtube.com/watch?v=3xL24Venor8">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-1.jpg" alt="">
                            </div>

                            <!-- portfolio-info -->
                        </a>
                        <div class="portfolio-info">
                            <h3>Ecommerce site</h3>
                            <small>banhangonline88.com</small>
                        </div>

                        <div class="description"> This Ecommerce site build on joomla technology,This system was built by myself, including the full functionality of an ecommerce site,
                            server language is php, client side uses jquery, javascript,html, css3, bootstrap 3
                            <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#modal_banhangonline_front_end_booking">
                                gallery project
                            </button>

                        </div>
                        <div class="modal fade" id="modal_banhangonline_front_end_booking" tabindex="-1" role="dialog" aria-labelledby="bhomyModalLabelfront_end_booking">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="bhomyModalLabelfront_end_booking">banhangonline88.com</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="carousel-example-generic-bho" class="carousel slide" >
                                            <!-- Indicators -->
                                            <?php
                                            $dir=PATH_ROOT.DS."img/banhangonline88";
                                            $banhangonline88 = scandir($dir);
                                            $banhangonline88=array_slice($banhangonline88,2);
                                            ?>
                                            <ol class="carousel-indicators">
                                                <?php for($i=0;$i<count($banhangonline88);$i++){ ?>
                                                    <li data-target="#carousel-example-generic" data-slide-to="0" class="<?php echo $i==0?'active':'' ?> "></li>
                                                <?php } ?>
                                            </ol>

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner" role="listbox">
                                                <?php for($i=0;$i<count($banhangonline88);$i++){ ?>
                                                    <?php
                                                    $file=$banhangonline88[$i];
                                                    ?>
                                                    <div class="item <?php echo $i==0?'active':'' ?>">
                                                        <img <?php echo $i == 0 ? '' : 'lazy-' ?>src="<?php echo URL_ROOT.'img/banhangonline88/'.$file ?>" alt="<?php echo $file ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <!-- Controls -->
                                            <a class="left carousel-control" href="#carousel-example-generic-bho" role="button" data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#carousel-example-generic-bho" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item" href="#">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-2.jpg" alt="">
                            </div>

                            <!-- portfolio-info -->
                        </a>
                        <div class="portfolio-info">
                            <h3>Tour, transfer,hotel</h3>
                            <small>asianventure.com</small>
                        </div>
                        <div class="description">This website build booking tour,
                            I am a person who directly analyzes functions, plans, direct assignments. website written in php, jquery, html, css3, bootstrap 3
                        </div>
                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item" href="#">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-3.jpg" alt="">
                            </div>

                            <!-- portfolio-info -->
                        </a>
                        <div class="portfolio-info">
                            <h3>Joomla template</h3>
                            <small>joomlart.com</small>
                        </div>
                        <div class="description">
                            Previously I studied at joomlart website design company and involved in some small development of sample products for the company.
                        </div>
                        <!-- .portfolio-item -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="Booking manager transfer, hotel, excursion" href="http://www.youtube.com/watch?v=BfzCNHFklSk">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-4.jpg" alt="">
                            </div>

                            <!-- portfolio-info -->
                        </a>
                        <div class="portfolio-info">
                            <h3>Backend tour, transfer,hotel manager</h3>
                            <small>viethandtravel.com</small>
                        </div>

                        <div class="description">
                            This is a management system for booking tours, management and operation during the tour, calculate the details for each tour participant,uses jquery, javascript,html, css3, bootstrap 3
                        </div>
                        <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#modal_backend_end_booking">
                            gallery project
                        </button>
                        <!-- Modal -->
                        <div class="modal fade" id="modal_backend_end_booking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Backend booking manger</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="carousel-example-generic-back_end_booking" class="carousel slide" data-ride="carousel">
                                            <!-- Indicators -->
                                            <?php
                                            $dir=PATH_ROOT.DS."img/backend_end_booking";
                                            $back_end_booking = scandir($dir);
                                            $back_end_booking=array_slice($back_end_booking,2);
                                            ?>
                                            <ol class="carousel-indicators">
                                                <?php for($i=0;$i<count($back_end_booking);$i++){ ?>
                                                 <li data-target="#carousel-example-generic" data-slide-to="0" class="<?php echo $i==0?'active':'' ?> "></li>
                                                <?php } ?>
                                            </ol>

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner" role="listbox">
                                                <?php for($i=0;$i<count($back_end_booking);$i++){ ?>
                                                    <?php
                                                    $file=$back_end_booking[$i];
                                                    ?>
                                                    <div class="item <?php echo $i==0?'active':'' ?>">
                                                        <img <?php echo $i == 0 ? '' : 'lazy-' ?>src="<?php echo URL_ROOT.'img/backend_end_booking/'.$file ?>" alt="<?php echo $file ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <!-- Controls -->
                                            <a class="left carousel-control" href="#carousel-example-generic-back_end_booking" role="button" data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#carousel-example-generic-back_end_booking" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="Frontend booking tour passenger and build room"  href="http://www.youtube.com/watch?v=DhP8Rsc2SxU">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-5.jpg" alt="">
                            </div>

                            <!-- Modal -->
                            <!-- portfolio-info -->
                        </a>
                        <div class="portfolio-info">
                            <h3>frontend tour, transfer,hotel manager</h3>
                            <small>viethandtravel.com</small>
                        </div>

                        <div class="description">
                            This is website booking tour on front end, calculate the details for each tour participant.uses jquery, javascript,html, css3, bootstrap 3
                        </div>
                        <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#modal_front_end_booking">
                            gallery project
                        </button>

                        <div class="modal fade" id="modal_front_end_booking" tabindex="-1" role="dialog" aria-labelledby="myModalLabelfront_end_booking">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabelfront_end_booking">front booking</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="carousel-example-generic-front_end_booking" class="carousel slide" >
                                            <!-- Indicators -->
                                            <?php
                                            $dir=PATH_ROOT.DS."img/front_end_booking";
                                            $front_end_booking = scandir($dir);
                                            $front_end_booking=array_slice($front_end_booking,2);
                                            ?>
                                            <ol class="carousel-indicators">
                                                <?php for($i=0;$i<count($front_end_booking);$i++){ ?>
                                                    <li data-target="#carousel-example-generic" data-slide-to="0" class="<?php echo $i==0?'active':'' ?> "></li>
                                                <?php } ?>
                                            </ol>

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner" role="listbox">
                                                <?php for($i=0;$i<count($front_end_booking);$i++){ ?>
                                                    <?php
                                                    $file=$front_end_booking[$i];
                                                    ?>
                                                    <div class="item <?php echo $i==0?'active':'' ?>">
                                                        <img <?php echo $i == 0 ? '' : 'lazy-' ?>src="<?php echo URL_ROOT.'img/front_end_booking/'.$file ?>" alt="<?php echo $file ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <!-- Controls -->
                                            <a class="left carousel-control" href="#carousel-example-generic-front_end_booking" role="button" data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#carousel-example-generic-front_end_booking" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="Booking transfer, hotel, excursion" href="http://www.youtube.com/watch?v=4brQaXGsbpo">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-6.jpg" alt="">
                            </div>

                            <!-- portfolio-info -->
                        </a>
                        <div class="portfolio-info">
                            <h3>frontend tour, transfer,hotel manager</h3>
                            <small>viethandtravel.com</small>
                        </div>

                        <div class="description">
                            This is website booking tour on front end, calculate the details for each tour participant.uses jquery, javascript,html, css3, bootstrap 3
                        </div>
                        <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#modal_front_end_booking">
                            gallery project
                        </button>

                        <!-- .portfolio-item -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</section>
<!-- .portfolio -->



<section id="contact" class="section-contact section-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="section-title">
                    <h2>Contact</h2>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3">
                        <address>
                            <strong>Address</strong><br>
                            Hanoi<br>
                            Tien phuong, Chuong My

                        </address>
                    </div>
                    <div class="col-md-3">
                        <address>
                            <strong>Mobile 1</strong><br>
                            0966742999
                        </address>
                    </div>
                    <div class="col-md-3">
                        <address>
                            <strong>Mobile 2</strong><br>
                            0936006058
                        </address>
                    </div>
                    <div class="col-md-3">
                        <address>
                            <strong>Email</strong><br>
                            <a href="mailto:#">nguyendinhcuong@gmail.com</a>
                        </address>
                    </div>

                </div>
                <!--.row-->

                <div class="feedback-form">
                    <h3>Contact</h3>

                    <form id="contactForm" action="sendemail.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="name" required="" class="form-control" id="InputName"
                                   placeholder="Full name">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" required="" class="form-control" id="InputEmail"
                                   placeholder="Email">
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" id="InputSubject"
                                   placeholder="Subject">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="4" required="" name="message" id="message-text"
                                      placeholder="content"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
                <!-- .feedback-form -->

            </div>
        </div>
        <!--.row-->

    </div>
    <!--.container-fluid-->
</section>
<!--.section-contact-->


<!-- .footer -->

</div>
<!-- #main-wrapper -->


<!-- jquery -->
<?php include PATH_ROOT.DS.'js_script.php';?>
</body>
</html>