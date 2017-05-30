package vantinviet.core.libraries.cms.application;

import android.annotation.TargetApi;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.res.Resources;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Environment;
import android.support.v7.app.AppCompatActivity;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.JavascriptInterface;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ScrollView;

import com.beardedhen.androidbootstrap.BootstrapEditText;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.Map;
import java.util.Random;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.component.JPluginHelper;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.legacy.request.JRequest;
import vantinviet.core.media.element.slider.banner_rotator.elementBanner_RotatorHelper;
import vantinviet.core.media.element.ui.grid.element_grid_helper;
import vantinviet.core.media.element.ui.link_image.element_link_image_helper;
import vantinviet.core.modules.mod_menu.mod_menu;
import vantinviet.core.modules.mod_virtuemart_category.mod_virtuemart_category_helper;

/**
 * Created by cuongnd on 6/10/2016.
 */
public class JApplicationSite extends JApplicationCms {
    private static JApplicationSite ourInstance;
    public static ProgressDialog dialog;
    public  String client="";
    public static int screen_size_width;
    public static int screen_size_height;
    public static String host="";
    public static boolean debug=true;
    public static String component_content;
    public static JApplicationSite getInstance(String client) {
        if (ourInstance == null) {
            ourInstance = new JApplicationSite(client);
        }
        return ourInstance;
    }

    public JApplicationSite(String client) {
        this.client=client;
    }

    public static void execute(JSONObject json_object) {
        doExecute(json_object);
    }

    public static void doExecute(JSONObject json_object) {
        initialiseApp(json_object);
    }

    public static void initialiseApp(JSONObject json_object) {
        try {
            JSONObject request = null;
            request = json_object.has("request")?json_object.getJSONObject("request"):new JSONObject();
            JRequest.request=request;
        } catch (JSONException e) {
            e.printStackTrace();
        }
        JUser user = JFactory.getUser();
        int guestUserGroup=1;
        JPluginHelper.importPlugin("system", "languagefilter");
        Map<String,String> option=new Hashtable<>();

    }




    private static void initChatting() {

    }
}
