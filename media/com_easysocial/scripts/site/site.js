EasySocial.require()
.script(

)
.library('history', 'dialog').done(function(){

	if (window.es.mobile) {
		EasySocial.require()
		.library('swiper')
		.done(function($) {

			var swiper = new Swiper('.swiper-container', {
				"freeMode": true,
				"slidesPerView": 'auto',
				"visibilityFullFit": true,
				"freeModeFluid": true,
				"slidesOffsetAfter": 88
				
			});
		});
	}
});
