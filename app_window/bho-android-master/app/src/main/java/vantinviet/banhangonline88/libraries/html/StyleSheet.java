package vantinviet.banhangonline88.libraries.html;

import com.google.gson.Gson;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import timber.log.Timber;

/**
 * Created by cuongnd on 17/04/2017.
 */

    public class StyleSheet {
    String border;
    String color;

    public static Map<String,StyleSheet> get_list_style_sheet(String style) {
        Map<String, StyleSheet> list_style_sheet = new HashMap<String, StyleSheet>();
        //Timber.d("style_product object %s",style.toString());
        try {
            JSONObject jsonObject= new JSONObject(style);
            Iterator<?> keys = jsonObject.keys();
            while(keys.hasNext() ) {
                String key = (String)keys.next();
                //Timber.d("key %s",key.toString());
                if ( jsonObject.get(key) instanceof JSONObject ) {
                    String value_of_key=jsonObject.get(key).toString();
                    Gson gson=new Gson();
                    StyleSheet style_sheet= gson.fromJson(value_of_key, StyleSheet.class);
                    list_style_sheet.put(key, style_sheet);
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return list_style_sheet;
    }
}
