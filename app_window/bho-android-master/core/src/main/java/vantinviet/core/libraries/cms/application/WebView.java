package vantinviet.core.libraries.cms.application;

import android.content.SharedPreferences;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.util.Base64;
import android.webkit.JavascriptInterface;

import com.google.gson.Gson;
import com.google.gson.JsonParseException;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.configuration.JConfig;


import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


/**
 * Created by cuongnd on 12/17/2015.
 */
public class WebView {
    private static JApplication app=JFactory.getApplication();
    private static WebView ourInstance;
    private  Fragment fragment=new Fragment();
    private Class<?> current_class;
    SharedPreferences sharedpreferences;
    String link;
    private static VTVConfig vtv_config=VTVConfig.getInstance();
    int caching = JConfig.getInstance().caching;
    public static WebView getInstance() {
        if (ourInstance == null) {
            ourInstance = new WebView();
        }
        return ourInstance;
    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void create_browser(String link) {
        app.getProgressDialog().show();
        vtv_WebView web_browser = JFactory.getWebBrowser();
        web_browser.vtv_postUrl(link);
        app.getProgressDialog().dismiss();
        web_browser.addJavascriptInterface(new MyJavaScriptInterfaceWebsite(), "HtmlViewer");


    }

    public void go_to_page(String html){

       /* app.getProgressDialog().dismiss();
        byte[] data= Base64.decode(html, Base64.DEFAULT);
        // create alert dialog
        try {

            html=new String(data, "UTF-8");
        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();

            return;
        }
        Timber.d("html response: %s",html);
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(html));
        reader.setLenient(true);
        Page page=null;
        try {
            page = gson.fromJson(reader, Page.class);
        }
        catch (JsonParseException e) {
            Timber.d("JsonParseException error : %s",e.toString());

            JUtilities.show_alert_dialog(app.getLink());
            return;
        }

        app.setAplication(page);
        System.out.print("Page response: "+page.toString());
        System.out.print("list_input response: "+page.getList_input().toString());
        String template_name=app.getTemplate().getTemplateName();
        //Timber.d("modules: %s",app.getModules().toString());
        //Timber.d("template: %s",template_name);
        Class<?> template_class = null;
        try {
            template_class = Class.forName(String.format("vantinviet.core.templates.%s.index",template_name));
            Constructor<?> cons = template_class.getConstructor(DrawerMenuItem.class);
            Object object = cons.newInstance(finalDrawerMenuItem);
            fragment=(Fragment)object;
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        }
        if (fragment != null) {
            FragmentManager frgManager = app.getCurrentActivity().getSupportFragmentManager();
            FragmentTransaction fragmentTransaction = frgManager.beginTransaction();
            fragmentTransaction.setAllowOptimization(false);
            fragmentTransaction.addToBackStack("hello");
            fragmentTransaction.replace(R.id.main_content_frame, fragment).commit();
        } else {
            Timber.e(new RuntimeException(), "Replace fragments with null newFragment parameter.");
        }
*/

    }
    private class MyJavaScriptInterfaceWebsite {

        public MyJavaScriptInterfaceWebsite() {
        }

        @JavascriptInterface
        public void showHTML(String html) {
            //Timber.d("html response: %s",html);
            if(vtv_config.getCaching()==1) {
                SharedPreferences.Editor editor = sharedpreferences.edit();
                editor.putString(link, html);
                editor.commit();
            }
            go_to_page(html);

        }

    }


}
