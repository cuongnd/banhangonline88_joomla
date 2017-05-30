<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="ezblog-body">
    <div id="ezblog-section"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTIONS_PAGE_HEADING'); ?></div>

    <ul class="reset-ul my-subscription">
<?php
if(!empty($my->id))
{
?>

<?php
	if(!empty($subscription)) 
	{
		if(!empty($subscription['sitesubscription']))
		{
		?>
            <li class="subscribe-site">
                <div class="subscription-title"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SITE'); ?></div>
                <ol>
    			<?php
    			foreach($subscription['sitesubscription'] as $sub)
    			{
    			?>
    				<li>
    					<span><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SITE_ACTIVE'); ?></span>
    					<span class="small fsm">
                            -
                            <a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE'); ?></a>
                        </span>
    				</li>
    			<?php
    			}
                ?>
                </ol>
            </li>
            <?php
	
		}
		
		if(!empty($subscription['subscription']))
		{
		?>
            <li class="subscribe-post">
    			<div class="subscription-title"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_POST'); ?></div>
                <ol>
    			<?php
    			foreach($subscription['subscription'] as $sub)
    			{
    			?>
    				<li>
    					<span><?php echo $sub->name; ?></span>
                        <span class="small fsm">
                            -
        					<a href="<?php echo $sub->link; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_LINK'); ?></a>
                            <b>&middot;</b>
        					<a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE'); ?></a>
                        </span>
    				</li>
                <?php
    			}
                ?>
                </ol>
            </li>
            <?php
		}
		
		if(!empty($subscription['categorysubscription'])) 
		{
		?>
            <li class="subscribe-category">
                <div class="subscription-title"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_CATEGORY'); ?></div>
                <ol>
    			<?php
    			foreach($subscription['categorysubscription'] as $sub)
    			{
    			?>
                    <li>
    					<span><?php echo $sub->name; ?></span>
                        <span class="small fsm">
                            -
        					<a href="<?php echo $sub->link; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_LINK'); ?></a>
                            <b>&middot;</b>
        					<a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE'); ?></a>
                        </span>
    				</li>
                <?php
    			}
                ?>
                </ol>
            </li>
            <?php
		}
		
		if(!empty($subscription['bloggersubscription'])) 
		{
		?>
            <li class="subscribe-blogger">
    			<div class="subscription-title"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_BLOGGER'); ?></div>
                <ol>
    			<?php
    			foreach($subscription['bloggersubscription'] as $sub)
    			{
    			?>
    				<li>
    					<span><?php echo $sub->name; ?></span>
                        <span class="small fsm">
                            -
        					<a href="<?php echo $sub->link; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_LINK'); ?></a>
                            <b>&middot;</b>
        					<a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE'); ?></a>
                        </span>
    				</li>
                <?php
    			}
                ?>
                </ol>
            </li>
            <?php
		}
		
		if(!empty($subscription['teamsubscription']))
		{
		?>
            <li class="subscribe-teamblog">
    			<div class="subscription-title"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_TEAM'); ?></div>
                <ol>
    			<?php
    			foreach($subscription['teamsubscription'] as $sub)
    			{
    			?>
        			<li>
        				<span><?php echo $sub->name; ?></span>
                        <span class="small fsm">
                            -
            				<a href="<?php echo $sub->link; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_LINK'); ?></a>
                            <b>&middot;</b>
            				<a href="<?php echo $sub->unsublink; ?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE'); ?></a>
                        </span>
        			</li>
                <?php
                }
                ?>
                </ol>
            </li>
            <?php
		}
	}
	else
	{
	?>
		<li>
			<div class="eblog-message info"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_NO_ACTIVE')?></div>
		</li>
	<?php
	}
}
else
{
?>
    <li>
        <div class="eblog-message info"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_LOGIN_TO_VIEW_SUBCRIPTION')?></div>
    </li>
<?php
}
?>
    </ul>
</div>