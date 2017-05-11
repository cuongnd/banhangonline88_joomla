package vantinviet.core.libraries.joomla.form;

import android.content.Context;
import android.view.View;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.HashMap;
import java.util.Map;

import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.md5;

/**
 * Created by cuongnd on 6/11/2016.
 */
public abstract class JFormField {
    private static JFormField ourInstance;
    public final Context context;
    protected String fieldName;
    protected String type="text";
    protected String label;
    public JFormField option;
    protected String value;
    protected String group;
    private File jarFile;
    private static String p_package="vantinviet.core.libraries.joomla.form.fields";
    public static Map<String, JFormField> map_form_field = new HashMap<String, JFormField>();
    public String name;
    protected String key;
    public int key_id;
    public String value_default="";
    private String aDefault;
    private boolean showLabel;

    public JFormField(){
        JApplication app= JFactory.getApplication();
        this.context=app.getCurrentActivity();
    }
    private static String getStanderFieldName(String fieldName) {
        String[] listField=new String[]{
                "text-Text",
                "textview-TextView",
                "button-Button",
                "rangeofintegers-RangeOfIntegers",
                "player-Player",
                "link-Link",
                "videoplayerlink-VideoPlayerLink"
        };
        for (int i=0;i<listField.length;i++)
        {
            String field=listField[i];
            String[] a_field = field.split("-");
            if(a_field[0].equals(fieldName))
            {
                return a_field[1];
            }
        }
        return "";
    }

    public void setName(String fieldName){
        this.fieldName=fieldName;
    }


    public abstract View getInput();


    public static JFormField getFormField(String type,String key) {
        JFormField formField = null;

        String className=p_package+".JFormField"+getStanderFieldName(type);
        System.out.println("className:"+className);
        try {
            Class<?> selected_class = Class.forName(className);
            Constructor<?> cons = selected_class.getConstructor();
            formField = (JFormField) cons.newInstance();


        } catch (ClassNotFoundException e) {
            e.printStackTrace();
            return formField;
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        }
        return formField;
    }

    public String getValue() {
        return value;
    }

    public static JFormField getInstance(JFormField field, String type, String name, String group, String value) {
        String label = "";
        String value_default = "";
        label = field.getLabel();
        value_default = field.getDefault();
        JMenu menu = JFactory.getMenu();
        JSONObject menuActive = menu.getMenuActive();
        String active_menu_item_id = "";
        try {
            active_menu_item_id = menuActive.getString("id");
        } catch (JSONException e) {
            e.printStackTrace();
        }
        System.out.println("active_menu_item_id:" + active_menu_item_id);
        String key = type + name + active_menu_item_id + value_default + label + group;
        key = md5.encryptMD5(key);
        JFormField form_field = (JFormField) map_form_field.get(key);
        if (form_field == null) {
            form_field = getFormField(type, key);
            form_field.setKey(key);
            form_field.setOption(field);
            form_field.setType(type);
            form_field.setName(name);
            form_field.setGroup(group);
            form_field.setValue(value);

            map_form_field.put(key, form_field);

        }
        return form_field;
    }

    public String getType() {
        return type;
    }

    public String getName() {
        return name;
    }

    public String getLabel() {
        return label;
    }

    public String getDefault() {
        return aDefault;
    }

    public void setKey(String key) {
        this.key = key;
    }

    public void setOption(JFormField option) {
        this.option = option;
    }

    public void setType(String type) {
        this.type = type;
    }

    public void setGroup(String group) {
        this.group = group;
    }

    public void setValue(String value) {
        this.value = value;
    }

    public boolean getShowLabel() {
        return showLabel;
    }

    public JFormField getOption() {
        return option;
    }
}
