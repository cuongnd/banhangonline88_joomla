package vantinviet.banhangonline88.libraries.html;

import android.widget.LinearLayout;
import android.widget.TextView;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.Arrays;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;

/**
 * Created by cuongnd on 11/04/2017.
 */

public class ParseHtml {
    String tag="";
    String lang="";
    String html="";
    String class_name="";
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
    public static ArrayList<String> get_list_class_column() {
        ArrayList<String> list_column = new ArrayList<String>();
        for (int i = 1; i <= 12; i++) {
            list_column.add("col-lg-"+i);
            list_column.add("col-md-"+i);
        }
        return list_column;
    }
    public static ArrayList<String> get_list_class_column_offset() {
        ArrayList<String> list_column = new ArrayList<String>();
        for (int i = 1; i <= 12; i++) {
            list_column.add("col-lg-offset-"+i);
            list_column.add("col-md-offset-"+i);
        }
        return list_column;
    }

    public static int getColumnWidth(ParseHtml html) {
        ArrayList<String>  list_class_column= get_list_class_column();
        String class_name=html.getClass_name();
        if (list_class_column != null) for (String column: list_class_column) {
            if(class_name.toLowerCase().contains(column.toLowerCase())){
                return Integer.parseInt(column);
            }
        }

        return 0;
    }

    public static int getColumnOffset(ParseHtml html) {
        ArrayList<String>  list_class_column_offset= get_list_class_column_offset();
        String class_name=html.getClass_name();
        if (list_class_column_offset != null) for (String column_offset: list_class_column_offset) {
            if(class_name.toLowerCase().contains(column_offset.toLowerCase())){
                return Integer.parseInt(column_offset);
            }
        }

        return 0;
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
        int screen_size_height=400;
        ParseHtml body_html=html.getChildren().get(0);
        return ParseHtml.render_layout(body_html,root_linear_layout,screen_size_width, screen_size_height /*WRAP_CONTENT*/);
    }

    public static LinearLayout render_layout(ParseHtml html, LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        root_linear_layout= render_layout_by_tag(html,root_linear_layout,screen_size_width,screen_size_height);
        ArrayList<ParseHtml> children_tag = html.getChildren();
        if (children_tag != null) for (ParseHtml sub_html : children_tag) {

            root_linear_layout=render_layout(sub_html,root_linear_layout,screen_size_width,screen_size_height);
        }
        return root_linear_layout;
    }

    private static LinearLayout render_layout_by_tag(ParseHtml html, LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        String class_name = html.getClass_name();
        JApplication app=JFactory.getApplication();
        ArrayList<String> list_allow_tag = get_list_allow_tag();
        if (list_allow_tag != null) for (String tag: list_allow_tag) {
            if(check_has_tag(class_name,tag))
            {
                tag = "render_tag_" + tag;
                try {
                    Class<?> c = html.getClass();
                    Method tag_method = c.getDeclaredMethod(tag,ParseHtml.class,LinearLayout.class,int.class,int.class);
                    return (LinearLayout)tag_method.invoke(html,html,root_linear_layout,screen_size_width,screen_size_height);

                    // production code should handle these exceptions more gracefully
                } catch (InvocationTargetException x) {
                    Throwable cause = x.getCause();
                    System.err.format("%s() failed: %s%n",tag, cause.getMessage());
                } catch (Exception x) {
                    x.printStackTrace();
                }
                //method = ParseHtml.class.getDeclaredMethod(tag,ParseHtml.class, LinearLayout.class,int.class,int.class);
                //return (LinearLayout)method.invoke(ParseHtml.class, html,root_linear_layout,screen_size_width,screen_size_height);

                break;
            }
        }
        return  new LinearLayout(app.getCurrentActivity());
    }

    private static boolean check_has_tag(String class_name, String tag) {
        if(tag=="column"){
            ArrayList<String> list_column = get_list_class_column();
            if (list_column != null) for (String column: list_column) {
                if(class_name.toLowerCase().contains(column.toLowerCase()))
                {
                    return true;
                }
            }
            return  false;
        }
        return class_name.toLowerCase().contains(tag.toLowerCase());
    }
    private static LinearLayout render_tag_row(ParseHtml html,LinearLayout root_linear_layout,int screen_size_width,int screen_size_height)
    {
        JApplication app=JFactory.getApplication();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(screen_size_width,screen_size_height  );
        layout_params.setMargins(0,10,0,10);
        LinearLayout new_row_linear_layout=new LinearLayout(app.getCurrentActivity());
        new_row_linear_layout.setLayoutParams(layout_params);
        new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
        boolean debug= VTVConfig.getDebug();
        if(debug){
            //new_row_linear_layout.setBackgroundResource(R.color.black);
        }
        root_linear_layout.addView(new_row_linear_layout);
        return root_linear_layout;
    }

    private static LinearLayout render_tag_column(ParseHtml html,LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        int column_width = ParseHtml.getColumnWidth(html);
        int column_offset = ParseHtml.getColumnOffset(html);
        column_width = screen_size_width * column_width / 12;
        layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
        column_offset = screen_size_width * column_offset / 12;
        layout_params.setMargins(column_offset, 0, 0, 0);
        LinearLayout new_column_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_column_linear_layout.setLayoutParams(layout_params);
        new_column_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
        String html_content = html.get_Html_content();
        TextView text_view=new TextView(app.getCurrentActivity());
        text_view.setText(html_content);
        new_column_linear_layout.addView(text_view);
        LinearLayout.LayoutParams new_vertical_wrapper_of_column_linear_layout_params = new LinearLayout.LayoutParams(column_width, WRAP_CONTENT);
        LinearLayout new_wrapper_of_column_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_wrapper_of_column_linear_layout.setLayoutParams(new_vertical_wrapper_of_column_linear_layout_params);
        new_wrapper_of_column_linear_layout.setOrientation(LinearLayout.VERTICAL);

        new_column_linear_layout.addView(new_wrapper_of_column_linear_layout);
        if (debug) {
            new_wrapper_of_column_linear_layout.setBackgroundResource(R.color.black);
        }
        root_linear_layout.addView(new_wrapper_of_column_linear_layout);
        return root_linear_layout;
    }
    private static LinearLayout render_tag_h4(ParseHtml html,LinearLayout root_linear_layout, int screen_size_width, int screen_size_height) {
        JApplication app = JFactory.getApplication();
        boolean debug = VTVConfig.getDebug();
        LinearLayout.LayoutParams layout_params;
        layout_params = new LinearLayout.LayoutParams(screen_size_width, screen_size_height);
        LinearLayout new_h4_linear_layout = new LinearLayout(app.getCurrentActivity());
        new_h4_linear_layout.setLayoutParams(layout_params);
        new_h4_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
        String html_content = html.get_Html_content();
        TextView text_view=new TextView(app.getCurrentActivity());
        text_view.setText(html_content);
        new_h4_linear_layout.addView(text_view);
        root_linear_layout.addView(new_h4_linear_layout);
        return root_linear_layout;
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

    public String get_Html_content() {
        return html;
    }
}
