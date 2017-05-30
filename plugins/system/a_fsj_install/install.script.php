<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);

if (!class_exists('PlgSystemA_FSJ_InstallInstallerScript')) {
	class PlgSystemA_FSJ_InstallInstallerScript
	{

		protected $sourcedir;
		protected $installerdir;
		protected $manifest;
		protected $parent;

		protected function setup($parent)
		{
			$this->parent       = $parent;
			$this->sourcedir    = $parent->getParent()->getPath('source');
			$this->manifest     = $parent->getParent()->getManifest();
			$this->installerdir = $this->sourcedir . '/' . 'installer';
		}

		public function install($parent)
		{
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');

			require_once($this->installerdir . '/' . 'fsj_installer.php');

			echo "<h3>Installing Freestyle Joomla Components</h3>";
			echo "<table cellpadding='3'>";
			foreach ($this->manifest->packages->package as $package)
			{
				$data = new stdClass();
				$data->id = (string)$package->attributes()->id;
				$data->xml = (string)$package->attributes()->xml;
				$data->source = $this->sourcedir . DS . $data->id;
				$data->xml_file = $data->source . DS . $data->xml;
				$data->installerdir = $this->installerdir;
				$data->manifest = $this->manifest;
				
				$installer = new FSJ_Installer();
				$installer->Install($data);
			}
			echo "</table>";
			
			// run validate install process
			jimport('fsj_core.admin.update');
			$updater = new FSJ_Updater();
			$log = $updater->Process();
?>		
	<h3>Setting up components</h3>
<?php $logno = 1; ?>
<?php foreach ($log as &$comp) : ?>
	<?php if (!is_array($comp)) continue; ?>
	<?php if (!array_key_exists("log", $comp)) continue; ?>
	<?php if (count($comp['log']) < 1) continue; ?>
	<?php $logno++; ?>
	<div>
	<div style="margin:4px;">
			<a href="#" onclick="ToggleLog('log<?php echo $logno; ?>');return false;">+<?php echo $comp['name']; ?></a>
		</div>
		<div id="log<?php echo $logno; ?>" style="display:none;margin-left:16px;">
<?php foreach ($comp['log'] as &$log): ?>
	<?php //if (!is_array($log['log'])) continue; ?>
	<?php //if (count($log['log']) < 1) continue; ?>
	<?php //if ($log['log'] == "") continue; ?>
		<?php $logno++; ?>
			<div style="margin:4px;">
				<a href="#" onclick="ToggleLog('log<?php echo $logno; ?>');return false;">+<?php echo $log['name']; ?></a>
			</div>
			<div id="log<?php echo $logno; ?>" style="display:none;">
				<pre style="margin-left: 20px;border: 1px solid black;padding: 2px;background-color: ghostWhite;"><?php echo $log['log']; ?><?php if ($log['log'] == "") echo "OK, no changes required"; ?></pre>
			</div>
<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; ?>

<script>
function ToggleLog(log)
{
	if (document.getElementById(log).style.display == "block")
	{
		document.getElementById(log).style.display = 'none';
	} else {
		document.getElementById(log).style.display = 'block';
	}
}
</script>
			
			
<?php
			return true;
		}

		public function uninstall($parent)
		{

		}

		public function update($parent)
		{
			return $this->install($parent);
		}

		public function preflight($type, $parent)
		{
			$this->setup($parent);
			
			// We should be checking here if we are installing a LITE package over the top of a PRO package.
			// If we are then cancel the install
		}

		public function postflight($type, $parent)
		{
		}

		public function abort($msg = null, $type = null)
		{
		}
	}
}