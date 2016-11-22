<?php
/**
 * @package         Modals
 * @version         8.0.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<?php if (JFactory::getApplication()->input->get('iframe')) : ?>
	<?php
	$this->language  = JFactory::getDocument()->language;
	$this->direction = JFactory::getDocument()->direction;
	?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>"
	      dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body class="contentpane modal">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
	</body>
	</html>
<?php else: ?>
	<?php
	require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
	$parameters = RLParameters::getInstance();
	$config     = $parameters->getPluginParams('modals');
	?>
	<?php if ($config->load_head) : ?>
		<jdoc:include type="head" />
	<?php endif; ?>
	<jdoc:include type="message" />
	<jdoc:include type="component" />
<?php endif; ?>
