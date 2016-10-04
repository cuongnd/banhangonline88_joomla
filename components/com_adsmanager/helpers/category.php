<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class JHTMLAdsmanagerCategory {
	
	/**
	 * Render Category Form List in different way
	 * @param String $name id and name of the input field
	 * @param int|array $current id or array of id of current selected categories
	 * @param tree $values tree of categories 
	 * @param array $options array("display":"normal"|"color"|"split"|"multiple")
	 */
	static function split($name, $value = null, $attribs = null,$tree) {
	}
	
	static function displaySplitCategories($name="category",$listcats,$catid,$options=array()) {
		$defaultoptions = array("root_allowed"=>true,"display_price" => false,"separator" => '<br/>',"class" => "","id" => $name);
		foreach($options as $key => $value) {
			$defaultoptions[$key] = $value;
		}
		$options = $defaultoptions;
		
		//var_dump($listcats);
		$document	= JFactory::getDocument();
		$document->addScript(JURI::root()."components/com_adsmanager/js/jquery.chained.js");
		$maxlevel = count($listcats) -1;
		
		$done = 0;
		$listids = array();
		$tmpcatid = $catid;
		for($i= $maxlevel;$i>=0;$i--) {
			foreach($listcats[$i] as $cat) {
				if ($cat->id == $tmpcatid) {
					$listids[] = $tmpcatid;
					$tmpcatid = $cat->parent;
					break;
				}
			}	
		}
		?>
		<input type="hidden" name="<?php echo $name?>" id="<?php echo $options['id'] ?>" value="<?php echo $catid; ?>"/>
		<?php 
		foreach($listcats as $level => $list) {
			$text = JText::_('ADSMANAGER_SELECT_CATEGORY_LEVEL_'.$level);
			if ($text == 'ADSMANAGER_SELECT_CATEGORY_LEVEL_'.$level) {
				$text = JText::_("ADSMANAGER_SELECT_CATEGORY");
			}
			?>
			<?php if ($level == $maxlevel) {?>
			<select class="<?php echo $name?>_cascade <?php echo $options['class']?>" id="<?php echo $options['id'] ?>_level_<?php echo $level?>">
			<?php } else { ?>
			<select class="<?php echo $name?>_cascade <?php echo $options['class']?>" id="<?php echo $options['id'] ?>_level_<?php echo $level?>">
			<?php } ?>
				<option value=""><?php echo $text;?></option>
				<?php foreach ($list as $row) {?>
				<?php $selected = (in_array($row->id,$listids)) ? 'selected="selected"' : '';?>
				<?php 
				$opt = array();
				$opt['attribs'] = "";
				$opt['label'] =  $row->name;
				if ($options['display_price'] == "true" && (function_exists("getCategoryOption"))) { 
					getCategoryOption($row,$opt);
					$opt['label'] = str_replace("&nbsp;"," ",$opt['label']);
				}
				$attribs = $opt['attribs'];
				$optionlabel = $opt['label'];	
				?>
				<option <?php echo $attribs; ?> <?php echo $selected; ?> value="<?php echo $row->id?>" class="<?php echo $row->parent ?>"><?php echo htmlspecialchars($optionlabel); ?></option>
				<?php }	?>
			</select>
			<?php echo $options['separator'] ?>
		<?php }?>
		<script type="text/javascript">
		<?php for($l=1;$l<= $maxlevel;$l++) {?>
		jQ("#<?php echo $options['id'] ?>_level_<?php echo ($l)?>").chained("#<?php echo $options['id'] ?>_level_<?php echo ($l-1)?>");
		<?php } ?>
		jQ("#<?php echo $options['id'] ?>_level_<?php echo $maxlevel?>").change(function() {
			catid = "";
			list = [];
			<?php for($level=$maxlevel; $level >=0; $level-- ) {?>
			list.push ('#<?php echo $options['id'] ?>_level_<?php echo $level?>');
			<?php } ?>
			for(i=0;i<list.length;i++) {
				select = list[i];
				//select has a selected value
				if (jQ(select).val() !== "") {
					catid = jQ(select).val(); break;
				} else if (1 == jQ("option", select).size() && jQ(select).val() === "") {
					//continue
				} else {
					<?php if ($options['root_allowed'] == false) { ?>
					catid = ""; break;
					<?php  } ?>
				}
			}
			jQ('#<?php echo $options['id'] ?>').val(catid).trigger('change');	
		});
		</script>
	<?php
	}
	
	/**
	 * Display a dropdown categories list with color change depending on levels
	 * @param string $name name and id of the select
	 * @param array $listcats list of categories
	 * @param int $catid default_catid
	 * @param array $options (display_price=>bool,color=> array(#dcdcc3,...))
	 */
	static function displayColorCategories($name="category",$listcats,$catid,$options=array(),$attr=array()) {
		$defaultoptions = array("root_allowed"=>true,"display_price" => false,"color" => array('#dcdcc3'),"allow_empty"=>false,"class"=> '',"id" => $name);
		foreach($options as $key => $value) {
			$defaultoptions[$key] = $value;
		}
		$options = $defaultoptions;
		
		$attrhtml = "";
		foreach($attr as $key => $v ) {
			if ($v != "") {
				$attrhtml .= "$key=$v ";
			} else {
				$attrhtml .= "$key ";
			}
		}
		?>
		<select id="<?php echo htmlspecialchars($options['id'])?>" class="<?php echo $options['class']?>" name="<?php echo htmlspecialchars($name)?>" <?php echo $attrhtml?>>
		<?php if (($catid == 0)||($options['allow_empty'])) { ?>
		<option value=""><?php echo JText::_('ADSMANAGER_SELECT_CATEGORY')?></option>
		<?php } ?>
		<?php foreach($listcats as $cat) {?>
		 
		 <?php if (isset($options['color'][$cat->level])) {
		 	$style = 'style="background-color:'.$options['color'][$cat->level].';"';
		 } else  {
		 	$style = '';
		 }
		 if (($options['root_allowed'] == false)&&($cat->leaf == false)) {
		 	$disabled = 'disabled="disabled"';
		 } else {
		 	$disabled = '';
		 }
		 $selected = ($cat->id == $catid) ? 'selected="selected"' : '';
		 ?>
		<?php 
		$opt = array();
		$opt['attribs'] = $disabled;
		$opt['label'] =  $cat->name;
		if ($options['display_price'] == "true" && (function_exists("getCategoryOption"))) { 
			getCategoryOption($cat,$opt);
			$opt['label'] = str_replace("&nbsp;"," ",$opt['label']);
		}
		$attribs = $opt['attribs'];
		$optionlabel = $opt['label'];	
		/*<optgroup label="<?php echo $optionlabel; ?>">*/
		?>
		<option <?php echo $attribs; ?> <?php echo $selected?> <?php echo $disabled ?> <?php echo $style ?> value="<?php echo $cat->id?>"><?php echo htmlspecialchars($optionlabel); ?></option>
		<?php }?>
		
		</select>
		<?php 
		
	}
	
	static function displayNormalCategories($name="category",$listcats,$catid,$options=array(),$attr=array()) {
		$defaultoptions = array("root_allowed"=>true,"display_price" => false,"separator" => " > ","allow_empty"=>false,"class"=>"","id" => $name);
		foreach($options as $key => $value) {
			$defaultoptions[$key] = $value;
		}
		$options = $defaultoptions;
		$attrhtml = "";
		foreach($attr as $key => $v ) {
			if ($v != "") {
				$attrhtml .= "$key=$v ";
			} else {
				$attrhtml .= "$key ";
			}
		}
		?>
		<select id="<?php echo htmlspecialchars($options['id'])?>" class="<?php echo $options['class']?>" name="<?php echo htmlspecialchars($name)?>" <?php echo $attrhtml;?>>
		<?php if (($catid == 0)||($options['allow_empty'])) { ?>
		<option value=""><?php echo JText::_('ADSMANAGER_SELECT_CATEGORY')?></option>
		<?php } ?>
		<?php 
		foreach($listcats as $cat) {
			if (($options['root_allowed'] == true)||($cat->leaf == true)) {
				$parent = "";
				foreach($cat->parents as $p) {
					$parent .= $p['name'].$options['separator'];
				}
				$selected = ($cat->id == $catid) ? 'selected="selected"' : '';
				?>
				<?php 
				$opt = array();
				$opt['attribs'] = "";
				$opt['label'] =  $parent.$cat->name;
				if ($options['display_price'] == "true" && (function_exists("getCategoryOption"))) { 
					getCategoryOption($cat,$opt);
					$opt['label'] = str_replace("&nbsp;"," ",$opt['label']);
				}
				$attribs = $opt['attribs'];
				$optionlabel = $opt['label'];	
				?>
				<option <?php echo $attribs; ?> <?php echo $selected?> value="<?php echo  $cat->id?>"><?php echo htmlspecialchars($optionlabel); ?></option>
				<?php 
			}
		}?>
		</select>
		<?php
	}
	
	static function displayComboboxCategories($name="category",$listcats,$catid,$options=array(),$attr=array()) {
		$document	= JFactory::getDocument();
		$document->addScript(JURI::root()."components/com_adsmanager/js/chosen/chosen.jquery.min.js");
		$document->addStyleSheet(JURI::root()."components/com_adsmanager/js/chosen/chosen.css");
		
		$defaultoptions = array("root_allowed"=>true,"display_price" => false,"separator" => " > ","class"=>"","id" => $name);
		foreach($options as $key => $value) {
			$defaultoptions[$key] = $value;
		}
		$options = $defaultoptions;
		$attrhtml = "";
		foreach($attr as $key => $v ) {
			if ($v != "") {
				$attrhtml .= "$key=$v ";
			} else {
				$attrhtml .= "$key ";
			}
		}
		?>
		<select id="<?php echo htmlspecialchars($options['id'])?>" class="<?php echo $options['class']?>" name="<?php echo htmlspecialchars($name)?>" <?php echo $attrhtml?>>
			<option value=""></option>
			<?php 
			foreach($listcats as $cat) {
				if (($options['root_allowed'] == true)||($cat->leaf == true)) {	
					$parent = "";
					foreach($cat->parents as $p) {
						$parent .= $p['name'].$options['separator'];
					}
					$selected = ($cat->id == $catid) ? 'selected="selected"' : '';
					?>
					<?php 
					$opt = array();
					$opt['attribs'] = "";
					$opt['label'] =  $parent.$cat->name;
					if ($options['display_price'] == "true" && (function_exists("getCategoryOption"))) { 
						getCategoryOption($cat,$opt);
						$opt['label'] = str_replace("&nbsp;"," ",$opt['label']);
					}
					$attribs = $opt['attribs'];
					$optionlabel = $opt['label'];	
					?>
					<option <?php echo $attribs; ?>  <?php echo $selected?> value="<?php echo $cat->id ?>"><?php echo htmlspecialchars($optionlabel); ?></option>
				<?php }
			}?>
		</select>
		<script type="text/javascript">
		jQ(function() {
			jQ( "#<?php echo $options['id'] ?>" ).chosen({
                no_results_text: <?php echo json_encode(JText::_('ADSMANAGER_SELECT_CATEGORY_NO_RESULT')) ?>,
                placeholder_text_single: <?php echo json_encode(JText::_('ADSMANAGER_SELECT_CATEGORY')) ?>,
                placeholder_text_multiple: <?php echo json_encode(JText::_('ADSMANAGER_SELECT_CATEGORY_MULTIPLE')) ?>
            });
		});
		</script>
		<?php
	}
	
	static function displayMultipleCategories($name="category",$listcats,$catids,$options=array(),$nbcats) {
		$document	= JFactory::getDocument();
		$document->addScript(JURI::root()."components/com_adsmanager/js/jquery.doubleselect.js");
		$defaultoptions = array("root_allowed"=>true,"display_price" => false,"separator" => " > ","id" => $name);
		if (strpos($name,"[]") === false) {
			$name = $name."[]";	
		}
		
		foreach($options as $key => $value) {
			$defaultoptions[$key] = $value;
		}
		$options = $defaultoptions;
		?>
		<select id="<?php echo htmlspecialchars($options['id'])?>" name="<?php echo htmlspecialchars($name)?>" multiple="multiple">
			<?php 
			foreach($listcats as $cat) {
				if (($options['root_allowed'] == true)||($cat->leaf == true)) {
					$parent = "";
					foreach($cat->parents as $p) {
						$parent .= $p['name'].$options['separator'];
					}
					?>
					<?php 
					$opt = array();
					$opt['attribs'] = "";
					$opt['label'] =  $parent.$cat->name;
					if ($options['display_price'] == "true" && (function_exists("getCategoryOption"))) { 
						getCategoryOption($cat,$opt);
						$opt['label'] = str_replace("&nbsp;"," ",$opt['label']);
					}
					if (in_array($cat->id,$catids)) {
						$opt['attribs'] .= ' selected="selected"';
					} 
					$attribs = $opt['attribs'];
					$optionlabel = $opt['label'];	
					?>
					<option <?php echo $attribs?> value="<?php echo $cat->id ?>"><?php echo htmlspecialchars($optionlabel) ?></option>
				<?php }
			}?>
			
			
		</select>
		<script type="text/javascript">
		nbmaxcats = <?php echo $nbcats?>;
		<?php 
		$before_add = "";
		$before_remove = "";
		//If PaidSystem 
		$app = JFactory::getApplication();
		if (function_exists("checkPaidField") && $app->isSite()) {
			$before_add = "paidsystem_before_add_categories();";
			$before_remove = "paidsystem_before_remove_categories();";
		}
		?>
		jQ(function() {
			jQ( "#<?php echo $options['id']?>" ).doubleselect({"add_button":<?php echo json_encode(JText::_('ADSMANAGER_ADD'))?>,
													"remove_button":<?php echo json_encode(JText::_('ADSMANAGER_DELETE'))?>,
													"max_selected":<?php echo $nbcats?>,
													"max_selected_text":<?php echo json_encode(JText::_('ADSMANAGER_NBCATS_LIMIT')); ?>,
													"add_function":function(){updateFields();},
													"remove_function":function(){updateFields();},
													"before_add_function":function(){<?php echo $before_add?>},
													"before_remove_function":function(){<?php echo $before_remove?>}});
		});
		</script>
	<?php 
	}
}