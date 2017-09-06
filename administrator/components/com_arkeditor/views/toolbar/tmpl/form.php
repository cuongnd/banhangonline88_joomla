<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

JHTML::_('behavior.tooltip'); 
JHtml::_('formbehavior.chosen', 'select');

define('ARKEDITOR_COMPONENT_VIEW', ARKEDITOR_COMPONENT. '/views/toolbar');

JHTML::stylesheet(ARKEDITOR_COMPONENT .'/css/icons.css');
JHTML::stylesheet(ARKEDITOR_COMPONENT_VIEW .'/css/sorttables.css');

jimport('joomla.environment.browser');
$browser = JBrowser::getInstance();
$name = $browser->getBrowser();
$version = $browser->getMajor();

if($name == 'msie' && $version == 7) 
{
	JHTML::stylesheet(ARKEDITOR_COMPONENT_VIEW .'/css/sorttables_ie7.css');
}

JFilterOutput::objectHTMLSafe( $this->toolbar, ENT_QUOTES, '' );
	
?>
<script language="javascript" type="text/javascript">
     window.addEvent('domready', function()
	 {
		var sortableList = new Sortables('.sortableList', {revert: true, clone:true,opacity:0});

		var sortableRow =  new Sortables('.sortableRow', {revert: true, clone:true, opacity:0,
										onStart : function(element,clone)
										{
											clone.style.zIndex = 999;										
										},
										onComplete : function(element)
										{
											if(element && element.id  in { Styles:1, Font:1, Format:1,FontSize:1 } )
											 {
												element.setStyles({
												'position': 'relative',
												'left' : '0px',
												'top': '2px'
												});
											}
											
											if(!Browser.ie)
											 {
												if(element)
												{
													element.setStyle('margin-right','4px');
												} 
											}
											sortableList.attach();
										}});
		
		$$('li.sortableItem').getElements('img').each(function(el){
		      el.addEvent('mousedown', function(event){
				sortableList.detach();
		      });
		  });

		$$('li.sortableItem').getElements('img').each(function(el){
		      el.addEvent('mouseup', function(event){
				sortableList.attach();
		      });
		  });
	 });

	function getForm()
	{
		return document.getElementById('adminForm');
	}

	Joomla.submitbutton = function(task)
	{
		if (task == 'toolbars.cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		// validation
		var form = getForm(), items = [];
		if (form.title.value == "") {
			alert( "<?php echo JText::_( 'COM_ARKEDITOR_LAYOUT_MANAGER_LAYOUT_TOOLBAR_MUST_HAVE_A_TITLE', true ); ?>" );
		} 
		else if (form.name.value == "") {
			alert( "<?php echo JText::_( 'COM_ARKEDITOR_LAYOUT_MANAGER_LAYOUT_TOOLBAR_MUST_HAVE_A_NAME', true ); ?>" );
		}else {
			// Serialize group layout
            if(document.id('groupLayout'))
            {
			    document.id('groupLayout').getElements('ul.sortableRow').each(function(el){
				    items.include(el.getChildren().map(function(o, i){
				      if(o.hasClass('spacer')){
					    return ';';
				      }
				      return o.id.replace(/[^A-Za-z0-9_]/gi, '');
				    }).join(','));  
			      });
             } 
			  form.rows.value = items.join(',/,') || '';
			submitform(task);
		}
	}

</script>
<?php
  $edit_name_Disabled = $this->toolbar->iscore ? ' readonly="readonly"' :'';
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		<?php ARKHelper::fixBug(); ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
<div id="main-container">
<?php endif;?>
		<fieldset style="width:700px;" class="adminform">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
				<li><a href="#header" data-toggle="tab"><?php echo JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_COMPONENT_HEADER_CONFIGURATION');?></a></li>
				<?php if(!$this->toolbar->default): ?>
				<li><a href="#editor" data-toggle="tab"><?php echo JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_COMPONENT_ASSIGNMENT');?></a></li>
				<?php endif; ?>
			</ul>
			<div  class="tab-content">
				<div class="tab-pane active" id="details">
					<table class="admintable">
						<tr>
							<td width="100" class="key">
								<label for="name">
									<?php echo JText::_( 'COM_ARKEDITOR_LAYOUT_MANAGER_DESCRIPTION' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="jform[title]" id="title" size="35" value="<?php echo $this->toolbar->title; ?>" <?php echo $edit_name_Disabled ?>/>
							</td>
						</tr>
						<td valign="top" class="key">
								<label for="file">
									<?php echo JText::_( 'COM_ARKEDITOR_LAYOUT_MANAGER_TOOLBAR_NAME' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="jform[name]" id="name" size="35" value="<?php echo $this->toolbar->name; ?>" <?php echo $edit_name_Disabled ?>/>
							</td>
						</tr>
				   </table>
					<div class="clr"></div>
					<?php if($this->total > 0) : ?>
							<table>
								 <tr>
									<td>
									<fieldset>
										<legend><?php echo JText::_( 'COM_ARKEDITOR_LAYOUT_MANAGER_LAYOUT_MANAGEMENT' ); ?></legend>

										<div class="cke_top" >&nbsp;</div>

									<div class="sortableList" id="groupLayout" style="position: relative">
								   <?php

									$totalRows = count($this->toolbarplugins);

									for( $i=0; $i< $totalRows+1; $i++){?>
										<div class="sortableListDiv">
											<span class="sortableListSpan">
											<ul class="sortableRow">
									<?php
										if($i < $totalRows)
										{
											$toolbarplugins =  $this->toolbarplugins[$i];
											$many = count($toolbarplugins);

											for( $x=0; $x< $many; $x++ )
											{
												$icons = $toolbarplugins[$x];

												if( is_array($icons)) {
												
													
													foreach($icons as $iconTitle)
													{
														$icon = $this->getItem($iconTitle);
														if(empty($icon))
															continue;						
														
														$extraAttr = "";

														if($icon->icon != "")
														{ 
															if(is_numeric($icon->icon))
															{
																$path = ARKEDITOR_COMPONENT_VIEW .'/images/spacer.gif';
																$extraAttr = ' class="cke_icon"  style="background-position:0px '.$icon->icon.'px;"'; 
															}
															else
															{
																$path = '../plugins/arkeditor/'.$icon->name.'/'.$icon->name.'/icons/'.$icon->icon;
																if(!file_exists(JPATH_PLUGINS.'/arkeditor/'.$icon->name.'/'.$icon->name.'/icons/'.$icon->icon))
																{
																	$path = ARKEDITOR_COMPONENT .'/icons/'.$icon->icon;
																	if(!file_exists(JPATH_COMPONENT.'/icons/'.$icon->icon))
																		continue;
																}	
															}
														}
														else
														{
															$path = ARKEDITOR_COMPONENT .'/icons/'.$icon->name.'.png';
														}

														?>
													  <li class="sortableItem" id="<?php echo $icon->title;?>"><img src="<?php echo $path;?>" alt="<?php echo $icon->title;?>" title="<?php echo $icon->title;?>" <?php echo $extraAttr; ?>/></li>
													 <?php
													  } 
													  ?>
													  <li class="sortableItem spacer" id="icon"><img src="<?php echo ARKEDITOR_COMPONENT;?>/icons/spacer.png" alt="<?php echo JText::_('Spacer');?>" title="<?php echo JText::_('Spacer');?>" /></li>
													 <?php  
												}
											}
										}
									   ?>
											</ul>
											</span>
										</div>
							 <?php }?>
									</div>

									<div class="cke_bot" >&nbsp;</div>

									</fieldset>
									<fieldset>
									<legend><?php echo JText::_( 'COM_ARKEDITOR_LAYOUT_MANAGER_LAYOUT_TOOLBAR_AVAILABLE_BUTTONS' ); ?></legend>

									<div class="cke_top" >&nbsp;</div>

									<div class="sortableList" style="position: relative">
									<?php 

									$max = ARKHelper::getNextAvailablePluginRowId() + 1;

									for( $i=1; $i<=$max; $i++ ){
									?>
										<div class="sortableListDiv">
											<span class="sortableListSpan">
											<ul class="sortableRow">
									   <?php 
											if( $i == $max){
												for( $x = 1; $x<=20; $x++ ){?>
													<li class="sortableItem spacer" id="icon0"><img src="<?php echo ARKEDITOR_COMPONENT;?>/icons/spacer.png" alt="<?php echo JText::_('Spacer');?>" title="<?php echo JText::_('Spacer');?>" /></li>
									  <?php 	}
											}
										if(!empty( $this->plugins)) {

											foreach( $this->plugins as $icon ){

											$extraAttr = "";
												
												if( $icon->row == $i ){
													if($icon->icon != "")
													{ 
														if(is_numeric($icon->icon))
														{
															$path = ARKEDITOR_COMPONENT_VIEW .'/images/spacer.gif';
															$extraAttr = ' class="cke_icon"  style="background-position:0px '.$icon->icon.'px;"'; 
														}
														else
														{
															$path = '../plugins/arkeditor/'.$icon->name.'/'.$icon->name.'/icons/'.$icon->icon;
															if(!file_exists(JPATH_PLUGINS.'/arkeditor/'.$icon->name.'/'.$icon->name.'/icons/'.$icon->icon))
															{
																$path = ARKEDITOR_COMPONENT .'/icons/'.$icon->icon;
															}	
														}
													}
													else
													{
														$path = ARKEDITOR_COMPONENT .'/icons/'.$icon->name.'.png';
													}	
									   ?>
													<li class="sortableItem" id="<?php echo $icon->title ;?>"><img src="<?php echo $path;?>" alt="<?php echo $icon->title;?>" title="<?php echo $icon->title;?>" <?php echo $extraAttr; ?> /></li>
										<?php }
											}
										}
										?>
											</ul>
											</span>
										</div>
								<?php }?>
									</div>     
									<div class="cke_bot" >&nbsp;</div>
									</fieldset>
									</td>
								</tr>
							</table>
					<?php endif; ?>
				</div>
				<div class="tab-pane" id="header">
					<?php echo $this->loadTemplate('header'); ?>	
				</div>
				<?php if(!$this->toolbar->default): ?>
				<div class="tab-pane" id="editor">
					<?php echo $this->loadTemplate('editor'); ?>
				</div>
				<?php endif; ?>
			</div>
		</fieldset>		
	</div>
	<input type="hidden" name="option" value="com_arkeditor" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->toolbar->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->toolbar->id; ?>" />
 	<input type="hidden" name="controller" value="toolbars" />
	<input type="hidden" name="rows" value="" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>