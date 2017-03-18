package vantinviet.banhangonline88.entities.messenger;

import java.util.List;
import java.util.StringTokenizer;

/**
 * Created by cuongnd on 18/03/2017.
 */

class Paramslist {
    // Preferences
    String chatrefresh;
    String lastmessagetime;
    String maxinactivitytime;
    String registration_email;
    String forceavailable;
    String usefullname;
    String start_open_mode;
    String chatboxes_open_mode;
    String show_send_button;
    String chat_title;
    String private_chat_title;
    String public_chat_title;
    String resizable_chatboxes;
    String resizable_sidebar;
    String chatrooms_users_details;
    String auto_close_popups;
    String exclude_onmobile;
    String emoticons_enabled;
    String emoticons_original_size;

    // Features
    String pdintegration;
    String filter_friendship;
    String pm_integration;
    String social_menu_item;
    String skypebridge;
    String groupchat;
    String groupchat_start_open_mode;
    String groupchatmode;
    String autoclear_conversation;
    String default_chatroom;
    String guestenabled;
    String guests_name_algo;
    String guests_name_length;
    String guestprefix;
    String searchfield;
    String history;
    String buddylist_visible;
    String privatechat_enabled;
    String typing_enabled;
    String lastreadmessage;
    String maximize_box;
    String show_suggestion_tooltip;
    String suggestion_tooltip_text;
    String geolocation_enabled;
    String geolocation_service;
    List<String> geolocation_gids;
    String usersbanning;
    String usersbanning_mode;
    String wordsbanning;
    String wordsbanned;
    String wordsbanned_replacement;
    String ipbanning;
    String iprange_multiple;

    // Chat rendering
    String chat_template;
    String chat_template_tooltip;
    String chat_template_tooltip_variant;
    String tooltip_bordercolor_override;
    String tooltip_bckcolor_override;
    String chat_color_override;
    String fontsize_override;
    String fontsize_titles_override;
    String submitlamform_color_override;
    String positionment_chatboxes;
    String public_chat_height_override;
    String private_chat_height_override;
    String sidebar_default_width_override;
    String public_chat_top_override;
    String show_users_count;
    String show_myusername;
    String micro_splacement_konstant;
    String rendering_mode;
    String sidebar_width;
    String sidebar_height;
    String search_width;
    String chatroom_width;
    String baloon_position;
    String separate_widgets;

    // Videochat peer-to-peer
    String webrtc_enabled;
    String webrtc_call_sound;
    String timeout_start_call;
    String timeout_end_call;
    String micvolume_default                ;
    String audiovolume_default;
    String videochat_auto_maximize_box;
    String hide_webcam_when_disabled;
    String show_webrtc_stats;
    String show_webrtc_vumeter;
    String enable_recording;
    String auto_quality_bandwidth_management;
    String serverload_reduction;
    String no_webrtc_fallback;
    String stun_servers;
    String turn_servers_enabled;
    String turn_servers;
    String turn_anyfirewall_enabled;
    String turn_anyfirewall_appname;
    String turn_anyfirewall_password;
    String turn_anyfirewall_request_timeout;

    // File system
    String avatarenable;
    String avatar_allowed_extensions;
    String cropmode;
    String avatarupload;
    String attachmentsenable;
    String maxfilesize;
    String disallowed_extensions;
    String easysocial_avatar_path;
    String easysocial_custom_avatar;
    String easysocial_custom_avatar_path;
    String kunena_avatars_resize_format;

    // Ticketing and live support
    List<String> chatadmins_gids;
    String auto_open_agentbox;
    String auto_open_agentbox_defaultmessage;
    String affect_public_chat;
    String offline_message_switcher;
    String offline_message;
    String tickets_form;
    String tickets_form_always_visible;
    String tickets_mailfrom;
    String tickets_fromname;
    String ticket_sent_notify;
    String ticket_notify_emails;
    String private_messaging_notification_email;
    String notification_email_switcher;
    String notification_email;
    String email_subject;
    String email_start_text;

    // Social login
    String fblogin_active;
    String appId;
    String secret;
    String gpluslogin_active;
    String gplusClientID;
    String gplusKey;
    String twitterlogin_active;
    String twitterKey;
    String twitterSecret;
    String sdkloadmode;
    String auth_type;
    String curl_ssl_verifypeer;

    // Language translation
    String language_translation_enabled;
    List<String> language_translation_groups;
    String language_translation_incomingmessages;
    String language_translation_selfmessages;
    String default_language;
    String default_to_language;

    // Permissions
    List<String> chat_accesslevels;
    String allow_guest_fileupload;
    String allow_guest_avatarupload;
    String allow_guest_skypebridge;
    String allow_guest_overridename;
    String allow_guest_banning;
    String allow_guest_buddylist;
    String allow_media_objects;
    List<String> allow_videochat;
    List<String> allow_media_recorder;
    List<String> allow_media_recorder_save;
    String limit_my_users_groups;
    String addchatroom;
    String addchatroom_groups;
    String deletechatroom;
    String deletechatroom_groups;
    String enablemoderation;
    List<String> moderation_groups;
    String start_at_hour;
    String stop_at_hour;

    // Advanced
    List<String> chat_exclusions;
    String chatrooms_latest;
    String chatrooms_latest_interval;
    String wall_history_delay;
    String wall_history_delay_autoload;
    String chatrooms_messages_stillinroom;
    String maxtimeinterval_groupmessages;
    String async_send_message;
    String unique_usernames;
    String download_msgs_multitabs_mode;
    String advanced_avatars_mgmt;
    String pm_num_loading_msgs;
    String caching;
    String cache_lifetime;
    String scripts_loading;
    String keep_latest_msgs;
    String enable_debug;
    String notifications_auto_enable;
    String notifications_time;
    String notifications_public_time;
    String includejquery;
    String noconflict;
    String includeevent;
}
