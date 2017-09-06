<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<table class="adminlist table table-striped table-hover" style="cellspacing:1px">
	<thead>
		<tr>
			<th class="title titlenum"><?php
				echo JText::_( 'HIKA_NUM' );
			?></th>
			<th class="title"><?php
				echo JText::_('HIKA_NAME');
			?></th>
			<th class="title titletoggle"><?php
				echo JText::_('HIKA_ENABLED');
			?></th>
			<th class="title titleid"><?php
				echo JText::_( 'ID' );
			?></th>
		</tr>
	</thead>
	<tbody>
<?php
$k = 0;
foreach($this->plugins as $i => &$row) {

	if(!HIKASHOP_J16) {
		$publishedid = 'published-' . $row->id;
	} else {
		$publishedid = 'enabled-' . $row->id;
	}
?>
		<tr class="row<?php echo $k;?>">
			<td align="center"><?php
				echo $i+1;
			?></td>
			<td><?php
				if($this->manage) {
					?><a href="<?php echo hikaserial::completeLink('plugins&task=edit&name=' . $row->element);?>"><?php
				}
				echo $row->name;
				if($this->manage) {
					?></a><?php
				}
			?></td>
			<td align="center">
				<span id="<?php echo $publishedid ?>" class="loading"><?php
					if($this->manage) {
						echo $this->toggleClass->toggle($publishedid, $row->published, 'plugins');
					} else {
						echo $this->toggleClass->display('activate', $row->published);
					}
				?></span>
			</td>
			<td align="center"><?php
				echo $row->id;
			?></td>
		</tr>
<?php
	$k = 1-$k;
}
?>
	</tbody>
</table>
