package vantinviet.banhangonline88.libraries.html;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.drawable.GradientDrawable;
import android.support.v4.graphics.drawable.RoundedBitmapDrawable;
import android.text.TextUtils;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.lang.reflect.Array;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.components.com_users.views.profile.view;
import vantinviet.banhangonline88.entities.template.bootstrap.Column;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.libraries.utilities.ImageConverter;
import vantinviet.banhangonline88.libraries.utilities.JUtilities;

import static android.view.Gravity.CENTER;
import static android.view.ViewGroup.LayoutParams.MATCH_PARENT;
import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;

/**
 * Created by cuongnd on 11/04/2017.
 */

public class TagHtml {
    String tag = "";
    String lang = "";
    String html = "";
    String class_name = "";
    String src = "";
    String type = "";
    private ArrayList<TagHtml> children;
    private ArrayList<String> apply_class;
    private ArrayList<String> apply_direct_class_path;
    private static ArrayList<String> _list_allow_tag;
    private String class_path;
    private int tag_width;
    private int tag_height;


    public static ArrayList<String> get_list_allow_tag() {
        ArrayList<String> list_allow_tag = new ArrayList<String>();
        list_allow_tag.add("row");
        list_allow_tag.add("column");
        list_allow_tag.add("h1");
        list_allow_tag.add("h2");
        list_allow_tag.add("h3");
        list_allow_tag.add("h4");
        list_allow_tag.add("h5");
        list_allow_tag.add("h6");
        list_allow_tag.add("img");
        list_allow_tag.add("image_button");
        list_allow_tag.add("div");
        list_allow_tag.add("icon");
        list_allow_tag.add("button_icon");
        return list_allow_tag;
    }




    public static int getDefaultColumnWidth(TagHtml tag) {
        ArrayList<Column> list_class_column = Column.get_list_default_class_column_width();
        String class_name = tag.getClass_name();
        class_name=class_name.toLowerCase();
        String[] splited_class_name = class_name.split("\\s+");
        int column_width=0;
        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if (list_class_column != null) for (Column column : list_class_column) {
                if (item_class.equals(column.getDefault_class_column_width())) {
                    column_width= Integer.parseInt(column.getDefault_span());
                    break;
                }
            }
            if(column_width!=0){
                break;
            }
        }
        return column_width;
    }

    public static int getDefaultColumnOffset(TagHtml tag) {

        ArrayList<Column> list_class_column_offset = Column.get_list_default_class_column_offset();
        String class_name = tag.getClass_name();
        class_name=class_name.toLowerCase();
        String[] splited_class_name = class_name.split("\\s+");
        int column_offset=0;
        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if (list_class_column_offset != null) for (Column column : list_class_column_offset) {
                if (item_class.equals(column.getDefault_class_column_offset())) {
                    column_offset= column.get_Default_offset();
                    break;
                }
            }
            if(column_offset!=0){
                break;
            }
        }
        return column_offset;
    }

    public static void set_style(TagHtml tag, LinearLayout linear_layout, String class_path,Map<String, StyleSheet> list_style_sheet) {
        ArrayList<StyleSheet> list_stylesheet = get_stylesheet_apply(tag,class_path,list_style_sheet);
        ArrayList<String> list_apply_direct_class_path = tag.getApply_direct_class_path();

        for (StyleSheet item_stylesheet: list_stylesheet) {
            Timber.d("tag %s",tag.getTagName());
            Timber.d("class_path %s",class_path);
            Timber.d("item_stylesheet %s",item_stylesheet.toString());
            String current_class_path=item_stylesheet.getClass_path().trim();
            Timber.d("list_apply_direct_class_path %s",list_apply_direct_class_path.toString());
            Timber.d("current_class_path item_stylesheet \"%s\"",current_class_path);
            if(JUtilities.in_array(list_apply_direct_class_path,current_class_path)){
                Timber.d("apply_direct_class_path true");
                item_stylesheet.apply_style(linear_layout,true,tag);
            }else {
                item_stylesheet.apply_style(linear_layout,false,tag);
                Timber.d("apply_direct_class_path false");
            }

        }

    }
    public static void set_style_button_icon(TagHtml tag, Button button,  String class_path, Map<String, StyleSheet> list_style_sheet) {
        ArrayList<StyleSheet> list_stylesheet = get_stylesheet_apply(tag,class_path,list_style_sheet);
        ArrayList<String> list_apply_direct_class_path = tag.getApply_direct_class_path();
        for (StyleSheet item_stylesheet: list_stylesheet) {
            Timber.d("tag %s",tag.getTagName());
            Timber.d("class_path %s",class_path);
            Timber.d("item_stylesheet %s",item_stylesheet.toString());
            String current_class_path=item_stylesheet.getClass_path().trim();
            Timber.d("list_apply_direct_class_path %s",list_apply_direct_class_path.toString());
            Timber.d("current_class_path item_stylesheet \"%s\"",current_class_path);
            if(JUtilities.in_array(list_apply_direct_class_path,item_stylesheet.getClass_path())){
                item_stylesheet.apply_style_button_icon(button,true,tag);
                Timber.d("apply_direct_class_path true");
            }else {
                item_stylesheet.apply_style_button_icon(button,false,tag);
                Timber.d("apply_direct_class_path false");
            }

        }
    }
    public String getType(){
        return type;
    }
    public static void set_style(TagHtml tag, ImageView image_view,String class_path,Map<String, StyleSheet> list_style_sheet) {
        ArrayList<StyleSheet> list_stylesheet = get_stylesheet_apply(tag,class_path,list_style_sheet);
        ArrayList<String> list_apply_direct_class_path = tag.getApply_direct_class_path();
        for (StyleSheet item_stylesheet: list_stylesheet) {
            Timber.d("tag %s",tag.getTagName());
            Timber.d("class_path %s",class_path);
            Timber.d("item_stylesheet %s",item_stylesheet.toString());
            String current_class_path=item_stylesheet.getClass_path().trim();
            Timber.d("list_apply_direct_class_path %s",list_apply_direct_class_path.toString());
            Timber.d("current_class_path item_stylesheet \"%s\"",current_class_path);
            if(JUtilities.in_array(list_apply_direct_class_path,item_stylesheet.getClass_path())){
                Timber.d("apply_direct_class_path true");
                item_stylesheet.apply_style(image_view,true,tag);
            }else {
                Timber.d("apply_direct_class_path false");
                item_stylesheet.apply_style(image_view,false,tag);

            }
        }
    }
    public static void set_style(TagHtml tag, TextView text_view,String class_path,Map<String, StyleSheet> list_style_sheet) {
        ArrayList<StyleSheet> list_stylesheet = get_stylesheet_apply(tag,class_path,list_style_sheet);
        ArrayList<String> list_apply_direct_class_path = tag.getApply_direct_class_path();
        for (StyleSheet item_stylesheet: list_stylesheet) {
            Timber.d("tag %s",tag.getTagName());
            Timber.d("class_path %s",class_path);
            Timber.d("item_stylesheet %s",item_stylesheet.toString());
            String current_class_path=item_stylesheet.getClass_path().trim();
            Timber.d("list_apply_direct_class_path %s",list_apply_direct_class_path.toString());
            Timber.d("current_class_path item_stylesheet \"%s\"",current_class_path);
            if(JUtilities.in_array(list_apply_direct_class_path,item_stylesheet.getClass_path())){
                Timber.d("apply_direct_class_path true");
                item_stylesheet.apply_style(text_view,true,tag);
            }else {
                Timber.d("apply_direct_class_path false");
                item_stylesheet.apply_style(text_view,false,tag);
            }

        }
    }
    public static void set_style(TagHtml tag, ImageButton image_button, String class_path,Map<String, StyleSheet> list_style_sheet) {
        ArrayList<StyleSheet> list_stylesheet = get_stylesheet_apply(tag,class_path,list_style_sheet);

        for (StyleSheet item_stylesheet: list_stylesheet) {
            Timber.d("tag %s",tag.getTagName());
            Timber.d("item_stylesheet %s",item_stylesheet.toString());
        }
        /*String[] splited_class_name = class_name.split("\\s+");
        String[] list_styles = Style.get_list_styles();
        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if (list_styles != null) for (String style : list_styles) {
                if (item_class.equals(style)) {
                    style = style.replaceAll("-", "_");
                    style = "style_" + style;
                    try {
                        Style cls = new Style();
                        Class c = cls.getClass();
                        Method style_method = c.getDeclaredMethod(style, TagHtml.class, LinearLayout.class);
                        style_method.invoke(cls, tag, linear_layout);
                        // production code should handle these exceptions more gracefully
                    } catch (InvocationTargetException x) {
                        Throwable cause = x.getCause();
                        System.err.format("%s() failed: %s%n", style, cause.getMessage());
                    } catch (Exception x) {
                        x.printStackTrace();
                    }
                }
            }
        }*/



    }
    private String[] get_list_style_item(String class_path){
        String[] splited_class_path= new String[] {};

        return splited_class_path;
    }

    private static ArrayList<StyleSheet> get_stylesheet_apply(TagHtml tag, String class_path, Map<String, StyleSheet> list_style_sheet) {
        ArrayList<StyleSheet> list_stylesheet = new ArrayList<StyleSheet>();
        String class_name = tag.getClass_name();
        class_name = class_name.toLowerCase();
        String current_class_tag = get_current_class_tag(tag);
        ArrayList<String> list_apply_class = new ArrayList<String>();
        list_apply_class = tag.getApply_class();



        for (String item_apply_class: list_apply_class) {
            StyleSheet item_style_sheet =  list_style_sheet.get(item_apply_class.toString());
            item_style_sheet.setClass_path(item_apply_class.toString());
            list_stylesheet.add(item_style_sheet);
        }
        return list_stylesheet;

    }


    @Override
    public String toString() {
        return "TagHtml{" +
                "tag=" + tag +
                ", class_name='" + class_name + '\'' +
                ", class_path='" + class_path + '\'' +
                ", lang='" + lang + '\'' +
                ", html='" + html + '\'' +
                '}';
    }

    public static void get_html_linear_layout(TagHtml tag, LinearLayout root_linear_layout, Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();
        int component_width = app.get_Component_width();
        int screen_size_height = WRAP_CONTENT;
        TagHtml body_tag = tag.getChildren().get(0);
        ArrayList<TagHtml> list_sub_tag = body_tag.getChildren();
        if (list_sub_tag != null) for (TagHtml sub_tag : list_sub_tag) {
            render_layout(sub_tag, root_linear_layout, component_width, screen_size_height,"",list_style_sheet);
        }
    }

    public static void render_layout(TagHtml tag, LinearLayout root_linear_layout, int screen_size_width, int screen_size_height,String class_path,Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();
        String type=tag.getType();
        String class_name="";
        String current_class_tag;
        LinearLayout.LayoutParams layout_params;
        if(TagHtml.is_row(tag)){
            layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
            layout_params.setMargins(0, 10, 0, 10);
            LinearLayout new_row_linear_layout = new LinearLayout(app.getCurrentActivity());
            new_row_linear_layout.setLayoutParams(layout_params);
            new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
            new_row_linear_layout.setId(R.id.row);
            current_class_tag = get_current_class_tag(tag);
            class_path+=" "+current_class_tag;
            //set_style(tag,new_row_linear_layout, "",list_style_sheet);
            ArrayList<TagHtml> list_column = tag.getChildren();
            if (list_column != null) for (TagHtml column_tag : list_column) {
                if(TagHtml.is_column(column_tag)) {
                    int column_width = TagHtml.getDefaultColumnWidth(column_tag);
                    int column_offset = TagHtml.getDefaultColumnOffset(column_tag);
                    column_width = screen_size_width * column_width / 12;
                    layout_params = new LinearLayout.LayoutParams(column_width, MATCH_PARENT);
                    column_offset = screen_size_width * column_offset / 12;
                    layout_params.setMargins(column_offset, 0, 0, 0);
                    LinearLayout new_column_linear_layout = new LinearLayout(app.getCurrentActivity());
                    new_column_linear_layout.setLayoutParams(layout_params);
                    new_column_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                    new_column_linear_layout.setId(R.id.column);
                    LinearLayout.LayoutParams new_vertical_wrapper_of_column_linear_layout_params = new LinearLayout.LayoutParams(column_width, MATCH_PARENT);
                    LinearLayout new_wrapper_of_column_linear_layout = new LinearLayout(app.getCurrentActivity());
                    new_wrapper_of_column_linear_layout.setLayoutParams(new_vertical_wrapper_of_column_linear_layout_params);
                    new_wrapper_of_column_linear_layout.setOrientation(LinearLayout.VERTICAL);
                    new_column_linear_layout.addView(new_wrapper_of_column_linear_layout);
                    new_row_linear_layout.addView(new_column_linear_layout);
                    current_class_tag = get_current_class_tag(column_tag);
                    class_path+=" "+current_class_tag;
                    //set_style(column_tag,new_wrapper_of_column_linear_layout,class_path,list_style_sheet);
                    ArrayList<TagHtml> list_sub_tag = column_tag.getChildren();

                    if (list_sub_tag != null) for (TagHtml item_sub_tag : list_sub_tag) {
                        render_layout(item_sub_tag, new_wrapper_of_column_linear_layout, column_width, screen_size_height,class_path,list_style_sheet);
                    }
                }else {
                    //sai cau truc bootstrap
                }
            }

            root_linear_layout.addView(new_row_linear_layout);
        }else {
            current_class_tag = get_current_class_tag(tag);
            class_path+=" "+current_class_tag;
            if(type.equals("div")) {
                LinearLayout sub_linear_layout = render_layout_by_tag_wrapper(tag, screen_size_width, screen_size_height, class_path, list_style_sheet);
                root_linear_layout.addView(sub_linear_layout);
                ArrayList<TagHtml> children_tag = tag.getChildren();
                if (children_tag != null) for (TagHtml sub_html : children_tag) {
                    render_layout(sub_html,sub_linear_layout, screen_size_width, screen_size_height,class_path,list_style_sheet);
                }
            }else{
                View sub_view_layout = render_layout_by_tag_view(tag, screen_size_width, screen_size_height, class_path, list_style_sheet);
                root_linear_layout.addView(sub_view_layout);
            }

        }

    }

    private static String get_current_class_tag(TagHtml tag) {
        String class_name=tag.getClass_name();
        String[] splited_class_name = class_name.split("\\s+");
        String tag_name=tag.getTagName();
            String current_class_tag=tag_name;
        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if(!item_class.equals(""))
            {
                current_class_tag+="."+item_class;
            }
        }
        return current_class_tag;
    }
    public static boolean is_column(TagHtml tag) {
        String class_name=tag.getType();
        return class_name.indexOf("column") != -1;
    }



    private static LinearLayout render_layout_by_tag_wrapper(TagHtml tag, int tag_width, int tag_height, String class_path, Map<String, StyleSheet> list_style_sheet) {
        Timber.d("class_path: %s ", class_path);
        tag.setClass_path(class_path);
        tag.setTag_width(tag_width);
        tag.setTag_height(tag_height);
        String class_name = tag.getClass_name();
        String tag_name = tag.getTagName().toLowerCase();
        JApplication app = JFactory.getApplication();
        ArrayList<String> list_allow_tag = get_list_allow_tag();
        boolean allow_tag=false;
        String element_type=tag.getType().toLowerCase().trim();
        if(list_allow_tag.contains(element_type)){
            element_type = "render_tag_" + element_type;
            try {
                Class<?> c = tag.getClass();

                Method tag_method = c.getDeclaredMethod(element_type, TagHtml.class, int.class, int.class, String.class, Map.class);
                return (LinearLayout) tag_method.invoke(tag, tag, tag_width, tag_height, class_path, list_style_sheet);
                // production code should handle these exceptions more gracefully
            } catch (InvocationTargetException x) {
                Throwable cause = x.getCause();
                System.err.format("%s() failed: %s%n", element_type, cause.getMessage());
            } catch (Exception x) {
                x.printStackTrace();
            }
            //method = TagHtml.class.getDeclaredMethod(tag,TagHtml.class, LinearLayout.class,int.class,int.class);
            //return (LinearLayout)method.invoke(TagHtml.class, html,root_linear_layout,screen_size_width,screen_size_height);
            allow_tag=true;
        }else{
            Timber.d("tag %s type %s class_path %s don't render",tag.getTagName(),tag.getType(),tag.getClass_path());
        }
        return new LinearLayout(app.getCurrentActivity());
    }
    private static View render_layout_by_tag_view(TagHtml tag, int tag_width, int tag_height, String class_path, Map<String, StyleSheet> list_style_sheet) {
        Timber.d("class_path: %s ", class_path);
        tag.setClass_path(class_path);
        tag.setTag_width(tag_width);
        tag.setTag_height(tag_height);
        String class_name = tag.getClass_name();
        String tag_name = tag.getTagName().toLowerCase();
        JApplication app = JFactory.getApplication();
        ArrayList<String> list_allow_tag = get_list_allow_tag();
        boolean allow_tag=false;
        String element_type=tag.getType().toLowerCase().trim();
        if(list_allow_tag.contains(element_type)){
            element_type = "render_tag_" + element_type;
            try {
                Class<?> c = tag.getClass();

                Method tag_method = c.getDeclaredMethod(element_type, TagHtml.class, int.class, int.class, String.class, Map.class);
                return (View)tag_method.invoke(tag, tag, tag_width, tag_height, class_path, list_style_sheet);
                // production code should handle these exceptions more gracefully
            } catch (InvocationTargetException x) {
                Throwable cause = x.getCause();
                System.err.format("%s() failed: %s%n", element_type, cause.getMessage());
            } catch (Exception x) {
                x.printStackTrace();
            }
            //method = TagHtml.class.getDeclaredMethod(tag,TagHtml.class, LinearLayout.class,int.class,int.class);
            //return (LinearLayout)method.invoke(TagHtml.class, html,root_linear_layout,screen_size_width,screen_size_height);
            allow_tag=true;
        }else{
            Timber.d("tag %s type %s class_path %s don't render",tag.getTagName(),tag.getType(),tag.getClass_path());
        }
        return new LinearLayout(app.getCurrentActivity());
    }

    private static boolean check_has_tag(TagHtml current_tag, String class_name, String tag) {
        class_name = class_name.toLowerCase();
        String[] splited_class_name = class_name.split("\\s+");
        tag = tag.toLowerCase();
        if (tag == "column") {
            ArrayList<Column> list_default_class_column_width = Column.get_list_default_class_column_width();
            if (list_default_class_column_width != null) for (Column column : list_default_class_column_width) {
                if (class_name.toLowerCase().contains(column.getDefault_class_column_width())) {
                    return true;
                }
            }
            return false;
        } else if (Arrays.asList(splited_class_name).contains(tag)) {
            //Timber.d("class_name: %s,tag: %s ",class_name,tag);
            return true;
        } else if(!current_tag.getClass_name().contains("row") && current_tag.getTagName().equals("div")) {
            return true;
        }else{
            return false;
        }

    }
    private static View render_tag_h4(TagHtml tag, int screen_size_width, int screen_size_height, String class_path,Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();
        boolean debug = VTVConfig.getDebug();
        String html_content = tag.get_Html_content();
        TextView text_view_h4 = new TextView(app.getCurrentActivity());
        text_view_h4.setText(html_content);
        LinearLayout.LayoutParams layout_params_text_view_h4;
        layout_params_text_view_h4 = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);
        text_view_h4.setLayoutParams(layout_params_text_view_h4);
        text_view_h4.setGravity(Gravity.CENTER_VERTICAL);
        set_style(tag,text_view_h4,class_path,list_style_sheet);
        return text_view_h4;
    }
    private static View render_tag_h2(TagHtml tag, int screen_size_width, int screen_size_height, String class_path,Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();

        boolean debug = VTVConfig.getDebug();

        String html_content = tag.get_Html_content();
        TextView text_view_h2 = new TextView(app.getCurrentActivity());
        text_view_h2.setText(html_content);

        LinearLayout.LayoutParams layout_params_text_view_h2;
        layout_params_text_view_h2 = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);
        text_view_h2.setLayoutParams(layout_params_text_view_h2);
        text_view_h2.setGravity(Gravity.CENTER_VERTICAL);
        set_style(tag,text_view_h2,class_path,list_style_sheet);
        return text_view_h2;
    }
    private static View render_tag_icon(TagHtml tag, int screen_size_width, int screen_size_height,String class_path,Map<String, StyleSheet> list_style_sheet) {

        JApplication app = JFactory.getApplication();

        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        String icon_name = tag.get_Icon_name(tag);
        ImageView image_view_icon = new ImageView(app.getCurrentActivity());
        Timber.d("PackageName %s",app.getCurrentActivity().getPackageName());
        Timber.d("icon_name %s",icon_name);
        int resID = app.getCurrentActivity().getResources().getIdentifier(icon_name , "drawable", "vantinviet.banhangonline88");

        // Set the ImageView image as drawable object
        image_view_icon.setImageResource(resID);




        set_style(tag,image_view_icon,class_path,list_style_sheet);
        return image_view_icon;
    }
    private static View render_tag_button_icon(TagHtml tag, int screen_size_width, int screen_size_height,String class_path,Map<String, StyleSheet> list_style_sheet) {

        JApplication app = JFactory.getApplication();
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, WRAP_CONTENT);
        String icon_name = tag.get_Icon_name(tag);
        Button image_button_icon = new Button(app.getCurrentActivity());
        int resID = app.getCurrentActivity().getResources().getIdentifier(icon_name , "drawable", "vantinviet.banhangonline88");
        image_button_icon.setCompoundDrawablesWithIntrinsicBounds(resID,0,0,0);
        image_button_icon.setGravity(CENTER);
        image_button_icon.setBackgroundColor(0);
        String content=tag.get_Html_content();
        image_button_icon.setText(content);
        set_style_button_icon(tag,image_button_icon,class_path,list_style_sheet);
        return image_button_icon;
    }
    private static LinearLayout render_tag_div(TagHtml tag, int screen_size_width, int screen_size_height, String class_path,Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(MATCH_PARENT, MATCH_PARENT);
        LinearLayout new_div_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_div_linear_layout.setLayoutParams(layout_params);
        new_div_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
        String html_content = tag.get_Html_content();
        if(!html_content.equals("")) {
            TextView text_view_content = new TextView(app.getCurrentActivity());
            text_view_content.setText(html_content);
            new_div_linear_layout.addView(text_view_content);
        }
        set_style(tag,new_div_linear_layout,class_path,list_style_sheet);
       /* LinearLayout new_column_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_column_linear_layout.setLayoutParams(layout_params);
        new_column_linear_layout.setOrientation(LinearLayout.VERTICAL);
        LinearLayout.LayoutParams new_vertical_wrapper_of_column_linear_layout_params = new LinearLayout.LayoutParams(screen_size_width, MATCH_PARENT);
        LinearLayout new_wrapper_of_column_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_wrapper_of_column_linear_layout.setLayoutParams(new_vertical_wrapper_of_column_linear_layout_params);
        new_wrapper_of_column_linear_layout.setOrientation(LinearLayout.VERTICAL);
        new_column_linear_layout.addView(new_wrapper_of_column_linear_layout);
        new_div_linear_layout.addView(new_column_linear_layout);
        set_style(tag,new_div_linear_layout,class_path,list_style_sheet);*/
        return new_div_linear_layout;
    }

    private static View render_tag_img(TagHtml tag, int screen_size_width, int screen_size_height,String class_path,Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(MATCH_PARENT, MATCH_PARENT);

        ImageView image_view = new ImageView(app.getCurrentActivity());
        String src = tag.getSrc();
        Picasso.with(app.getCurrentActivity()).load(src).into(image_view);
        set_style(tag,image_view,class_path,list_style_sheet);
        return image_view;
    }
    private static View render_tag_image_button(TagHtml html, int screen_size_width, int screen_size_height,String class_path,Map<String, StyleSheet> list_style_sheet) {
        JApplication app = JFactory.getApplication();
        boolean debug = VTVConfig.getDebug();
        ImageButton image_button = new ImageButton(app.getCurrentActivity());
        String src = html.getSrc();
        Picasso.with(app.getCurrentActivity()).load(src).into(image_button);
        return image_button;
    }

    public ArrayList<TagHtml> getChildren() {
        return children;
    }

    public String getTagName() {
        return tag;
    }

    public String getClass_name() {
        return class_name.trim();
    }

    public String get_Html_content() {
        return html;
    }

    public static boolean is_row(TagHtml tag) {
        String class_name=tag.getType();
        return class_name.indexOf("row") != -1;
    }

    public String getSrc() {
        return src;
    }

    public String get_Icon_name(TagHtml tag) {
        String class_name = tag.getClass_name();
        class_name=class_name.toLowerCase();
        String[] splited_class_name = class_name.split("\\s+");
        String icon_class_name="";
        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if (item_class.contains("icon-")) {
                icon_class_name=item_class;
                break;
            }
        }
        icon_class_name=icon_class_name.substring(5);
        return icon_class_name;
    }

    public ArrayList<String> getApply_class() {
        return apply_class;
    }
    public ArrayList<String> getApply_direct_class_path() {
        return apply_direct_class_path;
    }

    public void setClass_path(String class_path) {
        this.class_path = class_path;
    }

    public String getClass_path() {
        return class_path;
    }

    public void setTag_width(int tag_width) {
        this.tag_width = tag_width;
    }

    public void setTag_height(int tag_height) {
        this.tag_height = tag_height;
    }

    public int getTag_width() {
        return tag_width;
    }
}
