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
                        <li class="active"><a href="#activity">Hoạt động <span class="sr-only">(current)</span></a></li>
                        <li><a href="#skills">Kỹ năng</a></li>
                        <li><a href="#produts">Sản phẩm</a></li>
                        <li><a href="#contact">Liên hệ</a></li>
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
                    <h1 class="name">Nguyễn Đình Cường</h1>
                    <span>Web programmer</span>
                    <h3>http://banhangonline88.com/cuongnd/</h3>
                </div>
                <p>
                    Như chúng ta đã biết nghề lập trình không khô khan, nhàn chán như mọi người vẫn nghĩ mà nó là cả một thế giới thu nhỏ mà bạn có thể thỏa sức khám phá. Ở nghề nào cũng vậy để thành công bạn cũng cần phải nỗ lực và dành thời gian cho nó, chăm chút nó như một đứa trẻ vậy. Nhưng với công nghệ thông tin nói chung và nghề lập trình nói riêng lại càng cần hơn. Tại sao vậy ? Đơn giản thôi vì đây là một nghề không hoàn toàn dễ dàng, áp lực rất cao nếu bạn không có một cách nhìn nhận đúng đắn, yêu thích và đam mê sẽ rất khó để theo được nghề này.
                </p>
                <p class="hide">
                    Tôi có 7 năm kinh nghiệm trang việc thiết kê website thương mại điện tử Tôi Thành thạo các công cụ thiết kế website và ngôn ngữ lập trình website
                    hiểu biết rõ ưu nhược điểm của từng hệ thống mã nguồn mở
                </p>

                <div class="row">
                    <div class="col-md-1">
                        <div class="personal-details">
                            <strong>07/02/1983</strong>
                            <small>Ngày sinh</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="personal-details">
                            <strong>7</strong>
                            <small>Năm kinh nghiệm</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="personal-details">
                            <strong>0966 742 999</strong>
                            <small>Điện thoại</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="personal-details">
                            <span><b>Skype:</b> nguyen.dinh.cuong1</span>
                            <span><b>Email:</b> nguyendinhcuong@gmail.com</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="personal-details">
                            <strong>Đọc hiểu Tiếng anh chuyên nghành, giao tiếp cơ bản</strong>
                            <small>Ngoại ngữ</small>
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
                    <h2>Tôi đang làm những gì</h2>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Thiết kế xây dựng template joomla</h3>

                            <p>
                                Joomla là sở trường đặc biệt của tôi, tôi có thể tùy biết và xây dựng các mẫu template joomla một cách dễ dàng.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Xây dựng module, component joomla</h3>

                            <p>
                                Với hơn 7 năm kinh  nghiệm cứu và học tập hệ thống joomla bao gồm đọc code thư viện và tùy chỉnh đã giúp tôi phần nào hiểu cấu trúc của một hệ thống lớn như thế nào
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Viết các plugin jquery</h3>

                            <p>
                                Jquery thật là tuyệt với, đặc biệt là kết hợp nó với ngôn ngữ server chúng ta có thể xây dựng cho khách hàng hệ thao tác frontend thật là thân thiệt và dễ dàng
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Viết ứng dụng Android</h3>

                            <p>
                                Với thời đại công nghệ mobile thì sử dựng java để viết các ứng dụng Android phục vụ người dùng là một điều bắt buộc đối với một lập trình viên như tôi
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Viết các ứng dụng windows</h3>

                            <p>
                                Để phục vụ một số công việc cần chạy trên widows nên cũng đã tự trang bị cho mình ngôn ngữ C# VB.NET đã phục vụ một số mục đích
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Trang bị một số kỹ năng mềm</h3>

                            <p>
                                Ngoài các kiến thức phục vụ công việc, thì các kỹ năng mềm, như giao tiêp, thuyết trình... tôi cũng thường xuyên bồi đắp cho mình
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 wow bounceInUp">
                        <div class="expertise-item">
                            <h3>Tham gia xây dựng các hệ thống opensource khác</h3>

                            <div>
                                Ngoài joomla tôi còn có thể làm việc được với rất nhiều các hệ thống opensource
                                <ul class="open-source">
                                    <li>
                                        I. Loại hệ thống chuyên về Quản trị nội dung, cổng thông tin (CMS – Content Management System / Portals)
                                        <br/>
                                        Drupal,NukeViet,MODx,Mambo,PHP-Nuke
                                    </li>
                                    <li>
                                        II,Loại hệ thống chuyên về Diễn đàn (Forum)
                                        <br/>
                                        MyBB,phpBB...
                                    </li>
                                    <li>
                                        III. Loại hệ thống chuyên về Blog
                                        <br/>
                                        WordPress,Nucleus CMS,LifeType,Serendipity,Dotclear
                                    </li>
                                    <li>
                                        IV.Loại hệ thống chuyên về thương mại điện tử (eCommerce)
                                        <br/>
                                        Magento,Zen Cart,OpenCart,osCommerce,PrestaShop,osCSS,TomatoCart
                                    </li>
                                </ul>
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
                            <h4>Khoa công nghệ thông tin</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- .experience-item -->
                        <div class="content-item">
                            <h3>Bằng quản trị mạng MCITP</h3>

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

<section id="produts" class="section-wrapper portfolio-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="section-title">
                    <h2>Một số sản phẩm</h2>
                    <span>Nhấn vào hình ảnh để xem video giới thiệu</span>
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
                                <h3>Trang thương mại điện tử</h3>
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
                    <h2>Liên hệ</h2>
                </div>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-3">
                        <address>
                            <strong>Địa chỉ</strong><br>
                            Hà Nội<br>
                            Tiên Phương, Chương Mỹ

                        </address>
                    </div>
                    <div class="col-md-3">
                        <address>
                            <strong>Di động 1</strong><br>
                            0966742999
                        </address>
                    </div>
                    <div class="col-md-3">
                        <address>
                            <strong>Di động 2</strong><br>
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
                                   placeholder="Họ tên">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" required="" class="form-control" id="InputEmail"
                                   placeholder="Email">
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" id="InputSubject"
                                   placeholder="TIêu đề">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="4" required="" name="message" id="message-text"
                                      placeholder="Nội dung"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Gửi</button>
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
<?php include PATH_ROOT.DS.'js_script.php';?>

</body>
</html>