EasySocial.require().script('site/layout/dialog','site/dashboard/dashboard.guest.login').done(function($) {
	$('[data-guest-login]').addController('EasySocial.Controller.Dashboard.Guest.Login');
});
