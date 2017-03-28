package vantinviet.banhangonline88.ux.fragments;

import android.app.ProgressDialog;
import android.os.Bundle;
import android.os.Handler;
import android.support.annotation.NonNull;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebView;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.google.gson.Gson;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;

import timber.log.Timber;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.Utils;
import vantinviet.banhangonline88.ux.MainActivity;

/**
 * Fragment allow displaying useful information content like web page.
 * Requires input argument - id of selected page. Pages are created in OpenShop server administration.
 */
public class PageMenuItemFragment extends Fragment {

    /**
     * Name for input argument.
     */
    private static final String PAGE_ID = "page_id";

    private static final long TERMS_AND_CONDITIONS = -131;
    private static final String LINK = "link";
    private static final String PAGE_OBJECT = "page";

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
    private MyApplication app;
    private Fragment fragment;


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


    public void load_page(final String json_page)  {
        Gson gson = new Gson();
        DrawerMenuItem drawerMenuItem =new DrawerMenuItem();

        String url="";
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


        final DrawerMenuItem finalDrawerMenuItem = drawerMenuItem;
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
        MyApplication.getInstance().addToRequestQueue(getPage, CONST.PAGE_REQUESTS_TAG);
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
