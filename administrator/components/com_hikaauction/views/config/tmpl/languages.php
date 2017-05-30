<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="page-languages">
	<fieldset class="adminform">
		<legend><?php echo JText::_('LANGUAGES') ?></legend>
		<table class="adminlist table table-striped table-hover" cellpadding="1">
			<thead>
				<tr>
					<th class="title titlenum"><?php
						echo JText::_('HKA_NUM');
					?></th>
					<th class="title titletoggle"><?php
						echo JText::_('HKA_EDIT');
					?></th>
					<th class="title"><?php
						echo JText::_('HKA_NAME');
					?></th>
					<th class="title titletoggle"><?php
						echo JText::_('ID');
					?></th>
				</tr>
			</thead>
			<tbody>
<?php
	$k = 0;
	foreach($this->languages as $i => &$language) {
?>
				<tr class="row<?php echo $k; ?>">
					<td align="center"><?php
						echo $i+1;
					?></td>
					<td align="center"><?php
						if($this->manage)
							echo $language->edit;
					?></td>
					<td align="center"><?php
						echo $language->name;
					?></td>
					<td align="center"><?php
						echo $language->language;
					?></td>
				</tr>
<?php
		$k = 1 - $k;
		unset($language);
	}
?>
			</tbody>
		</table>
	</fieldset>
</div>
