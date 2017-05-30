package vantinviet.core.libraries.html.module;

import com.google.gson.annotations.SerializedName;

import org.json.JSONObject;

import java.util.ArrayList;

import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.form.JFormField;
import vantinviet.core.libraries.legacy.database.mysqli;

/**
 * Created by cuongnd on 25/03/2017.
 */

public class Module {
    protected int id;
    protected String title;
    protected String module;
    protected String position;
    protected String shotitle;
    protected String response;
    @SerializedName("params")
    protected Params params;
    protected String strparams="";
    private ArrayList<JFormField> fields;
    private JSONObject item;
    private ArrayList<JFormField> controlItems=new ArrayList<JFormField>();
    private ArrayList<JFormField> columnFields=new ArrayList<JFormField>();
    private ArrayList<String> items=new ArrayList<String>();
    private String content ="";
    private String style_module="";

    @Override
    public String toString() {
        return "Module{" +
                "id=" + id +
                ", title='" + title + '\'' +
                ", module='" + module + '\'' +
                ", position='" + position + '\'' +
                ", response='" + response + '\'' +
                ", params='" + params + '\'' +
                ", content='" + content + '\'' +
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

    public String getContent() {
        mysqli db= JFactory.getDBO();
        return content;
    }

    public String getModuleName() {
        return module;
    }

    public int getId() {
        return id;
    }

    public String getStrparams() {
        return strparams;
    }

    public String getResponse() {
        return response;
    }

    public String getModuleTtitle() {
        return title;
    }

    public String getStyle_module() {
        return style_module;
    }
}
