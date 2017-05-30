<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class plgAcymailingHikaserial extends JPlugin
{
	public $hikaserial_installed = false;

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin =& JPluginHelper::getPlugin('acymailing', 'hikaserial');
			if(version_compare(JVERSION,'2.5','<')){
				jimport('joomla.html.parameter');
				$this->params = new JParameter($plugin->params);
			} else {
				$this->params = new JRegistry($plugin->params);
			}
		}
		$this->init();
	}

	protected function init() {
		if(!$this->hikaserial_installed) {
			if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php')) {
				$this->hikaserial_installed = false;
			} else {
				$this->hikaserial_installed = true;
			}
		}
		return $this->hikaserial_installed;
	}

	public function acymailing_getPluginType() {
		$onePlugin = new stdClass();
		$onePlugin->name = 'HikaSerial';
		$onePlugin->function = 'acymailinghikaserial_show';
		$onePlugin->help = 'plugin-hikaserial';
		return $onePlugin;
	}

	public function acymailinghikaserial_show() {
	 	if(!$this->init()) {
	 		return 'Please install HikaSerial before using the HikaSerial tag plugin';
	 	}
		$app = JFactory::getApplication();
		$contentType = array(
		);

		$pageInfo = new stdClass();
		$paramBase = ACYMAILING_COMPONENT.'.hikaserial';
		$pageInfo->filter = new stdClass;
		$pageInfo->filter->order = new stdClass;
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $paramBase.".filter_order", 'filter_order', 'a.pack_id','cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word' );
		$pageInfo->search = $app->getUserStateFromRequest( $paramBase.".search", 'search', '', 'string' );
		$pageInfo->search = JString::strtolower( $pageInfo->search );
		$pageInfo->lang = $app->getUserStateFromRequest( $paramBase.".lang", 'lang','','string' );
		$pageInfo->contenttype = $app->getUserStateFromRequest( $paramBase.".contenttype", 'contenttype','|type:full','string' );
		$pageInfo->limit = new stdClass;
		$pageInfo->limit->value = $app->getUserStateFromRequest( $paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$pageInfo->limit->start = $app->getUserStateFromRequest( $paramBase.'.limitstart', 'limitstart', 0, 'int' );
		$db = JFactory::getDBO();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.$db->getEscaped($pageInfo->search).'%\'';
			$filters[] = 'a.pack_id LIKE '.$searchVal.' OR a.pack_name LIKE '.$searchVal.' OR a.pack_description LIKE '.$searchVal;
		}
		$whereQuery = '';
		if(!empty($filters)){
			$whereQuery = ' WHERE ('.implode(') AND (',$filters).')';
		}
		$query = 'SELECT SQL_CALC_FOUND_ROWS a.* FROM '.acymailing::table('hikaserial_pack',false).' as a';
		if(!empty($whereQuery)) $query.= $whereQuery;
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}
		$db->setQuery($query,$pageInfo->limit->start,$pageInfo->limit->value);
		$rows = $db->loadObjectList();
		if(!empty($pageInfo->search)){
			$rows = acymailing::search($pageInfo->search,$rows);
		}
		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements = new stdClass;
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value );
?>
	<script language="javascript" type="text/javascript">
	<!--
	function updateTagProd(packid){
		tag = '{hikaserial_genpack:'+packid;
		for(var i=0; i < document.adminForm.contenttype.length; i++){
			 if (document.adminForm.contenttype[i].checked){ tag += document.adminForm.contenttype[i].value; }
		}
		tag += '}';
		setTag(tag);
		insertTag();
	}
	//-->
	</script>
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'JOOMEXT_FILTER' ); ?>:
				<input type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'JOOMEXT_GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo JText::_( 'JOOMEXT_RESET' ); ?></button>
			</td>
		</tr>
	</table>
<?php  ?>
	<table class="adminlist table table-striped" cellpadding="1" width="100%">
		<thead>
			<tr>
				<th class="title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'a.pack_name', $pageInfo->filter->order->dir,$pageInfo->filter->order->value);
				?></th>
				<th class="title titleid"><?php
					echo JHTML::_('grid.sort', JText::_('ID'), 'a.pack_id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value);
				?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $pagination->getListFooter(); ?>
					<?php echo $pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$k = 0;
	for($i = 0,$a = count($rows);$i<$a;$i++){
		$row =& $rows[$i];
?>
			<tr id="content<?php echo $row->pack_id?>" class="row<?php echo $k; ?>" onclick="updateTagProd(<?php echo $row->pack_id; ?>);" style="cursor:pointer;">
				<td><?php
					echo $row->pack_name;
				?></td>
				<td align="center"><?php
					echo $row->pack_id;
				?></td>
			</tr>
<?php
		$k = 1-$k;
	}
?>
		</tbody>
	</table>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>" />
<?php
	}

	public function acymailing_replaceusertags(&$email,&$user,$send = true) {
		if(!$this->init()) {
	 		return;
	 	}
		if(empty($user->subid)) return;
		$match = '#{hikaserial_genpack:(.*)}#Ui';
		$variables = array('subject','body','altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match,$email->$var,$results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}
		if(!$found)
			return;
		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;
				$tags[$oneTag] = $this->generateCoupon($allresults,$i,$user,$send);
			}
		}
		foreach(array_keys($results) as $var){
			$email->$var = str_replace(array_keys($tags),$tags,$email->$var);
		}
	}

	public function generateCoupon($allresults, $i, $user, $send) {
		$db =& JFactory::getDBO();
		$packClass = hikaserial::get('class.pack');
		$config = hikaserial::config();

		$generator = null;
		$serials = array();
		$order = null;
		list($pack_id,$serial_status) = explode('|',$allresults[1][$i]);
		if(empty($serial_status)) {
			$serial_status = $config->get('used_serial_status', 'used');
		}
		$pack = $packClass->get($pack_id);

		if(substr($pack->pack_generator, 0, 4) == 'plg.') {
			$pluginName = substr($pack->pack_generator, 4);
			if(strpos($pluginName,'-') !== false){
				list($pluginName,$pluginId) = explode('-', $pluginName, 2);
				$pack->$pluginName = $pluginId;
			}
			$generator = hikaserial::import('hikaserial', $pluginName);
		}

		if($generator != null && method_exists($generator, 'generate')) {
			if(!$send)
				$generator->test = true;
			ob_start();
			$generator->generate($pack, $order, 1, $serials);
			ob_get_clean();
		}

		if($send && !empty($serials)) {
			$serial = reset($serials);
			$extra_data = '';
			if(is_object($serial)) {
				if(!empty($serial->extradata))
					$extra_data = serialize($serial->extradata);
				$serial = $serial->data;
			}

			$data = array(
				'serial_pack_id' => (int)$pack_id,
				'serial_data' => $db->Quote($serial),
				'serial_status' => $db->Quote($serial_status),
				'serial_assign_date' => 'NULL',
				'serial_order_id' => 'NULL',
				'serial_user_id' => 'NULL', // $user->id to hikashop_id
				'serial_order_product_id' => 'NULL',
				'serial_extradata' => $db->Quote($extra_data)
			);
			$query = 'INSERT IGNORE INTO ' . hikaserial::table('serial') . ' (' . implode(',', array_keys($data)) . ') VALUES (' . implode(',', $data) . ')';

			$db->setQuery($query);
			$db->query();
			unset($query);

			return $serial;
		} elseif(!empty($serials)) {
			$serial = reset($serials);
			if(is_object($serial))
				$serial = $serial->data;
			return $serial;
		}

		return '';
	}
}
