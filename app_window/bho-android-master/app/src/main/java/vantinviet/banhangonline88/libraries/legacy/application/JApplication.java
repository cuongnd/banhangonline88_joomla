package vantinviet.banhangonline88.libraries.legacy.application;

import android.content.Intent;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import android.support.v4.app.Fragment;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.AppCompatActivity;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.ScrollView;

import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.entities.template.Template;
import vantinviet.banhangonline88.libraries.android.http.JSONParser;
import vantinviet.banhangonline88.libraries.cms.application.JApplicationSite;
import vantinviet.banhangonline88.libraries.cms.menu.JMenu;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.application.JApplicationBase;
import vantinviet.banhangonline88.libraries.joomla.input.JInput;
import vantinviet.banhangonline88.ux.MainActivity;



/**
 * Created by cuongnd on 6/7/2016.
 */
public class JApplication extends JApplicationBase {
    private static final String CURRENT_LINK = "current_link";
    private static final String VTV_PREFERENCES = "vtv_preferences";
    public static JApplication instance;
    public AppCompatActivity context;
    private String redirect;
    public VTVConfig vtvConfig=VTVConfig.getInstance();
    Map<String, String> list_input = new HashMap<String, String>();
    private MainActivity mainActivity;
    private String title;
    public JInput input;
    private String component_response;
    SharedPreferences shared_preferences;
    public LinearLayout rootLinearLayout;
    public DrawerLayout maindrawerlayout;
    public Fragment main_navigation_drawer_fragment;
    public LinearLayout main_linear_layout;
    public RelativeLayout root_relative_layout;
    public int component_width;
    private ScrollView main_scroll_view;

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

    private static String call_json_get_content_website(String link) {
        String return_json="";
        int responseCode = 0;
        try {
            // instantiate our json parser
            JSONParser jParser = new JSONParser();
            JApplication app=JFactory.getApplication();


            JSONObject json_data = jParser.getJSONFromUrl(link);
            System.out.println("json_data:"+json_data.toString());
            if(json_data.has("link_redirect"))
            {
                String link_redirect=json_data.getString("link_redirect");
                app.setRedirect(link_redirect);
                return "";
            }
            System.out.println(json_data.toString());
            return_json = json_data.toString();

        } catch (Throwable t) {
            t.printStackTrace();
        }
        return return_json;

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
        Intent myIntent = new Intent(getCurrentActivity(), MainActivity.class);
        //myIntent.putExtra("key", value); //Optional parameters
        getCurrentActivity().startActivity(myIntent);

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

    public void execute() {
        super.doExecute();
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
}
