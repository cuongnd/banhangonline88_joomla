<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class fieldsClass extends acymailingClass{

	var $tables = array('fields');
	var $pkey = 'fieldid';
	var $errors = array();
	var $prefix = 'field_';
	var $suffix = '';
	var $excludeValue = array();
	var $formoption = '';

	var $labelClass = '';

	function getFields($area,&$user){

		if(empty($user)) $user = new stdClass();

		$where = array();
		$where[] = 'a.`published` = 1';
		if($area == 'backend'){
			$where[] = 'a.`backend` = 1';
			$where[] = 'a.`core` = 0';
		}elseif($area == 'backlisting'){
			$where[] = 'a.`listing` = 1';
		}elseif($area == 'frontcomp'){
			$where[] = 'a.`frontcomp` = 1';
		}elseif($area == 'frontlisting'){
			$where[] = 'a.`frontlisting` = 1';
		}elseif($area == 'frontjoomlaprofile'){
			$where[] = 'a.`frontjoomlaprofile` = 1';
		}elseif($area == 'frontjoomlaregistration'){
			$where[] = 'a.`frontjoomlaregistration` = 1';
		}elseif($area == 'joomlaprofile'){
			$where[] = 'a.`joomlaprofile` = 1';
		}elseif($area == 'module'){
		}elseif($area != 'all'){
			$area = $this->database->Quote($area);
			$namesField = str_replace(",", $area[0].",".$area[0],$area);
			$where[] = "a.`namekey` IN (".$namesField.")";
		}

		$this->database->setQuery('SELECT * FROM `#__acymailing_fields` as a WHERE '.implode(' AND ',$where).' ORDER BY a.`ordering` ASC');
		$fields = $this->database->loadObjectList('namekey');

		foreach($fields as $namekey => $field){
			if(!empty($fields[$namekey]->options)){
				$fields[$namekey]->options = unserialize($fields[$namekey]->options);
			}
			if(!empty($field->value)){
				$fields[$namekey]->value = $this->explodeValues($fields[$namekey]->value);
			}
			if($field->type == 'file') $this->formoption = 'enctype="multipart/form-data"';
			if(empty($user->subid)) $user->$namekey = $field->default;
		}
		return $fields;
	}

	function getFieldName($field){
		$addLabels = array('textarea','text','dropdown','multipledropdown','file');
		return '<label '.(empty($this->labelClass) ? '' : ' class="'.$this->labelClass.'" ').(in_array($field->type,$addLabels) ? ' for="'.$this->prefix.$field->namekey.$this->suffix.'" ' : '' ).'>'.$this->trans($field->fieldname).'</label>';
	}

	function trans($name){
		if(preg_match('#^[A-Z_]*$#',$name)){
			return JText::_($name);
		}
		return $name;
	}

	function listing($field,$value){
		$functionType = '_listing'.ucfirst($field->type);
		return method_exists($this,$functionType) ? $this->$functionType($field,$value) : nl2br($this->trans($value));
	}

	function explodeValues($values){
		$allValues = explode("\n",$values);
		$returnedValues = array();
		foreach($allValues as $id => $oneVal){
			$line = explode('::',trim($oneVal));
			$var = @$line[0];
			$val = @$line[1];
			if(strlen($val)<1) continue;

			$obj = new stdClass();
			$obj->value = $val;
			for($i=2;$i<count($line);$i++){
				$obj->{$line[$i]} = 1;
			}
			$returnedValues[$var] = $obj;
		}
		return $returnedValues;
	}


	function get($fieldid,$default = null){
		$column = is_numeric($fieldid) ? 'fieldid' : 'namekey';
		$query = 'SELECT a.* FROM '.acymailing_table('fields').' as a WHERE a.`'.$column.'` = '.$this->database->Quote($fieldid).' LIMIT 1';
		$this->database->setQuery($query);

		$field = $this->database->loadObject();
		if(!empty($field->options)){
			$field->options = unserialize($field->options);
		}

		if(!empty($field->value)){
			$field->value = $this->explodeValues($field->value);
		}

		return $field;
	}

	function chart($table,$field){

		static $a = false;
		$doc = JFactory::getDocument();
		if(!$a){
			$a = true;
			$doc->addScript("https://www.google.com/jsapi");
		}
		$namekey = acymailing_secureField($field->namekey);
		if(in_array($field->type,array('checkbox','multipledropdown'))){
			if(empty($field->value)) return;
			$results = array();
			foreach($field->value as $valName => $oneValue){
				if(strlen($oneValue->value) < 1) continue;
				$this->database->setQuery('SELECT COUNT(subid) as total, '.$this->database->Quote($valName).' as name FROM '.acymailing_table($table).' WHERE `'.$namekey.'` LIKE '.$this->database->Quote('%,'.$valName.',%').' OR `'.$namekey.'` LIKE '.$this->database->Quote($valName.',%').' OR `'.$namekey.'` LIKE '.$this->database->Quote('%,'.$valName).' OR `'.$namekey.'` = '.$this->database->Quote($valName));
				$myResult = $this->database->loadObject();
				if(!empty($myResult->total)) $results[] = $myResult;
			}
		}else{
			$this->database->setQuery('SELECT COUNT(`'.$namekey.'`) as total,`'.$namekey.'` as name FROM '.acymailing_table($table).' WHERE `'.$namekey.'` IS NOT NULL AND `'.$namekey.'` != \'\' GROUP BY `'.$namekey.'` ORDER BY total DESC LIMIT 20');
			$results = $this->database->loadObjectList();
		}

		?>
		<script language="JavaScript" type="text/javascript">
		 function drawChart<?php echo $namekey; ?>() {
			var dataTable = new google.visualization.DataTable();
			dataTable.addColumn('string');
			dataTable.addColumn('number');
			dataTable.addRows(<?php echo count($results); ?>);

			<?php
			$export = '';
			foreach($results as $i => $oneResult){
				$name = isset($field->value[$oneResult->name]) ? $this->trans($field->value[$oneResult->name]->value) : $oneResult->name;
				$export .= "\n".$name.','.$oneResult->total;
				?>
				dataTable.setValue(<?php echo $i ?>, 0, '<?php echo addslashes($name).' ('.$oneResult->total.')'; ?>');
				dataTable.setValue(<?php echo $i ?>, 1, <?php echo intval($oneResult->total); ?>);
			<?php } ?>

			var vis = new google.visualization.<?php echo (in_array($field->type,array('checkbox','multipledropdown'))) ? 'ColumnChart' : 'PieChart'; ?>(document.getElementById('fieldchart<?php echo $namekey;?>'));
					var options = {
						width: 600,
						height: 400,
						is3D:true,
						legendTextStyle: {color:'#333333'},
						legend:<?php echo (in_array($field->type,array('checkbox','multipledropdown'))) ? "'none'" : "'right'"; ?>
					};
					vis.draw(dataTable, options);
			}
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart<?php echo $namekey; ?>);

		function exportData<?php echo $namekey;?>(){
			if(document.getElementById('exporteddata<?php echo $namekey;?>').style.display == 'none'){
				document.getElementById('exporteddata<?php echo $namekey;?>').style.display = '';
			}else{
				document.getElementById('exporteddata<?php echo $namekey;?>').style.display = 'none';
			}
		}
			</script>

		<div style="width:600px;" class="acychart" id="fieldchart<?php echo $namekey;?>"></div>
		<img style="position:relative;top:-45px;left:5px;cursor:pointer;" onclick="exportData<?php echo $namekey;?>();" src="<?php echo ACYMAILING_IMAGES.'smallexport.png'; ?>" alt="<?php echo JText::_('ACY_EXPORT',true)?>" title="<?php echo JText::_('ACY_EXPORT',true)?>" />
		<textarea cols="50" rows="10" id="exporteddata<?php echo $namekey;?>" style="display:none;position:absolute;margin-top:-150px;"><?php echo $export; ?></textarea>
<?php
		}

	function saveForm(){

		$app = JFactory::getApplication();

		$field = new stdClass();
		$field->fieldid = acymailing_getCID('fieldid');

		$formData = JRequest::getVar( 'data', array(), '', 'array' );

		foreach($formData['fields'] as $column => $value){
			acymailing_secureField($column);
			if(is_array($value)){
				if(isset($value['day']) || isset($value['month']) || isset($value['year'])){
					$value = (empty($value['year']) ? '0000' :intval($value['year'])).'-'.(empty($value['month']) ? '00' : intval($value['month'])).'-'.(empty($value['day']) ? '00' : intval($value['day']));
				}else{
					$value = implode(',',$value);
				}
			}
			$field->$column = strip_tags($value);
		}

		$fieldValues = JRequest::getVar('fieldvalues', array(), '', 'array' );
		if(!empty($fieldValues)){
			$field->value = array();
			foreach($fieldValues['title'] as $i => $title){
				$title = trim(strip_tags($title));
				$value = trim(strip_tags($value));
				if(strlen($title)<1 AND strlen($fieldValues['value'][$i])<1) continue;
				$value = strlen($fieldValues['value'][$i])<1 ? $title : $fieldValues['value'][$i];
				$extra = '';
				if(!empty($fieldValues['disabled'][$i])) $extra .= '::disabled';
				$field->value[] = $title.'::'.$value.$extra;
			}
			$field->value = implode("\n",$field->value);
		}

		$fieldsOptions = JRequest::getVar( 'fieldsoptions', array(), '', 'array' );
		foreach($fieldsOptions as $column => $value){
			$fieldsOptions[$column] = strip_tags($value);
		}
		if($field->type == "customtext"){
			$fieldsOptions['customtext'] = JRequest::getVar('fieldcustomtext','','','string',JREQUEST_ALLOWRAW);
			if(empty($field->fieldid)) $field->namekey = 'customtext_'.date('z_G_i_s');
		}

		if(in_array($field->type,array('birthday','date')) && !empty($fieldsOptions['format']) && strpos($fieldsOptions['format'],'%') === false){
			$app->enqueueMessage('Invalid Format: "'.$fieldsOptions['format'].'"<br/><br/>Please use a combination of:<br/> - %d (which will be replaced by days)<br/> - %m (which will be replaced by months)<br/> - %Y (which will be replaced by years)','notice');
			$fieldsOptions['format'] = '';
		}

		$field->options = serialize($fieldsOptions);

		if(empty($field->fieldid) AND $field->type != 'customtext'){
			if(empty($field->namekey)) $field->namekey = $field->fieldname;
			$field->namekey = substr(preg_replace('#[^a-z0-9_]#i', '',strtolower($field->namekey)),0,50);
			if(empty($field->namekey) || !preg_match('#^[a-z]#',$field->namekey)){
				$this->errors[] = 'Please specify a valid Column Name';
				return false;
			}

			$columns = acymailing_getColumns('#__acymailing_subscriber');

			if(isset($columns[$field->namekey])){
				$this->errors[] = 'The field "'.$field->namekey.'" already exists';
				return false;
			}

			if($field->type == 'textarea'){
				$query = 'ALTER TABLE `#__acymailing_subscriber` ADD `'.$field->namekey.'` TEXT NULL';
			}else{
				$query = 'ALTER TABLE `#__acymailing_subscriber` ADD `'.$field->namekey.'` VARCHAR ( 250 ) NULL';
			}
			$this->database->setQuery($query);
			if(!$this->database->query()) return false;
		}

		$fieldid = $this->save($field);
		if(!$fieldid) return false;

		if(empty($field->fieldid)){
			$orderClass = acymailing_get('helper.order');
			$orderClass->pkey = 'fieldid';
			$orderClass->table = 'fields';
			$orderClass->reOrder();
		}
		JRequest::setVar( 'fieldid', $fieldid);
		return true;

	}

	function delete($elements){
		if(!is_array($elements)){
			$elements = array($elements);
		}

		foreach($elements as $key => $val){
			$elements[$key] = acymailing_getEscaped($val);
		}

		if(empty($elements)) return false;

		$this->database->setQuery('SELECT `namekey`,`fieldid` FROM `#__acymailing_fields`  WHERE `core` = 0 AND `fieldid` IN ('.implode(',',$elements).')');
		$fieldsToDelete = $this->database->loadObjectList('fieldid');

		if(empty($fieldsToDelete)) return false;

		$namekeys = array();
		foreach($fieldsToDelete as $oneField){
			if(substr($oneField->namekey,0,11) == 'customtext_') continue;
			$namekeys[] = $oneField->namekey;
		}
		if(!empty($namekeys)){
			$this->database->setQuery('ALTER TABLE `#__acymailing_subscriber` DROP `'.implode('`, DROP `',$namekeys).'`');
			$this->database->query();
		}


		$this->database->setQuery('DELETE FROM `#__acymailing_fields` WHERE `fieldid` IN ('.implode(',',array_keys($fieldsToDelete)).')');
		$result = $this->database->query();
		if(!$result) return false;

		$affectedRows = $this->database->getAffectedRows();

		$orderClass = acymailing_get('helper.order');
		$orderClass->pkey = 'fieldid';
		$orderClass->table = 'fields';
		$orderClass->reOrder();

		return $affectedRows;

	}

	private function _listingFile($field,$value){
		if(empty($value)) return;
		static $path = '';
		if(empty($path)){
			$config = acymailing_config();
			$path = trim(JPath::clean(html_entity_decode($config->get('uploadfolder'))),DS.' ').DS;
			$path = ACYMAILING_LIVE.str_replace(DS,'/',$path.'userfiles/');
		}

		if(preg_match('#\.(jpg|gif|png|jpeg|ico|bmp)$#i',$value)){
			$fileName = '<img src="'.$path.$value.'" style="max-width:120px;max-height:80px;"/>';
		}else{
			$fileName = str_replace('_',' ',substr($value,strpos($value,'_')));
		}
		return '<a href="'.$path.$value.'" target="_blank">'.$fileName.'</a>';
	}

	private function _listingPhone($field,$value){
		return str_replace(array(','),' ',$value);
	}


	private function _displayPhone($field,$value,$map,$inside){

		$value = trim($value,',');

		$mycountry = '';
		if(strpos($value,',')){
			$mycountry = substr($value,0,strpos($value,','));
			$num = substr($value,strlen($mycountry)+1);
		}elseif(strpos($value,' ') > 1 && strpos($value,' ') < 7){
			$mycountry = substr($value,0,strpos($value,' '));
			$num = substr($value,strlen($mycountry)+1);
		}else{
			$num = $value;
			if(strpos($value,'+') === 0){
				$numChar = 4;
				while($numChar > 0){
					if(isset($this->country[substr($value,1,$numChar)])){
						$mycountry = substr($value,0,$numChar+1);
						$num = substr($value,$numChar+1);
					}
					$numChar--;
				}
			}
		}

		if(strpos($mycountry,'+') !== 0 && substr($mycountry,0,2) == '00'){
			$mycountry = str_replace('00','+',$mycountry);
		}


		$countries = array();
		$countries['93'] = 'Afghanistan';
		$countries['355'] = 'Albania';
		$countries['213'] = 'Algeria';
		$countries['1684'] = 'American Samoa';
		$countries['376'] = 'Andorra';
		$countries['244'] = 'Angola';
		$countries['1264'] = 'Anguilla';
		$countries['672'] = 'Antarctica';
		$countries['1268'] = 'Antigua & Barbuda';
		$countries['54'] = 'Argentina';
		$countries['374'] = 'Armenia';
		$countries['297'] = 'Aruba';
		$countries['247'] = 'Ascension Island';
		$countries['61'] = 'Australia';
		$countries['43'] = 'Austria';
		$countries['994'] = 'Azerbaijan';
		$countries['1242'] = 'Bahamas';
		$countries['973'] = 'Bahrain';
		$countries['880'] = 'Bangladesh';
		$countries['1246'] = 'Barbados';
		$countries['375'] = 'Belarus';
		$countries['32'] = 'Belgium';
		$countries['501'] = 'Belize';
		$countries['229'] = 'Benin';
		$countries['1441'] = 'Bermuda';
		$countries['975'] = 'Bhutan';
		$countries['591'] = 'Bolivia';
		$countries['387'] = 'Bosnia/Herzegovina';
		$countries['267'] = 'Botswana';
		$countries['55'] = 'Brazil';
		$countries['1284'] = 'British Virgin Islands';
		$countries['673'] = 'Brunei';
		$countries['359'] = 'Bulgaria';
		$countries['226'] = 'Burkina Faso';
		$countries['257'] = 'Burundi';
		$countries['855'] = 'Cambodia';
		$countries['237'] = 'Cameroon';
		$countries['1'] = 'Canada/USA';
		$countries['238'] = 'Cape Verde Islands';
		$countries['1345'] = 'Cayman Islands';
		$countries['236'] = 'Central African Republic';
		$countries['235'] = 'Chad Republic';
		$countries['56'] = 'Chile';
		$countries['86'] = 'China';
		$countries['6724'] = 'Christmas Island';
		$countries['6722'] = 'Cocos Keeling Island';
		$countries['57'] = 'Colombia';
		$countries['269'] = 'Comoros';
		$countries['243'] = 'Congo Democratic Republic';
		$countries['242'] = 'Congo, Republic of';
		$countries['682'] = 'Cook Islands';
		$countries['506'] = 'Costa Rica';
		$countries['225'] = 'Cote D\'Ivoire';
		$countries['385'] = 'Croatia';
		$countries['53'] = 'Cuba';
		$countries['357'] = 'Cyprus';
		$countries['420'] = 'Czech Republic';
		$countries['45'] = 'Denmark';
		$countries['253'] = 'Djibouti';
		$countries['1767'] = 'Dominica';
		$countries['1809'] = 'Dominican Republic';
		$countries['593'] = 'Ecuador';
		$countries['20'] = 'Egypt';
		$countries['503'] = 'El Salvador';
		$countries['240'] = 'Equatorial Guinea';
		$countries['291'] = 'Eritrea';
		$countries['372'] = 'Estonia';
		$countries['251'] = 'Ethiopia';
		$countries['500'] = 'Falkland Islands';
		$countries['298'] = 'Faroe Island';
		$countries['679'] = 'Fiji Islands';
		$countries['358'] = 'Finland';
		$countries['33'] = 'France';
		$countries['596'] = 'French Antilles/Martinique';
		$countries['594'] = 'French Guiana';
		$countries['689'] = 'French Polynesia';
		$countries['241'] = 'Gabon Republic';
		$countries['220'] = 'Gambia';
		$countries['995'] = 'Georgia';
		$countries['49'] = 'Germany';
		$countries['233'] = 'Ghana';
		$countries['350'] = 'Gibraltar';
		$countries['30'] = 'Greece';
		$countries['299'] = 'Greenland';
		$countries['1473'] = 'Grenada';
		$countries['590'] = 'Guadeloupe';
		$countries['1671'] = 'Guam';
		$countries['502'] = 'Guatemala';
		$countries['224'] = 'Guinea Republic';
		$countries['245'] = 'Guinea-Bissau';
		$countries['592'] = 'Guyana';
		$countries['509'] = 'Haiti';
		$countries['504'] = 'Honduras';
		$countries['852'] = 'Hong Kong';
		$countries['36'] = 'Hungary';
		$countries['354'] = 'Iceland';
		$countries['91'] = 'India';
		$countries['62'] = 'Indonesia';
		$countries['964'] = 'Iraq';
		$countries['353'] = 'Ireland';
		$countries['972'] = 'Israel';
		$countries['39'] = 'Italy';
		$countries['1876'] = 'Jamaica';
		$countries['81'] = 'Japan';
		$countries['962'] = 'Jordan';
		$countries['254'] = 'Kenya';
		$countries['686'] = 'Kiribati';
		$countries['3774'] = 'Kosovo';
		$countries['965'] = 'Kuwait';
		$countries['996'] = 'Kyrgyzstan';
		$countries['856'] = 'Laos';
		$countries['371'] = 'Latvia';
		$countries['961'] = 'Lebanon';
		$countries['266'] = 'Lesotho';
		$countries['231'] = 'Liberia';
		$countries['218'] = 'Libya';
		$countries['423'] = 'Liechtenstein';
		$countries['370'] = 'Lithuania';
		$countries['352'] = 'Luxembourg';
		$countries['853'] = 'Macau';
		$countries['389'] = 'Macedonia';
		$countries['261'] = 'Madagascar';
		$countries['265'] = 'Malawi';
		$countries['60'] = 'Malaysia';
		$countries['960'] = 'Maldives';
		$countries['223'] = 'Mali Republic';
		$countries['356'] = 'Malta';
		$countries['692'] = 'Marshall Islands';
		$countries['222'] = 'Mauritania';
		$countries['230'] = 'Mauritius';
		$countries['52'] = 'Mexico';
		$countries['691'] = 'Micronesia';
		$countries['373'] = 'Moldova';
		$countries['377'] = 'Monaco';
		$countries['976'] = 'Mongolia';
		$countries['382'] = 'Montenegro';
		$countries['1664'] = 'Montserrat';
		$countries['212'] = 'Morocco';
		$countries['258'] = 'Mozambique';
		$countries['95'] = 'Myanmar (Burma)';
		$countries['264'] = 'Namibia';
		$countries['674'] = 'Nauru';
		$countries['977'] = 'Nepal';
		$countries['31'] = 'Netherlands';
		$countries['599'] = 'Netherlands Antilles';
		$countries['687'] = 'New Caledonia';
		$countries['64'] = 'New Zealand';
		$countries['505'] = 'Nicaragua';
		$countries['227'] = 'Niger Republic';
		$countries['234'] = 'Nigeria';
		$countries['683'] = 'Niue Island';
		$countries['6723'] = 'Norfolk';
		$countries['850'] = 'North Korea';
		$countries['47'] = 'Norway';
		$countries['968'] = 'Oman Dem Republic';
		$countries['92'] = 'Pakistan';
		$countries['680'] = 'Palau Republic';
		$countries['970'] = 'Palestine';
		$countries['507'] = 'Panama';
		$countries['675'] = 'Papua New Guinea';
		$countries['595'] = 'Paraguay';
		$countries['51'] = 'Peru';
		$countries['63'] = 'Philippines';
		$countries['48'] = 'Poland';
		$countries['351'] = 'Portugal';
		$countries['1787'] = 'Puerto Rico';
		$countries['974'] = 'Qatar';
		$countries['262'] = 'Reunion Island';
		$countries['40'] = 'Romania';
		$countries['7'] = 'Russia';
		$countries['250'] = 'Rwanda Republic';
		$countries['1670'] = 'Saipan/Mariannas';
		$countries['378'] = 'San Marino';
		$countries['239'] = 'Sao Tome/Principe';
		$countries['966'] = 'Saudi Arabia';
		$countries['221'] = 'Senegal';
		$countries['381'] = 'Serbia';
		$countries['248'] = 'Seychelles Island';
		$countries['232'] = 'Sierra Leone';
		$countries['65'] = 'Singapore';
		$countries['421'] = 'Slovakia';
		$countries['386'] = 'Slovenia';
		$countries['677'] = 'Solomon Islands';
		$countries['252'] = 'Somalia Republic';
		$countries['685'] = 'Somoa';
		$countries['27'] = 'South Africa';
		$countries['82'] = 'South Korea';
		$countries['34'] = 'Spain';
		$countries['94'] = 'Sri Lanka';
		$countries['290'] = 'St. Helena';
		$countries['1869'] = 'St. Kitts';
		$countries['1758'] = 'St. Lucia';
		$countries['508'] = 'St. Pierre';
		$countries['1784'] = 'St. Vincent';
		$countries['249'] = 'Sudan';
		$countries['597'] = 'Suriname';
		$countries['268'] = 'Swaziland';
		$countries['46'] = 'Sweden';
		$countries['41'] = 'Switzerland';
		$countries['963'] = 'Syria';
		$countries['886'] = 'Taiwan';
		$countries['992'] = 'Tajikistan';
		$countries['255'] = 'Tanzania';
		$countries['66'] = 'Thailand';
		$countries['228'] = 'Togo Republic';
		$countries['690'] = 'Tokelau';
		$countries['676'] = 'Tonga Islands';
		$countries['1868'] = 'Trinidad & Tobago';
		$countries['216'] = 'Tunisia';
		$countries['90'] = 'Turkey';
		$countries['993'] = 'Turkmenistan';
		$countries['1649'] = 'Turks & Caicos Island';
		$countries['688'] = 'Tuvalu';
		$countries['256'] = 'Uganda';
		$countries['380'] = 'Ukraine';
		$countries['971'] = 'United Arab Emirates';
		$countries['44'] = 'United Kingdom';
		$countries['598'] = 'Uruguay';
		$countries['1 '] = 'USA/Canada';
		$countries['998'] = 'Uzbekistan';
		$countries['678'] = 'Vanuatu';
		$countries['3966'] = 'Vatican City';
		$countries['58'] = 'Venezuela';
		$countries['84'] = 'Vietnam';
		$countries['1340'] = 'Virgin Islands (US)';
		$countries['681'] = 'Wallis/Futuna Islands';
		$countries['967'] = 'Yemen Arab Republic';
		$countries['260'] = 'Zambia';
		$countries['263'] = 'Zimbabwe';

		$dropCountry = array();
		$dropCountry[] = JHTML::_('select.option','',' - - - ');
		foreach($countries as $code => $country){
			$dropCountry[] = JHTML::_('select.option','+'.$code,$country.' (+'.intval($code).')');
		}

		$style = array();

		$class= empty($field->required) ? ' class="inputbox"' : ' class="inputbox required"';
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}

		$styleline = empty($style) ? '' : ' style="'.implode($style,';').'"';

		if(!isset($countries[trim($mycountry,'+')])){
			$mycountry = '';
			$num = $value;
		}

		$countrycode = JHTML::_('select.genericlist', $dropCountry, $map.'[country]', 'style="width:80px;"', 'value', 'text',$mycountry,$this->prefix.$field->namekey.$this->suffix.'_country');
		$inputphone = '<input type="text" name="'.$map.'[num]" '.$class.$styleline.' value="'.htmlspecialchars($num,ENT_COMPAT, 'UTF-8').'" />';
		return $countrycode.' '.$inputphone;
	}

	private function _listingBirthday($field,$value){
		if(empty($value) || $value == '0000-00-00') return;
		if(empty($field->options['format'])) $field->options['format'] = "%d %m %Y";
		list($year,$month,$day) = explode('-',$value);
		return str_replace(array('%Y','%m','%d'),array($year,$month,$day),$field->options['format']);
	}

	function display($field,$value,$map,$inside = false){
		if(empty($field->type)) return;
		$functionType = '_display'.ucfirst($field->type);
		return $this->$functionType($field,$value,$map,$inside);
	}

	private function _displayFile($field,$value,$map,$inside){
		$style = array();
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}
		$styleline = empty($style) ? '' : ' style="'.implode($style,';').'"';

		$id = str_replace(' ','_',$this->prefix.$field->namekey.$this->suffix);
		$result = '<input type="file" id="'.$id.'" name="'.$map.'" '.$styleline.' />';
		if(empty($value)) return $result;
		$config = acymailing_config();
		$uploadFolder = trim(JPath::clean(html_entity_decode($config->get('uploadfolder'))),DS.' ').DS;
		$fileName = str_replace('_',' ',substr($value,strpos($value,'_')));
		$result .= ' <span class="fileuploaded"><a href="'.ACYMAILING_LIVE.str_replace(DS,'/',$uploadFolder).'userfiles/'.$value.'" target="_blank">'.$fileName.'</a></span>';
		return $result;
	}

	private function _displayText($field,$value,$map,$inside){
		$class= empty($field->required) ? 'class="inputbox"' : 'class="inputbox required"';
		$style = array();
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}
		$styleline = empty($style) ? '' : ' style="'.implode($style,';').'"';
		$js = '';
		if($inside AND strlen($value) < 1){
			$value = $this->trans($field->fieldname);
			$valueInside = addslashes($value);
			$this->excludeValue[$field->namekey] = $valueInside;
			$js = 'onfocus="if(this.value == \''.$valueInside.'\') this.value = \'\';" onblur="if(this.value==\'\') this.value=\''.$valueInside.'\';"';
		}
		$id = str_replace(' ','_',$this->prefix.$field->namekey.$this->suffix);
		return '<input id="'.$id.'" '.$styleline.' '.$js.' type="text" '.$class.' name="'.$map.'" value="'.htmlspecialchars($value,ENT_COMPAT, 'UTF-8').'" />';
	}

	private function _displayTextarea($field,$value,$map,$inside){
		$class= empty($field->required) ? 'class="inputbox"' : 'class="inputbox required"';
		$js = '';
		if($inside AND strlen($value) < 1){
			$value = addslashes($this->trans($field->fieldname));
			$this->excludeValue[$field->namekey] = $value;
			$js = 'onfocus="if(this.value == \''.$value.'\') this.value = \'\';" onblur="if(this.value==\'\') this.value=\''.$value.'\';"';
		}
		$cols = empty($field->options['cols']) ? '' : 'cols="'.intval($field->options['cols']).'"';
		$rows = empty($field->options['rows']) ? '' : 'rows="'.intval($field->options['rows']).'"';
		return '<textarea '.$class.' id="'.$this->prefix.$field->namekey.$this->suffix.'" name="'.$map.'" '.$cols.' '.$rows.' '.$js.'>'.$value.'</textarea>';
	}

	private function _listingTextarea($field,$value){
		$noHtml = strip_tags($value);
		if(strlen($noHtml) > 80){
			return substr($noHtml,0,77).'...';
		}
		return $value;
	}

	private function _listingSelectedvals(&$field,$values){
		$return = '';
		foreach($values as $value){
			if(isset($field->value[$value]->value)) $return .= ', '.$this->trans($field->value[$value]->value);
			else $return .= ', '.$value;
		}

		return trim($return,', ');
	}

	private function _listingSingledropdown($field,$value){
		return $this->_listingSelectedvals($field,array($value));
	}

	private function _listingMultipledropdown($field,$value){
		return $this->_listingSelectedvals($field,explode(',',$value));
	}

	private function _listingRadio($field,$value){
		return $this->_listingSelectedvals($field,array($value));
	}

	private function _listingCheckbox($field,$value){
		return $this->_listingSelectedvals($field,explode(',',$value));
	}

	private function _displayCustomtext($field,$value,$map,$inside){
		return $this->trans(@$field->options['customtext']);
	}

	private function _displayRadio($field,$value,$map,$inside){
		return $this->_displayRadioCheck($field,$value,$map,'radio',$inside);
	}

	private function _displaySingledropdown($field,$value,$map,$inside){
		return $this->_displayDropdown($field,$value,$map,'single',$inside);
	}

	private function _displayMultipledropdown($field,$value,$map,$inside){
		$value = explode(',',$value);
		return $this->_displayDropdown($field,$value,$map,'multiple',$inside);
	}

	private function _displayDropdown($field,$value,$map,$type,$inside){
		$class= empty($field->required) ? '' : 'class="required"';
		$string = '';
		$style = array();
		if($type == "multiple"){
			$string.= '<input type="hidden" name="'.$map.'" value=" "/>'."\n";
			$map.='[]';
			$arg = 'multiple="multiple"';
			if(!empty($field->options['size'])) $arg .= ' size="'.intval($field->options['size']).'"';
		}else{
			$arg = 'size="1"';
			if(!empty($field->options['size'])){
				$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
			}
		}
		$styleline = empty($style) ? '' : ' style="'.implode($style,';').'"';
		$string .= '<select '.$class.' id="'.$this->prefix.$field->namekey.$this->suffix.'" name="'.$map.'" '.$arg.$styleline.' >'."\n";
		if(empty($field->value)) return $string;
		foreach($field->value as $oneValue => $myValue){
			$selected = ((is_string($value) AND $oneValue == $value) OR is_array($value) AND in_array($oneValue,$value)) ? 'selected="selected"' : '';
			$id = str_replace(' ','_',$this->prefix.$field->namekey.$this->suffix.'_'.$oneValue);
			$disabled = empty($myValue->disabled) ? '' : 'disabled="disabled"';
			$string .= '<option value="'.$oneValue.'" id="'.$id.'" '.$disabled.' '.$selected.' >'.$this->trans($myValue->value).'</option>'."\n";
		}
		$string .= '</select>';
		return $string;
	}

	private function _displayRadioCheck($field,$value,$map,$type,$inside){
		$string = '';
		if($inside) $string = $this->trans($field->fieldname).' ';
		if($type == 'checkbox'){
			$string.= '<input type="hidden" name="'.$map.'" value=" " />'."\n";
			$map.='[]';
		}
		if(empty($field->value)) return $string;
		foreach($field->value as $oneValue => $myValue){
			$checked = ((is_string($value) AND $oneValue == $value) OR is_array($value) AND in_array($oneValue,$value)) ? 'checked="checked"' : '';
			$id = str_replace(' ','_',$this->prefix.$field->namekey.$this->suffix.'_'.$oneValue);
			$disabled = empty($myValue->disabled) ? '' : 'disabled="disabled"';
			$string .= '<span id="span_'.$id.'"><label for="'.$id.'"><input type="'.$type.'" name="'.$map.'" value="'.htmlspecialchars($oneValue,ENT_COMPAT, 'UTF-8').'" id="'.$id.'" '.$disabled.' '.$checked.' /> '.$this->trans($myValue->value).'</label></span>'."\n";
		}
		return $string;
	}

	private function _displayDate($field,$value,$map,$inside){
		if(empty($field->options['format'])) $field->options['format'] = "%Y-%m-%d";
		$style = array();
		if(!empty($field->options['size'])){
			$style[] = 'width:'.(is_numeric($field->options['size']) ? ($field->options['size'].'px') : $field->options['size']);
		}
		$styleForCalendar = array();
		if(!empty($style)) $styleForCalendar['style'] = implode($style,';');

		if($inside AND strlen($value) < 1){
			$value = addslashes($this->trans($field->fieldname));
			$this->excludeValue[$field->namekey] = $value;
			$styleForCalendar['onfocus'] = 'if(this.value == \''.$value.'\') this.value = \'\'';
			$styleForCalendar['onblur'] = 'if(this.value==\'\') this.value=\''.$value.'\';';
		}

		if(!empty($field->required)) $styleForCalendar['class'] = 'required';

		if($value == '{now}' AND $map != 'data[fields][default]') $value = strftime($field->options['format'],time());
		return JHTML::_('calendar', $value, $map,$this->prefix.$field->namekey.$this->suffix,$field->options['format'],$styleForCalendar);
	}

	private function _displayBirthday($field,$value,$map,$inside){
		$class= empty($field->required) ? '' : 'class="required"';
		if(empty($field->options['format'])) $field->options['format'] = "%d %m %Y";
		$vals = explode('-',$value);
		$days = array();
		$days[] =  JHTML::_('select.option','',JText::_('ACY_DAY'));
		for($i=1;$i<32;$i++) $days[] = JHTML::_('select.option',(strlen($i) == 1) ? '0'.$i : $i,$i);
		$years = array();
		$years[] =  JHTML::_('select.option','',JText::_('ACY_YEAR'));
		for($i=1901;$i<date('Y')+10;$i++) $years[] = JHTML::_('select.option',$i,$i);
		$months = array();
		$months[] = JHTML::_('select.option','',JText::_('ACY_MONTH'));
		$months[] = JHTML::_('select.option','01',JText::_('JANUARY'));
		$months[] = JHTML::_('select.option','02',JText::_('FEBRUARY'));
		$months[] = JHTML::_('select.option','03',JText::_('MARCH'));
		$months[] = JHTML::_('select.option','04',JText::_('APRIL'));
		$months[] = JHTML::_('select.option','05',JText::_('MAY'));
		$months[] = JHTML::_('select.option','06',JText::_('JUNE'));
		$months[] = JHTML::_('select.option','07',JText::_('JULY'));
		$months[] = JHTML::_('select.option','08',JText::_('AUGUST'));
		$months[] = JHTML::_('select.option','09',JText::_('SEPTEMBER'));
		$months[] = JHTML::_('select.option','10',JText::_('OCTOBER'));
		$months[] = JHTML::_('select.option','11',JText::_('NOVEMBER'));
		$months[] = JHTML::_('select.option','12',JText::_('DECEMBER'));
		$dayField = JHTML::_('select.genericlist',   $days, $map.'[day]', $class.' style="max-width:80px;"', 'value', 'text',@$vals[2],$this->prefix.$field->namekey.$this->suffix.'_day');
		$monthField = JHTML::_('select.genericlist', $months  , $map.'[month]', $class.' style="max-width:130px;"', 'value', 'text',@$vals[1],$this->prefix.$field->namekey.$this->suffix.'_month');
		$yearField = JHTML::_('select.genericlist',$years   , $map.'[year]', $class.' style="max-width:100px;"', 'value', 'text',intval(@$vals[0]),$this->prefix.$field->namekey.$this->suffix.'_year');
		return str_replace(array('%d','%m','%Y'),array($dayField,$monthField,$yearField),$field->options['format']);
	}

	private function _displayCheckbox($field,$value,$map,$inside){
		$value = explode(',',$value);
		return $this->_displayRadioCheck($field,$value,$map,'checkbox',$inside);
	}
}
