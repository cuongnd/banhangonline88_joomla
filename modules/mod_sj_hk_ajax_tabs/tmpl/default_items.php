<?php
/**
 * @package SJ Ajax Tabs for HikaShop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
ImageHelper::setDefault($params);
if ((int)$params->get('nb_column', 3) > 6) {
	$params->set('nb_column', 6);
}
$nb_items = count($category_items);

$nb_column = (int)$params->get('nb_column', 3);
$nb_column = ($nb_column <= 0 || $nb_column > $nb_items) ? 1 : $nb_column;

$nb_row = (int)$params->get('nb_row', 1);
$nb_row = ($nb_row <= 0 || $nb_row > $nb_items) ? 1 : $nb_row;

$nb_pages = $nb_items * 1.0 / ($nb_column * $nb_row);
if (intval($nb_pages) < $nb_pages) {
	$nb_pages = intval($nb_pages) + 1;
} else {
	$nb_pages = intval($nb_pages);
}
$i = 0;

if ($nb_items > 0){
$item_container = 'items_container_' . $module->id . rand() . time();
if ((int)$params->get('pager_display', 1)) {
	@ob_start(); ?>
	<div class="pager-container">
		<ul class="pages">
			<li>
				<div class="page page-previous" href="#<?php echo $item_container; ?>" data-jslide="prev">&laquo; </div>
			</li>
			<?php for ($j = 0; $j < $nb_pages; $j++) { ?>
				<li>
					<div class="page number-page <?php if ($j == 0) {
						echo ' sel';
					} ?>" href="#<?php echo $item_container; ?>"
						 data-jslide="<?php echo $j; ?>"><?php echo($j + 1); ?></div>
				</li>
			<?php } ?>
			<li>
				<div class="page page-next" href="#<?php echo $item_container; ?>" data-jslide="next">&raquo;</div>
			</li>
		</ul>
	</div>
	<?php
	$pages_markup = @ob_get_contents();
	@ob_end_clean();
} else {
	$pages_markup = '';
}

// show tabs here if (bottom, left);
if (in_array($params->get('position'), array('bottom'))) {
	echo $pages_markup;
} ?>
<div class="items-container slide" id="<?php echo $item_container; ?>" data-interval="0">
	<div class="items-container-inner">
		<?php
		$cl1 = $cl2 = '';
		switch ($nb_column) {
			case 6:
				$cl1 = 4;
				$cl2 = 3;
				break;
			case 5:
				$cl1 = 4;
				$cl2 = 3;
				break;
			case 4:
				$cl1 = 4;
				$cl2 = 2;
				break;
			case 3:
				$cl1 = 3;
				$cl2 = 2;
				break;
			case 2:
				$cl1 = 2;
				$cl2 = 2;
				break;
			case 1:
				$cl1 = 1;
				$cl2 = 1;
				break;
			default:
				break;
		}

		$class_resp = null;
		$class_resp .= ' ajaxtabs01-' . $nb_column;
		$class_resp .= ' ajaxtabs02-' . $cl1;
		$class_resp .= ' ajaxtabs03-' . $cl2;
		$class_resp .= ' ajaxtabs04-1';
		$k = 0;
		foreach ($category_items as $key => $item) {

		$condition = (int)$params->get('display_votes',1) || (int)$params->get('display_add_to_cart',1) || (int)$params->get('display_add_to_wishlist',1);
		if($condition) include JModuleHelper::getLayoutPath($module->module, $layout.'_others');

		$i++;
		$classCurr = ($i == 1) ? ' active' : '';
		if ($nb_column * $nb_row > 1) {
		if ($i % ($nb_column * $nb_row) == 1){
		?>
		<div class="items-grid <?php echo $class_resp . '  ' . $classCurr ?>  item ">
			<?php
			}
			}else{
			?>
			<div class="items-grid <?php echo $class_resp . '  ' . $classCurr ?>  item ">
				<?php
				}
				if ($i % $nb_column == 0) {
					$item_last_css = ' last';
				} else {
					$item_last_css = '';
				}
				$k++;
				include JModuleHelper::getLayoutPath($module->module, $layout . '_item');
				$clear = 'clr1';
				if ($k % 2 == 0) $clear .= ' clr2';
				if ($k % 3 == 0) $clear .= ' clr3';
				if ($k % 4 == 0) $clear .= ' clr4';
				if ($k % 5 == 0) $clear .= ' clr5';
				if ($k % 6 == 0) $clear .= ' clr6';
				?>
				<div class="<?php echo $clear; ?>"></div>
				<?php
				if ($i % ($nb_column * $nb_row) == 0 || $i == $nb_items) {
					echo "</div>";
					$k = 0;
				}

				} ?>
			</div>
		</div>

		<?php
		// show tabs here if (bottom, left);
		if (in_array($params->get('position'), array('top', 'right', 'left'))) {
			echo $pages_markup;
		}?>

		<?php
		} else {
			?>
			<div class="noitem"><?php echo JText::_('Has no content to show!'); ?></div>
		<?php } ?>

