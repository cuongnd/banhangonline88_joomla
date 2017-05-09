package vantinviet.core.libraries.legacy.application;

import android.app.Activity;
import android.app.FragmentManager;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.os.Build;
import android.preference.PreferenceManager;
import android.support.annotation.RequiresApi;
import android.support.v7.app.AppCompatActivity;
import android.util.DisplayMetrics;
import android.widget.LinearLayout;
import android.widget.ScrollView;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;


import vantinviet.core.libraries.cms.application.JApplicationSite;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.html.bootstrap.Template;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.application.JApplicationBase;
import vantinviet.core.libraries.joomla.input.JInput;
import vantinviet.core.libraries.utilities.JUtilities;




/**
 * Created by cuongnd on 6/7/2016.
 */
public class JApplication extends JApplicationBase {
    private static final String CURRENT_LINK = "current_link";
    private static final String VTV_PREFERENCES = "vtv_preferences";
    public static JApplication instance;
    private static String link;
    public AppCompatActivity context;
    private String redirect;
    public VTVConfig vtvConfig=VTVConfig.getInstance();
    Map<String, String> list_input = new HashMap<String, String>();

    private String title;
    public JInput input;
    private String component_response;
    SharedPreferences shared_preferences;
    public LinearLayout root_linear_layout;
    public int component_width;
    private ScrollView main_scroll_view;
    private byte[] setPostBrowser;
    private Resources resources;
    private FragmentManager fragmentManager;
    private android.support.v4.app.FragmentManager supportFragmentManager;

    /* Static 'instance' method */
    public static JApplication getInstance() {

        if (instance == null) {
            instance = new JApplication();
        }
        return instance;
    }

    public JApplication(){
        input=JInput.getInstance();
    }
    public JMenu getMenu() {
        JMenu menu = JMenu.getInstance();
        return menu;
    }
    public Template getTemplate() {
        return template;
    }

    public ArrayList<Module> getModules() {
        return modules;
    }

    public String get_session() {
        //final SharedPreferences sharedpreferences = getSharedPreferences(MyPREFERENCES, Context.MODE_PRIVATE);
        //String session=sharedpreferences.getString(SESSION,"");
        String session="";
        return session;
    }
    public String get_token_android_link(String url) {
        String session=get_session();
        url=url+"&ignoreMessages=true&format=json&os=android&token="+session+"&"+session+"=1";
        return url;

    }
    public String get_page_config_app(String url) {
        String session=get_session();
        url=url+"&get_page_config_app=1&ignoreMessages=true&format=json&os=android&token="+session+"&"+session+"=1";
        return url;

    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void doExecute() {
        Timber.plant(new Timber.DebugTree());
        config_screen_size();
        SharedPreferences sharedpreferences;
        VTVConfig vtv_config = JFactory.getVTVConfig();
        int caching = vtv_config.getCaching();
        String link = getLink();
        if (link == null) {
            link = vtvConfig.getRootUrl();
        }
        setProgressDialog(JUtilities.generateProgressDialog(getContext(), false));
        setRoot_linear_layout((LinearLayout) getCurrentActivity().findViewById(R.id.root_linear_layout));
        setMain_scroll_view((ScrollView) getCurrentActivity().findViewById(R.id.main_scroll_view));

        WebView webview = WebView.getInstance();
        if (caching == 1) {
            sharedpreferences = getSharedPreferences(LIST_DATA_RESPONSE_BY_URL, getCurrentActivity().MODE_PRIVATE);
            String response_data = sharedpreferences.getString(link, "");
            if (response_data.equals("")) {
                //webview.create_browser(link);

            } else {
                webview.go_to_page(response_data);
            }
        } else {
            //getProgressDialog().show();
            webview.create_browser(link);
            //getProgressDialog().dismiss();
        }
    }

    private SharedPreferences getSharedPreferences(String listDataResponseByUrl, int modePrivate) {
        return null;
    }

    public static void config_screen_size() {
        //set screen size
        DisplayMetrics metrics = new DisplayMetrics();
        getCurrentActivity().getWindowManager().getDefaultDisplay().getMetrics(metrics);
        int screenDensity = (int) metrics.density;
        int screenDensityDPI = metrics.densityDpi;
        float screenscaledDensity = metrics.scaledDensity;

        int width = metrics.widthPixels;
        int height = metrics.heightPixels;

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
    }


    public void setCurrentActivity(Activity currentActivity) {
        this.currentActivity = currentActivity;
    }
    public static Activity getCurrentActivity() {
        return  currentActivity;
    }
    public void setRedirect(String link) {
        /*String test_page = "&Itemid=433";
        test_page = "";
        if (link.equals("")) {
            link =  vtvConfig.getRootUrl()+ "/index.php?os=android&screenSize=" + vtvConfig.getScreen_size_width() + "&version=" +vtvConfig.getLocal_version() + test_page+"&format=json&get_page_config_app=1";
        } else if (!link.contains( vtvConfig.getRootUrl())) {
            link =  vtvConfig.getRootUrl() + "/" + link;
        }else if(link.equals(vtvConfig.getRootUrl())){
            link =  vtvConfig.getRootUrl()+ "/index.php?os=android&screenSize=" + vtvConfig.getScreen_size_width() + "&version=" +vtvConfig.getLocal_version() + test_page+"&format=json&get_page_config_app=1";
        }else if (link.contains( vtvConfig.getRootUrl())) {
            link =  link+ "&os=android&screenSize=" + vtvConfig.getScreen_size_width() + "&version=" +vtvConfig.getLocal_version() + test_page+"&format=json&get_page_config_app=1";
        }*/
        setLink(link);
        saveCurrentLink(link);
        //Intent myIntent = new Intent(getCurrentActivity(), MainActivity.class);
        //myIntent.putExtra("key", value); //Optional parameters
        //getCurrentActivity().startActivity(myIntent);

    }

    private void saveCurrentLink(String link) {
        shared_preferences = PreferenceManager
                .getDefaultSharedPreferences(getCurrentActivity());
        SharedPreferences.Editor editor = shared_preferences.edit();
        editor.putString(CURRENT_LINK, link);
        editor.apply();
    }


    public String getCurrentLink() {
        shared_preferences = PreferenceManager
                .getDefaultSharedPreferences(getCurrentActivity());
        String current_link = shared_preferences.getString(CURRENT_LINK, null);
        return  current_link;
    }

    public void setRedirect(String link, Map<String, String> data_post) {
        JApplication app=JFactory.getApplication();
        String screenSize = Integer.toString(VTVConfig.screen_size_width/ VTVConfig.screenDensity) + "x" + Integer.toString( VTVConfig.screen_size_height);
        String local_version= VTVConfig.get_version();

        link=link+"&os=android&screenSize="+ screenSize+"&version="+local_version;
        System.out.println("link:"+link);
        JApplicationSite.host=link;
        Intent i = new Intent(this.context, app.context.getClass());
        this.context.startActivity(i);

    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void execute() {
        doExecute();
    }


    public void setAplication(Page page) {
        this.template = page.getTemplate();
        this.modules = page.getModules();
        this.list_input = page.getList_input();
        this.component_response = page.getComponent_response();
        this.input.setList_input(page.getList_input());
        this.input=JInput.getInstance();
    }

    public String getTitle() {
        return title;
    }

    public Map<String, String> getList_input() {
        return list_input;
    }

    public String getComponent_response() {
        return component_response;
    }

    public int get_Component_width() {
        return component_width;
    }

    public void setMain_scroll_view(ScrollView main_scroll_view) {
        this.main_scroll_view = main_scroll_view;
    }

    public ScrollView getMain_scroll_view() {
        return main_scroll_view;
    }


    public void setResources(Resources resources) {
        this.resources = resources;
    }
    public Resources getResources() {
        return resources;
    }


    public int get_layout_activity_main() {
        return R.layout.vtv_activity_main;
    }


    public void setRoot_linear_layout(LinearLayout root_linear_layout) {
        this.root_linear_layout = root_linear_layout;
    }

    public LinearLayout getRoot_linear_layout() {
        return root_linear_layout;
    }

    public void setFragmentManager(FragmentManager fragmentManager) {
        this.fragmentManager = fragmentManager;
    }
    public FragmentManager getFragmentManager() {
        return fragmentManager;
    }

    public void setSupportFragmentManager(android.support.v4.app.FragmentManager supportFragmentManager) {
        this.supportFragmentManager = supportFragmentManager;
    }

    public android.support.v4.app.FragmentManager getSupportFragmentManager() {
        return supportFragmentManager;
    }
}
