<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="fd" class="es mod-es-search module-search<?php echo $suffix;?>">
	<div class="mod-bd">
		<div class="es-widget">

			<div class="es-navbar-search" data-mod-search>
				<form action="<?php echo JRoute::_( 'index.php' );?>" method="post">
					<input type="text" name="q" class="form-control input-sm" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_( 'MOD_EASYSOCIAL_SEARCH_PHASE' , true );?>" />

					<input type="hidden" name="view" value="search" />
					<input type="hidden" name="option" value="com_easysocial" />
					<input type="hidden" name="Itemid" value="<?php echo FRoute::getItemId('search');?>" />
					<?php echo $modules->html( 'form.token' );?>
				</form>
			</div>
			<div class="mt-5 mr-10 fd-cf">
				<a class="pull-right fd-small" href="<?php echo FRoute::search( array( 'layout' => 'advanced' ) ); ?>"><?php echo JText::_('MOD_EASYSOCIAL_SEARCH_ADVANCED_SEARCH');?></a>
			</div>

		</div>
	</div>
</div>
