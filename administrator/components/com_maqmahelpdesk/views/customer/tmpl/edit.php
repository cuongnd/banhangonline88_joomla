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
	static function display(&$_row, $lists)
	{
		$GLOBALS['title_customer_form'] = ($_row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('client_access'); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=product"><?php echo JText::_('downloads'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=customer"><?php echo JText::_('clients_access'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('client')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('client'); ?>
					                    </span>
	                        </div>
	                        <div class="span10">
	                            <input type="text"
                                       class="span10"
	                                   id="searchclient"
	                                   name="searchclient"
	                                   value="<?php echo $_row->clientname; ?>" />
	                            <input type="hidden"
	                                   id="id_user"
	                                   name="id_user"
	                                   value="<?php echo $_row->id_user; ?>" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('dl_product')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('dl_product')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('dl_product'); ?>
					                    </span>
	                        </div>
	                        <div class="span10">
								<?php echo $lists['products']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('active')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('active')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('active'); ?>
					                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['isactive']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('serial_number')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('serial_number')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('serial_number'); ?>
					                    </span>
	                        </div>
	                        <div class="span8">
	                            <input type="text"
	                                   id="serialno"
	                                   name="serialno"
	                                   value="<?php echo $_row->serialno; ?>"
	                                   maxlength="250" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('start')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('start')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('start'); ?>
					                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo JHTML::Calendar($_row->servicefrom, 'servicefrom', 'servicefrom', '%Y-%m-%d', array('class' => 'text_area', 'size' => '12', 'maxlength' => '12')); ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('end')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('end')); ?>">
					                    <span class="label">
						                    <?php echo JText::_('end'); ?>
					                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo JHTML::Calendar($_row->serviceuntil, 'serviceuntil', 'serviceuntil', '%Y-%m-%d', array('class' => 'text_area', 'size' => '12', 'maxlength' => '12')); ?>
	                        </div>
	                    </div>
	                </div>
	            </div><?php

		        for ($x = 0; $x < count($lists['cfields']); $x++):
			        $cfield = $lists['cfields'][$x]; ?>
			        <div class="row-fluid">
				        <div class="span12">
					        <div class="row-fluid">
						        <div class="span2 showPopover"
						             data-original-title="<?php echo htmlspecialchars($cfield->caption); ?>"
						             data-content="<?php echo htmlspecialchars($cfield->caption); ?>">
						                    <span class="label">
							                    <?php echo $cfield->caption; ?>
						                    </span>
						        </div>
						        <div class="span10">
							        <?php echo HelpdeskForm::WriteField(0, $cfield->id_field, $cfield->ftype, $cfield->value, $cfield->size, $cfield->maxlength, 0, 0, 0, 0, 0, 0, $_row->id); ?>
						        </div>
					        </div>
				        </div>
			        </div><?php
		        endfor;?>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $_row->id; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form>

	    <script type="text/javascript">
	        Joomla.submitbutton = function (pressbutton) {
	            var form = document.adminForm;
	            if (pressbutton == 'customer') {
	                Joomla.submitform(pressbutton);
	                return;
	            }

	            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
	        }

	        $jMaQma(document).ready(function () {
	            $jMaQma("#searchclient").autocompletemqm("index.php?option=com_maqmahelpdesk&task=ajax_getclient&format=raw", {
	                selectFirst:false,
	                scroll:true,
	                scrollHeight:300,
	                formatItem:function (data, i, n, value) {
	                    return '<img src="' + data[2] + '" width="32" height="32" align="left">' + data[1];
	                },
	                selectExecute:function (data) {
	                    $jMaQma("#id_user").val(data[0]);
	                    $jMaQma("#searchclient").val(data[1]);
	                }
	            });
	            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
	        });
	    </script><?php
	}
}
