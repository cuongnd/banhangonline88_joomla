<?php
define('PATH_ROOT', __DIR__);
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
                    <img src="img/img-profile.jpg" class="img-responsive" alt=""/>
                </div>
                <!-- Profile Image -->
            </div>
            <div class="col-md-9">
                <div class="name-wrapper">
                    <h1 class="name">Nguyen Dinh Cuong</h1>
                    <span>Web programmer</span>
                </div>
                <p>
                    Như chúng ta đã biết nghề lập trình không khô khan, nhàn chán như mọi người vẫn nghĩ mà nó là cả một thế giới thu nhỏ mà bạn có thể thỏa sức khám phá. Ở nghề nào cũng vậy để thành công bạn cũng cần phải nỗ lực và dành thời gian cho nó, chăm chút nó như một đứa trẻ vậy. Nhưng với công nghệ thông tin nói chung và nghề lập trình nói riêng lại càng cần hơn. Tại sao vậy ? Đơn giản thôi vì đây là một nghề không hoàn toàn dễ dàng, áp lực rất cao nếu bạn không có một cách nhìn nhận đúng đắn, yêu thích và đam mê sẽ rất khó để theo được nghề này.
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

                            <p>
                                Joomla is my special strength, I can customize and build templates joomla easily.

                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Build module, component joomla</h3>

                            <p>
                                With more than 7 years of experience in rescuing and learning the joomla system including reading the library code and customizing it helped me partly understand the structure of a large system.                            </p>

                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Writing plugin jquery</h3>

                            <p>
                                Jquery is great, especially with the server language we can build for our customers. The frontend is really friendly and easy.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Write Android application</h3>

                            <p>
                                With the age of mobile technology, using java to write Android applications for the user is a must for a programmer like me.
                            </p>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Writing application windows</h3>

                            <p>
                                To serve some work needs to run on the widows should also have themselves equipped for C # language. VB.NET has served a number of purposes
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Equipped with some soft skills</h3>

                            <p>
                                In addition to the knowledge to serve the job, the soft skills, such as communication, presentation ... I also often compensate for myself
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- .row -->


    </div>
</section>
<!-- .expertise-wrapper -->

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
                            <small>2013 - Hiện tại</small>
                            <h3>Lập trình viên</h3>
                            <h4>Công ty Asianventure</h4>
                            <h4>Chuyên về hệ thống booking tour,hotel,transfer</h4>

                            <p>110 Bà triệu</p>
                        </div>
                        <!-- .experience-item -->
                    </div>
                    <div class="col-md-6">
                        <div class="content-item">
                            <small>2013 - 2015</small>
                            <h3>Lập trình viên</h3>
                            <h4>Joombooking.com</h4>
                            <h4>Chuyên về hệ thống booking tour,hotel,transfer</h4>
                        </div>
                        <!-- .experience-item -->
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="content-item">
                            <small>2009 - 2013</small>
                            <h3>Web Developer</h3>
                            <h4>ghiên cứu chuyện sâu về joomla</h4>

                            <p>Học tập và làm việc ở một số nơi trong đó có công ty Joomlart chuyên về template joomla</p>
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
                <div class="section-title"><h2>Bằng cấp</h2></div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="content-item">
                            <h3>Học viện công nghệ bưu chính viễn thông</h3>
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

                            <div class="portfolio-info">
                                <h3>Ecommerce site</h3>
                                <small>banhangonline88.com</small>
                            </div>
                            <!-- portfolio-info -->
                        </a>
                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item" href="#">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-2.jpg" alt="">
                            </div>

                            <div class="portfolio-info">
                                <h3>Tour, transfer,hotel</h3>
                                <small>asianventure.com</small>
                            </div>
                            <!-- portfolio-info -->
                        </a>
                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item" href="#">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-3.jpg" alt="">
                            </div>

                            <div class="portfolio-info">
                                <h3>Joomla template</h3>
                                <small>joomlart.com</small>
                            </div>
                            <!-- portfolio-info -->
                        </a>
                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="Booking manager transfer, hotel, excursion" href="http://www.youtube.com/watch?v=BfzCNHFklSk">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-4.jpg" alt="">
                            </div>

                            <div class="portfolio-info">
                                <h3>Backend tour, transfer,hotel manager</h3>
                                <small>viethandtravel.com</small>
                            </div>
                            <!-- portfolio-info -->
                        </a>
                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="Frontend booking tour passenger and build room"  href="http://www.youtube.com/watch?v=DhP8Rsc2SxU">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-5.jpg" alt="">
                            </div>

                            <div class="portfolio-info">
                                <h3>frontend tour, transfer,hotel manager</h3>
                                <small>viethandtravel.com</small>
                            </div>
                            <!-- Modal -->
                            <!-- portfolio-info -->
                        </a>

                        <!-- .portfolio-item -->
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a class="portfolio-item youtube" title="Booking transfer, hotel, excursion" href="http://www.youtube.com/watch?v=4brQaXGsbpo">
                            <div class="portfolio-thumb">
                                <img src="img/portfolio-6.jpg" alt="">
                            </div>

                            <div class="portfolio-info">
                                <h3>frontend tour, transfer,hotel manager</h3>
                                <small>viethandtravel.com</small>
                            </div>
                            <!-- portfolio-info -->
                        </a>
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
                            Hà Nội<br>
                            Tiên Phương, Chương Mỹ

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
                    <h3>Liên hệ</h3>

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