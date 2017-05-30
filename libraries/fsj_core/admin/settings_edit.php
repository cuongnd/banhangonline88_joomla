<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Settings_Edit
{
	static $nodesc = 0;
	
	function _Label(&$param)
	{
		return JText::_($param->label);
	}
	
	function _Warn(&$param)
	{
		if ($param->warning)
			return "<div class='fsj_settings_warn'>".$param->warning."</div>";
		return "";	
	}
	
	function _Reset(&$param,$id)
	{
		if ($param->attributes()->hasreset)
			return "<div><button class='button reset_field' id='resetfield|$id'>Reset</button></div>";
		return "";	
	}
	
	function _Description(&$param)
	{
		if ($param->description)
		{
			$text = JText::_($param->description);
			if ($text)
				return "<div class='fsj_settings_description'>".$text."</div>";
		}
		return "";
	}
	
	function _GlobalDisplay(&$param,$value)
	{
		$type = FSJ_XML::GetXMLAttribute($param,'type');
		if ($type == "yesno")
			return $value ? JText::_('JYES') : JText::_('JNO');
		if ( ($type == "string" || $type == "text") && $value == "")
			return JText::_("JNONE");
		if ($type == "combo")
		{
			foreach($param->values->value as $option)
			{
				if ($option->attributes()->id == $value)
					return (string)$option;
			}
		}
		return $value;	
	}
	
	function _DescRows(&$param)
	{
		if ($param->attributes()->descrows)
			return " rowspan='" . $param->attributes()->descrows ."'";
		return "";
	}
	
	function DisplaySetting($group, &$param, $current, $useglobal = false, $global = null)
	{
		if (FSJ_XML::GetXMLAttribute($param,'url') == 1) return;
		
		$type = FSJ_XML::GetXMLAttribute($param,'type');
		$subtype = FSJ_XML::GetXMLAttribute($param,'subtype');
		$id =  FSJ_XML::GetXMLAttribute($param,'id');
		
		if ($group)
		{
			$gid = FSJ_XML::GetXMLAttribute($group,'id');
			$id = $gid . "." . $id;	
		}	
		
		$did = str_replace(".","_",$id);	
?>
			<tr>
				<th width="200">
					<div class='fsj_settings_label'><?php echo FSJ_Settings_Edit::_Label($param); ?></div>
					<?php echo FSJ_Settings_Edit::_Reset($param,$id); ?>
					<?php echo FSJ_Settings_Edit::_Warn($param); ?>
				</th>
				<td width="320" class="fsj_settings_setting">		

				<?php if ($type == "yesno") :
					$yessel = ""; 
					$nosel = "";
					$globsel = "";
					if ($current == 1)
					{
						$yessel = "checked";
					} elseif ($current == -1)
					{
						$globsel = "checked";
					} else {
						$nosel = "checked";	
					}
				 ?>
				
					<?php if ($useglobal): ?>
						<input type="radio" name='<?php echo $id ?>' value='-1' id='<?php echo $id ?>_-1' <?php echo $globsel; ?> />
						<label for="<?php echo $id ?>_-1"><?php echo JText::_('FSJ_FORM_USE_GLOBAL'); ?></label>
					<?php endif; ?>
				
					<input type="radio" name='<?php echo $id ?>' value='1' id='<?php echo $id ?>_1' <?php echo $yessel; ?> />
					<label for="<?php echo $id ?>_1"><?php echo JText::_('JYES'); ?></label>
					<input type="radio" name='<?php echo $id ?>' value='0' id='<?php echo $id ?>_0' <?php echo $nosel; ?>  />
					<label for="<?php echo $id ?>_0"><?php echo JText::_('JNO'); ?></label>
					
				<?php elseif ($type == "combo" && $subtype == "lookup") : ?>
					
					<?php $data = $this->GetLookupData($param); ?>
					<?php $idfield = (string)$param->key_field; ?>
					<?php $titledfield = (string)$param->value_field; ?>
					<select name="<?php echo $id ?>">
						<?php if ($useglobal): ?>
							<option value="-1" <?php if ($current == 'global') echo "selected"; ?> ><?php echo JText::_('FSJ_FORM_USE_GLOBAL'); ?></option>
						<?php endif; ?>
						<?php foreach ($data as $value) :?>
							<option value="<?php echo $value[$idfield]; ?>" <?php if ($value[$idfield] == $current) echo "selected"; ?> ><?php echo JText::_($value[$titledfield]); ?></option>
						<?php endforeach;?>
					</select>
				
				<?php elseif ($type == "combo") : ?>

					<select name="<?php echo $id ?>">
						<?php if ($useglobal): ?>
							<option value="-1" <?php if ($current == 'global') echo "selected"; ?> ><?php echo JText::_('FSJ_FORM_USE_GLOBAL'); ?></option>
						<?php endif; ?>
						<?php foreach ($param->values->value as $value) :?>
							<?php $option = FSJ_XML::GetXMLAttribute($value,"id"); ?>
							<option value="<?php echo $option; ?>" <?php if ($option == $current) echo "selected"; ?> ><?php echo JText::_($value) ?></option>
						<?php endforeach;?>
					</select>
					
				<?php elseif ($type == "text") : ?>
					<?php
						$rows = 5;
						$cols = 40;
						FSJ_XML::GetXMLAttributeX($param,"rows",$rows);
						FSJ_XML::GetXMLAttributeX($param,"cols",$cols);
					?>
					<textarea name="<?php echo $id ?>" id="<?php echo $did; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $current; ?></textarea>

				<?php elseif ($type == "color") : ?>
					<input name="<?php echo $id ?>" value="<?php echo $current; ?>" />					
				<?php elseif ($type == "string") : ?>
					<?php if ($useglobal): ?>
						<input type="radio" name='<?php echo $id ?>_global' value='-1' <?php echo $current == -1 ? "checked" : ""; ?> />
						<label for="<?php echo $id ?>_-1"><?php echo JText::_('FSJ_FORM_USE_GLOBAL'); ?></label>
						<input type="radio" name='<?php echo $id ?>_global' value='1' id='<?php echo $did ?>_global_s' <?php echo $current != -1 ? "checked" : ""; ?> />
						<label for="<?php echo $id ?>_1"><?php echo JText::_('FSJ_FORM_SPECIFY_BELOW'); ?></label>
						<?php if ($current == "-1") $current = ""; ?>
						<br />
					<?php endif; ?>	
					<input name="<?php echo $id ?>" value="<?php echo $current; ?>" onchange="jQuery('#<?php echo $did ?>_global_s').attr('checked',true);"/>					
				<?php endif; ?> 
				
				</td>
				<?php if (!FSJ_Settings_Edit::$nodesc): ?>
				<?php $dr = FSJ_Settings_Edit::_DescRows($param); 
				if ($dr)
					FSJ_Settings_Edit::$nodesc = 1; ?>
				<td <?php echo $dr; ?>>
					<?php if ($useglobal): ?>
						<div class='fsj_settings_globalvalue'><?php echo JText::sprintf('FSJ_FORM_GLOBAL_VALUE',JText::_(FSJ_Settings_Edit::_GlobalDisplay($param,$global))); ?></div>
					<?php endif; ?>
					<?php echo FSJ_Settings_Edit::_Description($param); ?></div>			
				</td>
				<?php else: ?>
					<?php FSJ_Settings_Edit::$nodesc = 0; ?>
				<?php endif; ?>
			</tr>
<?php
	}
}
