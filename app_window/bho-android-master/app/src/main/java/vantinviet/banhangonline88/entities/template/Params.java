package vantinviet.banhangonline88.entities.template;

import java.util.ArrayList;

import vantinviet.banhangonline88.entities.template.bootstrap.Row;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Params {
    int layout_width;
    String layout_type;
    ArrayList<Row> android_layout =new ArrayList<Row>();
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
