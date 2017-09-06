<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$document=JFactory::getDocument();
require_once JPATH_ROOT.DS.'libraries/less.php_1.7.0.10/less.php/Less.php';
require_once JPATH_ROOT.DS.'templates/vina_bonnie/templatehelper.php';
$template_helper = templateHelper::getInstance();
$parser = Less_Parser::getInstance();
$parser->parseFile(JPATH_ROOT.DS.'templates/vina_bonnie/bootstrap-3.3.7/less/bootstrap.less', JUri::root());
$parser->ModifyVars( $template_helper->list_var_template_config );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/general.css" type="text/css" />
</head>
<body class="contentpane">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
