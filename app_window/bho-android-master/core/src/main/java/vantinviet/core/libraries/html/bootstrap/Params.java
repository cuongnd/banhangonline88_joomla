package vantinviet.core.libraries.html.bootstrap;

import java.util.ArrayList;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Params {
    int layout_width;
    String layout_type;
    ArrayList<Row> android_layout =new ArrayList<Row>();
    ArrayList<Row> android_navigation_view_left_layout =new ArrayList<Row>();
    ArrayList<Row> android_navigation_view_top_layout =new ArrayList<Row>();
    ArrayList<Row> android_navigation_view_bottom_layout =new ArrayList<Row>();
    ArrayList<Row> android_navigation_view_right_layout =new ArrayList<Row>();
    @Override
    public String toString() {
        return "Params{" +
                "layout_width=" + layout_width +
                ", layout_type='" + layout_type + '\'' +
                ", android_layout='" + android_layout + '\'' +
                '}';
    }

    public ArrayList<Row> getAndroid_layout() {
        return android_layout;
    }
}
