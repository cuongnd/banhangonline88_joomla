package vantinviet.core.libraries.legacy.request;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by cuongnd on 6/9/2016.
 */
public class JRequest {
    public static JSONObject request=null;
    public static String getString(String str_request, String value_default) {

        if(request!=null)
        {
            try {
                return request.has(str_request)?request.getString(str_request):value_default;
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
        return value_default;
    }
}
