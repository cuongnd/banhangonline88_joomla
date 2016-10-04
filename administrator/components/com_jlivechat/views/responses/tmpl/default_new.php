<?php 
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

defined('_JEXEC') or die('Restricted access'); 
?>
<script language="javascript" type="text/javascript">
    prepYUI();
</script>
<form action="index.php?option=com_jlivechat&view=responses" method="post" name="adminForm" id="adminForm">
    <table cellpadding="3" cellspacing="0" border="0">
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('RESPONSE_NAME_HELPTIP'); ?>"><?php echo JText::_('RESPONSE_NAME_LBL'); ?></span></td>
	    <td><input type="text" id="response_name" name="response_name" value="<?php echo JRequest::getVar('response_name', '', 'method'); ?>" size="30" /></td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('RESPONSE_CATEGORY_HELPTIP'); ?>"><?php echo JText::_('CATEGORY_LBL'); ?></span></td>
	    <td>
		<div class="category-wrapper">
		    <input type="text" id="response_category" name="response_category" value="<?php echo JRequest::getVar('response_category', '', 'method'); ?>" size="30" />
		    <div class="clr">&nbsp;</div>
		    <div id="response-cat-autocomplete"></div>
		</div>
	    </td>
	</tr>
	<tr>
	    <td class="label"><span><?php echo JText::_('RESPONSE_TXT_LBL'); ?></span></td>
	    <td>
		<?php
		    echo $this->editor->display('response_txt', JRequest::getVar('response_txt','', 'method', 'string', JREQUEST_ALLOWRAW), '520', '210', '60', '10', false);
		?>
	    </td>
	</tr>
    </table>
    
    <div class="clr">&nbsp;</div>
    <input type="hidden" name="task" value="create_response" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">

    var allCategories = {
	categories: [
			<?php
			    if(count($this->response_categories) > 0) {
				foreach($this->response_categories as $key => $category) {
			 ?>
			    "<?php echo $category['response_category']; ?>"<?php if($key != (count($this->response_categories) - 1)) { ?>, <?php } ?>
			    
			 <?php
				}
			    }
			 ?>
		    ]
    };

    var autoCompleteFunc = function() {
	// Use a LocalDataSource
	var oDS = new YAHOO.util.LocalDataSource(allCategories.categories);

	// Instantiate the AutoComplete
	var oAC = new YAHOO.widget.AutoComplete("response_category", "response-cat-autocomplete", oDS);
	oAC.prehighlightClassName = "yui-ac-prehighlight";
	oAC.useShadow = true;

	return {
	    oDS: oDS,
	    oAC: oAC
	};
    }();

</script>
