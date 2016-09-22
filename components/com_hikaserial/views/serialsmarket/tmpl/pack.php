<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikamarket::completeLink('serials&task=pack');?>" method="post" name="hikamarket_form" id="hikamarket_pack_form">
	<dl class="hikam_options large">
		<dt class="hikamarket_serials_pack_name"><label for="data_pack__pack_name"><?php echo JText::_('PACK_NAME'); ?></label></dt>
<?php
	if(hikamarket::acl('plugins/hikaserial/pack/edit/name')) { ?>
		<dd class="hikamarket_serials_pack_name input_large"><input type="text" size="45" id="data_pack__pack_name" name="data[pack][pack_name]" value="<?php echo $this->escape(@$this->pack->pack_name); ?>" /></dd>
<?php } else { ?>
		<dd class="hikamarket_serials_pack_name"><?php echo $this->escape(@$this->pack->pack_name); ?></dd>
<?php }

	if(hikamarket::acl('plugins/hikaserial/pack/edit/data')) { ?>
		<dt class="hikamarket_serials_pack_data"><label for="datapackpack_data"><?php echo JText::_('PACK_DATA'); ?></label></dt>
		<dd class="hikamarket_serials_pack_data"><?php
			echo $this->packDataType->display('data[pack][pack_data]', @$this->pack->pack_data);
		?></dd>
<?php }

	if(hikamarket::acl('plugins/hikaserial/pack/edit/generator')) { ?>
		<dt class="hikamarket_serials_pack_generator"><label for="datapackpack_generator"><?php echo JText::_('PACK_GENERATOR'); ?></label></dt>
		<dd class="hikamarket_serials_pack_generator"><?php
			echo $this->packGeneratorType->display('data[pack][pack_generator]', @$this->pack->pack_generator);
		?></dd>
<?php }

	if(hikamarket::acl('plugins/hikaserial/pack/edit/published')) { ?>
		<dt class="hikamarket_serials_pack_published"><label for="data[pack][pack_published]"><?php echo JText::_('HIKA_PUBLISHED'); ?></label></dt>
		<dd class="hikamarket_serials_pack_published"><?php
			echo JHTML::_('hikaselect.booleanlist', 'data[pack][pack_published]', '', @$this->pack->pack_published);
		?></dd>
<?php }

	if(hikamarket::acl('plugins/hikaserial/pack/edit/description')) { ?>
		<dt class="hikamarket_serials_pack_description"><label for="data[pack][pack_description]"><?php echo JText::_('HIKA_DESCRIPTION'); ?></label></dt>
		<dd class="hikamarket_serials_pack_description"><?php
			$ret = $this->editor->display();
			if($this->editor->editor == 'codemirror')
				echo str_replace(array('(function() {'."\n",'})()'."\n"),array('window.hikashop.ready(function(){', '});'), $ret);
			else
				echo $ret;
		?><div style="clear:both"></div></dd>
<?php }

	if($this->vendor->vendor_id <= 1 && hikamarket::acl('plugins/hikaserial/pack/edit/vendor')) { ?>
		<dt class="hikamarket_serials_pack_vendor"><label for="data[pack][pack_vendor_id]"><?php echo JText::_('HIKA_VENDOR'); ?></label></dt>
		<dd class="hikamarket_serials_pack_vendor"><?php
			echo $this->nameboxMarketType->display(
				'data[pack][pack_vendor_id]',
				(int)@$this->pack->pack_vendor_id,
				hikamarketNameboxType::NAMEBOX_SINGLE,
				'vendor',
				array(
					'delete' => true,
					'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>'
				)
			);
		?></dd>
<?php }
?>
	</dl>
	<input type="hidden" name="cid" value="<?php echo @$this->pack->pack_id ?>"/>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
	<input type="hidden" name="task" value="pack"/>
	<input type="hidden" name="ctrl" value="serials"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
