<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="widget-box">

	<?php if( is_array( $section ) ) {

		if( count( $section ) === 1 && is_string( $section[0] ) ) {
			echo $section[0];
		}
		else
		{
			if( is_string( $section[0] ) )
			{
				$header = array_shift( $section );

				echo $settings->renderHeader( $header );
			}

			foreach( $section as $data ) {

				if( is_array( $data ) ) {
					echo call_user_func_array( array( $settings, 'renderSetting' ), $data );
				}

				if( is_string( $data ) ) {
					echo $data;
				}
			}
		}
	}

	if( is_string( $section ) ) {
		echo $section;
	} ?>
</div>
