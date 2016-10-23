<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	05 November 2014
 * @file name	:	views/service/tmpl/viewservice.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	List of services provided by users (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('bootstrap.framework');
 JHtml::_('bootstrap.carousel');
 
 $user 	= JFactory::getUser();
 
 $config 		  = JblanceHelper::getConfig();
 $currencysym 	  = $config->currencySymbol;
 $showUsername	  = $config->showUsername;
 $enableAddThis   = $config->enableAddThis;
 $addThisPubid	  = $config->addThisPubid;
 
 $row = $this->row;
 $nameOrUsername = ($showUsername) ? 'username' : 'name';
 $isMine = ($row->user_id == $user->id);
 $sellerInfo 	= JFactory::getUser($row->user_id);
 
 $userType = JblanceHelper::getUserType($user->id); 
 
 $link_edit	= JRoute::_('index.php?option=com_jblance&view=service&layout=editservice&id='.$row->id);
 ?>
 
<script type="text/javascript">
<!--
//the below fix is provide because mootool-more conflicts with bootstrap carousel - https://github.com/joomla/joomla-cms/issues/475
window.addEvent('domready', function(){
	if(typeof jQuery != 'undefined' && typeof MooTools != 'undefined'){
		Element.implement({
			slide: function(how, mode){
			return this;
		}
		}); 
	}
});

window.addEvent('domready', function(){
	$$('input.service-extra-checkbox').each(function(el){
		el.addEvent('click', updateOrderAmount);
	});
});

var updateOrderAmount = function(e){
	var finalTotal = $('finaltotal').get('value').toFloat();
	var finalDuration = $('finalduration').get('value').toFloat();
	var baseDuration = $('finalduration').get('data-base-duration').toFloat();
	if(this.checked){
		//if fast is checked, set the base duration should be changed to fast duration
		finalTotal =  finalTotal + this.get('data-price').toFloat();

		if(this.get('name') == 'extras[fast]'){
			finalDuration = finalDuration + this.get('data-duration').toFloat() - baseDuration;
		}
		else {
			finalDuration = finalDuration + this.get('data-duration').toFloat();
		}
	}
	else {
		finalTotal = finalTotal - this.get('data-price').toFloat();
		//finalDuration = finalDuration - this.get('data-duration').toFloat();
		if(this.get('name') == 'extras[fast]'){
			finalDuration = finalDuration - this.get('data-duration').toFloat() + baseDuration;
		}
		else {
			finalDuration = finalDuration - this.get('data-duration').toFloat();
		}
	}

	$$('.sp-service-order').set('html', finalTotal);
	$$('.sp-service-duration').set('html', finalDuration);
	$('finaltotal').set('value', finalTotal);
	$('finalduration').set('value', finalDuration);
};
//-->
</script>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="userFormProject" id="userFormProject" class="form-validate" enctype="multipart/form-data">
	<div class="jbl_h3title"><h2><?php echo $row->service_title; ?></h2></div>
	
	<div class="page-actions">
		<?php if($enableAddThis) : ?>
		<div id="social-bookmark" class="page-action pull-left">
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style ">
				<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
				<a class="addthis_button_tweet"></a>
				<a class="addthis_button_google_plusone" g:plusone:size="medium"></a> 
				<a class="addthis_counter addthis_pill_style"></a>
			</div>
			<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo $addThisPubid; ?>"></script>
			<!-- AddThis Button END -->
		</div>
		<?php endif; ?>
		<!-- show Edit Service only to seller -->
		<?php if($isMine) : ?>
		<div class="page-action">
			<a href="<?php echo $link_edit; ?>"><i class="icon-edit"></i> <?php echo JText::_('COM_JBLANCE_EDIT_SERVICE'); ?></a>
		</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	
	<div class="row-fluid">
		<div class="span8">
			<div class="row-fluid">
				<div class="span12">
					<?php echo JBMediaHelper::renderImageCarousel($row->attachment, 'service'); ?>
				</div>
			</div>
			<div class="service-seller-pic">
				<?php 
				$attrib = 'width=64 height=64 class=""';
				$avatar = JblanceHelper::getThumbnail($row->user_id, $attrib);
				echo !empty($avatar) ? LinkHelper::GetProfileLink($row->user_id, $avatar, '', '', ' pull-left') : '&nbsp;'; ?>
			</div>
			<div class="service-seller-details">
				<?php echo LinkHelper::GetProfileLink($row->user_id, $this->escape($sellerInfo->$nameOrUsername)); ?>
				<div>
					<?php JblanceHelper::getAvarageRate($row->user_id); ?>
				</div>
			</div>
			
			<div class="clearfix"></div>
			<div style="text-align: justify;"><?php echo nl2br($row->description); ?></div>
			
			<div class="lineseparator"></div>
			
			<?php 
			$registry = new JRegistry;
			$registry->loadString($row->extras);
			$extras = $registry->toObject();
			
			$a = 0;
			foreach($extras as $key=>$value){
				if($value->enabled){
					$a++;
				}
			}
			if($a > 0){
			?>
			<h3><?php echo JText::_('COM_JBLANCE_GET_MORE_WITH_ADD_ONS'); ?>:</h3>
			<?php 
			foreach($extras as $key=>$value){
				if($value->enabled){
			?>
			<div class="well well-small">
				<div class="row-fluid">
					<div class="span10">
						<label class="checkbox">
							<input type="checkbox" name="extras[<?php echo $key; ?>]" class="service-extra-checkbox" data-price="<?php echo $value->price; ?>" data-duration="<?php echo $value->duration; ?>" /> 
							<?php 
							if($key == 'fast') :
								echo "<span class='label label-warning'>".  JText::_('COM_JBLANCE_FAST_DELIVERY')." </span> ". JText::sprintf('COM_JBLANCE_FAST_DELIVER_ORDER_JUST_DAYS', $value->duration);
							?>
							<?php else : ?>
								<?php echo $value->description.' (+'.JText::plural('COM_JBLANCE_N_DAYS', $value->duration).')'; ?>
							<?php endif; ?>
						</label>
					</div>
					<div class="span2">
						<span class="boldfont">+ <?php echo JblanceHelper::formatCurrency($value->price); ?></span>
					</div>
				</div>
			</div>
			<?php 
				}
			}
			}
			?>
			<!-- show the order button non-owner -->
			<?php //if(!$isMine) : ?>
			<div class="pull-right text-center">
				<!-- <div><?php echo JText::_('COM_JBLANCE_DELIVERY_IN'); ?> <span class="sp-service-duration"><?php echo $row->duration; ?></span> <?php echo strtolower(JText::_('COM_JBLANCE_BID_DAYS')); ?></div> -->
				<button type="submit" class="btn btn-large btn-info"><?php echo JText::_('COM_JBLANCE_ORDER_NOW'); ?> (<?php echo $currencysym; ?> <span class="sp-service-order"><?php echo $row->price; ?></span>)</button>
			</div>
			<?php //endif; ?>
		</div>
		<div class="span4 text-center">
			<div class="jb-sidenav" data-spy="affix" data-offset-top="10">
				<h6><?php echo JText::_('COM_JBLANCE_DELIVERY_IN'); ?> <span class="sp-service-duration"><?php echo $row->duration; ?></span> <?php echo strtolower(JText::_('COM_JBLANCE_BID_DAYS')); ?></h6>
				<button type="submit" class="btn btn-large btn-info btn-block"><?php echo JText::_('COM_JBLANCE_ORDER_NOW'); ?> (<?php echo $currencysym; ?> <span class="sp-service-order"><?php echo $row->price; ?></span>)</button>
			</div>
		</div>
	</div>
	<!-- show reviews -->
	<?php if(count($this->ratings)) : ?>
	<div class="row-fluid">
		<div class="span8">
		<h3><?php echo JText::_('COM_JBLANCE_REVIEWS'); ?>:</h3>
		<?php 
		for($i=0, $x=count($this->ratings); $i < $x; $i++){
			$rating = $this->ratings[$i];
			$rate = JblanceHelper::getUserRating($rating->target, $rating->order_id, 'COM_JBLANCE_SERVICE');
			$rateDate = JFactory::getDate($rating->rate_date);
		?>
			<div class="media">
			<?php
			$attrib = 'width=56 height=56 class="img-polaroid"';
			$avatar = JblanceHelper::getThumbnail($rating->actor, $attrib);
			echo !empty($avatar) ? LinkHelper::GetProfileLink($rating->actor, $avatar, '', '', ' pull-left') : '&nbsp;' ?>
				<div class="media-body">
					<span class="media-heading boldfont"><?php echo LinkHelper::GetProfileLink($rating->actor, 'test'); ?></span>
					<span class="dis-inl-blk" style="margin-left: 10px;"><?php echo JblanceHelper::getRatingHTML($rate); ?></span>
					<span class="dis-inl-blk font12"><?php echo JblanceHelper::showTimePastDHM($rateDate, 'SHORT'); ?></span>
					<div><?php echo $rating->comments; ?></div>
				</div>
			</div>
			<div class="lineseparator"></div>
			<?php 
		}
		?>
		</div>
	</div>
	<?php endif; ?>
	<input type="hidden" name="option" value="com_jblance" /> 
	<input type="hidden" name="task" value="service.placeorder" /> 
	<input type="hidden" name="service_id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="finaltotal" id="finaltotal" data-base-price="<?php echo $row->price; ?>" value="<?php echo $row->price; ?>" />
	<input type="hidden" name="finalduration" id="finalduration" data-base-duration="<?php echo $row->duration; ?>" value="<?php echo $row->duration; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>