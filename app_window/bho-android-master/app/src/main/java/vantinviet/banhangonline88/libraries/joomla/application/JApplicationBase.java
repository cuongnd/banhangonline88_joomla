package vantinviet.banhangonline88.libraries.joomla.application;

import android.app.Application;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.SharedPreferences;
import android.support.v7.app.AppCompatActivity;
import android.util.DisplayMetrics;

import java.util.ArrayList;

import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.configuration.JConfig;
import vantinviet.banhangonline88.entities.Document;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.entities.template.Template;
import vantinviet.banhangonline88.libraries.cms.application.WebView;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.input.JInput;
import vantinviet.banhangonline88.ux.MainActivity;

import static vantinviet.banhangonline88.libraries.cms.application.JApplicationSite.initialiseApp;
import static vantinviet.banhangonline88.ux.MainActivity.MyPREFERENCES;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class JApplicationBase extends Application {
    public Template template;
    public Document document;
    public ArrayList<Module> modules=new ArrayList<Module>();
    public JInput input;
    public AppCompatActivity currentActivity;
    private String link;
    public static final String LIST_DATA_RESPONSE_BY_URL = "list_data_response_by_url";
    private ProgressDialog progressDialog;

    public void doExecute() {
        //set screen size
        DisplayMetrics metrics = new DisplayMetrics();
        currentActivity = getCurrentActivity();
        currentActivity.getWindowManager().getDefaultDisplay().getMetrics(metrics);
        int screenDensity = (int) metrics.density;
        int screenDensityDPI = metrics.densityDpi;
        float screenscaledDensity = metrics.scaledDensity;
        WebView webview=WebView.getInstance();
        int width = metrics.widthPixels;
        int height = metrics.heightPixels;
        SharedPreferences sharedpreferences;
        System.out.println("Screen Density=" + screenDensity + "\n"
                + "Screen DensityDPI=" + screenDensityDPI + "\n"
                + "Screen Scaled DensityDPI=" + screenscaledDensity + "\n"
                + "Height=" + height + "\n"
                + "Width=" + width);

        int screen_size_width = width;
        int screen_size_height = height;
        if (screenDensity == 0) {
            screenDensity = 1;
        }
        String screenSize = Integer.toString(width / screenDensity) + "x" + Integer.toString(height);
        System.out.println(width / screenDensity);
        VTVConfig vtv_config = JFactory.getVTVConfig();
        System.out.println("vtv_config.rootUrl" + vtv_config.rootUrl);
        String local_version = vtv_config.get_version();
        //initChatting();
        VTVConfig.getInstance().setScreen_size_width(screen_size_width);
        VTVConfig.getInstance().setScreen_size_height(screen_size_height);
        VTVConfig.getInstance().setScreenDensity(screenDensity);

        int caching = vtv_config.getCaching();
        String link = getLink();

        if (caching == 1) {
            sharedpreferences = getSharedPreferences(LIST_DATA_RESPONSE_BY_URL, getCurrentActivity().MODE_PRIVATE);
            String response_data = sharedpreferences.getString(link, "");
            if (response_data.equals("")) {
                webview.create_browser(link);

            } else {
                webview.go_to_page(response_data);
            }
        } else {
            webview.create_browser(link);
        }
    }
    public void setCurrentActivity(AppCompatActivity currentActivity) {
        this.currentActivity = currentActivity;
    }
    public AppCompatActivity getCurrentActivity() {
        return  this.currentActivity;
    }

    public void setLink(String link) {
        this.link = link;
    }

    public String getLink() {
        return link;
    }

    public void setProgressDialog(ProgressDialog progressDialog) {
        this.progressDialog = progressDialog;
    }

    public ProgressDialog getProgressDialog() {
        return progressDialog;
    }
}
