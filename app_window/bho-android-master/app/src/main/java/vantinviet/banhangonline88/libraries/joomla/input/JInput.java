package vantinviet.banhangonline88.libraries.joomla.input;

import android.widget.LinearLayout;

import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class JInput {
    private static JInput instance;
    private static JApplication app= JFactory.getApplication();
    public String option="com_content";
    public Map<String, String> list_input = new HashMap<String, String>();
    private LinearLayout _component_linear_layout;

    /* Static 'instance' method */
    public static JInput getInstance() {

        if (instance == null) {
            instance = new JInput();
        }
        return instance;
    }
    public String getString(String key, String var_default) {
        Timber.d("list_input %s", list_input.toString());
        String value= list_input.get(key).toString();
        if(value==null){
            return var_default;
        }
        return value;
    }
    public JInput(){
        try{
            list_input =app.getList_input();
        }catch (NullPointerException e) {

        }

    }

    public String getString(String key) {
        Timber.d("list_input %s", list_input.toString());
        String value= list_input.get(key);
        if(value==null){
            return "";
        }
        return value;
    }

    public void setString(String key, String value) {
        list_input.put(key,value);
    }

    public void set_component_linear_layout(LinearLayout component_linear_layout) {
        this._component_linear_layout = component_linear_layout;
    }
    public LinearLayout get_component_linear_layout() {
        return this._component_linear_layout;
    }

    public void setList_input(Map<String, String> list_input) {
        this.list_input = list_input;
    }
}
