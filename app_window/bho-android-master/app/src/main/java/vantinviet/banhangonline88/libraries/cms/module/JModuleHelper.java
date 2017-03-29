package vantinviet.banhangonline88.libraries.cms.module;

import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
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
import vantinviet.banhangonline88.configuration.JConfig;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.entities.module.Params;
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
public class JModuleHelper {
    public static final String ANDROID_RENDER_FORM_HTML = "html";
    public static  String ANDROID_RENDER_FORM_TYPE_LIST = "list";
    public static Map<String, String> content_component = new HashMap<String, String>();
    public static List<String> columns;
    public static View linear_layout;
    public static JSONArray list_hidden_field_item;
    private static Map<String, String> mapStringInput;
    public static String android_render_form_type;
    private static JSONObject component_json_element;
    public static JSONArray list_hidden_field_list;


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

    public static void renderModule(Context context, Module module, LinearLayout linear_layout)  {

        Params params=module.getParams();
        String module_name=module.getModuleName();
        Timber.d("module: %s",module_name);
        Class<?> class_fragment = null;
        try {
            class_fragment = Class.forName("vantinviet.banhangonline88.modules." + module_name+"."+module_name);
            Constructor<?> cons = class_fragment.getConstructor(Module.class,LinearLayout.class);
            Object object = cons.newInstance(module,linear_layout);
        } catch (ClassNotFoundException e) {
            String android_render = params.getAndroidRender();
            if (android_render.equals("auto")) {
                auto_render_module(context, module, linear_layout);
            } else {
                customizable_render_module(context, module, linear_layout);
            }

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

    private static void customizable_render_module(Context context, Module module, LinearLayout linear_layout) {
    }

    private static void auto_render_module(Context context, Module module, LinearLayout linear_layout)  {
        Params params=module.getParams();
        android_render_form_type = params.get_android_render_form_type();
        System.out.println("android_render_form_type:" + android_render_form_type);
        if (android_render_form_type.equals(JModuleHelper.ANDROID_RENDER_FORM_TYPE_LIST)) {
            auto_render_module_list_type(context, module, linear_layout);
        } else if (android_render_form_type.equals(JModuleHelper.ANDROID_RENDER_FORM_HTML)){
            auto_render_module_form_html(context, module, linear_layout);
        }else {
            auto_render_module_form_type(context, module, linear_layout);
        }

    }

    private static void auto_render_module_form_type(Context context, Module module, View linear_layout) {
        JApplication app = JFactory.getApplication();
        JInput input = app.input;
        try {
            View view_field;
            ArrayList<JFormField> list_fields = module.getFields();
            JSONObject item = module.getItem();
            for (int i = 0; i < list_fields.size(); i++) {
                JFormField field = list_fields.get(i);
                String type = field.getType();
                String name = field.getName();
                String group = "";
                String value = "";
                value = item.has(name) ? item.getString(name) : "";
                System.out.println("value:" + value);
                JFormField formField = JFormField.getInstance(field, type, name, group, value);
                view_field = formField.getInput();
                ((LinearLayout) linear_layout).addView(view_field);
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }

        BootstrapButtonGroup bootstrap_button_group = new BootstrapButtonGroup(context);
        HorizontalScrollView scroll_view = new HorizontalScrollView(context);
        bootstrap_button_group.setBootstrapBrand(DefaultBootstrapBrand.PRIMARY);
        bootstrap_button_group.setOrientation(LinearLayout.HORIZONTAL);
        bootstrap_button_group.setRounded(false);
        bootstrap_button_group.setBootstrapSize(DefaultBootstrapSize.LG);
        scroll_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        View view_field;
        ArrayList<JFormField> list_control_item = module.getControlItems();
        for (int i = 0; i < list_control_item.size(); i++) {
            JFormField field = list_control_item.get(i);
            String type = field.getType();
            String name = field.getName();
            String group = "";
            String value = "";
            JFormField formField = JFormField.getInstance(field, type, name, group, value);
            view_field = formField.getInput();

            LayoutParams params = new LayoutParams(LayoutParams.FILL_PARENT, LayoutParams.WRAP_CONTENT);
            params.setMargins(10, 0, 10, 0);

            view_field.setLayoutParams(params);



            bootstrap_button_group.addView(view_field);
        }


        scroll_view.addView(bootstrap_button_group);
        LayoutParams params = new LayoutParams(LayoutParams.FILL_PARENT, LayoutParams.WRAP_CONTENT);
        params.setMargins(0, 30, 0, 30);

        scroll_view.setLayoutParams(params);
        scroll_view.setRight(0);
        ((LinearLayout) linear_layout).addView(scroll_view);


        String root_element = "html";
        //subRenderComponent.render_element(json_element, root_element, linear_layout, 0, 999);

    }
    private static void auto_render_module_form_html(Context context, Module module, View linear_layout) {

        String content=module.getContent();
        String header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">"
                + "<html>  <head>  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">"
                + "</head>  <body>";
        String footer = "</body></html>";

        WebView pageContent=new WebView(context);
        content=header + content + footer;
        pageContent.loadData(content, "text/html; charset=UTF-8", null);


        ((LinearLayout) linear_layout).addView(pageContent);

    }




    private static class TableClickListener implements TableDataClickListener {


        @Override
        public void onDataClicked(int rowIndex, Object clickedData) {
            String item_string = clickedData.toString();
            //Toast.makeText(MainActivity., item_string, Toast.LENGTH_SHORT).show();
        }
    }

    private static void auto_render_module_list_type(final Context context, Module module, final LinearLayout linear_layout)  {

        JApplication app = JFactory.getApplication();
        BootstrapButtonGroup bootstrap_button_group = new BootstrapButtonGroup(context);
        HorizontalScrollView scroll_view = new HorizontalScrollView(context);
        bootstrap_button_group.setBootstrapBrand(DefaultBootstrapBrand.PRIMARY);
        bootstrap_button_group.setOrientation(LinearLayout.HORIZONTAL);
        bootstrap_button_group.setRounded(false);
        bootstrap_button_group.setBootstrapSize(DefaultBootstrapSize.LG);
        scroll_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        View view_field;
        ArrayList<JFormField> list_control_item = module.getControlItems();
        for (int i = 0; i < list_control_item.size(); i++) {
            JFormField field = list_control_item.get(i);
            String type = field.getType();
            String name = field.getName();
            String label = field.getLabel();
            String group = "";
            String value = "";
            JFormField formField = JFormField.getInstance(field, type, name, group, value);
            view_field = formField.getInput();
            bootstrap_button_group.addView(view_field);
        }

        scroll_view.addView(bootstrap_button_group);
        LayoutParams params = new LayoutParams(LayoutParams.FILL_PARENT, LayoutParams.WRAP_CONTENT);
        params.setMargins(0, 30, 0, 30);

        scroll_view.setLayoutParams(params);
        scroll_view.setRight(0);
        ((LinearLayout) linear_layout).addView(scroll_view);


        SortableTableView<? extends Object> table_view = new SortableTableView<Object>(context);
        ArrayList<JFormField> columnFields = module.getColumnFields();
        List<String> list_column_title = new ArrayList<String>();
        columns = new ArrayList<String>();
        for (int i = 0; i < columnFields.size(); i++) {
            JFormField field = columnFields.get(i);
            String column_title = field.getLabel();
            list_column_title.add(column_title);
            String column_name = field.getName();
            columns.add(column_name);

        }
        System.out.println(list_column_title.toString());
        String[] array_column_title = new String[list_column_title.size()];
        list_column_title.toArray(array_column_title);
        SimpleTableHeaderAdapter simpleTableHeaderAdapter = new SimpleTableHeaderAdapter(context, array_column_title);

        simpleTableHeaderAdapter.setTextColor(context.getResources().getColor(R.color.table_header_text));
        table_view.setHeaderAdapter(simpleTableHeaderAdapter);

        int rowColorEven = context.getResources().getColor(R.color.table_data_row_even);
        int rowColorOdd = context.getResources().getColor(R.color.table_data_row_odd);
        table_view.setDataRowColoriser(TableDataRowColorizers.alternatingRows(rowColorEven, rowColorOdd));
        table_view.setHeaderSortStateViewProvider(SortStateViewProviders.brightArrows());

        table_view.setColumnWeight(0, 2);
        table_view.setColumnWeight(1, 3);
        table_view.setColumnWeight(2, 3);
        table_view.setColumnWeight(3, 2);
        table_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, 1000));
        ArrayList<String> items = module.getItems();
        List<String> list_data = new ArrayList<String>();
        for (int i = 0; i < items.size(); i++) {
            String row = items.get(i);
            list_data.add(row.toString());
        }



        ((LinearLayout) linear_layout).addView(table_view);

        System.out.println(columnFields);
        abstract class subRenderComponent {
            public abstract void render_element(JSONObject array_element, String root_element, View linear_layout, int level, int max_level) throws JSONException;
        }
        final subRenderComponent subRenderComponent = new subRenderComponent() {

            @Override
            public void render_element(JSONObject json_element, String root_element, View linear_layout, int level, int max_level) throws JSONException {
                int level1 = level + 1;
                Iterator<?> keys = json_element.keys();
                while (keys.hasNext()) {
                    String key = (String) keys.next();
                    System.out.println(json_element.get(key));
                    if (json_element.get(key) instanceof JSONObject) {
                        JSONObject a_object = (JSONObject) json_element.get(key);
                        render_element(a_object, root_element, linear_layout, level1, max_level);
                    }
                }


            }
        };
        String root_element = "html";
        //subRenderComponent.render_element(json_element, root_element, linear_layout, 0, 999);






    }

}
