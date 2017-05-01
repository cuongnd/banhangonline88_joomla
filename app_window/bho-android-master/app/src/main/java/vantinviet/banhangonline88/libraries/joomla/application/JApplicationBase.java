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
    public static AppCompatActivity currentActivity;
    private String link;
    public static final String LIST_DATA_RESPONSE_BY_URL = "list_data_response_by_url";
    private ProgressDialog progressDialog;




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
