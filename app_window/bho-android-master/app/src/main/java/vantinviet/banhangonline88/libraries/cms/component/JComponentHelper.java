package vantinviet.banhangonline88.libraries.cms.component;

import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.widget.HorizontalScrollView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;

import com.beardedhen.androidbootstrap.BootstrapButtonGroup;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapSize;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import de.codecrafters.tableview.SortableTableView;
import de.codecrafters.tableview.listeners.TableDataClickListener;
import de.codecrafters.tableview.toolkit.SimpleTableHeaderAdapter;
import de.codecrafters.tableview.toolkit.SortStateViewProviders;
import de.codecrafters.tableview.toolkit.TableDataRowColorizers;
import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.configuration.JConfig;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.entities.module.Params;
import vantinviet.banhangonline88.libraries.android.registry.JRegistry;
import vantinviet.banhangonline88.libraries.cms.menu.JMenu;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.cache.cache;
import vantinviet.banhangonline88.libraries.joomla.form.JFormField;
import vantinviet.banhangonline88.libraries.joomla.input.JInput;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.libraries.utilities.JUtilities;
import vantinviet.banhangonline88.libraries.utilities.md5;

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

            content = cache.get_content_component(md5_link);
            if (content == null || content.isEmpty()) {
                content = call_ajax_content_component(link);
                cache.set_content_component(md5_link, content);
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

    public static void renderComponent(Context context, LinearLayout linear_layout)  {
        Class<?> class_component = null;
        String option=input.getString("option","com_content");
        try {
            class_component = Class.forName(String.format("vantinviet.banhangonline88.components.%s.%s",option,option.substring(4)));
            Constructor<?> cons = class_component.getConstructor(LinearLayout.class);
            Object object = cons.newInstance(linear_layout);
        } catch (ClassNotFoundException e) {

        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.getCause().printStackTrace();
        } catch (java.lang.InstantiationException e) {
            e.printStackTrace();
        }
    }









}
