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
<div id="fd" class="es <?php echo $class;?>">

	<?php if( $tmpl != 'component' ){ ?>
		<div class="es-panel">
			<?php echo $this->includeTemplate( 'admin/structure/header' ); ?>


			<div class="es-panel-inner">

				<div class="sidebar">
					<?php echo $sidebar; ?>
				</div>

				<div class="es-content">
					<div class="es-dashboard-control">
						<?php echo $this->includeTemplate( 'admin/structure/control' ); ?>
					</div>

					<div class="es-wrapper accordion">
						<?php echo FD::profiler()->toHTML();?>

						<?php echo FD::getInstance( 'Info' )->toHTML(); ?>

						<?php echo $html; ?>
					</div>

					<br /><br /><br /><br />
				</div>
			</div>


		</div>
	<?php } else { ?>

		<?php echo FD::getInstance( 'Info' )->toHTML(); ?>

		<?php echo $html; ?>

	<?php } ?>


</div>
