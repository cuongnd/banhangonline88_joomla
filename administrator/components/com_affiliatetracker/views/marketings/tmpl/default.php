<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');
$params =JComponentHelper::getParams( 'com_affiliatetracker' );
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
    <div id="j-main-container" >
        <div class="navbar filter-bar">
            <div class="navbar-inner">
                <input type="text" name="keywords" id="keywords" value="<?php echo $this->keywords;?>" class="text_area keywords" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'TYPE_TO_SEARCH' ); ?>" />

                <button class="btn btn-inverse" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('FILTER_RESULTS'); ?>"><i class="icon-search"></i> <?php echo JText::_('GO'); ?></button>

                <button class="btn" type="button" onclick="document.getElementById('keywords').value='';document.getElementById('adminForm').submit();" title="<?php echo JText::_('RESET'); ?>"><i class="icon-remove"></i></button>
            </div>
        </div>

        <?php
        $modules = JModuleHelper::getModules("at_accounts_backend");
        $document =JFactory::getDocument();
        $renderer = $document->loadRenderer('module');
        $attribs  = array();
        $attribs['style'] = 'xhtml';
        foreach ( @$modules as $mod )
        {
            echo $renderer->render($mod, $attribs);
        }
        ?>

        <table class="table table-striped">
            <thead>
            <tr>
                <th width="5" class="hidden-phone"> <?php echo JHTML::_( 'grid.sort', 'ID', 'acc.id', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
                <th width="20" class="hidden-phone"> <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                <th> <?php echo JHTML::_( 'grid.sort', 'MM_TITLE', 'mm.title', $this->lists['order_Dir'], $this->lists['order']); ?> </th>
                <th> <?php echo JText::_('MM_DESCRIPTION'); ?> </th>
                <th width="5" class="hidden-phone"><?php echo JHTML::_( 'grid.sort', 'PUBLISHED', 'acc.publish', $this->lists['order_Dir'], $this->lists['order']); ?></th>
            </tr>
            </thead>
            <?php
            $k = 0;


            for ($i=0, $n=count( $this->items ); $i < $n; $i++)	{
                $row =$this->items[$i];
                $checked 	= JHTML::_('grid.id',   $i, $row->id );
                $link 		= JRoute::_( 'index.php?option=com_affiliatetracker&controller=marketing&task=edit&cid[]='. $row->id );

                if($row->publish){
                    $publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/tick.png','Published');
                    $link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=marketing&task=unpublish&cid[]='. $row->id);
                }
                else{
                    $publicat = JHTML::image('administrator/components/com_affiliatetracker/assets/images/publish_x.png','Not Published');
                    $link_publicat = JRoute::_('index.php?option=com_affiliatetracker&controller=marketing&task=publish&cid[]='. $row->id);
                }

                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <td class="hidden-phone"><?php echo $row->id; ?></td>
                    <td class="hidden-phone"><?php echo $checked; ?></td>
                    <td><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></td>
                    <td><?php echo $row->description; ?></td>
                    <td align="right" class="center"><a href="<?php echo $link_publicat; ?>"><?php echo $publicat; ?></a></td>
                </tr>
                <?php
                $k = 1 - $k;
            }
            ?>
            <tfoot>

            <tr>
                <td colspan="12" ><?php  echo $this->pagination->getListFooter(); ?></td>
            </tr>
            </tfoot>
        </table>

    </div>
    <input type="hidden" name="option" value="com_affiliatetracker" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="marketing" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<div align="center"><?php echo AffiliateHelper::showATFooter(); ?></div>