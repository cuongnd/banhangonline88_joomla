<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configurationLink	= 'index.php?option=com_cmgroupbuying&view=configuration';
$userProfileLink	= 'index.php?option=com_cmgroupbuying&view=profile';
$categoryLink		= 'index.php?option=com_cmgroupbuying&view=categories';
$managementLink		= 'index.php?option=com_cmgroupbuying&view=management';
$locationLink		= 'index.php?option=com_cmgroupbuying&view=locations';
$partnerLink		= 'index.php?option=com_cmgroupbuying&view=partners';
$productLink		= 'index.php?option=com_cmgroupbuying&view=products';
$dealLink			= 'index.php?option=com_cmgroupbuying&view=deals';
$orderLink			= 'index.php?option=com_cmgroupbuying&view=orders';
$mailTemplateLink	= 'index.php?option=com_cmgroupbuying&view=mailtemplates';
$aggregationLinkLink= 'index.php?option=com_cmgroupbuying&view=aggregationlinks';
$aggregatorSiteLink	= 'index.php?option=com_cmgroupbuying&view=aggregatorsites';
$reportLink			= 'index.php?option=com_cmgroupbuying&view=reports';
$couponLink			= 'index.php?option=com_cmgroupbuying&view=coupons';
$freeCouponLink		= 'index.php?option=com_cmgroupbuying&view=freecoupons';
?>
<div class="cmgroupbuying">
	<div class="row-fluid">
		<div class="span12">
			<div id="cpanel">
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_CONFIGURATION'); ?>" href="<?php echo $configurationLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-configuration.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_CONFIGURATION'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_CONFIGURATION'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_USER_PROFILE'); ?>" href="<?php echo $userProfileLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-profile.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_USER_PROFILE'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_USER_PROFILE'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_MANAGEMENT_PERMISSIONS'); ?>" href="<?php echo $managementLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-management.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_MANAGEMENT_PERMISSIONS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_MANAGEMENT_PERMISSIONS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_CATEGORIES'); ?>" href="<?php echo $categoryLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-category.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_CATEGORIES'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_CATEGORIES'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_LOCATIONS'); ?>" href="<?php echo $locationLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-location.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_LOCATIONS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_LOCATIONS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_PARTNERS'); ?>" href="<?php echo $partnerLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-partner.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_PARTNERS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_PARTNERS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_PRODUCTS'); ?>" href="<?php echo $productLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-product.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_PRODUCTS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_PRODUCTS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_DEALS'); ?>" href="<?php echo $dealLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-deal.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_DEALS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_DEALS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_ORDERS'); ?>" href="<?php echo $orderLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-order.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_ORDERS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_ORDERS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_COUPONS'); ?>" href="<?php echo $couponLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-coupon.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_COUPONS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_COUPONS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_FREE_COUPONS'); ?>" href="<?php echo $freeCouponLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-free-coupon.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_FREE_COUPONS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_FREE_COUPONS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_MAIL_TEMPLATES'); ?>" href="<?php echo $mailTemplateLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-mail_template.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_MAIL_TEMPLATES'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_MAIL_TEMPLATES'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATOR_SITES'); ?>" href="<?php echo $aggregatorSiteLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-aggregator_site.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATOR_SITES'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATOR_SITES'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATION_LINKS'); ?>" href="<?php echo $aggregationLinkLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-aggregation_link.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATION_LINKS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATION_LINKS'); ?></a>
					</div>
				</div>
				<div class="icon-wrapper">
					<div class="icon">
						<a title="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_REPORTS'); ?>" href="<?php echo $reportLink; ?>">
						<img src="components/com_cmgroupbuying/assets/images/icons/icon-48-report.png" width="48px" alt="<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_REPORTS'); ?>" align="middle" border="0" /><br />
						<?php echo JText::_('COM_CMGROUPBUYING_DASHBOARD_REPORTS'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<div id="cpanel">
				<div style="border: 1px solid rgb(204, 204, 204); background: none repeat scroll 0% 0% rgb(255, 255, 255); padding: 15px;">
					<h3>Welcome to CMGroupBuying!</h3>
					<p></p>
					<p>Thank you for choosing CMGroupBuying as your group buying website solution. We hope our product could help you build a successful group buying website and you would give some feedback about this component to make it better.</p>
					<p>Any question, feedback or bug report, you can send to us via our website - <a href="http://www.cmext.vn/" target="_blank">www.cmext.vn</a>.</p>
					<p><strong>Product information:</strong></p>
					<ul>
						<li>Version: 2.8.0</li>
						<li>Released: February 2014</li>
						<li>License: <a href="http://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU/GPL</a></li>
						<li>Author: <a href="http://www.cmext.vn/" target="_blank">CMExtension team</a></li>
					</ul>
					<p><strong>We want to say thanks to:</strong></p>
					<ul>
						<li><a href="http://www.joomla.org/" target="_blank">Joomla! developers</a> for a great CMS</li>
						<li><a href="http://www.keith-wood.name/" target="_blank">Keith Wood</a> for countdown script</li>
						<li><a href="http://slideshow.hohli.com/" target="_blank">Anton Shevchuk</a> for slideshow script</li>
						<li><a href="http://dinbror.dk/bpopup" target="_blank">Bjoern Klinggaard</a> for popup script</li>
						<li>Silvestre Herrera for <a href="http://art.gnome.org/themes/icon/1168" target="_blank">Yasis icon theme</a></li>
						<li><a href="http://www.akeebabackup.com" target="_blank">Nicholas K. Dionysopoulos</a> for sample code of payment plugin</li>
						<li><a href="http://phpqrcode.sourceforge.net/" target="_blank">Dominik Dzienia</a> for PHP QR Code encoder library</li>
						<li><a href="http://www.tcpdf.org" target="_blank">Nicola Asuni</a> for TCPDF library</li>
						<li>Developers of the Joomla components and applications that we have used or integrated</li>
						<li>Finally, special thanks to you and everyone who have supported CMExtension and CMGroupBuying!</li>
					</ul>
					<p><strong>Latest news from CMExtension team:</strong></p>
					<?php
					if(function_exists('curl_version'))
					{
						$ch = curl_init('http://www.cmext.vn/news/cmgroupbuying.xml');
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						$data = curl_exec($ch);
						curl_close($ch);

						if(simplexml_load_string($data))
						{
							$doc = new SimpleXmlElement($data, LIBXML_NOCDATA);
							echo "<ul>";

							foreach($doc->item as $item)
							{
								echo '<li>' . $item->date . ': <a href="' . $item->url . '" target="_blank">' . $item->title . '</a></li>';
							}

							echo "</ul>";
						}
						else
						{
							echo "Can't get information from developer website.";
						}
					}
					?>
					<p><strong>If you use CMGroupBuying, please post a rating and a review at the <a href="http://extensions.joomla.org/extensions/e-commerce/shopping-cart/20008" target="_blank">Joomla! Extensions Directory</a></strong>.</p>
				</div>
			</div>
		</div>
	</div>
</div>
