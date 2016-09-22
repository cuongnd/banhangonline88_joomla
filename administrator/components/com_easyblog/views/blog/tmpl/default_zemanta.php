<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addScript( 'http://static.zemanta.com/core/jquery.js');
$document->addScript( 'http://static.zemanta.com/core/jquery.zemanta.js');
$document->addStyleSheet( 'http://static.zemanta.com/core/zemanta-widget.css' );
?>
<div id="zemanta-sidebar">
	<div id="zemanta-control" class="zemanta"></div><div id="zemanta-message" class="zemanta">Loading Zemanta...</div><div id="zemanta-filter" class="zemanta"></div><div id="zemanta-gallery" class="zemanta"></div><div id="zemanta-articles" class="zemanta"></div><div id="zemanta-preferences" class="zemanta"></div>
</div>
<script type="text/javascript">
window.ZemantaGetAPIKey = function () {
	return '<?php echo $this->config->get( 'layout_dashboard_zemanta_api' );?>';
}
</script>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_easyblog/assets/js/zemanta.platform.js"></script>
