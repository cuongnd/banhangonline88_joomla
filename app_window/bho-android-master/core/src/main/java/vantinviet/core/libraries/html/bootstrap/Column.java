package vantinviet.core.libraries.html.bootstrap;

import com.google.gson.annotations.SerializedName;

import java.util.ArrayList;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Column {
    @SerializedName("span")
    String span="";
    @SerializedName("offset")
    String offset="0";
    @SerializedName("xxxsspan")
    String xxxsspan="";
    @SerializedName("xxxsoffset")
    String xxxsoffset="";
    @SerializedName("xxsspan")
    String xxsspan="";
    @SerializedName("xxsoffset")
    String xxsoffset="";
    @SerializedName("xsspan")
    String xsspan="";
    @SerializedName("xsoffset")
    String xsoffset="";
    @SerializedName("smspan")
    String smspan="";
    @SerializedName("smoffset")
    String smoffset="";
    @SerializedName("mdspan")
    String mdspan="";
    @SerializedName("mdoffset")
    String mdoffset="";
    @SerializedName("type")
    String type="";
    @SerializedName("position")
    String position="";
    @SerializedName("style")
    String style="";
    @SerializedName("customclass")
    String customclass="";
    @SerializedName("responsiveclass")
    String responsiveclass="";
    @SerializedName("is_sub_content")
    String is_sub_content="";
    private ArrayList<Row> children=new ArrayList<Row>();
    private int width;
    private String class_default_offset;
    private String default_class_column_offset;
    private String class_default_width;
    private String default_class_column_width;
    private int default_offset=0;

    public String toString() {
        return "Column{" +
                "span=" + span +
                ", offset='" + offset + '\'' +
                ", xxxsspan='" + xxxsspan + '\'' +
                ", xxxsoffset='" + xxxsoffset + '\'' +
                ", xxsspan='" + xxsspan + '\'' +
                ", xxsoffset='" + xxsoffset + '\'' +
                ", xsspan='" + xsspan + '\'' +
                ", xsoffset='" + xsoffset + '\'' +
                ", smspan='" + smspan + '\'' +
                ", smoffset='" + smoffset + '\'' +
                ", mdspan='" + mdspan + '\'' +
                ", mdoffset='" + mdoffset + '\'' +
                ", type='" + type + '\'' +
                ", position='" + position + '\'' +
                ", style='" + style + '\'' +
                ", customclass='" + customclass + '\'' +
                ", responsiveclass='" + responsiveclass + '\'' +
                ", is_sub_content='" + is_sub_content + '\'' +
                ", children='" + children + '\'' +
                '}';
    }

    public String getDefault_span() {
        return span;
    }


    public ArrayList<Row> getRows() {
        return children;
    }

    public String getType() {
        return type;
    }

    public String getPosition() {
        return position;
    }

    public void setSpan(String span) {
        this.span = span;
    }

    public int get_Default_offset() {
        return default_offset;
    }
    public String getOffset() {
        return offset;
    }
    public void setDefault_offset(int default_offset) {
        this.default_offset = default_offset;
    }

    public void setDefault_class_column_width(String default_class_column_width) {
        this.default_class_column_width = default_class_column_width;
    }
    public String getDefault_class_column_width() {
        return default_class_column_width;
    }
    public void setDefault_class_column_offset(String default_class_column_offset) {
        this.default_class_column_offset = default_class_column_offset;
    }

    public String getDefault_class_column_offset() {
        return default_class_column_offset;
    }

    public static ArrayList<Column> get_list_default_class_column_offset() {

        ArrayList<Column> list_list_default_class_column_offset = new ArrayList<Column>();
        for (int i = 1; i <= 12; i++) {
            Column column_default_offset = new Column();
            column_default_offset.setDefault_offset(i);
            column_default_offset.setDefault_class_column_offset(String.format("col-md-offset-%d", i));
            list_list_default_class_column_offset.add(column_default_offset);
        }
        return list_list_default_class_column_offset;
    }
    public static ArrayList<Column> get_list_default_class_column_width() {
        ArrayList<Column> list_class_default_column_width = new ArrayList<Column>();
        for (int i = 1; i <= 12; i++) {
            Column column_md=new Column();
            column_md.setSpan(String.valueOf(i));
            column_md.setDefault_class_column_width(String.format("col-md-%d",i));
            list_class_default_column_width.add(column_md);
        }
        return list_class_default_column_width;
    }



}
