package vantinviet.banhangonline88.libraries.joomla.input;

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
    public Map<String, String> input = new HashMap<String, String>();
    /* Static 'instance' method */
    public static JInput getInstance() {

        if (instance == null) {
            instance = new JInput();
        }
        return instance;
    }
    public String getString(String key, String var_default) {
        Timber.d("input %s", input.toString());
        String value= input.get(key);
        if(value==null){
            return var_default;
        }
        return value;
    }
    public JInput(){
        try{
            input =app.getInput();
        }catch (NullPointerException e) {

        }

    }
}
