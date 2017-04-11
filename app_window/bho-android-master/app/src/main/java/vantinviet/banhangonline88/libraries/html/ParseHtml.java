package vantinviet.banhangonline88.libraries.html;

import android.widget.LinearLayout;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;

import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static vantinviet.banhangonline88.libraries.joomla.JFactory.getContext;

/**
 * Created by cuongnd on 11/04/2017.
 */

public class ParseHtml {
    String tag;
    String lang;
    String html;
    String class_name;
    private ArrayList<ParseHtml> children;
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
        return list_allow_tag;
    }
    public static ArrayList<String> get_list_tag_column() {
        ArrayList<String> list_column = new ArrayList<String>();
        for (int i = 1; i <= 12; i++) {
            list_column.add("col-lg-"+i);
            list_column.add("col-md-"+i);
        }
        return list_column;
    }

    @Override
    public String toString() {
        return "ParseHtml{" +
                "tag=" + tag +
                ", lang='" + lang + '\'' +
                ", html='" + html + '\'' +
                '}';
    }

    public static LinearLayout get_html_linear_layout(ParseHtml html) {
        JApplication app= JFactory.getApplication();
        LinearLayout root_linear_layout=new LinearLayout(app.getCurrentActivity());
        int screen_size_width=400;
        root_linear_layout=ParseHtml.render_layout(html,root_linear_layout,screen_size_width, WRAP_CONTENT);
        return root_linear_layout;
    }

    public static LinearLayout render_layout(ParseHtml html, LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        LinearLayout.LayoutParams layout_params;
        ArrayList<ParseHtml> children_tag = html.getChildren();
        if (children_tag != null) for (ParseHtml sub_html : children_tag) {
            root_linear_layout= render_layout_by_tag(sub_html,root_linear_layout,screen_size_width,screen_size_height);
            root_linear_layout=render_layout(sub_html,root_linear_layout,screen_size_width,screen_size_height);
        }
        return root_linear_layout;
    }

    private static LinearLayout render_layout_by_tag(ParseHtml html, LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        String class_name = html.getClass_name();
        ArrayList<String> list_allow_tag = get_list_allow_tag();
        if (list_allow_tag != null) for (String tag: list_allow_tag) {
            if(check_has_tag(class_name,tag))
            {
                tag = "render_tag_" + tag;
                //no paramater
                Class noparams[] = {};
                Method method = null;
                try {
                    method = ParseHtml.class.getDeclaredMethod(tag, (Class<?>[]) new Object[]{root_linear_layout, screen_size_width, screen_size_height});
                    return (LinearLayout)method.invoke(ParseHtml.class, null);
                } catch (NoSuchMethodException e) {
                    e.printStackTrace();
                } catch (InvocationTargetException e) {
                    e.printStackTrace();
                } catch (IllegalAccessException e) {
                    e.printStackTrace();
                }
                break;
            }
        }
        return root_linear_layout;
    }

    private static boolean check_has_tag(String class_name, String tag) {
        if(tag=="column"){
            ArrayList<String> list_column = get_list_tag_column();
            if (list_column != null) for (String column: list_column) {
                return class_name.toLowerCase().contains(column.toLowerCase());
            }
        }
        return class_name.toLowerCase().contains(tag.toLowerCase());
    }

    private static LinearLayout render_tag_row(LinearLayout root_linear_layout, int screen_size_width, int screen_size_height)
    {
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
        layout_params.setMargins(0, 10, 0, 10);
        LinearLayout new_row_linear_layout = new LinearLayout(getContext());
        new_row_linear_layout.setLayoutParams(layout_params);
        new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

        layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
        LinearLayout new_wrapper_of_row_linear_layout = new LinearLayout(getContext());
        new_wrapper_of_row_linear_layout.setLayoutParams(layout_params);
        new_wrapper_of_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

        return null;
    }
    private static LinearLayout render_tag_column(LinearLayout root_linear_layout, int screen_size_width, int screen_size_height)
    {
       /* LinearLayout.LayoutParams layout_params;
        int column_width = Integer.parseInt(column.getSpan());
        column_width = screen_size_width * column_width / 12;
        layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
        int column_offset = Integer.parseInt(column.getOffset().equals("") ? "0" : column.getOffset());
        column_offset = screen_size_width * column_offset / 12;
        layout_params.setMargins(column_offset, 0, 0, 0);
        LinearLayout new_column_linear_layout = new LinearLayout(getContext());
        new_column_linear_layout.setLayoutParams(layout_params);
        new_column_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

        LinearLayout.LayoutParams new_vertical_wrapper_of_column_linear_layout_params = new LinearLayout.LayoutParams(column_width, WRAP_CONTENT);
        LinearLayout new_wrapper_of_column_linear_layout = new LinearLayout(getContext());
        new_wrapper_of_column_linear_layout.setLayoutParams(new_vertical_wrapper_of_column_linear_layout_params);
        new_wrapper_of_column_linear_layout.setOrientation(LinearLayout.VERTICAL);*/

        return null;
    }
    public ArrayList<ParseHtml> getChildren() {
        return children;
    }

    public String getTag() {
        return tag;
    }

    public String getClass_name() {
        return class_name;
    }
}
