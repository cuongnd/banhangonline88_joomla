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
	public static function display($row, $lists)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$editor = JFactory::getEditor();
		$user = JFactory::getUser();
		$GLOBALS['form_typename'] = $row->name; ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=forms"><?php echo JText::_('forms'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="tabbable tabs-left contentarea">
	            <ul class="nav nav-tabs equalheight">
	                <li class="active"><a href="#tab1" data-toggle="tab"><img
	                        src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/forms.png"
	                        border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('general');?></a></li>
	                <li><a href="#tab2" data-toggle="tab"><img
	                        src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/addons.png"
	                        border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('actions');?></a></li>
	                <li><a href="#tab3" data-toggle="tab"><img
	                        src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
	                        border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('fields');?></a></li>
	            </ul>
	            <div class="tab-content contentbar withleft pad5">
	                <div id="tab1" class="tab-pane active equalheight">
	                    <div class="row-fluid">
	                        <div class="span12">
	                            <div class="row-fluid">
	                                <div class="span2 showPopover"
	                                     data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
	                                     data-content="<?php echo htmlspecialchars(JText::_('form_name_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
	                                </div>
	                                <div class="span10">
	                                    <input type="text"
	                                           id="name"
	                                           name="name"
	                                           value="<?php echo $row->name; ?>"
	                                           maxlength="100" />
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row-fluid">
	                        <div class="span12">
	                            <div class="row-fluid">
	                                <div class="span2 showPopover"
	                                     data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"
	                                     data-content="<?php echo htmlspecialchars(JText::_('form_desc_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('description'); ?>
				                    </span>
	                                </div>
	                                <div class="span10">
										<?php echo $editor->display('description', $row->description, '100%', '500', '75', '20');?>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="row-fluid">
	                        <div class="span12">
	                            <div class="row-fluid">
	                                <div class="span2 showPopover"
	                                     data-original-title="<?php echo htmlspecialchars(JText::_('published')); ?>"
	                                     data-content="<?php echo htmlspecialchars(JText::_('form_published_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('published'); ?>
				                    </span>
	                                </div>
	                                <div class="span10">
										<?php echo $lists['show']; ?>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div id="tab2" class="tab-pane equalheight">
						<?php if ($row->id) : ?>
	                    <div id="formactions" name="formactions" align="left"></div>
						<?php else: ?>
	                    <img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>
	                    <b><?php echo JText::_('actions_firsttime');?></b>
						<?php endif;?>
	                    <div class="clr"></div>
	                </div>
	                <div id="tab3" class="tab-pane equalheight">
						<?php if ($row->id) : ?>
	                    <div id="formfields" name="formfields" align="left"></div>
						<?php else: ?>
	                    <img src="../components/com_maqmahelpdesk/images/info.png" align="absmiddle"/>
	                    <b><?php echo JText::_('fields_firsttime');?></b>
						<?php endif;?>
	                    <div class="clr"></div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	        <input type="hidden" name="task" value=""/>
	    </form>

	    <script type='text/javascript'>
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'show_action'){
                $jMaQma('.nav-tabs li:eq(1) a').tab('show');
                showAction(0);
                return;
            }else if (pressbutton == 'show_field'){
                $jMaQma('.nav-tabs li:eq(2) a').tab('show');
                showField(0);
                return;
            }else if (pressbutton == 'forms') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.name.value == "") {
                alert("<?php echo JText::_('name_required'); ?>");
            } else {
			    <?php echo $editor->save('description'); ?>
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        $jMaQma(document).ready(function () {
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });

		<?php if ($row->id > 0) { ?>
        function getActions() {
            $jMaQma("div#loading").show();
            $jMaQma("div#formactions").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=actions&tmpl=component&format=raw",
                    {
                        action:'list',
                        id_form:'<?php echo $row->id; ?>'
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        function showAction(ID) {
            if (ID == 0) {
                ACTION = 'new';
            } else {
                ACTION = 'edit';
            }
            $jMaQma("div#loading").show();
            $jMaQma("div#formactions").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=actions&tmpl=component&format=raw",
                    {
                        action:ACTION,
                        id_form:'<?php echo $row->id; ?>',
                        id:ID
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        function cancelAction() {
            getActions();
        }

        function saveAction() {
            if (document.adminForm.action_published0.checked) {
                PUBLISHED = 0;
            } else {
                PUBLISHED = 1;
            }

            $jMaQma("div#loading").show();
            $jMaQma("div#formactions").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=actions&tmpl=component&format=raw",
                    {
                        action:'save',
                        id_form:'<?php echo $row->id; ?>',
                        id:document.adminForm.action_id.value,
                        type:document.adminForm.action_type.value,
                        value:document.adminForm.action_value.value,
                        published:PUBLISHED
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        function deleteAction(ID) {
            $jMaQma("div#loading").show();
            $jMaQma("div#formactions").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=actions&tmpl=component&format=raw",
                    {
                        action:'delete',
                        id_form:'<?php echo $row->id; ?>',
                        id:ID
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        getActions();

        function getFieldsTags() {
            $jMaQma("div#loading").show();
            $jMaQma("div#fieldsvars").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=fields&tmpl=component&format=raw",
                    {
                        action:'tags',
                        id_form:'<?php echo $row->id; ?>'
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        function getFields() {
            $jMaQma("div#loading").show();
            $jMaQma("div#formfields").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=fields&tmpl=component&format=raw",
                    {
                        action:'list',
                        id_form:'<?php echo $row->id; ?>'
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        function showField(ID) {
            if (ID == 0) {
                ACTION = 'new';
            } else {
                ACTION = 'edit';
            }

            $jMaQma("div#loading").show();
            $jMaQma("div#formfields").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=fields&tmpl=component&format=raw",
                    {
                        action:ACTION,
                        id_form:'<?php echo $row->id; ?>',
                        id:ID
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        function cancelField() {
            getFields();
            getFieldsTags();
        }

        function saveField() {
            if (document.adminForm.field_required0.checked) {
                REQUIRED = 0;
            } else {
                REQUIRED = 1;
            }

            $jMaQma("div#loading").show();
            $jMaQma("div#formfields").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=fields&tmpl=component&format=raw",
                    {
                        action:'save',
                        id_form:'<?php echo $row->id; ?>',
                        id:document.adminForm.field_id.value,
                        caption:document.adminForm.field_caption.value,
                        order:document.adminForm.field_order.value,
                        type:document.adminForm.field_type.value,
                        value:document.adminForm.field_value.value,
                        size:document.adminForm.field_size.value,
                        maxlength:document.adminForm.field_maxlength.value,
                        required:REQUIRED
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
            getFieldsTags()
        }

        function deleteField(ID) {
            $jMaQma("div#loading").show();
            $jMaQma("div#formfields").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=fields&tmpl=component&format=raw",
                    {
                        action:'delete',
                        id_form:'<?php echo $row->id; ?>',
                        id:ID
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
            getFieldsTags()
        }

        function saveFieldOrder() {
            $jMaQma("div#loading").show();

            ORDERS = '';
            for (i = 0; i < document.adminForm.nr_forms.value; i++) {
                ORDERS = ORDERS + document.getElementById('form' + i).value + '|' + document.getElementById('order' + i).value + ';';
            }

            $jMaQma("div#formfields").load("index.php?option=com_maqmahelpdesk&task=forms_ajax&page=fields&tmpl=component&format=raw",
                    {
                        action:'saveorder',
                        id_form:'<?php echo $row->id; ?>',
                        orders:ORDERS
                    },
                    function () {
                        $jMaQma("div#loading").hide();
                    });
        }

        getFields();
        getFieldsTags();
		<?php } ?>

        $jMaQma(document).ready(function () {
            $jMaQma(".equalheight").equalHeights();
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script><?php
	}
}
