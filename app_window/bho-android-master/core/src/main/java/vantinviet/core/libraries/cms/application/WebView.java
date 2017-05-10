package vantinviet.core.libraries.cms.application;

import android.content.SharedPreferences;
import android.os.Build;
import android.os.Handler;
import android.os.Looper;
import android.support.annotation.RequiresApi;
import android.util.Base64;
import android.webkit.JavascriptInterface;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.JsonParseException;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikamarket.classes.Image;
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
        vtv_WebView web_browser = JFactory.getWebBrowser();
        web_browser.vtv_postUrl(link);
        web_browser.addJavascriptInterface(new MyJavaScriptInterfaceWebsite(), "HtmlViewer");


    }

    public void go_to_page(String html){
        final String[] html1 = {html};
        Handler refresh2 = new Handler(Looper.getMainLooper());
        refresh2.post(new Runnable() {
            public void run()
            {
                byte[] data= Base64.decode(html1[0], Base64.DEFAULT);
                // create alert dialog
                try {

                    html1[0] =new String(data, "UTF-8");
                } catch (UnsupportedEncodingException e) {
                    e.printStackTrace();

                    return;
                }
                //Timber.d("html response: %s", html1[0]);
                Gson gson = new Gson();
                JsonReader reader = new JsonReader(new StringReader(html1[0]));
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

                //System.out.print("Page -------------response: "+page.toString());
               // System.out.print("list_input response: "+page.getList_input().toString());

                String template_name=app.getTemplate().getTemplateName();
                //Timber.d("modules: %s",app.getModules().toString());
                //Timber.d("template: %s",template_name);
                Class<?> template_class = null;
                try {
                    template_class = Class.forName(String.format("vantinviet.core.templates.%s.index",template_name));
                    Method method = template_class.getDeclaredMethod("buildLayout",LinearLayout.class);
                    method.invoke(template_class,app.getRoot_linear_layout());
                    bg.setVisibility(LinearLayout.GONE);
                } catch (ClassNotFoundException e) {
                    e.printStackTrace();
                } catch (IllegalAccessException e) {
                    e.printStackTrace();
                } catch (NoSuchMethodException e) {
                    e.printStackTrace();
                } catch (InvocationTargetException e) {
                    e.printStackTrace();
                }
            }
        });


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
