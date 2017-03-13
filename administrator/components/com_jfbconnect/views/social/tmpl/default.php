<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');
jimport('sourcecoast.utilities');
JHTML::_('behavior.tooltip');
$model = $this->model;
$k2IsInstalled = SCSocialUtilities::isJoomlaComponentEnabled('com_k2');

$provider = $this->filter_provider;
$task = JRequest::getVar('task', '', 'post');
if(!empty($task)) { $provider = '';}
?>
<script type="text/javascript">
    function toggleHide(rowId, styleType)
    {
        document.getElementById(rowId).style.display = styleType;
    }
</script>

<div class="sourcecoast">
<form method="post" id="adminForm" name="adminForm">
<?php
if (defined('SC30')):
?>
<div class="row-fluid">
    <ul class="nav nav-tabs">
        <li <?php echo $provider ? '' : 'class="active"';?>><a href="#social_content_comment" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_COMMENTS'); ?></a></li>
        <li><a href="#social_content_like" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_LIKE'); ?></a></li>

        <?php
        if ($k2IsInstalled)
        {
            echo '<li><a href="#social_content_k2_comment" data-toggle="tab">' . JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_COMMENTS') . '</a></li>';
            echo '<li><a href="#social_content_k2_like" data-toggle="tab">' . JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_LIKE') . '</a></li>';
        }
        ?>
        <li><a href="#social_notifications" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_NOTIFICATIONS'); ?></a></li>
        <li><a href="#social_misc" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_MISC'); ?></a></li>
        <li <?php echo $provider ? 'class="active"' : '';?>><a href="#social_examples" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_EXAMPLES'); ?></a></li>
    </ul>
</div>
<div class="tab-content">
<?php
endif; //SC30
if (defined('SC16')):
    jimport('joomla.html.pane');
    $pane = JPane::getInstance('tabs');
    echo $pane->startPane('content-pane');
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_COMMENTS'), 'social_content_comment_pane');
endif; //SC16
?>
<div class="tab-pane <?php echo $provider ? '' : 'active';?>" id="social_content_comment">
<div class="config_row">
    <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_SETTING_LABEL"); ?></div>
    <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <?php $socialCommentArticleView = $model->getSetting('social_comment_article_view'); ?>
        <select name="social_comment_article_view">
            <option value="1" <?php echo ($socialCommentArticleView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
            <option value="2" <?php echo ($socialCommentArticleView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
            <option value="3" <?php echo ($socialCommentArticleView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
            <option value="0" <?php echo ($socialCommentArticleView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
        </select>
    </div>
    <div style="clear:both"></div>
</div>
<div id="social_comment_article_settings">
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_article_comment_max_num" value="<?php echo $model->getSetting('social_article_comment_max_num') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_article_comment_width" value="<?php echo $model->getSetting('social_article_comment_width') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_comment_color_scheme" class="radio btn-group">
                <input type="radio" id="social_article_comment_color_schemeL" name="social_article_comment_color_scheme"
                       value="light" <?php echo $model->getSetting('social_article_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                <input type="radio" id="social_article_comment_color_schemeD" name="social_article_comment_color_scheme"
                       value="dark" <?php echo $model->getSetting('social_article_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_comment_order_by" class="radio btn-group">
                <input type="radio" id="social_article_comment_order_byS" name="social_article_comment_order_by"
                       value="social" <?php echo $model->getSetting('social_article_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                <input type="radio" id="social_article_comment_order_byR" name="social_article_comment_order_by"
                       value="reverse_time" <?php echo $model->getSetting('social_article_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                <input type="radio" id="social_article_comment_order_byT" name="social_article_comment_order_by"
                       value="time" <?php echo $model->getSetting('social_article_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<div class="config_row">
    <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BLOG_SETTING_LABEL"); ?></div>
    <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <?php $socialCommentFrontPageView = $model->getSetting('social_comment_frontpage_view'); ?>
        <select name="social_comment_frontpage_view">
            <option value="1" <?php echo ($socialCommentFrontPageView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
            <option value="2" <?php echo ($socialCommentFrontPageView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
            <option value="3" <?php echo ($socialCommentFrontPageView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
            <option value="0" <?php echo ($socialCommentFrontPageView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
        </select>
    </div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_LABEL"); ?>:
    </div>
    <div class="config_option">
        <?php $socialCommentCategoryView = $model->getSetting('social_comment_category_view'); ?>
        <select name="social_comment_category_view">
            <option value="1" <?php echo ($socialCommentCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
            <option value="2" <?php echo ($socialCommentCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
            <option value="3" <?php echo ($socialCommentCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
            <option value="0" <?php echo ($socialCommentCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
        </select>
    </div>
    <div style="clear:both"></div>
</div>
<div id="social_comment_blog_settings">

    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_blog_comment_max_num" value="<?php echo $model->getSetting('social_blog_comment_max_num') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_blog_comment_width" value="<?php echo $model->getSetting('social_blog_comment_width') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_comment_color_scheme" class="radio btn-group">
                <input type="radio" id="social_blog_comment_color_schemeL" name="social_blog_comment_color_scheme"
                       value="light" <?php echo $model->getSetting('social_blog_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                <input type="radio" id="social_blog_comment_color_schemeD" name="social_blog_comment_color_scheme"
                       value="dark" <?php echo $model->getSetting('social_blog_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_comment_order_by" class="radio btn-group">
                <input type="radio" id="social_blog_comment_order_byS" name="social_blog_comment_order_by"
                       value="social" <?php echo $model->getSetting('social_blog_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                <input type="radio" id="social_blog_comment_order_byR" name="social_blog_comment_order_by"
                       value="reverse_time" <?php echo $model->getSetting('social_blog_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                <input type="radio" id="social_blog_comment_order_byT" name="social_blog_comment_order_by"
                       value="time" <?php echo $model->getSetting('social_blog_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<div class="config_row">
    <div class="config_setting_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING"); ?></div>
    <div style="clear:both"></div>
</div>

<div class="config_row">
    <div class="config_setting_option hasTip"
         title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING_DESC"); ?>">
        <?php $catType = $model->getSetting('social_comment_cat_include_type'); ?>
        <fieldset id="social_comment_cat_include_type" class="radio btn-group">
            <input type="radio" id="social_comment_cat_include_type0" name="social_comment_cat_include_type"
                   value="0" <?php echo($catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', 'none')" />
            <label for="social_comment_cat_include_type0"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ALL_LABEL"); ?></label>
            <input type="radio" id="social_comment_cat_include_type1" name="social_comment_cat_include_type"
                   value="1" <?php echo($catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', '')" />
            <label for="social_comment_cat_include_type1"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?></label>
            <input type="radio" id="social_comment_cat_include_type2" name="social_comment_cat_include_type"
                   value="2" <?php echo($catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', '')" />
            <label for="social_comment_cat_include_type2"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?></label>
        </fieldset>
    </div>
    <div style="clear:both"></div>
</div>
<div class="config_row" id="comment_cat_ids" style="display:<?php echo($catType == "0" ? 'none' : ''); ?>">
    <?php
    $catids = $model->getSetting('social_comment_cat_ids');
    $categories = unserialize($catids);

    $db = JFactory::getDBO();
    $query = "SELECT id, title FROM #__categories WHERE extension='com_content'";
    $db->setQuery($query);
    $cats = $db->loadAssocList();
    $attribs = 'multiple="multiple"';
    echo '<td>' . JHTML::_('select.genericlist', $cats, 'social_comment_cat_ids[]', $attribs, 'id', 'title', $categories, 'social_comment_cat_ids') . '</td>';
    ?>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_SETTING"); ?></div>
    <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_INCLUDE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <input type="text" name="social_comment_article_include_ids" value="<?php echo $model->getSetting('social_comment_article_include_ids'); ?>"
               size="20">
    </div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_EXCLUDE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <input type="text" name="social_comment_article_exclude_ids" value="<?php echo $model->getSetting('social_comment_article_exclude_ids'); ?>"
               size="20">
    </div>
    <div style="clear:both"></div>
</div>
</div>
<?php
if (defined('SC16')):
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_LIKE'), 'social_content_like_pane');
endif;
?>
<div class="tab-pane" id="social_content_like">
<div class="config_row">
    <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_VIEW_SETTING"); ?></div>
    <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_DESC2'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <?php $socialLikeArticleView = $model->getSetting('social_like_article_view'); ?>
        <select name="social_like_article_view">
            <option value="1" <?php echo ($socialLikeArticleView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
            <option value="2" <?php echo ($socialLikeArticleView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
            <option value="3" <?php echo ($socialLikeArticleView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
            <option value="0" <?php echo ($socialLikeArticleView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
        </select>
    </div>
    <div style="clear:both"></div>
</div>
<div id="social_like_article_settings">
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <select name="social_article_like_layout_style">
                <option value="standard" <?php echo $model->getSetting('social_article_like_layout_style') == 'standard' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_STANDARD_LABEL"); ?></option>
                <option value="box_count" <?php echo $model->getSetting('social_article_like_layout_style') == 'box_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOX_COUNT_LABEL"); ?></option>
                <option value="button_count" <?php echo $model->getSetting('social_article_like_layout_style') == 'button_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_COUNT_LABEL"); ?></option>
                <option value="button" <?php echo $model->getSetting('social_article_like_layout_style') == 'button' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_facebook" class="radio btn-group">
                <input type="radio" id="social_article_like_show_facebook1" name="social_article_like_show_facebook"
                       value="1" <?php echo $model->getSetting('social_article_like_show_facebook') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_facebook1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_facebook0" name="social_article_like_show_facebook"
                       value="0" <?php echo $model->getSetting('social_article_like_show_facebook') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_facebook0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_faces" class="radio btn-group">
                <input type="radio" id="social_article_like_show_faces1" name="social_article_like_show_faces"
                       value="1" <?php echo $model->getSetting('social_article_like_show_faces') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_faces1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_faces0" name="social_article_like_show_faces"
                       value="0" <?php echo $model->getSetting('social_article_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_faces0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SHARE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SEND_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_send_button" class="radio btn-group">
                <input type="radio" id="social_article_like_show_send_button1" name="social_article_like_show_send_button"
                       value="1" <?php echo $model->getSetting('social_article_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_send_button1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_send_button0" name="social_article_like_show_send_button"
                       value="0" <?php echo $model->getSetting('social_article_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_send_button0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_linkedin" class="radio btn-group">
                <input type="radio" id="social_article_like_show_linkedin1" name="social_article_like_show_linkedin"
                       value="1" <?php echo $model->getSetting('social_article_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_linkedin1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_linkedin0" name="social_article_like_show_linkedin"
                       value="0" <?php echo $model->getSetting('social_article_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_linkedin0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_twitter" class="radio btn-group">
                <input type="radio" id="social_article_like_show_twitter1" name="social_article_like_show_twitter"
                       value="1" <?php echo $model->getSetting('social_article_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_twitter1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_twitter0" name="social_article_like_show_twitter"
                       value="0" <?php echo $model->getSetting('social_article_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_twitter0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_googleplus" class="radio btn-group">
                <input type="radio" id="social_article_like_show_googleplus1" name="social_article_like_show_googleplus"
                       value="1" <?php echo $model->getSetting('social_article_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_googleplus1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_googleplus0" name="social_article_like_show_googleplus"
                       value="0" <?php echo $model->getSetting('social_article_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_googleplus0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_show_pinterest" class="radio btn-group">
                <input type="radio" id="social_article_like_show_pinterest1" name="social_article_like_show_pinterest"
                       value="1" <?php echo $model->getSetting('social_article_like_show_pinterest') ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_show_pinterest1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_article_like_show_pinterest0" name="social_article_like_show_pinterest"
                       value="0" <?php echo $model->getSetting('social_article_like_show_pinterest') ? '""' : 'checked="checked"'; ?> />
                <label for="social_article_like_show_pinterest0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_verb_to_display" class="radio btn-group">
                <input type="radio" id="social_article_like_verb_to_displayLike" name="social_article_like_verb_to_display"
                       value="like" <?php echo $model->getSetting('social_article_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_verb_to_displayLike"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LIKE_LABEL"); ?></label>
                <input type="radio" id="social_article_like_verb_to_displayRec" name="social_article_like_verb_to_display"
                       value="recommend" <?php echo $model->getSetting('social_article_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_verb_to_displayRec"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_RECOMMEND_LABEL"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC3"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_article_like_color_scheme" class="radio btn-group">
                <input type="radio" id="social_article_like_color_schemeL" name="social_article_like_color_scheme"
                       value="light" <?php echo $model->getSetting('social_article_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                <input type="radio" id="social_article_like_color_schemeD" name="social_article_like_color_scheme"
                       value="dark" <?php echo $model->getSetting('social_article_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                <label for="social_article_like_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_LABEL"); ?>:
        </div>
        <div class="config_option">
            <select name="social_article_like_font">
                <option value="" <?php echo $model->getSetting('social_article_like_font') == 'arial' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT1"); ?></option>
                <option value="lucida grande" <?php echo $model->getSetting('social_article_like_font') == 'lucida grande' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT2"); ?></option>
                <option value="segoe ui" <?php echo $model->getSetting('social_article_like_font') == 'segoe ui' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT3"); ?></option>
                <option value="tahoma" <?php echo $model->getSetting('social_article_like_font') == 'tahoma' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT4"); ?></option>
                <option value="trebuchet ms" <?php echo $model->getSetting('social_article_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT5"); ?></option>
                <option value="verdana" <?php echo $model->getSetting('social_article_like_font') == 'verdana' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT6"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC3"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_article_like_width" value="<?php echo $model->getSetting('social_article_like_width') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<div class="config_row">
    <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BLOG_SETTING_LABEL"); ?></div>
    <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_DESC2'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <?php $socialLikeFrontPageView = $model->getSetting('social_like_frontpage_view'); ?>
        <select name="social_like_frontpage_view">
            <option value="1" <?php echo ($socialLikeFrontPageView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
            <option value="2" <?php echo ($socialLikeFrontPageView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
            <option value="3" <?php echo ($socialLikeFrontPageView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
            <option value="0" <?php echo ($socialLikeFrontPageView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
        </select>
    </div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_DESC2'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_LABEL"); ?>:
    </div>
    <div class="config_option">
        <?php $socialLikeCategoryView = $model->getSetting('social_like_category_view'); ?>
        <select name="social_like_category_view">
            <option value="1" <?php echo ($socialLikeCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
            <option value="2" <?php echo ($socialLikeCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
            <option value="3" <?php echo ($socialLikeCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
            <option value="0" <?php echo ($socialLikeCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
        </select>
    </div>
    <div style="clear:both"></div>
</div>
<div id="social_like_blog_settings">
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <select name="social_blog_like_layout_style">
                <option value="standard" <?php echo $model->getSetting('social_blog_like_layout_style') == 'standard' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_STANDARD_LABEL"); ?></option>
                <option value="box_count" <?php echo $model->getSetting('social_blog_like_layout_style') == 'box_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOX_COUNT_LABEL"); ?></option>
                <option value="button_count" <?php echo $model->getSetting('social_blog_like_layout_style') == 'button_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_COUNT_LABEL"); ?></option>
                <option value="button" <?php echo $model->getSetting('social_blog_like_layout_style') == 'button' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_facebook" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_facebook1" name="social_blog_like_show_facebook"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_facebook') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_facebook1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_facebook0" name="social_blog_like_show_facebook"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_facebook') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_facebook0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_faces" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_faces1" name="social_blog_like_show_faces"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_faces') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_faces1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_faces0" name="social_blog_like_show_faces"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_faces0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SHARE_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SEND_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_send_button" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_send_button1" name="social_blog_like_show_send_button"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_send_button1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_send_button0" name="social_blog_like_show_send_button"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_send_button0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_linkedin" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_linkedin1" name="social_blog_like_show_linkedin"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_linkedin1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_linkedin0" name="social_blog_like_show_linkedin"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_linkedin0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_twitter" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_twitter1" name="social_blog_like_show_twitter"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_twitter1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_twitter0" name="social_blog_like_show_twitter"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_twitter0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_googleplus" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_googleplus1" name="social_blog_like_show_googleplus"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_googleplus1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_googleplus0" name="social_blog_like_show_googleplus"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_googleplus0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_LABEL"); ?>
            :
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_show_pinterest" class="radio btn-group">
                <input type="radio" id="social_blog_like_show_pinterest1" name="social_blog_like_show_pinterest"
                       value="1" <?php echo $model->getSetting('social_blog_like_show_pinterest') ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_show_pinterest1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_blog_like_show_pinterest0" name="social_blog_like_show_pinterest"
                       value="0" <?php echo $model->getSetting('social_blog_like_show_pinterest') ? '""' : 'checked="checked"'; ?> />
                <label for="social_blog_like_show_pinterest0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_verb_to_display" class="radio btn-group">
                <input type="radio" id="social_blog_like_verb_to_displayLike" name="social_blog_like_verb_to_display"
                       value="like" <?php echo $model->getSetting('social_blog_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_verb_to_displayLike"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LIKE_LABEL"); ?></label>
                <input type="radio" id="social_blog_like_verb_to_displayRec" name="social_blog_like_verb_to_display"
                       value="recommend" <?php echo $model->getSetting('social_blog_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_verb_to_displayRec"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_RECOMMEND_LABEL"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
        </div>
        <div class="config_option">
            <fieldset id="social_blog_like_color_scheme" class="radio btn-group">
                <input type="radio" id="social_blog_like_color_schemeL" name="social_blog_like_color_scheme"
                       value="light" <?php echo $model->getSetting('social_blog_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                <input type="radio" id="social_blog_like_color_schemeD" name="social_blog_like_color_scheme"
                       value="dark" <?php echo $model->getSetting('social_blog_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                <label for="social_blog_like_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_LABEL"); ?>:
        </div>
        <div class="config_option">
            <select name="social_blog_like_font">
                <option value="arial" <?php echo $model->getSetting('social_blog_like_font') == 'arial' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT1"); ?></option>
                <option value="lucida grande" <?php echo $model->getSetting('social_blog_like_font') == 'lucida grande' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT2"); ?></option>
                <option value="segoe ui" <?php echo $model->getSetting('social_blog_like_font') == 'segoe ui' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT3"); ?></option>
                <option value="tahoma" <?php echo $model->getSetting('social_blog_like_font') == 'tahoma' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT4"); ?></option>
                <option value="trebuchet ms" <?php echo $model->getSetting('social_blog_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT5"); ?></option>
                <option value="verdana" <?php echo $model->getSetting('social_blog_like_font') == 'verdana' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT6"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC4"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_blog_like_width" value="<?php echo $model->getSetting('social_blog_like_width') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<div class="config_row">
    <div class="config_setting_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING"); ?></div>
    <div style="clear:both"></div>
</div>

<div class="config_row">
    <div class="config_setting_option hasTip"
         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING_DESC2'); ?>">
        <?php $catType = $model->getSetting('social_like_cat_include_type'); ?>
        <fieldset id="social_like_cat_include_type" class="radio btn-group">
            <input type="radio" id="social_like_cat_include_type0" name="social_like_cat_include_type"
                   value="0" <?php echo($catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', 'none')" />
            <label for="social_like_cat_include_type0"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ALL_LABEL"); ?></label>
            <input type="radio" id="social_like_cat_include_type1" name="social_like_cat_include_type"
                   value="1" <?php echo($catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', '')" />
            <label for="social_like_cat_include_type1"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?></label>
            <input type="radio" id="social_like_cat_include_type2" name="social_like_cat_include_type"
                   value="2" <?php echo($catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', '')" />
            <label for="social_like_cat_include_type2"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?></label>
        </fieldset>
    </div>
    <div style="clear:both"></div>
</div>
<div class="config_row" id="like_cat_ids" style="display:<?php echo($catType == "0" ? 'none' : ''); ?>">
    <?php
    $catids = $model->getSetting('social_like_cat_ids');
    $categories = unserialize($catids);

    $db = JFactory::getDBO();
    $query = "SELECT id, title FROM #__categories WHERE extension='com_content'";
    $db->setQuery($query);
    $cats = $db->loadAssocList();
    $attribs = 'multiple="multiple"';
    echo '<td>' . JHTML::_('select.genericlist', $cats, 'social_like_cat_ids[]', $attribs, 'id', 'title', $categories, 'social_like_cat_ids') . '</td>';
    ?>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_SETTING"); ?></div>
    <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
    <div style="clear:both"></div>
</div>

<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_ARTICLE_INCLUDE_DESC'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <input type="text" name="social_like_article_include_ids" value="<?php echo $model->getSetting('social_like_article_include_ids'); ?>" size="20">
    </div>
    <div style="clear:both"></div>
</div>
<div class="config_row">
    <div class="config_setting hasTip"
         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_ARTICLE_EXCLUDE_DESC'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?>:
    </div>
    <div class="config_option">
        <input type="text" name="social_like_article_exclude_ids" value="<?php echo $model->getSetting('social_like_article_exclude_ids'); ?>" size="20">
    </div>
    <div style="clear:both"></div>
</div>

</div>
<?php
if (defined('SC16')):
    echo $pane->endPanel();
endif; //SC16
if ($k2IsInstalled)
{
    if (defined('SC16')):
        echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_COMMENTS'), 'social_content_k2_comment_pane');
    endif;
    ?>
    <div class="tab-pane" id="social_content_k2_comment">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ITEM_VIEW_SETTING"); ?></div>
        <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_ITEM_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_ITEM_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentItemView = $model->getSetting('social_k2_comment_item_view'); ?>
            <select name="social_k2_comment_item_view">
                <option value="1" <?php echo ($socialK2CommentItemView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentItemView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentItemView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2CommentItemView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_k2_comment_item_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COMMENTS_DESC3'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_item_comment_max_num" value="<?php echo $model->getSetting('social_k2_item_comment_max_num') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_DESC5'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_item_comment_width" value="<?php echo $model->getSetting('social_k2_item_comment_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC4'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_comment_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_k2_item_comment_color_schemeL" name="social_k2_item_comment_color_scheme"
                           value="light" <?php echo $model->getSetting('social_k2_item_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_k2_item_comment_color_schemeD" name="social_k2_item_comment_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_k2_item_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_comment_order_by" class="radio btn-group">
                    <input type="radio" id="social_k2_item_comment_order_byS" name="social_k2_item_comment_order_by"
                           value="social" <?php echo $model->getSetting('social_k2_item_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                    <input type="radio" id="social_k2_item_comment_order_byR" name="social_k2_item_comment_order_by"
                           value="reverse_time" <?php echo $model->getSetting('social_k2_item_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                    <input type="radio" id="social_k2_item_comment_order_byT" name="social_k2_item_comment_order_by"
                           value="time" <?php echo $model->getSetting('social_k2_item_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BLOG_SETTING_LABEL'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_CATEGORY_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_CATEGORY_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentCategoryView = $model->getSetting('social_k2_comment_category_view'); ?>
            <select name="social_k2_comment_category_view">
                <option value="1" <?php echo ($socialK2CommentCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2CommentCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_TAG_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_TAG_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentTagView = $model->getSetting('social_k2_comment_tag_view'); ?>
            <select name="social_k2_comment_tag_view">
                <option value="1" <?php echo ($socialK2CommentTagView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentTagView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentTagView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2CommentTagView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_USERPAGE_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_USERPAGE_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentUserpageView = $model->getSetting('social_k2_comment_userpage_view'); ?>
            <select name="social_k2_comment_userpage_view">
                <option value="1" <?php echo ($socialK2CommentUserpageView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentUserpageView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentUserpageView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2CommentUserpageView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_LATEST_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_LATEST_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentLatestView = $model->getSetting('social_k2_comment_latest_view'); ?>
            <select name="social_k2_comment_latest_view">
                <option value="1" <?php echo ($socialK2CommentLatestView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentLatestView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentLatestView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2CommentLatestView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_k2_comment_blog_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COMMENTS_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_comment_max_num" value="<?php echo $model->getSetting('social_k2_blog_comment_max_num') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_comment_width" value="<?php echo $model->getSetting('social_k2_blog_comment_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_comment_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_comment_color_schemeL" name="social_k2_blog_comment_color_scheme"
                           value="light" <?php echo $model->getSetting('social_k2_blog_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_k2_blog_comment_color_schemeD" name="social_k2_blog_comment_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_k2_blog_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_comment_order_by" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_comment_order_byS" name="social_k2_blog_comment_order_by"
                           value="social" <?php echo $model->getSetting('social_k2_blog_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                    <input type="radio" id="social_k2_blog_comment_order_byR" name="social_k2_blog_comment_order_by"
                           value="reverse_time" <?php echo $model->getSetting('social_k2_blog_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                    <input type="radio" id="social_k2_blog_comment_order_byT" name="social_k2_blog_comment_order_by"
                           value="time" <?php echo $model->getSetting('social_k2_blog_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <!-- Categories -->
    <div class="config_row">
        <div class="config_setting_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_CATEGORY_SETTING'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting_option hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_CATEGORY_SETTING_DESC'); ?>">
            <?php $k2CatType = $model->getSetting('social_k2_comment_cat_include_type'); ?>
            <fieldset id="social_k2_comment_cat_include_type" class="radio btn-group">
                <input type="radio" id="social_k2_comment_cat_include_type0" name="social_k2_comment_cat_include_type"
                       value="0" <?php echo($k2CatType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', 'none')" />
                <label for="social_k2_comment_cat_include_type0"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_ALL_LABEL'); ?></label>
                <input type="radio" id="social_k2_comment_cat_include_type1" name="social_k2_comment_cat_include_type"
                       value="1" <?php echo($k2CatType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', '')" />
                <label for="social_k2_comment_cat_include_type1"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL'); ?></label>
                <input type="radio" id="social_k2_comment_cat_include_type2" name="social_k2_comment_cat_include_type"
                       value="2" <?php echo($k2CatType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', '')" />
                <label for="social_k2_comment_cat_include_type2"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL'); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row" id="k2_comment_cat_ids" style="display:<?php echo($k2CatType == "0" ? 'none' : ''); ?>">
        <?php
        $k2catids = $model->getSetting('social_k2_comment_cat_ids');
        $k2categories = unserialize($k2catids);

        $query = "SELECT `id`, `name` FROM #__k2_categories";
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $k2cats = $db->loadAssocList();
        $attribs = 'multiple="multiple"';
        echo '<td>' . JHTML::_('select.genericlist', $k2cats, 'social_k2_comment_cat_ids[]', $attribs, 'id', 'name', $k2categories, 'social_k2_comment_cat_ids') . '</td>';
        ?>
        <div style="clear:both"></div>
    </div>
    <!-- End Categories -->
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_INCLUDE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL'); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_k2_comment_item_include_ids" value="<?php echo $model->getSetting('social_k2_comment_item_include_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_EXCLUDE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL'); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_k2_comment_item_exclude_ids" value="<?php echo $model->getSetting('social_k2_comment_item_exclude_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    </div>
    <?php
    if (defined('SC16')):
        echo $pane->endPanel();
        echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_LIKE'), 'social_content_k2_like_pane');
    endif;
    ?>
    <div class="tab-pane" id="social_content_k2_like">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_ITEM_VIEW_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_ITEM_VIEW_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_ITEM_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2LikeItemView = $model->getSetting('social_k2_like_item_view'); ?>
            <select name="social_k2_like_item_view">
                <option value="1" <?php echo ($socialK2LikeItemView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2LikeItemView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2LikeItemView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2LikeItemView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_k2_like_item_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <select name="social_k2_item_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'standard' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_STANDARD_LABEL"); ?></option>
                    <option value="box_count" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'box_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOX_COUNT_LABEL"); ?></option>
                    <option value="button_count" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'button_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_COUNT_LABEL"); ?></option>
                    <option value="button" <?php echo $model->getSetting('social_k2_item_like_layout_style') == 'button' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_LABEL"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_facebook" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_facebook1" name="social_k2_item_like_show_facebook"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_facebook') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_facebook1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_facebook0" name="social_k2_item_like_show_facebook"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_facebook') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_facebook0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACES_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACES_LABEL'); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_faces" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_faces1" name="social_k2_item_like_show_faces"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_faces1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_faces0" name="social_k2_item_like_show_faces"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_faces0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_SHARE_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_SEND_LABEL'); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_send_button" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_send_button1" name="social_k2_item_like_show_send_button"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_send_button1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_send_button0" name="social_k2_item_like_show_send_button"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_send_button0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_linkedin" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_linkedin1" name="social_k2_item_like_show_linkedin"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_linkedin1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_linkedin0" name="social_k2_item_like_show_linkedin"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_linkedin0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_twitter" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_twitter1" name="social_k2_item_like_show_twitter"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_twitter1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_twitter0" name="social_k2_item_like_show_twitter"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_twitter0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_LABEL'); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_googleplus" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_googleplus1" name="social_k2_item_like_show_googleplus"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_googleplus1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_googleplus0" name="social_k2_item_like_show_googleplus"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_googleplus0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_show_pinterest" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_show_pinterest1" name="social_k2_item_like_show_pinterest"
                           value="1" <?php echo $model->getSetting('social_k2_item_like_show_pinterest') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_show_pinterest1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_item_like_show_pinterest0" name="social_k2_item_like_show_pinterest"
                           value="0" <?php echo $model->getSetting('social_k2_item_like_show_pinterest') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_item_like_show_pinterest0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_verb_to_display" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_verb_to_displayLike" name="social_k2_item_like_verb_to_display"
                           value="like" <?php echo $model->getSetting('social_k2_item_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_verb_to_displayLike"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_LIKE_LABEL'); ?></label>
                    <input type="radio" id="social_k2_item_like_verb_to_displayRec" name="social_k2_item_like_verb_to_display"
                           value="recommend" <?php echo $model->getSetting('social_k2_item_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_verb_to_displayRec"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_RECOMMEND_LABEL'); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC4'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_like_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_k2_item_like_color_schemeL" name="social_k2_item_like_color_scheme"
                           value="light" <?php echo $model->getSetting('social_k2_item_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_k2_item_like_color_schemeD" name="social_k2_item_like_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_k2_item_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_like_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_FONT_DESC3'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_FONT_LABEL'); ?>:
            </div>
            <div class="config_option">
                <select name="social_k2_item_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_k2_item_like_font') == 'arial' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT1"); ?></option>
                    <option value="lucida grande" <?php echo $model->getSetting('social_k2_item_like_font') == 'lucida grande' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT2"); ?></option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_k2_item_like_font') == 'segoe ui' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT3"); ?></option>
                    <option value="tahoma" <?php echo $model->getSetting('social_k2_item_like_font') == 'tahoma' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT4"); ?></option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_k2_item_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT5"); ?></option>
                    <option value="verdana" <?php echo $model->getSetting('social_k2_item_like_font') == 'verdana' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT6"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_DESC6'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_item_like_width" value="<?php echo $model->getSetting('social_k2_item_like_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BLOG_SETTING_LABEL'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_CATEGORY_VIEW_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_CATEGORY_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2LikeCategoryView = $model->getSetting('social_k2_like_category_view'); ?>
            <select name="social_k2_like_category_view">
                <option value="1" <?php echo ($socialK2LikeCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2LikeCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2LikeCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2LikeCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_TAG_VIEW_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_TAG_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2LikeTagView = $model->getSetting('social_k2_like_tag_view'); ?>
            <select name="social_k2_like_tag_view">
                <option value="1" <?php echo ($socialK2LikeTagView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2LikeTagView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2LikeTagView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2LikeTagView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_USERPAGE_VIEW_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_USERPAGE_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2LikeUserpageView = $model->getSetting('social_k2_like_userpage_view'); ?>
            <select name="social_k2_like_userpage_view">
                <option value="1" <?php echo ($socialK2LikeUserpageView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2LikeUserpageView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2LikeUserpageView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2LikeUserpageView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_LATEST_VIEW_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_LATEST_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2LikeLatestView = $model->getSetting('social_k2_like_latest_view'); ?>
            <select name="social_k2_like_latest_view">
                <option value="1" <?php echo ($socialK2LikeLatestView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2LikeLatestView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2LikeLatestView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <option value="0" <?php echo ($socialK2LikeLatestView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_k2_like_blog_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <select name="social_k2_blog_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'standard' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_STANDARD_LABEL"); ?></option>
                    <option value="box_count" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'box_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOX_COUNT_LABEL"); ?></option>
                    <option value="button_count" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'button_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_COUNT_LABEL"); ?></option>
                    <option value="button" <?php echo $model->getSetting('social_k2_blog_like_layout_style') == 'button' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_LABEL"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_facebook" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_facebook1" name="social_k2_blog_like_show_facebook"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_facebook') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_facebook1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_facebook0" name="social_k2_blog_like_show_facebook"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_facebook') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_facebook0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACES_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FACES_LABEL'); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_faces" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_faces1" name="social_k2_blog_like_show_faces"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_faces1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_faces0" name="social_k2_blog_like_show_faces"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_faces0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_SHARE_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_SEND_LABEL'); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_send_button" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_send_button1" name="social_k2_blog_like_show_send_button"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_send_button1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_send_button0" name="social_k2_blog_like_show_send_button"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_send_button0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_linkedin" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_linkedin1" name="social_k2_blog_like_show_linkedin"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_linkedin1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_linkedin0" name="social_k2_blog_like_show_linkedin"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_linkedin0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_twitter" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_twitter1" name="social_k2_blog_like_show_twitter"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_twitter1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_twitter0" name="social_k2_blog_like_show_twitter"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_twitter0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_GOOGLE_LABEL'); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_googleplus" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_googleplus1" name="social_k2_blog_like_show_googleplus"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_googleplus') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_googleplus1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_googleplus0" name="social_k2_blog_like_show_googleplus"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_googleplus') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_googleplus0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_show_pinterest" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_show_pinterest1" name="social_k2_blog_like_show_pinterest"
                           value="1" <?php echo $model->getSetting('social_k2_blog_like_show_pinterest') ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_show_pinterest1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_k2_blog_like_show_pinterest0" name="social_k2_blog_like_show_pinterest"
                           value="0" <?php echo $model->getSetting('social_k2_blog_like_show_pinterest') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_k2_blog_like_show_pinterest0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_verb_to_display" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_verb_to_displayLike" name="social_k2_blog_like_verb_to_display"
                           value="like" <?php echo $model->getSetting('social_k2_blog_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_verb_to_displayLike"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_LIKE_LABEL'); ?></label>
                    <input type="radio" id="social_k2_blog_like_verb_to_displayRec" name="social_k2_blog_like_verb_to_display"
                           value="recommend" <?php echo $model->getSetting('social_k2_blog_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_verb_to_displayRec"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_RECOMMEND_LABEL"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_like_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_like_color_schemeL" name="social_k2_blog_like_color_scheme"
                           value="light" <?php echo $model->getSetting('social_k2_blog_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_k2_blog_like_color_schemeD" name="social_k2_blog_like_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_k2_blog_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_like_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_FONT_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_FONT_LABEL'); ?>:
            </div>
            <div class="config_option">
                <select name="social_k2_blog_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_k2_blog_like_font') == 'arial' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT1"); ?></option>
                    <option value="lucida grande" <?php echo $model->getSetting('social_k2_blog_like_font') == 'lucida grande' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT2"); ?></option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_k2_blog_like_font') == 'segoe ui' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT3"); ?></option>
                    <option value="tahoma" <?php echo $model->getSetting('social_k2_blog_like_font') == 'tahoma' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT4"); ?></option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_k2_blog_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT5"); ?></option>
                    <option value="verdana" <?php echo $model->getSetting('social_k2_blog_like_font') == 'verdana' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT6"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_DESC4'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_like_width" value="<?php echo $model->getSetting('social_k2_blog_like_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <!-- Category -->
    <div class="config_row">
        <div class="config_setting_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_CATEGORY_SETTING'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting_option hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_CATEGORY_SETTING_DESC2'); ?>">
            <?php $k2catType = $model->getSetting('social_k2_like_cat_include_type'); ?>
            <fieldset id="social_k2_like_cat_include_type" class="radio btn-group">
                <input type="radio" id="social_k2_like_cat_include_type0" name="social_k2_like_cat_include_type"
                       value="0" <?php echo($k2catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_like_cat_ids', 'none')" />
                <label for="social_k2_like_cat_include_type0"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_ALL_LABEL'); ?></label>
                <input type="radio" id="social_k2_like_cat_include_type1" name="social_k2_like_cat_include_type"
                       value="1" <?php echo($k2catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_like_cat_ids', '')" />
                <label for="social_k2_like_cat_include_type1"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL'); ?></label>
                <input type="radio" id="social_k2_like_cat_include_type2" name="social_k2_like_cat_include_type"
                       value="2" <?php echo($k2catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_like_cat_ids', '')" />
                <label for="social_k2_like_cat_include_type2"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL'); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row" id="k2_like_cat_ids" style="display:<?php echo($k2catType == "0" ? 'none' : ''); ?>">
        <?php
        $k2catids = $model->getSetting('social_k2_like_cat_ids');
        $k2categories = unserialize($k2catids);

        $query = "SELECT `id`, `name` FROM #__k2_categories";
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $k2cats = $db->loadAssocList();
        $attribs = 'multiple="multiple"';
        echo '<td>' . JHTML::_('select.genericlist', $k2cats, 'social_k2_like_cat_ids[]', $attribs, 'id', 'name', $k2categories, 'social_k2_like_cat_ids') . '</td>';
        ?>
        <div style="clear:both"></div>
    </div>
    <!-- End Categories -->
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_INCLUDE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL'); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_k2_like_item_include_ids" value="<?php echo $model->getSetting('social_k2_like_item_include_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_EXCLUDE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL'); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_k2_like_item_exclude_ids" value="<?php echo $model->getSetting('social_k2_like_item_exclude_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    </div>

    <?php
    if (defined('SC16')):
        echo $pane->endPanel();
    endif; //SC16
}
if (defined('SC16')):
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_NOTIFICATIONS'), 'social_notifications_pane');
endif;
?>

<div class="tab-pane" id="social_notifications">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_COMMENTS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_COMMENTS_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_notification_comment_enabled" class="radio btn-group">
                <input type="radio" id="social_notification_comment_enabled1" name="social_notification_comment_enabled"
                       value="1" <?php echo $model->getSetting('social_notification_comment_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_notification_comment_enabled1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_notification_comment_enabled0" name="social_notification_comment_enabled"
                       value="0" <?php echo $model->getSetting('social_notification_comment_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_notification_comment_enabled0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_LIKES_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_LIKES_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_notification_like_enabled" class="radio btn-group">
                <input type="radio" id="social_notification_like_enabled1" name="social_notification_like_enabled"
                       value="1" <?php echo $model->getSetting('social_notification_like_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_notification_like_enabled1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_notification_like_enabled0" name="social_notification_like_enabled"
                       value="0" <?php echo $model->getSetting('social_notification_like_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_notification_like_enabled0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_ADDRESS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_ADDRESS_LABEL'); ?></div>
        <div class="config_option">
            <textarea name="social_notification_email_address" rows="3"
                      cols="30"><?php echo $model->getSetting('social_notification_email_address') ?></textarea><br />
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_GOOGLE_ANALYTICS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_GOOGLE_ANALYTICS_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_notification_google_analytics" class="radio btn-group">
                <input type="radio" id="social_notification_google_analytics1" name="social_notification_google_analytics"
                       value="1" <?php echo $model->getSetting('social_notification_google_analytics') ? 'checked="checked"' : ""; ?> />
                <label for="social_notification_google_analytics1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_notification_google_analytics0" name="social_notification_google_analytics"
                       value="0" <?php echo $model->getSetting('social_notification_google_analytics') ? '""' : 'checked="checked"'; ?> />
                <label for="social_notification_google_analytics0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
if (defined('SC16')):
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_MISC'), 'social_misc_pane');
endif;
?>
<div class="tab-pane" id="social_misc">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SOCIAL_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_TAG_ADMIN_KEY_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TAG_ADMIN_KEY_LABEL'); ?></div>
        <div class="config_option">
            <input type="text" name="social_tag_admin_key" value="<?php echo $model->getSetting('social_tag_admin_key') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_POINTS_INTEGRATION_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_POINTS_INTEGRATION_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_alphauserpoints_enabled" class="radio btn-group">
                <input type="radio" id="social_alphauserpoints_enabled1" name="social_alphauserpoints_enabled"
                       value="1" <?php echo $model->getSetting('social_alphauserpoints_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_alphauserpoints_enabled1"><?php echo JText::_('JENABLED'); ?></label>
                <input type="radio" id="social_alphauserpoints_enabled0" name="social_alphauserpoints_enabled"
                       value="0" <?php echo $model->getSetting('social_alphauserpoints_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_alphauserpoints_enabled0"><?php echo JText::_('JDISABLED'); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
</div>
<?php
if (defined('SC16')):
    echo $pane->endPanel();
    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_EXAMPLES'), 'social_examples_pane');
endif;
?>
<div class="tab-pane <?php echo $provider ? 'active' : '';?>" id="social_examples">
    <?php require(JPATH_ROOT . '/administrator/components/com_jfbconnect/views/social/tmpl/examples.php'); ?>
</div>
<?php
if (defined('SC30')):
    echo '</div>';
endif; //SC30
if (defined('SC16')):
    echo $pane->endPanel();
    echo $pane->endPane();
endif; //SC16
?>
<input type="hidden" name="option" value="com_jfbconnect" />
<input type="hidden" name="controller" value="social" />
<input type="hidden" name="cid[]" value="0" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_('form.token'); ?>

</form>
</div>
<script type="text/javascript">
    function showHideSettings(settingsId, selectors)
    {
        var hide = true;
        jfbcJQuery.each(selectors, function (i, selector)
                {
                    if (jfbcJQuery("select[name=" + selector + "]").val() != "0")
                        hide = false;
                }
        );
        if (hide)
            jfbcJQuery("#" + settingsId).css("display", "none");
        else
            jfbcJQuery("#" + settingsId).css("display", "block");
    }
    ;

    var selectNames = {
        'social_comment_article_settings': ['social_comment_article_view'],
        'social_comment_blog_settings': ['social_comment_frontpage_view', 'social_comment_category_view'],
        'social_like_article_settings': ['social_like_article_view'],
        'social_like_blog_settings': ['social_like_frontpage_view', 'social_like_category_view'],
        'social_k2_comment_item_settings': ['social_k2_comment_item_view'],
        'social_k2_comment_blog_settings': ['social_k2_comment_category_view', 'social_k2_comment_tag_view', 'social_k2_comment_userpage_view', 'social_k2_comment_latest_view'],
        'social_k2_like_item_settings': ['social_k2_like_item_view'],
        'social_k2_like_blog_settings': ['social_k2_like_category_view', 'social_k2_like_tag_view', 'social_k2_like_userpage_view', 'social_k2_like_latest_view']
    };

    jfbcJQuery(document).ready(function ()
    {
        jfbcJQuery.each(selectNames, function (settingsId, selectors)
        {
            jfbcJQuery.each(selectors, function (i, selector)
            {
                showHideSettings(settingsId, selectors);
                jfbcJQuery("select[name=" + selector + "]").change(function ()
                {
                    showHideSettings(settingsId, selectors);
                });
            });
        });
    });

</script>