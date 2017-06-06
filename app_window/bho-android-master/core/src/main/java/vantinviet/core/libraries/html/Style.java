package vantinviet.core.libraries.html;

import android.content.Context;
import android.view.Gravity;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.ArrayList;
import java.util.Timer;

import timber.log.Timber;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

import static com.facebook.FacebookSdk.getApplicationContext;


/**
 * Created by cuongnd on 14/04/2017.
 */

public class Style {

    public static String[] get_list_styles() {
        String[] list_styles = new String[]{
                "text-center",
                "text-left",
                "text-right",
                "pull-left",
                "pull-right"
        };
        return  list_styles;
    }
    public void style_text_center(TagHtml tag, LinearLayout linear_layout){
        linear_layout.setGravity(Gravity.CENTER | Gravity.CENTER_HORIZONTAL);
    }
    public void style_text_left(TagHtml tag, LinearLayout linear_layout){
        linear_layout.setGravity(Gravity.LEFT | Gravity.CENTER_HORIZONTAL);
    }
    public void style_text_right(TagHtml tag, LinearLayout linear_layout){
        linear_layout.setGravity(Gravity.RIGHT | Gravity.CENTER_HORIZONTAL);
    }
    public void style_pull_left(TagHtml tag, LinearLayout linear_layout){
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.FILL_PARENT,
                LinearLayout.LayoutParams.WRAP_CONTENT);
        //linear_layout.setLayoutParams(params);
    }
    public void style_pull_right(TagHtml tag, LinearLayout linear_layout){
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT,
                LinearLayout.LayoutParams.MATCH_PARENT);
        //linear_layout.setLayoutParams(params);
    }

    public static void apply_style(LinearLayout linear_layout, String class_name) {
        if(class_name==null || class_name.isEmpty()){
            return;
        }
        JApplication app= JFactory.getApplication();
        String package_name = app.getContext().getPackageName();
        class_name=class_name.replaceAll("-","_");
        String[] splited_class_name = class_name.split("\\s+");

        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if(!item_class.equals(""))
            {
                Class<?> class_style = null;
                try {
                    class_style = Class.forName(String.format("%s.styles.%s_style",package_name,item_class));
                    Constructor<?> cons = class_style.getConstructor(LinearLayout.class);
                    cons.newInstance(linear_layout);
                } catch (ClassNotFoundException e) {
                    //e.printStackTrace();
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



    }
    public static void apply_style_module(LinearLayout linear_layout, String class_name) {
        if(class_name==null || class_name.isEmpty()){
            return;
        }
        class_name=class_name.replaceAll("-","_");
        JApplication app= JFactory.getApplication();
        String package_name = app.getContext().getPackageName();
        String[] splited_class_name = class_name.split("\\s+");

        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if(!item_class.equals(""))
            {
                Class<?> class_style = null;
                try {
                    class_style = Class.forName(String.format("%s.styles.modules.%s_style",package_name,item_class));
                    Constructor<?> cons = class_style.getConstructor(LinearLayout.class);
                    cons.newInstance(linear_layout);
                } catch (ClassNotFoundException e) {
                    //e.printStackTrace();
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



    }
}
