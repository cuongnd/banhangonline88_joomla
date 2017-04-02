package vantinviet.banhangonline88.configuration;

import vantinviet.banhangonline88.libraries.joomla.cache.jstorage.JCacheFile;
import vantinviet.banhangonline88.libraries.joomla.filesystem.JFile;

import org.json.JSONException;
import org.json.JSONObject;

import java.lang.reflect.Field;
import java.util.HashMap;
import java.util.Map;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class JConfig {
    private static JConfig instance;
    public  String FILE_NAME_OF_FILE_CONFIG = "VTVConfig.ini";
    public float cachetime=150000;
    public int caching=0;
    public String cache_handler="file";
    public String android_ses_id=null;
    private String rootUrl="http://banhangonline88.com/";

    public static JConfig getInstance() {
        if(instance==null)
        {
            instance=new JConfig();
        }
        return instance;
    }
    public String toStringConfig() {
        Field[] fields = this.getClass().getFields();
        Map<String,String> list_value=new HashMap<>();
        for(Field field : fields) {
            String name = field.getName();

            Object value = null;
            try {
                value = field.get(this);
            } catch (IllegalAccessException e) {
                e.printStackTrace();
            }
            list_value.put(name, String.valueOf(value));
        }
        JSONObject json_list_var= new JSONObject(list_value);

        return json_list_var.toString();
    }
    public  String get_content_file_config() {
        String content="";
        if(!JFile.exists(this.FILE_NAME_OF_FILE_CONFIG, JCacheFile.CACHE_PATH))
        {
            return content;
        }
        content= JFile.read(this.FILE_NAME_OF_FILE_CONFIG, JCacheFile.CACHE_PATH);
        return content;
    }

    public String get(String name, String value_default) {

        Field[] fields = this.getClass().getFields();
        for(Field field : fields) {
            String name1 = field.getName();

            Object value1 = null;
            try {
                value1 = field.get(this);
            } catch (IllegalAccessException e) {
                e.printStackTrace();
            }
            if(name1.equals(name)&&value1!=null)
            {
                return String.valueOf(value1);
            }
        }

        String content_file_cache=this.get_content_file_config();
        JSONObject json_object=new JSONObject();
        try {
            json_object=new JSONObject(content_file_cache);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        try {
            return json_object.has(name)?json_object.getString(name):value_default;
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return value_default;

    }



    public class getInstance extends JConfig {
    }
}
