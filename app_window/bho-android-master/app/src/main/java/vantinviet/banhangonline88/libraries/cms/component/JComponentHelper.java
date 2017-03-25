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
import com.vantinviet.vtv.R;
import com.vantinviet.vtv.configuration.JConfig;
import com.vantinviet.vtv.libraries.android.registry.JRegistry;
import com.vantinviet.vtv.libraries.cms.menu.JMenu;
import com.vantinviet.vtv.libraries.joomla.JFactory;
import com.vantinviet.vtv.libraries.joomla.cache.cache;
import com.vantinviet.vtv.libraries.joomla.filesystem.JPath;
import com.vantinviet.vtv.libraries.joomla.form.JFormField;
import com.vantinviet.vtv.libraries.joomla.form.fields.JFormFieldButton;
import com.vantinviet.vtv.libraries.joomla.input.JInput;
import com.vantinviet.vtv.libraries.legacy.application.JApplication;
import com.vantinviet.vtv.libraries.legacy.request.JRequest;
import com.vantinviet.vtv.libraries.utilities.JUtilities;
import com.vantinviet.vtv.libraries.utilities.md5;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import de.codecrafters.tableview.SortableTableView;
import de.codecrafters.tableview.TableDataAdapter;
import de.codecrafters.tableview.listeners.TableDataClickListener;
import de.codecrafters.tableview.toolkit.SimpleTableHeaderAdapter;
import de.codecrafters.tableview.toolkit.SortStateViewProviders;
import de.codecrafters.tableview.toolkit.TableDataRowColorizers;

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

    public static void renderComponent(Context context, JSONObject json_element, LinearLayout linear_layout) throws JSONException {
        component_json_element = json_element;
        linear_layout = linear_layout;
        JMenu menu = JFactory.getMenu();
        JSONObject menu_active = menu.getMenuActive();
        JRegistry menu_active_params = JRegistry.getParams(menu_active);
        String android_render = menu_active_params.get("android_render", "auto", "String");
        if (android_render.equals("auto")) {
            auto_render_component(context, json_element, linear_layout);
        } else {
            customizable_render_component(context, json_element, linear_layout);
        }

    }

    private static void customizable_render_component(Context context, JSONObject json_element, LinearLayout linear_layout) {
    }

    private static void auto_render_component(Context context, JSONObject json_element, LinearLayout linear_layout) throws JSONException {
        JMenu menu = JFactory.getMenu();
        JSONObject menu_active = menu.getMenuActive();
        JRegistry menu_active_params = JRegistry.getParams(menu_active);
        android_render_form_type = menu_active_params.get("android_render_form_type", "list", "String");
        JComponentHelper.android_render_form_type=android_render_form_type;
        System.out.println("android_render_form_type:" + android_render_form_type);
        if (android_render_form_type.equals(JComponentHelper.ANDROID_RENDER_FORM_TYPE_LIST)) {
            auto_render_component_list_type(context, json_element, linear_layout);
        } else {
            auto_render_component_form_type(context, json_element, linear_layout);
        }

    }

    private static void auto_render_component_form_type(Context context, JSONObject json_element, View linear_layout) {
        JApplication app = JFactory.getApplication();
        JInput input = app.input;
        String component = input.getString("option", "");
        String component_path = JPath.getComponentPath(component);
        try {
            View view_field;
            JSONArray item_fields = json_element.has("item_fields") ? json_element.getJSONArray("item_fields") : new JSONArray();
            JSONObject item_json_object = json_element.has("item") ? json_element.getJSONObject("item") : new JSONObject();
            for (int i = 0; i < item_fields.length(); i++) {
                JSONObject field = item_fields.getJSONObject(i);
                if (field.has("name") && field.has("label")) {
                    String type = field.has("type") ? field.getString("type") : "text";
                    String name = field.getString("name");
                    String label = field.getString("label");
                    String group = "";
                    String value = "";
                    value = item_json_object.has(name) ? item_json_object.getString(name) : "";
                    System.out.println("value:" + value);
                    JFormField formField = JFormField.getInstance(field, type, name, group, value);
                    view_field = formField.getInput();
                    ((LinearLayout) linear_layout).addView(view_field);
                }
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

        try {
            View view_field;
            JSONArray list_control_item = json_element.has("list_control_item") ? json_element.getJSONArray("list_control_item") : new JSONArray();
            for (int i = 0; i < list_control_item.length(); i++) {
                JSONObject field = list_control_item.getJSONObject(i);
                if (field.has("name") && field.has("label")) {
                    String type = field.has("type") ? field.getString("type") : "text";
                    String name = field.getString("name");
                    String label = field.getString("label");
                    String group = "";
                    String value = "";
                    JFormField formField = JFormField.getInstance(field, type, name, group, value);
                    view_field = formField.getInput();

                    LayoutParams params = new LayoutParams(LayoutParams.FILL_PARENT, LayoutParams.WRAP_CONTENT);
                    params.setMargins(10, 0, 10, 0);

                    view_field.setLayoutParams(params);



                    bootstrap_button_group.addView(view_field);
                }
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }


        scroll_view.addView(bootstrap_button_group);
        LayoutParams params = new LayoutParams(LayoutParams.FILL_PARENT, LayoutParams.WRAP_CONTENT);
        params.setMargins(0, 30, 0, 30);

        scroll_view.setLayoutParams(params);
        scroll_view.setRight(0);
        ((LinearLayout) linear_layout).addView(scroll_view);






        try {
            JSONArray list_hidden_field_item = (JSONArray)(json_element.has("list_hidden_field_item") ? json_element.getJSONArray("list_hidden_field_item") : new JSONArray());
            JComponentHelper.list_hidden_field_item = list_hidden_field_item;
        } catch (JSONException e) {
            e.printStackTrace();
        }

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

    public static Map<String, String> getMapStringInputComponent(JFormFieldButton form_field_button) {
        Map<String, String> map_input_component = new HashMap<String, String>();
        if (android_render_form_type.equals("list")) {
            JSONObject option=form_field_button.option;
            try {
                String d_default= option.has("default") ?option.getString("default") : "";
                if(d_default.contains("="))
                {
                    String[] parts = d_default.split("=");
                    String part1 = parts[0];
                    String part2 = parts[1];
                    map_input_component.put(part1, part2);
                }

            } catch (JSONException e) {
                e.printStackTrace();
            }

        } else {
            try {
                JSONArray item_fields = component_json_element.has("item_fields") ? component_json_element.getJSONArray("item_fields") : new JSONArray();
                for (int i = 0; i < item_fields.length(); i++) {
                    JSONObject field = item_fields.getJSONObject(i);
                    if (field.has("name") && field.has("label")) {
                        String type = field.has("type") ? field.getString("type") : "text";
                        String name = field.getString("name");
                        String label = field.getString("label");
                        String group = "";
                        JFormField formField = JFormField.getInstance(field, type, name, group, "");
                        String value = formField.getValue();
                        map_input_component.put(name, value);
                    }
                }

            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
        return map_input_component;
    }

    public static JSONObject getParams(String component_name) {
        return null;
    }

    public static Map<String, String> getMapStringControl(JFormFieldButton button) {
        JSONArray list_hidden_field_item= JComponentHelper.android_render_form_type.equals(JComponentHelper.ANDROID_RENDER_FORM_TYPE_LIST)? JComponentHelper.list_hidden_field_list:JComponentHelper.list_hidden_field_item;
        Map<String, String> map_input_control = new HashMap<String, String>();
        boolean exists_option=false;
        for (int i=0;i<list_hidden_field_item.length();i++){
            try {
                JSONObject item=list_hidden_field_item.getJSONObject(i);
                String name = item.getString("name");
                if(name.equals("option"))
                {
                    exists_option=true;
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
        if(!exists_option)
        {
            String option=JRequest.getString("option","");
            map_input_control.put("option", option);
        }
        String str_value=button.getValue();
        System.out.println("button.value_default:"+button.value_default);
        System.out.println("str_value:"+str_value);
        str_value=str_value.equals("")||str_value==null?button.value_default:str_value;
        map_input_control.put(button.name,str_value );
        return map_input_control;
    }

    private static class TableClickListener implements TableDataClickListener {


        @Override
        public void onDataClicked(int rowIndex, Object clickedData) {
            String item_string = clickedData.toString();
            //Toast.makeText(MainActivity., item_string, Toast.LENGTH_SHORT).show();
        }
    }

    private static void auto_render_component_list_type(final Context context, JSONObject json_element, final LinearLayout linear_layout) throws JSONException {

        JApplication app = JFactory.getApplication();
        BootstrapButtonGroup bootstrap_button_group = new BootstrapButtonGroup(context);
        HorizontalScrollView scroll_view = new HorizontalScrollView(context);
        bootstrap_button_group.setBootstrapBrand(DefaultBootstrapBrand.PRIMARY);
        bootstrap_button_group.setOrientation(LinearLayout.HORIZONTAL);
        bootstrap_button_group.setRounded(false);
        bootstrap_button_group.setBootstrapSize(DefaultBootstrapSize.LG);
        scroll_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        try {
            View view_field;
            JSONArray list_control_item = json_element.has("list_control_list") ? json_element.getJSONArray("list_control_list") : new JSONArray();
            for (int i = 0; i < list_control_item.length(); i++) {
                JSONObject field = list_control_item.getJSONObject(i);
                if (field.has("name") && field.has("label")) {
                    String type = field.has("type") ? field.getString("type") : "text";
                    String name = field.getString("name");
                    String label = field.getString("label");
                    String group = "";
                    String value = "";
                    JFormField formField = JFormField.getInstance(field, type, name, group, value);
                    view_field = formField.getInput();
                    bootstrap_button_group.addView(view_field);
                }
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }


        scroll_view.addView(bootstrap_button_group);
        LayoutParams params = new LayoutParams(LayoutParams.FILL_PARENT, LayoutParams.WRAP_CONTENT);
        params.setMargins(0, 30, 0, 30);

        scroll_view.setLayoutParams(params);
        scroll_view.setRight(0);
        ((LinearLayout) linear_layout).addView(scroll_view);

        try {
            JSONArray list_hidden_field_list = json_element.has("list_hidden_field_list") ? json_element.getJSONArray("list_hidden_field_list") : new JSONArray();
            JComponentHelper.list_hidden_field_list = list_hidden_field_list;
        } catch (JSONException e) {
            e.printStackTrace();
        }


        SortableTableView<? extends Object> table_view = new SortableTableView<Object>(context);
        String columnFields = json_element.getString("columnFields");
        final JSONArray fields = new JSONArray(columnFields);
        List<String> list_column_title = new ArrayList<String>();
        columns = new ArrayList<String>();
        for (int i = 0; i < fields.length(); i++) {
            JSONObject field = fields.getJSONObject(i);
            if (field.has("label")) {
                String column_title = field.getString("label");
                list_column_title.add(column_title);
            }

            if (field.has("name")) {
                String column_name = field.getString("name");
                columns.add(column_name);

            }

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
        JSONArray items = json_element.getJSONArray("items");
        List<String> list_data = new ArrayList<String>();
        for (int i = 0; i < items.length(); i++) {
            JSONObject row = items.getJSONObject(i);
            list_data.add(row.toString());
        }

        table_view.setDataAdapter(new TableDataAdapter(context, list_data) {
            @Override
            public View getCellView(int rowIndex, int columnIndex, ViewGroup parentView) {
                Object item = getRowData(rowIndex);
                JSONObject item_json_object = new JSONObject();
                try {
                    item_json_object = new JSONObject(item.toString());
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                View view_field = new View(context);
                if (columnIndex < fields.length()) {
                    JSONObject field = null;
                    try {
                        field = (JSONObject) fields.getJSONObject(columnIndex);
                        if (field.has("name") && field.has("label")) {
                            String type = field.has("type") ? field.getString("type") : "text";
                            String name = field.getString("name");
                            String label = field.getString("label");
                            String group = "";
                            String value = "";
                            value = item_json_object.has(name)?item_json_object.getString(name):"";
                            name=name+"_"+String.valueOf(rowIndex);
                            System.out.println("value:" + value);
                            field.put("show_label", false);
                            JFormField formField = JFormField.getInstance(field, type, name, group, value);
                            view_field = formField.getInput();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }


                }
                return view_field;
            }
        });
        table_view.addDataClickListener(new TableClickListener());


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
