package vantinviet.core.components.com_hikashop.views.checkout.tmpl;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.ViewPager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.JavascriptInterface;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JAlert;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.libraries.utilities.MessageType;

import static vantinviet.core.libraries.legacy.application.JApplication.getCurrentActivity;

/**
 * TODO: document your custom register_view class.
 */
public class login extends LinearLayout  implements MaterialTabListener{
    static JApplication app= JFactory.getApplication();
    private View checkout_tmpl_login_view;
    private MaterialTabHost tabHost;
    private ViewPager vpPager;
    private ScreenSlidePagerAdapter adapterViewPager;
    private JUser user;
    public login(Context context) {
        super(context);
        user=JFactory.getUser();
        if(user.getId()>0) {
            TextView text_view=new TextView(app.getContext());
            text_view.setText(R.string.str_you_are_logged_in);
            checkout_tmpl_login_view =(View)text_view;
        }else{
            checkout_tmpl_login_view = inflate(getContext(), R.layout.components_com_hikashop_views_checkout_tmpl_login, this);
            init();
        }
    }
    private void init() {
        tabHost = (MaterialTabHost) checkout_tmpl_login_view.findViewById(R.id.tabHost_login_register);
        vpPager= (ViewPager) checkout_tmpl_login_view.findViewById(R.id.pager_login_register);
        MaterialTab tab_login=new MaterialTab(app.getContext(),false);
        tab_login.setText(getContext().getString(R.string.str_login));
        tab_login.setTabListener(this);
        tabHost.addTab(tab_login);
        MaterialTab tab_register=new MaterialTab(app.getContext(),false);
        tab_register.setText(getContext().getString(R.string.str_register));
        tab_register.setTabListener(this);
        tabHost.addTab(tab_register);


        adapterViewPager = new ScreenSlidePagerAdapter(app.getSupportFragmentManager());
        vpPager.setAdapter(adapterViewPager);
        vpPager.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {

            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onPageSelected(int position) {
                tabHost.setSelectedNavigationItem(position);

            }
        });
    }

    @Override
    public void onTabSelected(MaterialTab tab) {
        vpPager.setCurrentItem(tab.getPosition());
    }

    @Override
    public void onTabReselected(MaterialTab tab) {

    }

    @Override
    public void onTabUnselected(MaterialTab tab) {

    }


    public static class TabLogin  extends Fragment {
        private View login_view;
        public EditText txt_username;
        public EditText txt_password;


        @Override
        public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

            login_view = inflater.inflate(R.layout.components_com_hikashop_views_checkout_tmpl_login_login, container, false);
            init();
            return login_view;
        }


        public void init(){

            Button btn_login=(Button)login_view.findViewById(R.id.btn_login);
            btn_login.setOnClickListener(new OnClickListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onClick(View v) {
                    login();
                }
            });
        }
        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        public void login(){
            if(validate()){
                vtv_WebView web_browser = JFactory.getWebBrowser();
                Map<String, String> post = new HashMap<String, String>();
                post.put("option", "com_users");
                post.put("task", "user.ajax_login");
                post.put("username", txt_username.getText().toString().trim());
                post.put("password", txt_password.getText().toString().trim());
                web_browser.vtv_postUrl(VTVConfig.rootUrl,post);
                app.getProgressDialog(R.string.str_login_processing).show();
                web_browser.addJavascriptInterface(new ajax_login(), "HtmlViewer");
            }

        }

        private boolean validate() {
            txt_username = (EditText)login_view.findViewById(R.id.txt_username);
            txt_password = (EditText)login_view.findViewById(R.id.txt_password);
            if(txt_username.getText().toString().trim().isEmpty()){
                JUtilities.alert(MessageType.ERROR,R.string.username_requirement);
                return false;
            }else if(txt_password.getText().toString().trim().isEmpty()){
                JUtilities.alert(MessageType.ERROR,R.string.password_requirement);
                return false;
            }
            return true;
        }

        private class ajax_login {
            JApplication app=JFactory.getApplication();
            public ajax_login() {
            }

            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @JavascriptInterface
            public void showHTML(String html) {
                app.getProgressDialog().dismiss();
                html = JUtilities.get_string_by_string_base64(html);
                Page page = JUtilities.getGsonParser().fromJson(html, Page.class);
                String component_response = page.getComponent_response();
                Timber.d("html response %s", component_response);
                Response response = JUtilities.getGsonParser().fromJson(component_response, Response.class);
                int user_id = response.getId();
                if(user_id>0){
                    AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                            getCurrentActivity());
                    alertDialogBuilder
                            .setTitle(MessageType.INFO)
                            .setMessage(R.string.str_login_successful)
                            .setCancelable(false)
                            .setPositiveButton(R.string.str_close,new DialogInterface.OnClickListener() {
                                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                                public void onClick(DialogInterface dialog, int id) {
                                    app.getCurrentActivity().runOnUiThread(new Runnable() {
                                        @Override
                                        public void run() {
                                            app.refresh_page();
                                        }
                                    });

                                }
                            });
                    AlertDialog alertDialog = alertDialogBuilder.create();
                    // show it
                    alertDialog.show();


                }else {
                    JAlert.show(MessageType.ERROR,R.string.str_login_false, app,"refresh_page");
                }
            }


            private class Response {
                JUser user;
                int id;
                String name;

                public int getId() {
                    return id;
                }
            }
        }
    }
    public static class TabRegister  extends Fragment {
        private View register_view;
        public EditText txt_email;
        public EditText txt_username;
        public EditText txt_password;
        public EditText txt_retype_password;


        @Override
        public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
            register_view = inflater.inflate(R.layout.components_com_hikashop_views_checkout_tmpl_login_register, container, false);
            txt_email = (EditText) register_view.findViewById(R.id.txt_email);
            txt_username = (EditText) register_view.findViewById(R.id.txt_username);
            txt_password = (EditText) register_view.findViewById(R.id.txt_password);
            txt_retype_password = (EditText) register_view.findViewById(R.id.txt_retype_password);
            init();
            return register_view;
        }


        public void init(){

            Button btn_register=(Button) register_view.findViewById(R.id.btn_register);
            btn_register.setOnClickListener(new OnClickListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onClick(View v) {
                    register();
                }
            });
        }
        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        public void register(){
            if(validate()){
                String str_email=txt_email.getText().toString().trim();
                String str_username=txt_username.getText().toString().trim();
                String str_password=txt_password.getText().toString().trim();
                String str_retype_password=txt_retype_password.getText().toString().trim();

                vtv_WebView web_browser = JFactory.getWebBrowser();
                Map<String, String> post = new HashMap<String, String>();
                post.put("option", "com_users");
                post.put("task", "user.ajax_register");
                post.put("username", str_username);
                post.put("email", str_email);
                post.put("password", str_password);
                web_browser.vtv_postUrl(VTVConfig.rootUrl,post);
                app.getProgressDialog(R.string.str_register_processing).show();
                web_browser.addJavascriptInterface(new ajax_register(), "HtmlViewer");
            }

        }

        private boolean validate() {
            String str_email=txt_email.getText().toString().trim();
            String str_username=txt_username.getText().toString().trim();
            String str_password=txt_password.getText().toString().trim();
            String str_retype_password=txt_retype_password.getText().toString().trim();
            if(str_email.isEmpty()){
                JAlert.show(MessageType.ERROR,R.string.str_email_requirement);
                return  false;
            }else if(!JUtilities.isValidEmail(str_email)){
                JAlert.show(MessageType.ERROR,R.string.str_error_invalid_email);
                return  false;
            }else if(str_username.isEmpty()){
                JUtilities.alert(MessageType.ERROR,R.string.username_requirement);
                return false;
            }else if(str_password.isEmpty()){
                JUtilities.alert(MessageType.ERROR,R.string.password_requirement);
                return false;
            }else if(str_retype_password.isEmpty()){
                JUtilities.alert(MessageType.ERROR,R.string.str_retype_password_requirement);
                return false;
            }else if(!str_password.equals(str_retype_password)){
                JUtilities.alert(MessageType.ERROR,R.string.str_password_incorrect);
                return false;
            }
            return true;
        }

        private class ajax_register {
            JApplication app=JFactory.getApplication();
            public ajax_register() {
            }

            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @JavascriptInterface
            public void showHTML(String html) {
                app.getProgressDialog().dismiss();
                html = JUtilities.get_string_by_string_base64(html);
                Page page = JUtilities.getGsonParser().fromJson(html, Page.class);
                String component_response = page.getComponent_response();
                Timber.d("html response %s", component_response);
                Response response = JUtilities.getGsonParser().fromJson(component_response, Response.class);
                int error = response.getError();
                if (error == 1) {
                    JAlert.show(MessageType.ERROR, response.getErrorMessenger());
                } else {
                    JAlert.show(MessageType.INFO, R.string.str_register_successfull, app, "refresh_page");
                }
            }


            private class Response {
                JUser user;
                int id;
                String name;
                private int error=0;
                private String errorMessenger;

                public int getId() {
                    return id;
                }

                public int getError() {
                    return error;
                }

                public String getErrorMessenger() {
                    return errorMessenger;
                }
            }
        }
    }


    private class ScreenSlidePagerAdapter extends FragmentStatePagerAdapter {
       ArrayList<Class> list_objectSet=new ArrayList<Class>();
        public ScreenSlidePagerAdapter(FragmentManager fm) {
            super(fm);
            list_objectSet.add(TabLogin.class);
            list_objectSet.add(TabRegister.class);
        }


        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        @Override
        public Fragment getItem(int position) {
            Constructor<?> cons = null;
            try {
                cons = list_objectSet.get(position).getConstructor();
                Object object = cons.newInstance();
                return (Fragment)object;
            } catch (NoSuchMethodException e) {
                e.printStackTrace();
            } catch (IllegalAccessException e) {
                e.printStackTrace();
            } catch (InstantiationException e) {
                e.printStackTrace();
            } catch (InvocationTargetException e) {
                e.printStackTrace();
            }
            return new Fragment();
        }
        @Override
        public int getCount()
        {
            return list_objectSet.size();
        }

    }



}
