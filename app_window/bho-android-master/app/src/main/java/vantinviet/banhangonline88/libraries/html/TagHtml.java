package vantinviet.banhangonline88.libraries.html;

import android.view.Gravity;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListPopupWindow;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.entities.template.bootstrap.Column;
import vantinviet.banhangonline88.libraries.cms.module.JModuleHelper;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

import static android.view.ViewGroup.LayoutParams.FILL_PARENT;
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
    private ArrayList<TagHtml> children;
    private static ArrayList<String> _list_allow_tag;


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

    public static void set_style(TagHtml tag, LinearLayout linear_layout) {
        String class_name=tag.getClass_name();
        class_name=class_name.toLowerCase();
        String[] splited_class_name = class_name.split("\\s+");
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
        }



    }

    @Override
    public String toString() {
        return "TagHtml{" +
                "tag=" + tag +
                ", lang='" + lang + '\'' +
                ", html='" + html + '\'' +
                '}';
    }

    public static void get_html_linear_layout(TagHtml tag, LinearLayout root_linear_layout) {
        JApplication app = JFactory.getApplication();
        int component_width = app.get_Component_width();
        int screen_size_height = WRAP_CONTENT;
        TagHtml body_tag = tag.getChildren().get(0);
        ArrayList<TagHtml> list_sub_tag = body_tag.getChildren();
        if (list_sub_tag != null) for (TagHtml sub_tag : list_sub_tag) {
            render_layout(sub_tag, root_linear_layout, component_width, screen_size_height);
        }
    }

    public static void render_layout(TagHtml tag, LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        LinearLayout.LayoutParams layout_params;
        if(TagHtml.is_row(tag)){
            layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
            layout_params.setMargins(0, 10, 0, 10);
            LinearLayout new_row_linear_layout = new LinearLayout(app.getCurrentActivity());
            new_row_linear_layout.setLayoutParams(layout_params);
            new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
            set_style(tag,new_row_linear_layout);
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
                    LinearLayout.LayoutParams new_vertical_wrapper_of_column_linear_layout_params = new LinearLayout.LayoutParams(column_width, MATCH_PARENT);
                    LinearLayout new_wrapper_of_column_linear_layout = new LinearLayout(app.getCurrentActivity());
                    new_wrapper_of_column_linear_layout.setLayoutParams(new_vertical_wrapper_of_column_linear_layout_params);
                    new_wrapper_of_column_linear_layout.setOrientation(LinearLayout.VERTICAL);
                    new_column_linear_layout.addView(new_wrapper_of_column_linear_layout);
                    new_row_linear_layout.addView(new_column_linear_layout);
                    set_style(column_tag,new_wrapper_of_column_linear_layout);
                    ArrayList<TagHtml> list_sub_tag = column_tag.getChildren();
                    if (list_sub_tag != null) for (TagHtml item_sub_tag : list_sub_tag) {
                        render_layout(item_sub_tag, new_wrapper_of_column_linear_layout, column_width, screen_size_height);
                    }
                }else {
                    //sai cau truc bootstrap
                }
            }

            root_linear_layout.addView(new_row_linear_layout);
        }else {
            LinearLayout sub_linear_layout=render_layout_by_tag(tag, screen_size_width, screen_size_height);

            root_linear_layout.addView(sub_linear_layout);
            ArrayList<TagHtml> children_tag = tag.getChildren();
            if (children_tag != null) for (TagHtml sub_html : children_tag) {
                render_layout(sub_html,sub_linear_layout, screen_size_width, screen_size_height);
            }
        }

    }

    private static boolean is_column(TagHtml tag) {
        String class_name = tag.getClass_name();
        class_name=class_name.toLowerCase();
        String[] splited_class_name = class_name.split("\\s+");
        ArrayList<Column> list_class_column_width = Column.get_list_default_class_column_width();
        boolean is_column=false;
        if (splited_class_name != null) for (String item_class : splited_class_name) {
            if (list_class_column_width != null) for (Column column : list_class_column_width) {
                if (item_class.equals(column.getDefault_class_column_width())) {
                    is_column=true;
                    break;
                }
            }
            if(is_column){
                break;
            }
        }
        return is_column;
    }

    private static LinearLayout render_layout_by_tag(TagHtml tag, int screen_size_width, int screen_size_height) {
        String class_name = tag.getClass_name();
        JApplication app = JFactory.getApplication();
        ArrayList<String> list_allow_tag = get_list_allow_tag();
        class_name = (class_name.equals("") || class_name == null) ? tag.getTagName() : class_name;
        if (list_allow_tag != null) for (String tag_item : list_allow_tag) {
            if (check_has_tag(class_name, tag_item)) {
                tag_item = "render_tag_" + tag_item;
                try {
                    Class<?> c = tag.getClass();
                    Method tag_method = c.getDeclaredMethod(tag_item, TagHtml.class, int.class, int.class);
                    return (LinearLayout) tag_method.invoke(tag, tag, screen_size_width, screen_size_height);

                    // production code should handle these exceptions more gracefully
                } catch (InvocationTargetException x) {
                    Throwable cause = x.getCause();
                    System.err.format("%s() failed: %s%n", tag_item, cause.getMessage());
                } catch (Exception x) {
                    x.printStackTrace();
                }
                //method = TagHtml.class.getDeclaredMethod(tag,TagHtml.class, LinearLayout.class,int.class,int.class);
                //return (LinearLayout)method.invoke(TagHtml.class, html,root_linear_layout,screen_size_width,screen_size_height);

                break;
            }
        }
        return new LinearLayout(app.getCurrentActivity());
    }

    private static boolean check_has_tag(String class_name, String tag) {
        class_name = class_name.toLowerCase();
        tag = tag.toLowerCase();
        if (tag == "column") {
            ArrayList<Column> list_default_class_column_width = Column.get_list_default_class_column_width();
            if (list_default_class_column_width != null) for (Column column : list_default_class_column_width) {
                if (class_name.toLowerCase().contains(column.getDefault_class_column_width())) {
                    return true;
                }
            }
            return false;
        } else if (class_name.indexOf(tag) != -1) {
            Timber.d("class_name: %s,tag: %s ",class_name,tag);
            return true;
        } else {
            return false;
        }

    }
    private static LinearLayout render_tag_h4(TagHtml tag, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        LinearLayout new_h4_linear_layout = new LinearLayout(app.getCurrentActivity());
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);

        new_h4_linear_layout.setLayoutParams(layout_params);
        String html_content = tag.get_Html_content();
        TextView text_view_h4 = new TextView(app.getCurrentActivity());
        text_view_h4.setText(html_content);

        LinearLayout.LayoutParams layout_params_text_view_h4;
        layout_params_text_view_h4 = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);
        text_view_h4.setLayoutParams(layout_params_text_view_h4);
        text_view_h4.setGravity(Gravity.CENTER_VERTICAL);
        new_h4_linear_layout.addView(text_view_h4);
        return new_h4_linear_layout;
    }
    private static LinearLayout render_tag_icon(TagHtml tag, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        LinearLayout new_icon_linear_layout = new LinearLayout(app.getCurrentActivity());
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);

        new_icon_linear_layout.setLayoutParams(layout_params);
        String icon_name = tag.get_Icon_name(tag);
        TextView text_view_icon = new TextView(app.getCurrentActivity());
        text_view_icon.setText(icon_name);

        LinearLayout.LayoutParams layout_params_text_view_icon;
        layout_params_text_view_icon = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);
        text_view_icon.setLayoutParams(layout_params_text_view_icon);
        text_view_icon.setGravity(Gravity.CENTER_VERTICAL);
        new_icon_linear_layout.addView(text_view_icon);
        return new_icon_linear_layout;
    }
    private static LinearLayout render_tag_div(TagHtml html, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, WRAP_CONTENT);
        LinearLayout new_div_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_div_linear_layout.setLayoutParams(layout_params);
        return new_div_linear_layout;
    }

    private static LinearLayout render_tag_img(TagHtml html, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        LinearLayout new_img_linear_layout = new LinearLayout(app.getCurrentActivity());
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, MATCH_PARENT);

        new_img_linear_layout.setLayoutParams(layout_params);
        ImageView image_view = new ImageView(app.getCurrentActivity());
        String src = html.getSrc();
        Picasso.with(app.getCurrentActivity()).load(src).into(image_view);
        new_img_linear_layout.addView(image_view);
        return new_img_linear_layout;
    }
    private static LinearLayout render_tag_image_button(TagHtml html, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        LinearLayout new_img_linear_layout = new LinearLayout(app.getCurrentActivity());
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, WRAP_CONTENT);

        new_img_linear_layout.setLayoutParams(layout_params);
        new_img_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
        ImageButton image_view = new ImageButton(app.getCurrentActivity());
        String src = html.getSrc();
        Picasso.with(app.getCurrentActivity()).load(src).into(image_view);
        new_img_linear_layout.addView(image_view);
        return new_img_linear_layout;
    }

    public ArrayList<TagHtml> getChildren() {
        return children;
    }

    public String getTagName() {
        return tag;
    }

    public String getClass_name() {
        return class_name;
    }

    public String get_Html_content() {
        return html;
    }

    public static boolean is_row(TagHtml html) {
        String class_name=html.getClass_name();
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
        return icon_class_name;
    }
}
