<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MaQmaHtmlEdit
{
	static function display(&$row, $lists)
	{
		$GLOBALS['titulo_category_edit'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('category'); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=workgroup"><?php echo JText::_('workgroups'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=category"><?php echo JText::_('categories'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('name')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
	                        </div>
	                        <div class="span10">
	                            <input type="text"
	                                   id="name"
	                                   name="name"
	                                   class="span10"
	                                   value="<?php echo $row->name; ?>"
	                                   maxlength="100"
	                                   onblur="CreateSlug('name');" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('slug')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('slug_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('slug'); ?>
				                    </span>
	                        </div>
	                        <div class="span10">
	                            <input type="text"
	                                   id="slug"
	                                   name="slug"
                                       class="span10"
	                                   value="<?php echo $row->slug; ?>"
	                                   maxlength="100" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('workgroup')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('workgroup'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['workgroup']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('parent')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('parent')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('parent'); ?>
				                    </span>
	                        </div>
	                        <div id="parentField" class="span8">
								<?php echo $lists['parent']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('tickets')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('tickets')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('tickets'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['tickets']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('kb')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('kb')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('kb'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['kb']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('discussions')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('discussions')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('discussions'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['discussions']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('glossary')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('glossary')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('glossary'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['glossary']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('bugtracker')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('bugtracker')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('bugtracker'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['bugtracker']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('published')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('published')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('published'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['show']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" id="id" name="id" value="<?php echo $row->id; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form><?php
	}
}
