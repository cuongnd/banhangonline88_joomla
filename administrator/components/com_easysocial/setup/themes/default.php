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
<!doctype html>
<html lang="en">
<head>
	<title><?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION' ); ?> - <?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_STEP' );?> <?php echo $active; ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="<?php echo JURI::root();?>administrator/components/com_easysocial/setup/assets/images/logo.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
	<link rel="stylesheet" href="<?php echo JURI::base();?>components/com_easysocial/setup/assets/styles/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo JURI::base();?>components/com_easysocial/setup/assets/styles/style.css" type="text/css" />
	<script src="<?php echo JURI::base();?>components/com_easysocial/setup/assets/scripts/jquery.js" type="text/javascript"></script>
	<script src="<?php echo JURI::base();?>components/com_easysocial/setup/assets/scripts/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo JURI::base();?>components/com_easysocial/setup/assets/scripts/application.js" type="text/javascript"></script>
	<script type="text/javascript">
	<?php require( JPATH_ROOT . '/administrator/components/com_easysocial/setup/assets/scripts/script.js' ); ?>
	</script>
</head>
<body class="step<?php echo $active;?>">
<div id="es-header">
	<div class="navbar">
		<div class="navbar-inner">
			<div>
				<div class="brand">
					<img src="<?php echo JURI::root();?>administrator/components/com_easysocial/setup/assets/images/logo.png" class="easysocial-logo pull-left" />
					<div class="title">EasySocial Installer</div>
					<div class="tagline">Building Awesome Social Network for your Joomla! site.</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="es-wrap">

	<div class="es-installer-header">
		<h2 class="section-heading">
			<span>
		<?php if( $activeStep->template == 'complete' ){ ?>
			<?php echo JText::_( 'COM_EASYSOCIAL_INSTALLATION_COMPLETED' );?>
		<?php } else { ?>
			<?php echo JText::_( $activeStep->title );?>
		<?php } ?>
			</span>
		</h2>
	</div>

	<div class="es-installer">

		<?php if( $activeStep->template != 'complete' ){ ?>
		<div class="navbar es-stepbar">
			<div class="navbar-inner">
				<div class="navbar-collapse">
					<div class="media">
						<div class="media-object pull-left">
							<?php include( dirname( __FILE__ ) . '/default.steps.php' ); ?>
						</div>
						<div class="media-body">
							<div class="divider-vertical-last"></div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php } ?>

		<div class="es-installer-body">
			<?php include( dirname( __FILE__ ) . '/default.content.php' ); ?>
		</div>

		<?php include( dirname( __FILE__ ) . '/default.footer.php' ); ?>
	</div>

</div>

</body>
</html>
