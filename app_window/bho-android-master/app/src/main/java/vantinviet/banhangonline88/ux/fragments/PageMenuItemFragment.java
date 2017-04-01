package vantinviet.banhangonline88.ux.fragments;

import android.annotation.TargetApi;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.support.annotation.NonNull;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.util.Base64;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.JavascriptInterface;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.beardedhen.androidbootstrap.BootstrapEditText;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;
import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

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
import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.Random;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import timber.log.Timber;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import android.content.SharedPreferences;

import vantinviet.banhangonline88.configuration.JConfig;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.libraries.cms.application.AsyncJsonElementViewLoader;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.Utils;
import vantinviet.banhangonline88.ux.MainActivity;

import static android.content.Context.MODE_PRIVATE;
import static vantinviet.banhangonline88.SettingsMy.PREF_USER_EMAIL;
import static vantinviet.banhangonline88.ux.MainActivity.MyPREFERENCES;
import static vantinviet.banhangonline88.ux.MainActivity.SESSION;

/**
 * Fragment allow displaying useful information content like web page.
 * Requires input argument - id of selected page. Pages are created in OpenShop server administration.
 */
public class PageMenuItemFragment extends Fragment  {

    /**
     * Name for input argument.
     */
    private static final String PAGE_ID = "page_id";

    private static final long TERMS_AND_CONDITIONS = -131;
    private static final String LINK = "link";
    private static final String PAGE_OBJECT = "page";
    private static final String LIST_DATA_RESPONSE_BY_URL = "list_data_response_by_url";

    private ProgressDialog progressDialog;

    /**
     * Reference of empty layout
     */
    private View layoutEmpty;
    /**
     * Reference of content layout
     */
    private View layoutContent;

    // Content view elements
    private TextView pageTitle;
    private WebView pageContent;
    private static MyApplication app;
    private String data;
    private String mimeType;
    private String encoding;
    DrawerMenuItem drawerMenuItem =new DrawerMenuItem();
    private  Fragment fragment=new Fragment();
    final DrawerMenuItem finalDrawerMenuItem = drawerMenuItem;
    SharedPreferences sharedpreferences;
    String url;
    /**
     * Create fragment instance which displays Terms and Conditions defined on server.
     *
     * @return fragment instance for display.
     */
    public static PageMenuItemFragment newInstance(DrawerMenuItem drawerMenuItem) {
        Bundle args = new Bundle();
        Gson gson = new Gson();

        args.putString(PageMenuItemFragment.PAGE_OBJECT,gson.toJson(drawerMenuItem));
        PageMenuItemFragment fragment = new PageMenuItemFragment();
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Timber.d("load home.");
        Timber.d("%s - onCreateView", this.getClass().getSimpleName());
        View view = inflater.inflate(R.layout.fragment_page, container, false);
        MainActivity.setActionBarTitle(getString(R.string.app_name));
        progressDialog = Utils.generateProgressDialog(getActivity(), false);
        layoutEmpty = view.findViewById(R.id.page_empty);
        layoutContent = view.findViewById(R.id.page_content_layout);

        pageTitle = (TextView) view.findViewById(R.id.page_title);
        pageContent = (WebView) view.findViewById(R.id.page_content);

        // Check if fragment received some arguments.
        if (getArguments() != null && getArguments().getString(PageMenuItemFragment.PAGE_OBJECT) !=null) {
            String json_page=getArguments().getString(PageMenuItemFragment.PAGE_OBJECT);
            System.out.println("page id loading");
            System.out.println(json_page);
            load_page(json_page);
        } else {

            load_page(null);
            Timber.e(new RuntimeException(), "Created fragment with null arguments.");
            setContentVisible(false);
            MsgUtils.showToast(getActivity(), MsgUtils.TOAST_TYPE_INTERNAL_ERROR, "", MsgUtils.ToastLength.LONG);
        }
        return view;
    }

    public void start_remote(String host){
        sharedpreferences = MyApplication.getInstance().getSharedPreferences(LIST_DATA_RESPONSE_BY_URL,Context.MODE_PRIVATE);


        int caching= JConfig.getInstance().caching;
        if(caching==1){
            String response_data=sharedpreferences.getString(host,"");
            if(response_data.equals("")) {
                create_browser(host);

            }else{
                go_to_page(response_data);
            }
        }else{
            create_browser(host);
        }


    }

    private void create_browser(String host) {
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


        WebView web_browser = JFactory.getWebBrowser();
        web_browser.getSettings().setJavaScriptEnabled(true);
        web_browser.getSettings().setSupportZoom(true);
        web_browser.getSettings().setBuiltInZoomControls(true);
        web_browser.setWebViewClient(web_view_client);


        web_browser.clearHistory();
        web_browser.clearFormData();
        web_browser.clearCache(true);

        System.out.println("-------host---------");
        System.out.println(host);
        System.out.println("-------host---------");
        web_browser.loadUrl(host);
        web_browser.addJavascriptInterface(new MyJavaScriptInterfaceWebsite(), "HtmlViewer");
    }


    public void go_to_page(String html){
        byte[] data=Base64.decode(html, Base64.DEFAULT);
        try {
            html=new String(data, "UTF-8");
        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();
        }

        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(html));
        reader.setLenient(true);

        Page page = gson.fromJson(reader, Page.class);
        System.out.print("Page response: "+page.toString());
        String template=page.getTemplate().getTemplateName();
        String str_fragmentManager = String.format("fragment_template_%s",template);
        Timber.d("modules: %s",page.getModules().toString());
        Timber.d("template: %s",str_fragmentManager);
        Class<?> class_fragment = null;
        try {
            class_fragment = Class.forName("vantinviet.banhangonline88.ux.fragments." + str_fragmentManager);
            Constructor<?> cons = class_fragment.getConstructor(DrawerMenuItem.class,Page.class);
            Object object = cons.newInstance(finalDrawerMenuItem,page);
            fragment=(Fragment)object;
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (java.lang.InstantiationException e) {
            e.printStackTrace();
        }
        if (fragment != null) {
            FragmentManager frgManager = getFragmentManager();
            FragmentTransaction fragmentTransaction = frgManager.beginTransaction();
            fragmentTransaction.setAllowOptimization(false);
            fragmentTransaction.addToBackStack("hello");
            fragmentTransaction.replace(R.id.main_content_frame, fragment).commit();
        } else {
            Timber.e(new RuntimeException(), "Replace fragments with null newFragment parameter.");
        }
    }
    private class MyJavaScriptInterfaceWebsite {

        public MyJavaScriptInterfaceWebsite() {
        }

        @JavascriptInterface
        public void showHTML(String html) {
            Timber.d("json_string %s",html);
            SharedPreferences.Editor editor = sharedpreferences.edit();
            editor.putString(url,html );
            editor.commit();
            go_to_page(html);
        }

    }



    public void load_page(final String json_page)  {
        Gson gson = new Gson();
        DrawerMenuItem drawerMenuItem =new DrawerMenuItem();

        if(json_page==null)
        {
            url=EndPoints.API_URL1+"?";
        }else{
            drawerMenuItem =  gson.fromJson(json_page, DrawerMenuItem.class);
            Timber.d("drawerMenuItem %s",drawerMenuItem.toString());
            url=drawerMenuItem.getLink();
            if(url==null || url.equals("")){
                url=EndPoints.API_URL1+"?";
            }
        }
        Timber.d("url:%s",url);
        app=MyApplication.getInstance();
        url=app.get_page_config_app(url);
        start_remote(url);

       /* final DrawerMenuItem finalDrawerMenuItem = drawerMenuItem;
        GsonRequest<Page> getPage = new GsonRequest<>(Request.Method.GET, url, null, Page.class,
                new Response.Listener<Page>() {
                    @Override
                    public void onResponse(@NonNull Page page) {
                        String template=page.getTemplate().getTemplateName();
                        String str_fragmentManager = String.format("fragment_template_%s",template);
                        Timber.d("modules: %s",page.getModules().toString());
                        Timber.d("template: %s",str_fragmentManager);
                        Class<?> class_fragment = null;
                        try {
                            class_fragment = Class.forName("vantinviet.banhangonline88.ux.fragments." + str_fragmentManager);
                            Constructor<?> cons = class_fragment.getConstructor(DrawerMenuItem.class,Page.class);
                            Object object = cons.newInstance(finalDrawerMenuItem,page);
                            fragment=(Fragment)object;
                        } catch (ClassNotFoundException e) {
                            e.printStackTrace();
                        } catch (IllegalAccessException e) {
                            e.printStackTrace();
                        } catch (NoSuchMethodException e) {
                            e.printStackTrace();
                        } catch (InvocationTargetException e) {
                            e.printStackTrace();
                        } catch (java.lang.InstantiationException e) {
                            e.printStackTrace();
                        }
                        FragmentManager frgManager = getFragmentManager();
                        FragmentTransaction fragmentTransaction = frgManager.beginTransaction();
                        fragmentTransaction.setAllowOptimization(false);
                        app.fragment=fragment;
                        app.frgManager=frgManager;
                        fragmentTransaction.replace(R.id.main_content_frame, fragment).commit();
                        frgManager.executePendingTransactions();


                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                if (progressDialog != null) progressDialog.cancel();
                setContentVisible(false);
                MsgUtils.logAndShowErrorMessage(getActivity(), error);
            }
        },getFragmentManager() ,null);
        getPage.setRetryPolicy(MyApplication.getDefaultRetryPolice());
        getPage.setShouldCache(false);
        MyApplication.getInstance().addToRequestQueue(getPage, CONST.PAGE_REQUESTS_TAG);*/
    }



    /**
     * Method hides progress dialog and show received content.
     *
     * @param page page data received from server.
     */
    private void handleResponse(Page page) {
        if (page != null && page.getText() != null && !page.getText().isEmpty()) {
            setContentVisible(true);
            pageTitle.setText(page.getTitle());
            String data = page.getText();
            String header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">"
                    + "<html>  <head>  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">"
                    + "</head>  <body>";
            String footer = "</body></html>";

            pageContent.loadData(header + data + footer, "text/html; charset=UTF-8", null);
        } else {
            setContentVisible(false);
        }
        // Slow disappearing of progressDialog due to slow page content processing.
        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (progressDialog != null) progressDialog.cancel();
            }
        }, 200);
    }

    /**
     * Display content layout or empty layout.
     *
     * @param visible true for visible content.
     */
    private void setContentVisible(boolean visible) {
        if (layoutEmpty != null && layoutContent != null) {
            if (visible) {
                layoutEmpty.setVisibility(View.GONE);
                layoutContent.setVisibility(View.VISIBLE);
            } else {
                layoutEmpty.setVisibility(View.VISIBLE);
                layoutContent.setVisibility(View.GONE);
            }
        }
    }

    @Override
    public void onStop() {
        MyApplication.getInstance().cancelPendingRequests(CONST.PAGE_REQUESTS_TAG);
        if (progressDialog != null) progressDialog.cancel();
        super.onStop();
    }
}
