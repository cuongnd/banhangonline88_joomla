<?php

/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
	
<?php if ($this->showfilter): ?>
	<table width="100%">
	<tr>
	<td width="100%">
				<?php echo JText::_("FSJ_FORM_FILTER"); ?>:
<input type="text" name="filter" id="filter" value="" title=""/>
				<button onclick="document.pick<?php echo $this->id; ?>Form.submit();"><?php echo JText::_("FSJ_FORM_GO"); ?></button>
				<button id="form_reset"><?php echo JText::_("FSJ_FORM_RESET"); ?></button>
				
			</td>
			<td nowrap="nowrap">
				More
			</td>
		</tr>
	</table>
<?php endif; ?>

<?php include JPATH_LIBRARIES.DS.'fsj_core'.DS.'plugins'.DS.'picktype'.DS."picktype.{$this->plugin->params->pick->type}".DS.'tmpl'.DS.$this->plugin->params->pick->type . '.php'; ?>
