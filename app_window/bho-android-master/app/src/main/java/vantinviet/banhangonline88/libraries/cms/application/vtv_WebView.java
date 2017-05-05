package vantinviet.banhangonline88.libraries.cms.application;

import android.content.Context;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.text.TextUtils;
import android.webkit.*;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonParser;

import org.apache.http.util.EncodingUtils;
import org.json.JSONArray;
import org.json.JSONException;

import java.io.StringWriter;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import timber.log.Timber;
import vantinviet.banhangonline88.libraries.utilities.JUtilities;

/**
 * Created by cuong on 5/5/2017.
 */

@RequiresApi(api = Build.VERSION_CODES.KITKAT)
public class vtv_WebView extends android.webkit.WebView {
    private String[] _browser_post;
    private String[] _android_post;

    public vtv_WebView(Context context) {
        super(context);
    }
    @Override
    public void  addJavascriptInterface(Object object, String name)
    {
        super.addJavascriptInterface(object, name);
    }
    Map<String, String>  android_post = get_android_post();
    Map<String, String> browser_post = get_browser_post(android_post);

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void  vtv_postUrl(String url, Map<String, String>  add_more_post)
    {
        Map<String, String> both = JUtilities.concatAllMap(add_more_post,android_post,browser_post);
        List<String> list_post = new ArrayList<String>();
        for (Map.Entry<String, String> entry : both.entrySet())
        {
            String key=entry.getKey();
            String value=entry.getValue();
            list_post.add(key+"="+value);
        }
        String link_post= TextUtils.join("&", list_post);
        System.out.println("postUrl :" +url+"?"+link_post+"&base64=0");
        byte[] post = EncodingUtils.getBytes(link_post, "BASE64");
        super.postUrl(url, post);
    }
    public void  vtv_postUrl(String  url)
    {
        Map<String, String> both = JUtilities.concatAllMap(android_post,browser_post);
        List<String> list_post = new ArrayList<String>();
        for (Map.Entry<String, String> entry : both.entrySet())
        {
            String key=entry.getKey();
            String value=entry.getValue();
            list_post.add(key+"="+value);
        }
        String link_post= TextUtils.join("&", list_post);
        byte[] post = EncodingUtils.getBytes(link_post, "BASE64");
        System.out.println("postUrl :" +url+"?"+link_post);
        super.postUrl(url, post);
    }

    public Map<String, String> get_browser_post(Map<String, String> android_post) {
        Gson gson = new Gson();
        String json_post = gson.toJson(android_post);
        Map<String, String> map_browser_post = new HashMap<String, String>();
        map_browser_post.put("browser_post", json_post);
        return map_browser_post;
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public  Map<String, String>  get_android_post() {
        Map<String, String> map_android_post = new HashMap<String, String>();
        map_android_post.put("get_page_config_app", "1");
        map_android_post.put("ignoreMessages", "true");
        map_android_post.put("format", "json");
        map_android_post.put("os", "android");
        map_android_post.put("vtlai_firewall_redirect", "home");
        return map_android_post;
    }
}
