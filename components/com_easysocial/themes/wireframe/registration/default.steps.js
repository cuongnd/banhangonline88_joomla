EasySocial.require()
.script( 'site/registrations/registrations' )
.done(function($){

	$( '[data-registration-form]' ).implement(
		EasySocial.Controller.Registrations.Form ,
		{
			"previousLink"	: "<?php echo FRoute::registration( array( 'layout' => 'steps' , 'step' => ( $currentStep - 1 ) ) , false );?>"
		}
	);

});
