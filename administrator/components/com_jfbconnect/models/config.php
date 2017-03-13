<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JFBConnectModelConfig extends JModelLegacy
{
    protected $settings;

    private $providerStandardSettings = array(
        'app_id',
        'secret_key',
        'login_button'
    );

    private $componentSettings = array(
        'facebook_curl_disable_ssl' => '0',
        'facebook_perm_custom' => '',

        'google_openid_fallback' => '0',

        'linkedin_perm_custom'=>'',
        'instagram_callback_ssl'=>'0',
        'meetup_widget_api_key' => '',

        //'github_app_name' => '', # Added for user agent customization. Instead, we're hard-coding it to 'SourceCoast - JFBConnect'.

        'cache_duration' => '15',

        'login_use_popup' => '1',
        'automatic_registration' => '1',
        'registration_component' => 'jfbconnect',
        'registration_generate_username' => '0',
        'auto_username_format' => '0', //0 = fb_, 1=first.last, 2=firlas, 3=email
        'generate_random_password' => '1',
        'registration_show_username' => '1',
        'registration_show_password' => '1',
        'registration_show_email' => '0',
        'registration_show_name' => '1',
        'registration_display_mode' => 'horizontal',
        'registration_send_new_user_email' => '1',
        'joomla_skip_newuser_activation' => '1',
        'facebook_new_user_redirect' => "",
        'facebook_login_redirect' => "",
        'facebook_auto_login' => "0",
        'facebook_display_errors' => '0',
        'facebook_auto_map_by_email' => '0',
        'facebook_language_locale' => '',
        'facebook_login_show_modal' => '0',
        'show_powered_by_link' => '0',
        'affiliate_id' => "",
        'sc_download_id' => "",
        'experimental' => "",
        'logout_joomla_only' => '0',
        'show_login_with_joomla_reg' => '1', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_toolbar_enable' => '1',
        'social_force_scheme' => '0', // 0 = no, 1 = 'http', 2 = 'https'
        'social_tags_always_parse' => '0', // 0 = don't always parse, 1 = xfbml:true always
        'social_tag_admin_key' => '',
        'social_comment_article_include_ids' => '',
        'social_comment_article_exclude_ids' => '',
        'social_comment_cat_include_type' => '0', //0=ALL, 1=Include, 2=Exclude
        'social_comment_cat_ids' => '',
        'social_comment_sect_include_type' => '0',
        'social_comment_sect_ids' => '',
        'social_comment_article_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_comment_frontpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_comment_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_comment_section_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_article_comment_max_num' => '10',
        'social_article_comment_width' => '350',
        'social_article_comment_color_scheme' => 'light',
        'social_article_comment_order_by' => 'social',
        'social_blog_comment_max_num' => '10',
        'social_blog_comment_width' => '350',
        'social_blog_comment_color_scheme' => 'light',
        'social_blog_comment_order_by' => 'social',
        'social_k2_comment_item_include_ids' => '',
        'social_k2_comment_item_exclude_ids' => '',
        'social_k2_comment_cat_include_type' => '0', //0=ALL, 1=Include, 2=Exclude
        'social_k2_comment_cat_ids' => '',
        'social_k2_comment_item_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_tag_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_userpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_comment_latest_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_item_comment_max_num' => '10',
        'social_k2_item_comment_width' => '350',
        'social_k2_item_comment_color_scheme' => 'light',
        'social_k2_item_comment_order_by' => 'social',
        'social_k2_blog_comment_max_num' => '10',
        'social_k2_blog_comment_width' => '350',
        'social_k2_blog_comment_color_scheme' => 'light',
        'social_k2_blog_comment_order_by' => 'social',
        'social_like_article_include_ids' => '',
        'social_like_article_exclude_ids' => '',
        'social_like_cat_include_type' => '0',
        'social_like_cat_ids' => '',
        'social_like_sect_include_type' => '0',
        'social_like_sect_ids' => '',
        'social_like_article_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_like_frontpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_like_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_like_section_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_article_like_layout_style' => 'standard', //standard, box_count, button_count or button
        'social_article_like_show_faces' => '1', //1=Yes, 0=No
        'social_article_like_show_send_button' => '0', //0=No, 1=Yes
        'social_article_like_width' => '250',
        'social_article_like_verb_to_display' => 'like', //like or recommend
        'social_article_like_font' => 'arial', //arial, lucida grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_article_like_color_scheme' => 'light', //light or dark
        'social_article_like_show_facebook' => '0', //0=No, 1=Yes
        'social_article_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_article_like_show_twitter' => '0', //0=No, 1=Yes
        'social_article_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_article_like_show_pinterest' => '0', //0=No, 1=Yes
        'social_blog_like_layout_style' => 'standard', //standard, box_count, button_count or button
        'social_blog_like_show_faces' => '1', //1=Yes, 0=No
        'social_blog_like_show_send_button' => '0', //0=No, 1=Yes
        'social_blog_like_width' => '250',
        'social_blog_like_verb_to_display' => 'like', //like or recommend
        'social_blog_like_font' => 'arial', //arial, lucida grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_blog_like_color_scheme' => 'light', //light or dark
        'social_blog_like_show_facebook' => '0', //0=No, 1=Yes
        'social_blog_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_blog_like_show_twitter' => '0', //0=No, 1=Yes
        'social_blog_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_blog_like_show_pinterest' => '0', //0=No, 1=Yes
        'social_k2_like_item_include_ids' => '',
        'social_k2_like_item_exclude_ids' => '',
        'social_k2_like_cat_include_type' => '0',
        'social_k2_like_cat_ids' => '',
        'social_k2_like_item_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_category_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_tag_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_userpage_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_like_latest_view' => '0', //0=None, 1=Top, 2=Bottom, 3=Both
        'social_k2_item_like_layout_style' => 'standard', //standard, box_count, button_count or button
        'social_k2_item_like_show_faces' => '1', //1=Yes, 0=No
        'social_k2_item_like_show_send_button' => '0', //0=No, 1=Yes
        'social_k2_item_like_width' => '250',
        'social_k2_item_like_verb_to_display' => 'like', //like or recommend
        'social_k2_item_like_font' => 'arial', //arial, lucida grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_k2_item_like_color_scheme' => 'light', //light or dark
        'social_k2_item_like_show_facebook' => '0', //0=No, 1=Yes
        'social_k2_item_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_k2_item_like_show_twitter' => '0', //0=No, 1=Yes
        'social_k2_item_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_k2_item_like_show_pinterest' => '0', //0=No, 1=Yes
        'social_k2_blog_like_layout_style' => 'standard', //standard, box_count, button_count or button
        'social_k2_blog_like_show_faces' => '1', //1=Yes, 0=No
        'social_k2_blog_like_show_send_button' => '0', //0=No, 1=Yes
        'social_k2_blog_like_width' => '250',
        'social_k2_blog_like_verb_to_display' => 'like', //like or recommend
        'social_k2_blog_like_font' => 'arial', //arial, lucida grande, segoe ui, tahoma, trebuchet ms, verdana
        'social_k2_blog_like_color_scheme' => 'light', //light or dark
        'social_k2_blog_like_show_facebook' => '0', //0=No, 1=Yes
        'social_k2_blog_like_show_linkedin' => '0', //0=No, 1=Yes
        'social_k2_blog_like_show_twitter' => '0', //0=No, 1=Yes
        'social_k2_blog_like_show_googleplus' => '0', //0=No, 1=Yes
        'social_k2_blog_like_show_pinterest' => '0', //0=No, 1=Yes
        'social_graph_fields' => '',
        'social_notification_comment_enabled' => '0',
        'social_notification_like_enabled' => '0',
        'social_notification_email_address' => '',
        'social_notification_google_analytics' => '0',
        'social_alphauserpoints_enabled' => '0',

        // Canvas Settings
        'canvas_tab_template' => '-1',
        'canvas_canvas_template' => '-1',
        'canvas_tab_resize_enabled' => '0',
        'canvas_canvas_resize_enabled' => '0',

        // AutoTune
        'autotune_authorization' => '',
        'autotune_field_descriptors' => '',
        'autotune_app_config' => '',

        // JQuery / Bootstrap
        'jquery_load' => '1',
        'bootstrap_css' => '1'
    );

    function __construct()
    {
        $this->table = '#__jfbconnect_config';
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables');

        parent::__construct();
    }

    function store()
    {
        $row = & $this->getTable();
        $data = JRequest::get('post');
        if (!$row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        $row->updated_at = JFactory::getDate()->toSql();
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return true;
    }

    function update($setting, $value)
    {
        if (is_array($value) || is_object($value))
            $value = serialize($value);
        else
            $value = trim($value);
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('id'))
                ->from($this->_db->qn($this->table))
                ->where($this->_db->qn('setting') . "=" . $this->_db->quote($setting));
        $this->_db->setQuery($query);
        $settingId = $this->_db->loadResult();

        $row = $this->getTable();
        $row->id = $settingId;
        $row->setting = $setting;
        $row->value = $value;
        if (!$settingId)
            $row->created_at = JFactory::getDate()->toSql();
        $row->updated_at = JFactory::getDate()->toSql();
        if (!$row->check())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if ($setting == 'sc_download_id')
        {
            JFBCFactory::model('updates')->refreshUpdateSites($value);
        }

        $this->settings = null;

        return true;
    }

    function getSettings()
    {
        if (!$this->settings)
        {
            $this->settings = new JRegistry();
            $query = "SELECT setting,value FROM " . $this->table;
            $this->_db->setQuery($query);
            $settings = $this->_db->loadAssocList('setting', 'value');

            $this->settings->loadArray($settings);
        }
        return $this->settings;
    }

    function get($setting, $default = "")
    {
        $value = null;

        $this->getSettings();

        if ($this->settings->exists($setting))
        {
            $value = $this->settings->get($setting, $default);
        }
        else # load default value
        {
            // Do a quick check to see if it's a component setting, and get it's default
            if (array_key_exists($setting, $this->componentSettings))
                $value = $this->componentSettings[$setting];
        }

        if (strpos($setting, "autotune_") !== false)
            $value = @unserialize($value); // Suppress the notice that the string may not be serialized in the first place

        if ($setting == 'experimental')
        {
            $reg = new JRegistry();
            if (!empty($value))
                $reg->loadArray(json_decode($value));
            $value = $reg;
        }

        if ($value === null || $value === '' || $value === false)
        {
            $value = $default;
        }

        return $value;
    }

    // Deprecated, use get instead as it's simpler and more common
    function getSetting($setting, $default = '')
    {
        return $this->get($setting, $default);
    }

    function delete()
    {
        $cids = JRequest::getVar('cid', array(0), 'post', 'array');
        $row = & $this->getTable();
        if (count($cids))
        {
            foreach ($cids as $cid)
            {
                if (!$row->delete($cid))
                {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            }
        }
        return true;
    }

    /*
     * $configs = Array (POST) of setting->value pairs.
     */

    function saveSettings($configs)
    {
        $this->getSettings();

        // Create list of provider-specific settings. These to be moved to their own table ~6.2
        $providerSettings = array();
        foreach (JFBCFactory::getAllProviders() as $p)
        {
            foreach ($this->providerStandardSettings as $setting)
            {
                $providerSettings[$p->systemName . '_' . $setting] = '';
            }
        }

        $allSettings = array_merge($providerSettings, $this->componentSettings);
        $settings = array_intersect_key($configs, $allSettings);
        foreach ($settings as $setting => $value)
        {
            if ($setting == 'experimental')
                $value = json_encode($value);
            $this->update($setting, $value);
        }

        $this->settings = null; // Clear all the settings so they're reloaded next time any are needed
    }

    function getUpdatedDate($field)
    {
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->qn('updated_at'))
                ->from($this->_db->qn('#__jfbconnect_config'))
                ->where($this->_db->qn('setting') . '=' . $this->_db->q($field));
        $this->_db->setQuery($query);
        $date = $this->_db->loadResult();
        return $date;
    }
}
