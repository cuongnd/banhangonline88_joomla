package vantinviet.banhangonline88.libraries.joomla.form.fields;

import android.view.View;

import com.beardedhen.androidbootstrap.BootstrapButton;
import com.vantinviet.vtv.libraries.cms.component.JComponentHelper;
import com.vantinviet.vtv.libraries.joomla.JFactory;
import com.vantinviet.vtv.libraries.legacy.application.JApplication;
import com.vantinviet.vtv.libraries.joomla.form.JFormField;
import com.vantinviet.vtv.libraries.utilities.JUtilities;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Map;

/**
 * Created by cuongnd on 6/11/2016.
 */
public class JFormFieldButton extends JFormField{
    static Map<String, JFormFieldButton> map_form_field_button = new HashMap<String, JFormFieldButton>();
    public JFormFieldButton(){

    }

    public JFormFieldButton(JSONObject field,String type, String name, String group,String value){

        this.type=type;
        this.name=name;
        this.group=group;
        this.option=field;
        this.value=value;
        try {
            this.value_default=field.has("default")?field.getString("default"):"";
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }
    @Override
    public View getInput() {
        BootstrapButton button= new BootstrapButton(context);
        button.setText(this.label);
        button.setPadding(20, 10, 20, 10);
        button.setTextSize(23);

        button.setOnClickListener(getOnClickDoSomething(button,this));
        return button;
    }

    private View.OnClickListener getOnClickDoSomething(final BootstrapButton button, final JFormFieldButton form_field_button) {
        return new View.OnClickListener() {
            public void onClick(View v) {
                try {
                    JSONArray list_hidden_field_item= JComponentHelper.android_render_form_type.equals(JComponentHelper.ANDROID_RENDER_FORM_TYPE_LIST)? JComponentHelper.list_hidden_field_list:JComponentHelper.list_hidden_field_item;
                    Map<String, String>  map_str_hidden_field_item=JUtilities.getMapString(list_hidden_field_item,"name","default");
                    Map<String, String>  map_input=JComponentHelper.getMapStringInputComponent(form_field_button);
                    Map<String, String>  map_control=JComponentHelper.getMapStringControl(form_field_button);
                    String str_http_map_control=JUtilities.http_build_query(map_control);
                    String str_http_map_input=JUtilities.http_build_query_form(map_input);
                    String str_http_map_str_hidden_field_item=JUtilities.http_build_query(map_str_hidden_field_item);
                    str_http_map_input=!str_http_map_input.equals("")?"&"+str_http_map_input:"";
                    str_http_map_str_hidden_field_item=!str_http_map_str_hidden_field_item.equals("")?"&"+str_http_map_str_hidden_field_item:"";
                    String link ="index.php?"+ str_http_map_control+str_http_map_input+str_http_map_str_hidden_field_item;
                    System.out.println("link button:" + link);
                    JApplication app= JFactory.getApplication();
                    app.setRedirect(link,map_input);
                } catch (UnsupportedEncodingException e) {
                    e.printStackTrace();
                }
            }
        };
    }

}
