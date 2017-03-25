package vantinviet.banhangonline88.entities.module;

import com.google.gson.annotations.SerializedName;

import org.json.JSONObject;

import java.util.ArrayList;

import vantinviet.banhangonline88.libraries.joomla.form.JFormField;

/**
 * Created by cuongnd on 25/03/2017.
 */

public class Module {
    int id;
    String title;
    String module;
    String position;
    String showtitle;
    String response;
    @SerializedName("params")
    Params params;
    private ArrayList<JFormField> fields;
    private JSONObject item;
    private ArrayList<JFormField> controlItems;
    private ArrayList<JFormField> columnFields;
    private ArrayList<String> items;

    @Override
    public String toString() {
        return "Module{" +
                "id=" + id +
                ", title='" + title + '\'' +
                ", module='" + module + '\'' +
                ", position='" + position + '\'' +
                ", showtitle='" + showtitle + '\'' +
                ", response='" + response + '\'' +
                ", params='" + params + '\'' +
                '}';
    }

    public String getPosition() {
        return position;
    }
    public Params getParams() {
        return params;
    }

    public String getTitle() {
        return title;
    }

    public ArrayList<JFormField> getFields() {
        return fields;
    }

    public JSONObject getItem() {
        return item;
    }

    public ArrayList<JFormField> getControlItems() {
        return controlItems;
    }

    public ArrayList<JFormField> getColumnFields() {
        return columnFields;
    }

    public ArrayList<String> getItems() {
        return items;
    }
}
