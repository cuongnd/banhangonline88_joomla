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
		?>
	    <script language="javascript" type="text/javascript">
	        Joomla.submitbutton = function (pressbutton) {
	            var form = document.adminForm;
	            if (pressbutton == 'customfield') {
	                Joomla.submitform(pressbutton);
	                return;
	            }

	            if (form.caption.value == "") {
	                alert("<?php echo JText::_('caption_required'); ?>");
	            } else {
	                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
	            }
	        }

	        function checkType(FTYPE) {
	            switch (FTYPE) {

	                case 'htmleditor':
	                case 'textarea':
	                    $jMaQma('#d1').hide();
	                    $jMaQma('#d2').hide();
	                    $jMaQma('#d3').hide();
	                    $jMaQma('#d4').show();
	                    $jMaQma('#d5').hide();
	                    $jMaQma('#d6').hide();
	                    break;

	                case 'radio':
	                case 'checkbox':
	                case 'select':
	                    $jMaQma('#d1').show();
	                    $jMaQma('#d2').show();
	                    $jMaQma('#d3').hide();
	                    $jMaQma('#d4').show();
	                    $jMaQma('#d5').hide();
	                    $jMaQma('#d6').hide();
	                    break;

	                case 'dbselect':
	                    $jMaQma('#d1').hide();
	                    $jMaQma('#d2').show();
	                    $jMaQma('#d3').hide();
	                    $jMaQma('#d4').show();
	                    $jMaQma('#d5').show();
	                    $jMaQma('#d6').hide();
	                    break;

	                case 'date':
	                    $jMaQma('#d1').hide();
	                    $jMaQma('#d2').show();
	                    $jMaQma('#d3').hide();
	                    $jMaQma('#d4').hide();
	                    $jMaQma('#d5').hide();
	                    $jMaQma('#d6').hide();
	                    break;

	                case 'text':
	                    $jMaQma('#d1').hide();
	                    $jMaQma('#d2').hide();
	                    $jMaQma('#d3').show();
	                    $jMaQma('#d4').hide();
	                    $jMaQma('#d5').hide();
	                    $jMaQma('#d6').hide();
	                    break;

	                case 'country':
	                case 'state':
	                    $jMaQma('#d1').hide();
	                    $jMaQma('#d2').show();
	                    $jMaQma('#d3').hide();
	                    $jMaQma('#d4').show();
	                    $jMaQma('#d5').hide();
	                    $jMaQma('#d6').hide();
	                    break;

	                case 'note':
	                    $jMaQma('#d1').hide();
	                    $jMaQma('#d2').show();
	                    $jMaQma('#d3').hide();
	                    $jMaQma('#d4').show();
	                    $jMaQma('#d5').hide();
	                    $jMaQma('#d6').show();
	                    break;
	            }

	            return false;
	        }
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	    <div class="breadcrumbs">
	        <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	        <a href="index.php?option=com_maqmahelpdesk&task=customfield"><?php echo JText::_('cfields'); ?></a>
	        <span><?php echo JText::_('edit'); ?></span>
	    </div>
	    <div class="contentarea pad5">
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('type')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('type')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('type'); ?>
				                    </span>
	                </div>
	                <div class="span10">
						<?php echo $lists['cftype']; ?>
	                </div>
	            </div>
	        </div>
	    </div>
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
	                           id="caption"
	                           name="caption"
                               class="span10"
	                           value="<?php echo $row->caption; ?>"
	                           maxlength="100" />
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('help_text')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('help_text')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('help_text'); ?>
				                    </span>
	                </div>
	                <div class="span10">
	                    <textarea name="tooltip"
                                  class="span10"
	                              rows="4"><?php echo $row->tooltip; ?></textarea>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('field_type')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('field_type')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('field_type'); ?>
				                    </span>
	                </div>
	                <div class="span10">
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype0"
	                               name="ftype"
	                               value="radio"
	                               onclick="checkType('radio');"/> <?php echo JText::_('formfield_radio'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype1"
	                               name="ftype"
	                               value="text"
	                               onclick="checkType('text');"/> <?php echo JText::_('formfield_text'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype2"
	                               name="ftype"
	                               value="textarea"
	                               onclick="checkType('textarea');"/> <?php echo JText::_('formfield_textarea'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype6"
	                               name="ftype"
	                               value="dbselect"
	                               onclick="checkType('dbselect');"/> <?php echo JText::_('formfield_dbselect'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype3"
	                               name="ftype"
	                               value="checkbox"
	                               onclick="checkType('checkbox');"/> <?php echo JText::_('formfield_checkbox'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype4"
	                               name="ftype"
	                               value="select"
	                               onclick="checkType('select');"/> <?php echo JText::_('formfield_select'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype5"
	                               name="ftype"
	                               value="htmleditor"
	                               onclick="checkType('htmleditor');"/> <?php echo JText::_('htmleditor'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype10"
	                               name="ftype"
	                               value="date"
	                               onclick="checkType('date');"/> <?php echo JText::_('date'); ?>
	                    </label>
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype7"
	                               name="ftype"
	                               value="country"
	                               onclick="checkType('country');"/> <?php echo JText::_('formfield_country'); ?>
	                    </label>
	                    <!--label class="checkbox inline">
	                                <input type="radio"
	                                       id="ftype8"
	                                       name="ftype"
	                                       value="state"
	                                       onclick="checkType('state');" /> <?php echo JText::_('formfield_state'); ?>
	                            </label-->
	                    <label class="checkbox inline">
	                        <input type="radio"
	                               id="ftype9"
	                               name="ftype"
	                               value="note"
	                               onclick="checkType('note');"/> <?php echo JText::_('formfield_note'); ?>
	                    </label>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('value')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('value')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('value'); ?>
				                    </span>
	                </div>
	                <div class="span10">
	                    <textarea id="value"
	                              name="value"
                                  class="span10"
	                              style="height:150px;"><?php echo $row->value; ?></textarea>
	                    <div id="d1" style="display:none;">
							<?php echo JText::_('field_desc'); ?>
	                    </div>
	                    <div id="d3" style="display:none;">
							<?php echo JText::_('default_value'); ?>
	                    </div>
	                    <div id="d5" style="display:none;">
							<?php echo JText::_('dbselect_description'); ?>
	                    </div>
	                    <div id="d6" style="display:none;">
							<?php echo JText::_('note_description'); ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('size')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('size')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('size'); ?>
				                    </span>
	                </div>
	                <div class="span8">
	                    <input type="text"
	                           id="size"
	                           name="size"
	                           value="<?php echo $row->size; ?>"
	                           maxlength="10"
	                           size="10" />
	                    <div id="d2" style="display:none;">
							<?php echo JText::_('size_dont_apply'); ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('maxlength')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('maxlength')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('maxlength'); ?>
				                    </span>
	                </div>
	                <div class="span8">
	                    <input type="text"
	                           id="maxlength"
	                           name="maxlength"
	                           value="<?php echo $row->maxlength; ?>"
	                           maxlength="10"
	                           size="4" />
	                    <div id="d4" style="display:none;">
							<?php echo JText::_('maxlength_dont_apply'); ?>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    </div>

	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	    <input type="hidden" name="task" value=""/>
	    </form><?php

		if ($row->id > 0) {
			switch ($row->ftype) {
				case 'radio':
					$ftype_number = '0';
					break;
				case 'text':
					$ftype_number = '1';
					break;
				case 'textarea':
					$ftype_number = '2';
					break;
				case 'checkbox':
					$ftype_number = '3';
					break;
				case 'select':
					$ftype_number = '4';
					break;
				case 'htmleditor':
					$ftype_number = '5';
					break;
				case 'dbselect':
					$ftype_number = '6';
					break;
				case 'country':
					$ftype_number = '7';
					break;
				case 'state':
					$ftype_number = '8';
					break;
				case 'note':
					$ftype_number = '9';
					break;
				case 'date':
					$ftype_number = '10';
					break;
			} ?>
        <script type='text/javascript'> $jMaQma("#ftype<?php echo $ftype_number; ?>").attr("checked", "checked");
        checkType('<?php echo $row->ftype; ?>'); </script><?php
		} ?>
	    <script type="text/javascript">
	        $jMaQma(document).ready(function () {
	            $jMaQma("textarea").autoGrow();
	            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
	        });
	    </script><?php
	}
}
