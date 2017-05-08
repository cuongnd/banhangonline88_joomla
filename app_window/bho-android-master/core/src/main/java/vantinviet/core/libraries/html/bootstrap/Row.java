package vantinviet.core.libraries.html.bootstrap;

import java.util.ArrayList;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Row {
    String name;
    String css_class;
    String responsive;
    String backgroundcolor;
    String textcolor;
    String linkcolor;
    String linkhovercolor;
    String margin;
    String padding;
    private ArrayList<Column> children=new ArrayList<Column>();
    public String toString() {
        return "Row{" +
                "name=" + name +
                ", css_class='" + css_class + '\'' +
                ", responsive='" + responsive + '\'' +
                ", backgroundcolor='" + backgroundcolor + '\'' +
                ", textcolor='" + textcolor + '\'' +
                ", linkcolor='" + linkcolor + '\'' +
                ", linkhovercolor='" + linkhovercolor + '\'' +
                ", margin='" + margin + '\'' +
                ", padding='" + padding + '\'' +
                ", children='" + children + '\'' +
                '}';
    }

    public ArrayList<Column> getColumns() {
        return children;
    }

    public String getName() {
        return name;
    }
}
