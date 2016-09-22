<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
<div id="iframedoc"></div>
<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=fields" method="post" name="adminForm" id="adminForm" >
	<table class="adminlist table table-striped table-hover" cellpadding="1">
		<thead>
			<tr>
				<th class="title titlenum" rowspan="2">
					<?php echo JText::_( 'ACY_NUM' );?>
				</th>
				<th class="title titlebox" rowspan="2">
					<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);" />
				</th>
				<th class="title" rowspan="2">
					<?php echo JText::_('FIELD_COLUMN'); ?>
				</th>
				<th class="title" rowspan="2">
					<?php echo JText::_('FIELD_LABEL'); ?>
				</th>
				<th class="title" rowspan="2">
					<?php echo JText::_('FIELD_TYPE'); ?>
				</th>
				<th class="title titletoggle" rowspan="2">
					<?php echo JText::_('REQUIRED'); ?>
				</th>
				<th class="title titleorder" rowspan="2">
					<?php echo JText::_('ACY_ORDERING'); echo JHTML::_('grid.order',  $this->rows );?>
				</th>
				<th colspan="4"><?php echo JText::_('FRONTEND'); ?></th>
				<th colspan="3"><?php echo JText::_('BACKEND'); ?></th>
				<th class="title titletoggle" rowspan="2">
					<?php echo JText::_('ACY_PUBLISHED'); ?>
				</th>
				<th class="title titletoggle" rowspan="2">
					<?php echo JText::_('CORE'); ?>
				</th>
				<th class="title titleid" rowspan="2">
					<?php echo JText::_( 'ACY_ID' ); ?>
				</th>
			</tr>
			<tr>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_ACYPROFILE'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_ACYLISTING'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_FRONTJOOMLEREGISTRATION'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_JOOMLAPROFILE'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_ACYPROFILE'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_ACYLISTING'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JText::_('DISPLAY_JOOMLAPROFILE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$k = 0;

				for($i = 0,$a = count($this->rows);$i<$a;$i++){
					$row =& $this->rows[$i];

					$publishedid = 'published_'.$row->fieldid;
					$requiredid = 'required_'.$row->fieldid;
					$backendid = 'backend_'.$row->fieldid;
					$frontcompid = 'frontcomp_'.$row->fieldid;
					$listingid = 'listing_'.$row->fieldid;
					$frontlistingid = 'frontlisting_'.$row->fieldid;
					$frontjoomlaregistrationid = 'frontjoomlaregistration_'.$row->fieldid;
					$frontjoomlaprofileid = 'frontjoomlaprofile_'.$row->fieldid;
					$joomlaprofileid = 'joomlaprofile_'.$row->fieldid;
			?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
					<?php echo $i+1; ?>
					</td>
					<td align="center">
						<?php echo JHTML::_('grid.id', $i, $row->fieldid ); ?>
					</td>
					<td>
						<a href="<?php echo acymailing_completeLink('fields&task=edit&fieldid='.$row->fieldid); ?>">
							<?php echo $row->namekey; ?>
						</a>
					</td>
					<td>
						<?php echo $this->fieldsClass->trans($row->fieldname); ?>
					</td>
					<td>
						<?php echo $this->fieldtype->allValues[$row->type]; ?>
					</td>
					<td align="center">
						<span id="<?php echo $requiredid ?>" class="loading"><?php echo $this->toggleClass->toggle($requiredid,(int) $row->required,'fields') ?></span>
					</td>
					<td class="order">
						<span><?php echo $this->pagination->orderUpIcon( $i, $row->ordering >= @$this->rows[$i-1]->ordering ,'orderup', 'Move Up',true ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon( $i, $a, $row->ordering <= @$this->rows[$i+1]->ordering , 'orderdown', 'Move Down' ,true); ?></span>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
					</td>
					<td align="center">
						<span id="<?php echo $frontcompid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontcompid,(int) $row->frontcomp,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $frontlistingid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontlistingid,(int) $row->frontlisting,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $frontjoomlaregistrationid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontjoomlaregistrationid,(int) $row->frontjoomlaregistration,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $frontjoomlaprofileid ?>" class="loading"><?php echo $this->toggleClass->toggle($frontjoomlaprofileid,(int) $row->frontjoomlaprofile,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $backendid ?>" class="loading"><?php echo $this->toggleClass->toggle($backendid,(int) $row->backend,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $listingid ?>" class="loading"><?php echo $this->toggleClass->toggle($listingid,(int) $row->listing,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $joomlaprofileid ?>" class="loading"><?php echo $this->toggleClass->toggle($joomlaprofileid,(int) $row->joomlaprofile,'fields') ?></span>
					</td>
					<td align="center">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid,(int) $row->published,'fields') ?></span>
					</td>
					<td align="center">
						<?php echo $this->toggleClass->display('activate',$row->core); ?>
					</td>
					<td width="1%" align="center">
						<?php echo $row->fieldid; ?>
					</td>
				</tr>
			<?php
					$k = 1-$k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="fields" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
