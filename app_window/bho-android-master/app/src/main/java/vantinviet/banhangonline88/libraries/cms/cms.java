package vantinviet.banhangonline88.libraries.cms;

import android.content.Context;
import android.content.SharedPreferences;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.util.Base64;
import android.webkit.JavascriptInterface;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import com.google.android.gms.maps.SupportMapFragment;
import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;

import timber.log.Timber;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.configuration.JConfig;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.ux.fragments.PageMenuItemFragment;

import static vantinviet.banhangonline88.ux.MainActivity.mInstance;
import static vantinviet.banhangonline88.ux.fragments.PageMenuItemFragment.LIST_DATA_RESPONSE_BY_URL;

/**
 * Created by cuongnd on 12/17/2015.
 */
public class cms {
    private static MyApplication app;
    DrawerMenuItem drawerMenuItem =new DrawerMenuItem();
    private  Fragment fragment=new Fragment();
    final DrawerMenuItem finalDrawerMenuItem = drawerMenuItem;
    SharedPreferences sharedpreferences;
    String url;
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
        byte[] data= Base64.decode(html, Base64.DEFAULT);
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
            FragmentManager frgManager = mInstance.getSupportFragmentManager();
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
    public void start_remote(String host){
        sharedpreferences = MyApplication.getInstance().getSharedPreferences(LIST_DATA_RESPONSE_BY_URL, Context.MODE_PRIVATE);


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

    public void load_page(final String json_page)  {
        Gson gson = new Gson();
        DrawerMenuItem drawerMenuItem =new DrawerMenuItem();

        if(json_page==null)
        {
            url= EndPoints.API_URL1+"?";
        }else{
            drawerMenuItem =  gson.fromJson(json_page, DrawerMenuItem.class);
            Timber.d("drawerMenuItem %s",drawerMenuItem.toString());
            url=drawerMenuItem.getLink();
            if(url==null || url.equals("")){
                url=EndPoints.API_URL1+"?";
            }
        }
        Timber.d("url:%s",url);
        app= MyApplication.getInstance();
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

}
