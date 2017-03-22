package vantinviet.banhangonline88.entities.template.bootstrap;

import com.google.gson.annotations.SerializedName;

import java.util.ArrayList;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Column {
    @SerializedName("span")
    String span="";
    @SerializedName("offset")
    String offset="";
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
}
