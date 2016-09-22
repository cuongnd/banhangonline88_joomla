<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="frmForm" method="post" action="">
<div class="row">
	<div class="col-md-12">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-bs-toggle="collapse" data-target="#privacyForm">
					<h6><?php echo JText::_( 'COM_EASYSOCIAL_USERS_PRIVACY_PANEL_TITLE' );?></h6>
					<span class="bg-accordion"> <i class="icon-si"></i> </span>
				</a>
			</div>

			<div class="accordion-body in">
				<div class="wbody">

				<?php if(empty( $this->privacy) ) { ?>

					<div class="row">
						<div class="col-md-12">
							<?php echo JText::_('COM_EASYSOCIAL_PRIVACY_NOT_FOUND'); ?>
						</div>
					</div>

				<?php } else { ?>
					<?php

						$index = 0;
						foreach( $this->privacy as $key => $groups) { ?>

					<div class="row">
						<div class="col-md-12">
							<h4><?php echo JText::_('COM_EASYSOCIAL_PROFILES_' . strtoupper($key) ); ?></h4>
						</div>
					</div>

						<?php


						foreach($groups as $item) {
							$gKey  =  strtoupper($key);
							$rule  =  strtoupper($item->rule);
							$ruleLangKeys = 'COM_EASYSOCIAL_PROFILES_' . strtoupper($gKey) . '_' . strtoupper($rule);
							$hasCustom = false;
							$isCustom  = false;
						?>

						<div class="row privacyItem">
							<div class="col-md-5">
								<?php echo JText::_( $ruleLangKeys ); ?>
							</div>
							<div class="col-md-7">

								<select class="form-control input-sm privacySelection" name="privacy[<?php echo $gKey;?>][<?php echo $rule;?>]"
								<?php echo $this->html( 'bootstrap.popover' , JText::_('COM_EASYSOCIAL_PROFILES_' . $gKey)  , JText::_( $ruleLangKeys ) , 'right' );?>>
									<?php foreach( $item->options as $option => $value) {
										$hasCustom = ( $option == 'custom' ) ? true : $hasCustom;
										$isCustom = ( $value ) ? true : false;
									?>
										<option value="<?php echo $option?>" <?php echo ($value) ? ' selected="selected"' : ''?> ><?php echo JText::_( 'COM_EASYSOCIAL_PRIVACY_OPTION_' . strtoupper($option)); ?></option>
									<?php } ?>
								</select>

								<?php if( $hasCustom ) { ?>

								<div style="width:100%; <?php echo ($isCustom) ? '' : ' display:none;' ?>" class="customContainer">
									<?php
										$this->set( 'custom_data'	, $item->custom_data );
										$this->set( 'index', $index++ );
										$this->set( 'privacy_element_name'	, 'privacy['. $gKey .'][' . $rule . '_custom]' );
									?>

									<?php echo $this->loadTemplate( 'site:/privacy/default.custom' ); ?>
								</div>

								<?php } ?>

							</div>
						</div>

						<?php } ?>

					<?php } ?>

					<div class="row">
						<input type="submit" value="Submit" name="submit" />
					</div>

				<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="privacy" />
<input type="hidden" name="task" value="store" />
<?php echo JHTML::_( 'form.token' );?>
</form>
