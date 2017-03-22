package vantinviet.banhangonline88.entities.template;

import java.util.ArrayList;

import vantinviet.banhangonline88.entities.template.bootstrap.Column;
import vantinviet.banhangonline88.entities.template.bootstrap.Row;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Params {
    int layout_width;
    String layout_type;
    ArrayList<Column> layout=new ArrayList<Column>();
    @Override
    public String toString() {
        return "Params{" +
                "layout_width=" + layout_width +
                ", layout_type='" + layout_type + '\'' +
                ", layout='" + layout + '\'' +
                '}';
    }

    public ArrayList<Column> getLayout() {
        return layout;
    }
}
