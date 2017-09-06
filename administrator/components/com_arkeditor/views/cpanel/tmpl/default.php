<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

define('ARKEDITOR_COMPONENT_VIEW', JUri::root() . 'administrator/components/com_arkeditor/views/cpanel');

//load style sheet
JFactory::getDocument()->addStyleSheet( ARKEDITOR_COMPONENT_VIEW . '/css/cpanel.css', 'text/css' );

// Define Modules that need assistance
$needsborder = array( 'mod_arkquickicon' );
?>
<div class="row-fluid">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container" class="span12">
<?php endif;?>
		<div class="row-fluid">
			<div class="span6 pos-left">
				<?php
					foreach ($this->left as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title nav-header">' . $icon . $module->title . '</div>' : '';
						$class 	= ( in_array( $module->module, $needsborder ) ) ? 'row-striped' : '';
						echo '<div class="well well-small ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
			</div>
			<div class="span6 pos-right">
				<?php
					foreach ($this->right as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title nav-header">' . $icon . $module->title . '</div>' : '';
						$class 	= ( in_array( $module->module, $needsborder ) ) ? 'row-striped' : '';
						echo '<div class="well well-small ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12 pos-bottom">
				<?php
					foreach ($this->bottom as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title nav-header">' . $icon . $module->title . '</div>' : '';
						$class 	= ( in_array( $module->module, $needsborder ) ) ? 'row-striped' : '';
						echo '<div class="well well-small ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
			</div>
		</div>
	</div>
</div>