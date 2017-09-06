package vantinviet.core.libraries.cms.component;

import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.configuration.JConfig;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.cache.JCache;
import vantinviet.core.libraries.joomla.input.JInput;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.libraries.utilities.md5;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class JComponentHelper {
    public static  String ANDROID_RENDER_FORM_TYPE_LIST = "list";
    public static Map<String, String> content_component = new HashMap<String, String>();
    public static List<String> columns;
    public static View linear_layout;
    public static JSONArray list_hidden_field_item;
    private static Map<String, String> mapStringInput;
    public static String android_render_form_type;
    private static JSONObject component_json_element;
    public static JSONArray list_hidden_field_list;
    public static JInput input= JInput.getInstance();;


    public static String getContentComponent(String link) {
        System.out.println(link);
        JConfig config = JFactory.getConfig();
        String content = "";
        String md5_link = md5.encryptMD5(link);
        int caching = config.caching;
        if (caching == 1) {

            content = JCache.get_content_component(md5_link);
            if (content == null || content.isEmpty()) {
                content = call_ajax_content_component(link);
                JCache.set_content_component(md5_link, content);
            }
            return content;

        } else {
            content = content_component.get(md5_link);
            if (content == null || content.isEmpty()) {
                content = call_ajax_content_component(link);
                content_component.put(md5_link, content);
            }
        }
        return content;
    }

    private static String call_ajax_content_component(String link) {
        String content = JUtilities.callURL(link);
        if (content.toLowerCase().contains("link_redirect")) {
            try {
                JSONObject json_object_content = new JSONObject(content);
                String link_redirect = json_object_content.getString("link_redirect");
                System.out.println(link_redirect);
                return call_ajax_content_component(link_redirect);
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
        return content;


    }

    public static void renderComponent(Context context, LinearLayout linear_layout, int component_width)  {
        Class<?> class_component = null;
        Timber.d("input %s",input.toString());
        String option=input.getString("option","com_content");
        try {
            class_component = Class.forName(String.format("vantinviet.core.components.%s.%s",option,option.substring(4)));
            Constructor<?> cons = class_component.getConstructor(LinearLayout.class,int.class);
            Object object = cons.newInstance(linear_layout,component_width);
        } catch (ClassNotFoundException e) {

        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.getCause().printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        }
    }









}
