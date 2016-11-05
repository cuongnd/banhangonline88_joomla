<?php
/**
 * SocialBacklinks Plugins view Network Settings layout
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( );
foreach ($this->networks as $plugin) :

try {
	$connected = $plugin->isLoggedIn( );
}
catch (SBPluginsException $e) {
	$connected = false;
}

$name = $plugin->getAlias( );
?>
<div class="social-block <?php echo $name ?>">
	<div class="social-text">
		<?php
		if ( $plugin->enabled && $connected ) {
			$class = 'on-button';
			$class1 = '';
		}
		else {
			$class = 'off-button';
			$class1 = ' class="off"';
		}
		$param = array(
			'view' => 'plugin',
			'task' => 'save',
			'plugin' => $name,
			'name' => 'enabled',
		);
		$js_param = json_encode( $param );
		$js_param = str_replace( array(
			'{',
			'}',
			'"'
		), array(
			'',
			'',
			"'"
		), $js_param );
		?>
		<div class="on-off-wrapper">
			<div class="on-off-cell">
				<div id="<?php echo $name ?>" class="social-on-off <?php echo $class ?>">
					<span style="display: none"><?php echo $js_param
						?></span>
				</div>
			</div>
		</div>
	</div>
	<?php
	if ( $connected ) {
		$class2 = '';
	}
	else {
		$class2 = ' disabled';
	}
	?>
	<div class="iconwrapper">
		<span id="<?php echo $name ?>-icon"<?php echo $class1; ?>></span>
	</div>

	<div class="social-text sp-view">
		<div class="social-title">
			<?php echo JText::_( 'SB_' . strtoupper( $name ) ); ?>
		</div>

		<div class="information">
			<a class="modal" rel="{handler:'iframe',size:{<?php echo $plugin->window_size ?>}}"
			href="<?php echo JRoute::_("index.php?option={$this->option}&view=plugin&task=networkInfo&network={$name}&tmpl=component") ?>"> <span class="info-icon"> <span> <?php echo JText::_('SB_PREVIEW')
					?></span> </span> </a>
		</div>

		<div class="config">
			<a class="modal" rel="{handler:'iframe',size:{x:570,y:250}}"
			href="<?php echo JRoute::_("index.php?option={$this->option}&view=plugin&task=editSettings&plugintype=network&network={$name}&tmpl=component") ?>"> <span class="config-icon"> <span> <?php echo JText::_('SB_SETTINGS')
					?></span> </span> </a>
		</div>

		<div id="<?php echo $name ?>-disconnect" class="logout <?php echo $class2; ?>">
			<a class="modal" rel="{handler:'iframe',size:{x:400,y:200}}" href="<?php echo $plugin->getLogoutUrl( ) ?>"> <span class="logout-icon"> <span title="<?php echo JText::sprintf( 'SB_NETWORK_DISCONNECT', JText::_( 'SB_' . strtoupper( $name ) ) ); ?>"> <?php echo JText::_( 'SB_LOGOUT' )
					?></span> </span> </a>
		</div>

		<div class="login" id="<?php echo $name ?>-connect" style="display: none">
			<a href="<?php echo $plugin->getLoginUrl( ) ?>" class="connect-link"> <span class="logout-icon"> <span title="<?php echo JText::sprintf( 'SB_NETWORK_CONNECT', JText::_( 'SB_' . strtoupper( $name ) ) ); ?>"> <?php echo JText::_( 'SB_LOGIN' )
					?></span> </span> </a>
		</div>
	</div>

	<div class="success-block"></div>
	<div class="ajax-loader"></div>
	<div class="ajax-overlay"></div>
</div>
<?php endforeach; ?>
