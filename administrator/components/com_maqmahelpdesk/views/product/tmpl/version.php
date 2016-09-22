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

class MaQmaHtmlVersion
{
	static function display($id_product, $id, $version)
	{
		$editor = JFactory::getEditor();
		$GLOBALS['title_version_id'] = ($version->id > 0 ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('version'); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            ShowProgress();
			<?php echo $editor->save('vdescription'); ?>
            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }

        function ShowProgress() {
            middle_width = screen.width / 2;
            middle_height = screen.height / 2;

            $jMaQma("#Layer1").css('left', middle_width - 100);
            $jMaQma("#Layer1").css('top', middle_height - 250);
            $jMaQma("#Layer1").show('fade');
        }
	    </script>

	    <div id="overDiv" style="position:absolute; visibility: hidden;z-index: 1000;"></div>

	    <div id="Layer1"
	         style="position: absolute; margin-left: auto; margin-right: auto; width: 200px; height: 125px; z-index: 1; display: none; background-color: #efefef; layer-background-color: #FF0000; border: 1px solid #99989D;">
	        <table width="100%">
	            <tr>
	                <td bgcolor="#330099" height="25">
	                    &nbsp;<span style="color:#FFFFFF"><?php echo JText::_('upload_msg'); ?></span>
	                </td>
	            </tr>
	            <tr>
	                <td align="center" valign="middle">
	                    <p><?php echo JText::_('file_uploading'); ?></p>

	                    <p><img src="../components/com_maqmahelpdesk/images/loading.gif"></p>

	                    <p><?php echo JText::_('please_wait'); ?></p>
	                </td>
	            </tr>
	        </table>
	    </div>

	    <form action="index.php" method="POST" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="cid" value="<?php echo $id_product;?>"/>
	        <input type="hidden" name="id_product" value="<?php echo $id_product;?>"/>
	        <input type="hidden" name="id_version" value="<?php echo (!$version ? 0 : $version->id);?>'"/>
	        <input type="hidden" name="task" value="product_saveversion"/>

	        <table class="admintable" cellspacing="1" width="100%">
	            <tr>
	                <td nowrap valign="top" class="key">
							<span class="editlinktip hasTip"
	                              title="<?php echo htmlspecialchars(JText::_('date')); ?>"><?php echo JText::_('date'); ?></span>
	                </td>
	                <td>
						<?php echo JHTML::Calendar(($version->date == '' ? date("Y-m-d") : $version->date), 'vdate', 'vdate', '%Y-%m-%d', array('class' => 'inputbox', 'size' => '12', 'maxlength' => '10')); ?>
	                </td>
	            </tr>

	            <tr>
	                <td nowrap valign="top" class="key">
							<span class="editlinktip hasTip"
	                              title="<?php echo htmlspecialchars(JText::_('version')); ?>"/><?php echo JText::_('version'); ?></span>
	                </td>
	                <td>
	                    <input type="text" name="pversion" size="50" maxlength="100" class="inputbox"
	                           value="<?php echo (!$version ? '' : $version->version);?>">
	                </td>
	            </tr>
	            <tr>
	                <td nowrap valign="top" class="key">
							<span class="editlinktip hasTip"
	                              title="<?php echo htmlspecialchars(JText::_('description')); ?>"><?php echo JText::_('description'); ?></span>
	                </td>
	                <td>
						<?php
						// parameters : areaname, content, width, height, cols, rows
						echo $editor->display('vdescription', $version->description, '100%', '200', '75', '20');
						?>
	                </td>
	            </tr>
	            <tr>
	                <td nowrap valign="top" class="key">
							<span class="editlinktip hasTip"
	                              title="<?php echo htmlspecialchars(JText::_('file')); ?>"><?php echo JText::_('file'); ?></span>
	                </td>
	                <td>
						<?php echo JText::_('upload_file'); ?><input type="file" name="filename" size="50"
	                                                                 class="inputbox"/>
	                    <br/><?php echo JText::_('or'); ?><br/>
						<?php echo JText::_('ftp_uploaded'); ?><input type="text" name="filename_exists" size="50"
	                                                                  class="inputbox"/>
	                </td>
	            </tr>
	        </table>
	    </form><?php
	}
}
