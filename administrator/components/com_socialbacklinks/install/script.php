<?php
/**
 * PHP install file for Social Backlinks.
 * Installes the plugins and do some upgrade stuff.
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.installer.installer' );

class com_socialBacklinksInstallerScript
{
	function install( $parent )
	{
		$lang = JFactory::getLanguage();
		$lang->load( 'com_socialbacklinks', JPATH_ADMINISTRATOR );
		
		// Check requirements for correct component work
		JLoader::register( 'SBHelpersRequirements', JPATH_ADMINISTRATOR . '/components/com_socialbacklinks/helpers/requirements.php' );
		$helper = new SBHelpersRequirements( );
		if ( !$helper->check( ) )
		{
			$errors = $helper->getErrors( );
		}
		else {
			$errors = null;
		}
		
		$installer = new SBInstaller( $parent );
		$installer->setDefaultConfig( );
		?>
<link type="text/css" href="<?php echo JURI::root( true ) ?>/media/com_socialbacklinks/css/install.css" rel="stylesheet">
<div class="install-result">
	<div class="header-text">
		<?php 
		if ( !empty( $errors ) )
		{
			echo JText::_( 'SB_INSTALLED_PARTLY_SUCCESSFULLY' ) . ':';
		}
		else {
			echo JText::_( 'SB_INSTALLED_SUCCESSFULLY' );
		}
		?>
	</div>
	<?php if ( !empty( $errors ) ) : ?>
		<ul>
			<?php foreach ( $errors AS $error ) : ?>
				<li><?php echo $error ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
		<?php
		// install and enable plugins
		$installer->installPlugins();
		
		$results = $installer->getErrors( );
		?>
	<table class="adminlist">
		<thead>
			<tr>
				<th class="first">
					<?php echo JText::_( 'SB_EXTENSION' ) ?>
				</th>
				<th class="last" width="110">
					<?php echo JText::_( 'SB_RESULT' ) ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<td style="padding: 0 0 0 10px;">
					<?php echo JText::_( 'SB_SOCIALBACKLINKS' ) . ' Component' ?>
				</td>
				<td style="text-align: center;">
					<span class="success">
						<?php echo JText::_( 'SB_INSTALLED' ) ?>
					</span>
				</td>
			</tr>
			<?php 
			$k = 1;
			foreach ( $results AS $result ) : 
			?>
				<?php
				$parts = explode( '~', $result );
				if ( count( $parts ) < 3 )
				{
					continue;
				}
				?>
				<tr class="row<?php echo $k ?>">
					<td style="padding: 0 0 0 10px;">
						<?php echo JText::_( 'SB_' . strtoupper( $parts[0] ) . '_PLUGIN' ) ?>
					</td>
					<td style="text-align: center;">
						<span class="<?php echo $parts[2] ?>">
							<?php echo $parts[1] ?>
						</span>
					</td>
				</tr>
			<?php 
				$k = 1 - $k;
			endforeach; 
			?>
		</tbody>
	</table>
<?php if ( empty( $errors ) ) : ?>
<script type="text/javascript">
/*<![CDATA[*/
	window.addEvent( "domready", function()
	{
		var interval = self.setInterval( function() 
		{
			var counter_block = document.getElement(".redirect-block").getElement(".counter");
			var counter = parseInt( counter_block.get('text') );
			counter = counter - 1;
			if (counter)
			{
				counter_block.set('text', counter);
			}
			else {
				window.location.href = "<?php echo JURI::base( ) ?>index.php?option=com_socialbacklinks";
				clearInterval(interval);
			}
		}, 1000 );
	});
/*]]>*/
</script>
	<div class="redirect-block">
		<div class="ajax-loader"></div>
		<div class="redirect-msg">
			<?php echo JText::_( 'SB_INSTALL_REDIRECT' ) ?>
			<span class="counter">5</span>
			<?php echo JText::_( 'SB_SECONDS' ) ?>
		</div>
	</div>
<?php endif; ?>
</div>

		<?php
		return true;
	}
	
	function update( $parent )
	{
		$lang = JFactory::getLanguage();
		$lang->load( 'com_socialbacklinks', JPATH_ADMINISTRATOR );
		
		// Check requirements for correct component work
		JLoader::register( 'SBHelpersRequirements', JPATH_ADMINISTRATOR . '/components/com_socialbacklinks/helpers/requirements.php' );
		$helper = new SBHelpersRequirements( );
		if ( !$helper->check( ) )
		{
			$errors = $helper->getErrors( );
		}
		else {
			$errors = null;
		}
		?>
<link type="text/css" href="<?php echo JURI::root( true ) ?>/media/com_socialbacklinks/css/install.css" rel="stylesheet">
<div class="install-result">
	<div class="header-text">
		<?php 
		if ( !empty( $errors ) )
		{
			echo JText::_( 'SB_INSTALLED_PARTLY_SUCCESSFULLY' ) . ':';
		}
		else {
			echo JText::_( 'SB_UPDATED_SUCCESSFULLY' );
		}
		?>
	</div>
	<?php if ( !empty( $errors ) ) : ?>
		<ul>
			<?php foreach ( $errors AS $error ) : ?>
				<li><?php echo $error ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
		<?php
		// install and enable plugins
		$installer = new SBInstaller( $parent );
		$installer->installPlugins( );
		
		$results = $installer->getErrors( );
		?>
	<table class="adminlist">
		<thead>
			<tr>
				<th class="first">
					<?php echo JText::_( 'SB_EXTENSION' ) ?>
				</th>
				<th class="last" width="110">
					<?php echo JText::_( 'SB_RESULT' ) ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<td style="padding: 0 0 0 10px;">
					<?php echo JText::_( 'SB_SOCIALBACKLINKS' ) . ' Component' ?>
				</td>
				<td style="text-align: center;">
					<span class="success">
						<?php echo JText::_( 'SB_UPDATED' ) ?>
					</span>
				</td>
			</tr>
			<?php 
			$k = 1;
			foreach ( $results AS $result ) : 
			?>
				<?php
				$parts = explode( '~', $result );
				if ( count( $parts ) < 3 )
				{
					continue;
				}
				?>
				<tr class="row<?php echo $k ?>">
					<td style="padding: 0 0 0 10px;">
						<?php echo JText::_( 'SB_' . strtoupper( $parts[0] ) . '_PLUGIN' ) ?>
					</td>
					<td style="text-align: center;">
						<span class="<?php echo $parts[2] ?>">
							<?php echo $parts[1] ?>
						</span>
					</td>
				</tr>
			<?php 
				$k = 1 - $k;
			endforeach; 
			?>
		</tbody>
	</table>
<?php if ( empty( $errors ) ) : ?>
<script type="text/javascript">
/*<![CDATA[*/
	window.addEvent( "domready", function()
	{
		var interval = self.setInterval( function() 
		{
			var counter_block = document.getElement(".redirect-block").getElement(".counter");
			var counter = parseInt( counter_block.get('text') );
			counter = counter - 1;
			if (counter)
			{
				counter_block.set('text', counter)
			}
			else if (counter == 0) {
				window.location.href = "<?php echo JURI::base( ) ?>index.php?option=com_socialbacklinks";
				clearInterval(interval);
			}
		}, 1000 )
	});
/*]]>*/
</script>
	<div class="redirect-block">
		<div class="ajax-loader"></div>
		<div class="redirect-msg">
			<?php echo JText::_( 'SB_INSTALL_REDIRECT' ) ?>
			<span class="counter">5</span>
			<?php echo JText::_( 'SB_SECONDS' ) ?>
		</div>
	</div>
<?php endif; ?>
</div>

		<?php
		return true;
	}
	
	function uninstall( $parent )
	{
		$lang = JFactory::getLanguage();
		$lang->load( 'com_socialbacklinks', JPATH_ADMINISTRATOR );

		$installer = new SBInstaller( $parent );
		$installer->uninstallPlugins( );
		
		$results = $installer->getErrors( );
		
		$style = file_get_contents( JURI::root( ) . 'media/com_socialbacklinks/css/install.css' );
?>
<style type="text/css">
	<?php echo $style; ?>
	.install-result .adminlist thead tr th { background-color: #0096D3; }
</style>
<div class="install-result">
	<div class="header-text">
		<?php 
		$mail_link_start = '<a href="mailto:contact@joomunited.com">';
		$mail_link_end = '</a>';
		echo JText::sprintf( 'SB_REMOVED_SUCCESSFULLY', $mail_link_start, $mail_link_end );
		?>
	</div>
	<table class="adminlist uninstall-table">
		<thead>
			<tr>
				<th class="first">
					<?php echo JText::_( 'SB_EXTENSION' ) ?>
				</th>
				<th class="last" width="110">
					<?php echo JText::_( 'SB_RESULT' ) ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="row0">
				<td style="padding: 0 0 0 10px;">
					<?php echo JText::_( 'SB_SOCIALBACKLINKS' ) . ' Component' ?>
				</td>
				<td style="text-align: center;">
					<span class="success">
						<?php echo JText::_( 'SB_REMOVED' ) ?>
					</span>
				</td>
			</tr>
			<?php 
			$k = 1;
			foreach ( $results AS $result ) : 
			?>
				<?php
				$parts = explode( '~', $result );
				if ( count( $parts ) < 3 )
				{
					continue;
				}
				?>
				<tr class="row<?php echo $k ?>">
					<td style="padding: 0 0 0 10px;">
						<?php echo JText::_( 'SB_' . strtoupper( $parts[0] ) . '_PLUGIN' ) ?>
					</td>
					<td style="text-align: center;">
						<span class="<?php echo $parts[2] ?>">
							<?php echo $parts[1] ?>
						</span>
					</td>
				</tr>
			<?php 
				$k = 1 - $k;
			endforeach; 
			?>
		</tbody>
	</table>
</div>
<?php
		return true;
	}
	
	
	
	function preflight( $type, $parent )
	{
		return true;
	}
	
	function postflight( $type, $parent )
	{
		return true;
	}
}

/**
 * Social Backlinks Installer class to help with install/uninstall component and elements
 */
class SBInstaller extends JObject
{
	/**
	 * Installer object
	 * @var JInstallerComponent
	 */
	private $_installer = null;
	
	/**
	 * Results of installation
	 * @var array
	 */
	private $_results = array( );
	
	/**
	 * Social Backlinks Install object constructor
	 */
	public function __construct( $installer )
	{
		$this->_installer = $installer;
	}
	
	/**
	 * Put into database default configuration
	 * 
	 * @return boolean
	 */
	public function setDefaultConfig( )
	{
		$db = $this->_installer->getParent()->getDBO();
		
		$query = 'INSERT INTO `#__socialbacklinks_configs` (`section`, `name`, `value`) VALUES' 
				. " ('basic', 'last_sync', now()),"
				. " ('basic', 'sync_periodicity', '5')," 
				. " ('basic', 'need_send_errors', '0'),"
				. " ('basic', 'errors_recipient_type', '0'),"
				. " ('basic', 'clean_history', '1')," 
				. " ('basic', 'clean_history_periodicity', '30'),"
				. " ('basic', 'sync_domain', '')";
		$db->setQuery( $query );
		
		return $db->execute( );
	}
	
	/**
	 * Install plugins required for correct work
	 * 
	 * @param JInstallerComponent $com_installer
	 * 
	 * @return void
	 */
	public function installPlugins( )
	{
		$db = $this->_installer->getParent()->getDBO();
		
		// get list of installed Social Backlinks plugins
		$query = 'SELECT `element` FROM `#__extensions`' 
				. ' WHERE `type` = ' . $db->Quote( 'plugin' )
				. ' AND ( `element` = ' . $db->Quote( 'sbsynchronizer' )
				. ' OR `element` = ' . $db->Quote( 'sbtrigger' )
				. ' OR `folder` = ' . $db->Quote( 'socialbacklinks' ) . ' )';
				
		$db->setQuery( $query );
		$installed_plugs = $db->loadColumn( );
		
		// infos for plugins shipped with component
		$plugin_path = $this->_installer->getParent( )->getPath( 'source' ) . '/extensions/';
		
		$plugins = null;
		foreach ( $this->_installer->getParent( )->getManifest()->children( ) as $child )
		{
			if ( $child->getName() == 'plugins' )
			{
				$plugins = $child;
			}
		}
		
		if ( is_a( $plugins, 'SimpleXMLElement' ) && count( $plugins->children( ) ) ) 
		{
			foreach ( $plugins->children( ) as $plugin )
			{
			    set_time_limit(0);
				$attributes = $plugin->attributes();
				$pname		= $attributes['plugin'];
				$pfolder	= $attributes['folder'];
				$ppublish	= $attributes['publish'];
				$pelement	= $attributes['element'];
				$pversion	= $attributes['version'];
				
				// checks if there is such plugin in package
				if ( !file_exists( $plugin_path . $pname ) ) {
					continue;
				}
				
				$inst = new JInstaller( ); // do not use the component installer (getInstance); own installer object is needed
				// upgrade installed plugins only (automator and content are installed everytime); only when version is different
				if ( !in_array( $pelement, $installed_plugs ) )
				{
					$inst_result = $inst->install( $plugin_path . $pname );
					
					if ( !$inst_result )
					{
						$this->setError( $pelement . '~' . JText::_( 'SB_FAIL' ) . '~fail' );
					}
					else {
						if ( $ppublish )
						{
							// enable plugin
							$query = 'UPDATE `#__extensions` SET `enabled` = 1' 
									. " WHERE `type` = 'plugin'" 
									. ' AND `element` = ' . $db->quote( $pelement )
									. ' AND `folder` = ' . $db->quote( $pfolder );
							$db->setQuery( $query );
							$db->execute( $query );
						}
						$this->setError( $pelement . '~' . JText::_( 'SB_INSTALLED' ) . '~success' );
					}
				}
				elseif ( in_array( $pelement, $installed_plugs ) 
					&& $this->isNewerVersion( $this->getInstalledVersion( $pfolder .'/'. $pelement .'/'. $pelement , 'plugin' ), $pversion ) )
				{
					$inst_result = $inst->install( $plugin_path . $pname );
					
					if ( !$inst_result )
					{
						$this->setError( $pelement . '~' . JText::_( 'SB_FAIL' ) . '~fail' );
					}
					else {
						if ( $ppublish )
						{
							// enable plugin
							$query = 'UPDATE `#__extensions` SET `enabled` = 1' 
									. " WHERE `type` = 'plugin'" 
									. ' AND `element` = ' . $db->quote( $pelement )
									. ' AND `folder` = ' . $db->quote( $pfolder );
							$db->setQuery( $query );
							$db->execute( $query );
						}
						$this->setError( $pelement . '~' . JText::_( 'SB_UPDATED' ) . '~success' );
					}
				}
			}
		}
	}
	
	/**
	 * Check is installing version newer than installed
	 * 
	 * @param float $installed_version
	 * @param float $new_version
	 * 
	 * @return boolean
	 */
	private function isNewerVersion( $installed_version, $new_version )
	{
		return version_compare( $installed_version, $new_version, '<' );
	}
	
	/**
	 * Returns installed version
	 * 
	 * @param string $name of item
	 * @param string $type of item
	 * 
	 * @return float
	 */
	private function getInstalledVersion( $name, $type )
	{
		$data = null;
		$result = '';
		$path = '';
		
		switch ( $type )
		{
			case 'component':
				$path = JPATH_COMPONENT_ADMINISTRATOR . "/{$name}.xml";
				break;
			
			case 'module':
				$path = JPATH_ADMINISTRATOR . "/modules/{$name}/{$name}.xml";
				break;
			
			case 'plugin':
				$path = JPATH_ROOT . "/plugins/{$name}.xml";
				break;
		}
		
		if ( !empty( $path ) )
		{
			$data = JApplicationHelper::parseXMLInstallFile( $path );
		}
		
		if ( !empty( $data ) )
		{
			$result = $data['version'];
		}
		
		return $result;
	}
	
	/**
	 * Uninstall plugins
	 * 
	 * @param object $com_installer
	 * 
	 * @return void
	 */
	public function uninstallPlugins( )
	{
		$db = $this->_installer->getParent()->getDBO();
		$query = 'SELECT `extension_id`, `name`, `element` FROM `#__extensions`' 
				. ' WHERE `type` = ' . $db->Quote( 'plugin' )
				. ' AND ( `element` = ' . $db->Quote( 'sbsynchronizer' )
				. ' OR `element` = ' . $db->Quote( 'sbtrigger' )
				. ' OR `folder` = ' . $db->Quote( 'socialbacklinks' ) . ' )';
		$db->setQuery( $query );
		$plugs = $db->loadObjectList( );
		
		$installer = new JInstaller( );
		foreach ( $plugs as $plug )
		{
			$inst_result = $installer->uninstall( 'plugin', $plug->extension_id );
			
			if ( !$inst_result )
			{
				$this->setError( $plug->element . '~' . JText::_( 'SB_FAIL' ) . '~fail' );
			}
			else {
				$this->setError( $plug->element . '~' . JText::_( 'SB_REMOVED' ) . '~success' );
			}
		}
	}
	
}
