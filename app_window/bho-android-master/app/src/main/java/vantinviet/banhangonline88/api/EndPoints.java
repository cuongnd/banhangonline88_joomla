package vantinviet.banhangonline88.api;

import vantinviet.banhangonline88.CONST;

public class EndPoints {

    /**
     * Base server url.
     */
    private static final String API_URL                  = "http://77.93.198.186/v1.2/";    // staging
    public static final String API_URL1                  = "http://banhangonline88.com/";    // staging

    //public static final String SHOPS                    = API_URL.concat(CONST.ORGANIZATION_ID + "/shops");
    public static final String SHOPS                    = API_URL1.concat("index.php?format=json&tmpl=component&ignoreMessages=true&option=com_hikashop&ctrl=country&task=ajax_get_list_country");
    public static final String SHOPS_SINGLE             = API_URL.concat(CONST.ORGANIZATION_ID + "/shops/%d");
    //public static final String NAVIGATION_DRAWER        = API_URL.concat("%d/navigation_drawer");
    public static final String NAVIGATION_DRAWER        = API_URL1.concat("index.php?format=json&tmpl=component&ignoreMessages=true&option=com_menus&task=items.ajax_get_left_menu_category");
    //public static final String BANNERS                  = API_URL.concat("%d/banners");
    public static final String BANNERS                  = API_URL1.concat("index.php?format=json&tmpl=component&ignoreMessages=true&option=com_menus&task=items.ajax_get_navigation");
    public static final String PAGES_SINGLE             = API_URL.concat("%d/pages/%d");
    public static final String PAGES_TERMS_AND_COND     = API_URL.concat("%d/pages/terms");
    //public static final String PRODUCTS                 = API_URL.concat("%d/products");
    public static final String PRODUCTS                 = API_URL1.concat("index.php?os=android&tmpl=component&format=json&option=com_hikashop&view=product");
    public static final String PRODUCTS_SINGLE          = API_URL.concat("%d/products123/%d");
    //public static final String PRODUCTS_SINGLE_RELATED  = API_URL.concat("%d/products/%d?include=related");
    public static final String PRODUCTS_SINGLE_RELATED  = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_hikashop&view=product&cid[]=%d&layout=android");
    public static final String USER_REGISTER            = API_URL.concat("%d/users/register");
    public static final String USER_LOGIN_EMAIL         = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_users&task=user.ajax_login");;
    public static final String USER_LOGIN_FACEBOOK      = API_URL.concat("%d/login/facebook");
    public static final String USER_RESET_PASSWORD      = API_URL.concat("%d/users/reset-password");
    public static final String USER_SINGLE              = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_users&task=user.ajax_get_user_info");
    public static final String USER_CHANGE_PASSWORD     = API_URL.concat("%d/users/%d/password");
    public static final String CART                     = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_hikashop&ctrl=product&task=ajax_get_cart");;
    public static final String CART_INFO                = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_hikashop&ctrl=product&task=ajax_get_cart_info");
    public static final String CART_ITEM                = API_URL.concat("%d/cart/%d");
    public static final String CART_DELIVERY_INFO       = API_URL.concat("%d/cart/delivery-info");
    public static final String CART_DISCOUNTS           = API_URL.concat("%d/cart/discounts");
    public static final String CART_DISCOUNTS_SINGLE    = API_URL.concat("%d/cart/discounts/%d");
    public static final String ORDERS                   = API_URL.concat("%d/orders");
    public static final String ORDERS_SINGLE            = API_URL.concat("%d/orders/%d");
    public static final String BRANCHES                 = API_URL.concat("%d/branches");
    public static final String WISHLIST                 = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_hikashop&ctrl=product&task=ajax_get_wishlist");
    public static final String WISHLIST_SINGLE          = API_URL.concat("%d/wishlist/%d");
    public static final String WISHLIST_IS_IN_WISHLIST  = API_URL.concat("%d/wishlist/is-in-wishlist/%d");
    public static final String REGISTER_NOTIFICATION    = API_URL.concat("%d/devices");


    // Notifications parameters
    public static final String NOTIFICATION_LINK        = "link";
    public static final String NOTIFICATION_MESSAGE     = "message";
    public static final String NOTIFICATION_TITLE       = "title";
    public static final String NOTIFICATION_IMAGE_URL   = "image_url";
    public static final String NOTIFICATION_SHOP_ID     = "shop_id";
    public static final String NOTIFICATION_UTM         = "utm";
    public static final String LINK_FIRST_LOAD_WEBSITE = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_users&view=profile");;
    public static final String LINK_NOTIFICATION = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_jchat");;
    public static final String MESSENGERS = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_jchat&initialize=1");
    public static final String SAVEENTITY = API_URL1.concat("index.php?format=json&os=android&tmpl=component&ignoreMessages=true&option=com_jchat");

    private EndPoints() {}
}
