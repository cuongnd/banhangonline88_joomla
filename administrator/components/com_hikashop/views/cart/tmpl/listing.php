<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikashop_completeLink('cart'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(HIKASHOP_BACK_RESPONSIVE) { ?>
	<div class="row-fluid">
		<div class="span8">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" />
				<button class="btn" onclick="this.form.limitstart.value=0;this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="this.form.limitstart.value=0;document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span4">
<?php } else { ?>
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_('FILTER'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->pageInfo->search);?>" class="text_area" />
				<button class="btn" onclick="this.form.limitstart.value=0;this.form.submit();"><?php echo JText::_('GO'); ?></button>
				<button class="btn" onclick="this.form.limitstart.value=0;document.getElementById('search').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td nowrap="nowrap">
<?php }

if(HIKASHOP_BACK_RESPONSIVE) { ?>
		</div>
	</div>
<?php } else { ?>
			</td>
		</tr>
	</table>
<?php } ?>
	<table id="hikashop_cart_listing" class="adminlist table table-striped table-hover" cellpadding="1">
		<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_( 'HIKA_NUM' );?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="hikashop.checkAll(this);" />
				</th>
				<th class="title title_product_id">
					<?php echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'a.cart_id', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title title_cart_user_id">
					<?php echo JHTML::_('grid.sort', JText::_('HIKA_USERNAME'), 'a.user_id', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title title_cart_current">
					<?php echo JHTML::_('grid.sort', JText::_('SHOW_DEFAULT'), 'a.cart_current', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title title_cart_quantity">
					<?php  echo JText::_('PRODUCT_QUANTITY'); ?>
				</th>
				<th class="title title_cart_total">
					<?php echo JText::_('CART_PRODUCT_TOTAL_PRICE'); ?>
				</th>
				<th class="title title_cart_date">
					<?php echo JHTML::_('grid.sort', JText::_('DATE'), 'a.cart_modified', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
				<th class="title title_cart_action">
					<?php echo JText::_('HIKA_ACTION'); ?>
				</th>
				<th class="title title_cart_id">
					<?php echo JHTML::_('grid.sort', JText::_('ID'), 'a.cart_id', $this->pageInfo->filter->order->dir,$this->pageInfo->filter->order->value ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$config =& hikashop_config();
	$cart_type = JRequest::getString('cart_type', 'cart');

	$i = 0;
	$k = 1;
	foreach($this->carts as $cart) {
		if(empty($cart->cart_id))
			continue;
?>
			<tr class="row<?php echo $k; ?>">
				<td align="center"><?php
					echo $this->pagination->getRowOffset($i);
				?></td>
				<td align="center"><?php
					echo JHTML::_('grid.id', $i, $cart->cart_id );
				?></td>
				<td align="center"><?php
					if(hikashop_isAllowed($config->get('acl_wishlist_manage','all'))){
						echo "<a href=".hikashop_completeLink('cart&task=edit&cart_type='.$cart_type.'&cart_id='.$cart->cart_id.'&cid[]='.$cart->cart_id,false,true).">".$cart->cart_name."</a>";
					} else {
						echo $cart->cart_name;
					}
				?></td>
				<td align="center"><?php
		$user = null;
		if($cart->user_id != 0) {
			$userClass = hikashop_get('class.user');

			$user = $userClass->get($cart->user_id,'cms');
			if(is_null($user)){
				$user = $userClass->get($user_id);
			}
			if(is_null($user)) {
				echo JText::_('NO_REGISTRATION');
			} else {
				if(!empty($user->username)) {
					echo $user->name.' ( '.$user->username.' )</a><br/>';
				}
				$target = '';
				if($this->popup)
					$target = '" target="_top';
				$url = hikashop_completeLink('user&task=edit&cid[]='.$user->user_id);

				if(hikashop_isAllowed($config->get('acl_user_manage','all')))
					echo $user->user_email.'<a href="'.$url.$target.'"><img src="'.HIKASHOP_IMAGES.'edit.png" alt="edit"/></a>';
			}
		} else {
			echo JText::_('NO_REGISTRATION');
		}
				?></td>
				<td align="center">
<?php if($cart->cart_current == 1) { ?>
					<a href="<?php echo hikashop_completeLink('cart&task=edit&cart_type='.$cart_type.'&cid[]='.$cart->cart_id.'&user_id='.$cart->user_id);?>">
						<img src="../media/com_hikashop/images/icon-16-default.png" alt="current"/>
					</a>
<?php } ?>
				</td>
				<td align="center"><?php
					echo (int)@$cart->quantity;
				?></td>
				<td align="center">
					<span class='hikashop_product_price_full hikashop_product_price'><?php
						echo $this->currencyHelper->format($cart->price, $cart->currency);
					?></span>
				</td>
				<td align="center"><?php
					echo hikashop_getDate($cart->cart_modified);
				?></td>
				<td align="center">
<?php if($this->manage) { ?>
					<a href="<?php echo hikashop_completeLink('cart&task=edit&cart_type='.$cart_type.'&cid[]='.$cart->cart_id);?>">
						<img src="<?php echo HIKASHOP_IMAGES; ?>edit.png"/>
					</a>
<?php } ?>
				</td>
				<td width="1%" align="center"><?php
					echo $cart->cart_id;
				?></td>
			</tr>
<?php
		$i++;
		$k = 1 - $k;
	}
?>
		</tbody>
	</table>
	<input type="hidden" name="cart_type" value="<?php echo $this->escape(JRequest::getString('cart_type', 'cart')); ?>" />
	<input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
