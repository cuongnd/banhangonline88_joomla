<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial.require()
.script( 'admin/migrators/migrator' )
.done(function($)
{
	// Implement discover controller.
	$( '[data-cb-migrator-form]' ).implement(
		EasySocial.Controller.Migrators.Migrator,
		{
			component: "cb"
		});
});
