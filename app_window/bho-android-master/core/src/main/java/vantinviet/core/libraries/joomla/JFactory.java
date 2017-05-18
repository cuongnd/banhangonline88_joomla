package vantinviet.core.libraries.joomla;

import android.content.Context;
import android.content.pm.ApplicationInfo;
import android.content.pm.PackageManager;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.text.TextUtils;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import org.apache.http.util.EncodingUtils;

import vantinviet.core.VTVConfig;
import vantinviet.core.configuration.JConfig;
import vantinviet.core.configuration.JConfig_countdown;
import vantinviet.core.libraries.cms.application.JApplicationSite;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.session.JSession;
import vantinviet.core.libraries.joomla.uri.JUri;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.legacy.database.mysqli;
import vantinviet.core.libraries.utilities.JUtilities;

import static vantinviet.core.libraries.legacy.controller.JControllerLegacy.app;


/**
 * Created by cuongnd on 6/8/2016.
 */
public class JFactory {

    private static Object config;
    private static Context context;
    private static JApplication application;
    private static JUser user;
    public static String language;
    private static vtv_WebView webBrowser=null;
    private static VTVConfig vtv_config;

    public static JMenu getMenu() {
        return JMenu.getInstance();
    }

    public static JUri getUri(String link) {
        return JUri.getInstance(link);

    }

    public static JConfig getConfig() {
        JApplication app=JFactory.getApplication();
        Context context= app.getContext();
        String app_name= JFactory.getAppLable(context);
        JConfig config;
        switch (app_name) {
            case "countdown":
                config= JConfig_countdown.getInstance();
                break;
            default:
                config= JConfig.getInstance();
                break;
        }
        return config;
    }




    public static String getAppLable(Context context) {
        PackageManager packageManager = context.getPackageManager();
        ApplicationInfo applicationInfo = null;
        try {
            applicationInfo = packageManager.getApplicationInfo(context.getApplicationInfo().packageName, 0);
        } catch (final PackageManager.NameNotFoundException e) {
        }
        return (String) (applicationInfo != null ? packageManager.getApplicationLabel(applicationInfo) : "Unknown");
    }


    public static JApplication getApplication() {
        return JApplication.getInstance();
    }
    public static JApplicationSite getApplication(String client) {
        return JApplicationSite.getInstance(client);
    }
    public static JSession getSession() {
        return JSession.getInstance();
    }


    public static JUser getUser() {
        return JUser.getInstance();
    }
    public static JUser getUser(int id) {
        return JUser.getInstance(id);
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public static vtv_WebView getWebBrowser() {
        if(webBrowser==null)
        {
            webBrowser = new vtv_WebView(app.getContext());



        }
        WebViewClient web_view_client = new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                return false;
            }

            @Override
            public void onPageFinished(WebView view, String url) {
                view.loadUrl("javascript:HtmlViewer.showHTML" +
                        "(document.getElementsByTagName('body')[0].innerHTML);");
            }
        };
        webBrowser.getSettings().setJavaScriptEnabled(true);
        webBrowser.getSettings().setSupportZoom(true);
        webBrowser.getSettings().setBuiltInZoomControls(true);
        webBrowser.setWebViewClient(web_view_client);
        webBrowser.clearHistory();
        webBrowser.clearFormData();
        webBrowser.clearCache(true);
        return webBrowser;
    }

    public static VTVConfig getVTVConfig() {
        if(vtv_config==null)
        {
            vtv_config=new VTVConfig();
        }
        return vtv_config;
    }

    public static mysqli getDBO() {
        return mysqli.getInstance();
    }
}
