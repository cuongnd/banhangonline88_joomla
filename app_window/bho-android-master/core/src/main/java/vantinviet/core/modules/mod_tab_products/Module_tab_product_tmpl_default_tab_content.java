package vantinviet.core.modules.mod_tab_products;

import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.JavascriptInterface;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.fasterxml.jackson.annotation.JsonAutoDetect;
import com.fasterxml.jackson.annotation.JsonIgnore;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.deser.std.StringArrayDeserializer;
import com.fasterxml.jackson.databind.exc.InvalidDefinitionException;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.modules.mod_easysocial_login.tmpl.m_default_logout;

/**
 * Created by neokree on 16/12/14.
 */

/**
 * Created by neokree on 16/12/14.
 */
public class Module_tab_product_tmpl_default_tab_content extends Fragment {
    public Mod_tab_product_helper.List_category_product list_category_product;
    private ArrayList<Product> list_product=new ArrayList<Product>();
    public  LinearLayout view;
    public boolean is_loaded=false;
    private JApplication app=JFactory.getApplication();
    public Module module;
    public RecyclerViewDataAdapter adapter;
    public CategoryListDataAdapter category_adapter;
    public Show_product_recycler_view show_product_recycler_view;
    public RecyclerView cagory_recycler_view;;

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        this.view = (LinearLayout)inflater.inflate(R.layout.modules_mod_tab_products_tmpl_default_tab_content_wrapper, container, false);


        if(list_category_product.getIs_loaded()==1) {
            re_layout();
        }else {
            LinearLayoutLoading loading=new LinearLayoutLoading(app.getContext());
            this.view.addView(loading);
            /*Timber.d("hello Request_ajax");
            Timber.d("list_category_product %s",list_category_product.toString());
            vtv_WebView web_browser = JFactory.getWebBrowser();
            web_browser_setup(web_browser);
            app.getProgressDialog().show();
            web_browser.addJavascriptInterface(new ajax_list_category_product(), "HtmlViewer");*/
        }

        return view;
    }


    private void build_layout() {

    }
    private void re_layout() {
        this.view.removeAllViews();
        LinearLayoutContent view_content=new LinearLayoutContent(app.getContext());
        this.view.addView(view_content);
        show_product_recycler_view = new Show_product_recycler_view(list_category_product);
        RecyclerView product_recycler_view = (RecyclerView) view_content.findViewById(R.id.product_recycler_view);
        product_recycler_view.setHasFixedSize(true);

        adapter = new RecyclerViewDataAdapter(getContext(), show_product_recycler_view);
        product_recycler_view.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.VERTICAL, false));
        product_recycler_view.setAdapter(adapter);


        cagory_recycler_view = (RecyclerView) view_content.findViewById(R.id.category_recycler_view);
        cagory_recycler_view.setHasFixedSize(true);
        ArrayList<Category> list_category= new ArrayList<Category>();;
        try{
            list_category = list_category_product.getList_sub_category_detail();
        }catch (Exception ex){
            Timber.d("ex %s",ex.toString());
        }

        category_adapter = new CategoryListDataAdapter(getContext(), list_category);
        cagory_recycler_view.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        cagory_recycler_view.setAdapter(category_adapter);
    }


    public void init() {
        Timber.d("hello 1224 ");
    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    private void web_browser_setup(vtv_WebView web_browser) {
        Category category=list_category_product.getDetail();
        Map<String, String> post = new HashMap<String, String>();
        JMenu menu = JFactory.getMenu();
        int active_menu_item_id = menu.getMenuactive().getId();
        post.put("option", "com_modules");
        post.put("task", "module.app_ajax_render_module");
        post.put("module_id",String.valueOf(module.getId()));
        Timber.d("tab category name %s",category.getName());
        post.put("category_id",String.valueOf(category.getCategory_id()));
        Params params=new Params();
        String paramsjsonInString = JUtilities.getGsonParser().toJson(params);

        Timber.d("paramsjsonInString %s",paramsjsonInString);

        post.put("params",paramsjsonInString);
        post.put(app.getSession().getFormToken(), "1");
        post.put("tmpl", "component");
        post.put("Itemid", String.valueOf(active_menu_item_id));
        web_browser.vtv_postUrl(VTVConfig.rootUrl, post);
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void getAjax_load_data() {
        vtv_WebView web_browser = JFactory.getWebBrowser();
        web_browser_setup(web_browser);
        web_browser.addJavascriptInterface(new ajax_list_category_product(), "HtmlViewer");
    }

    private class Params {
        public String layout="_:products.app";
        public ClassParent parent=new ClassParent();

        private class ClassParent {
            String sub1="sub1";
            String sub2="sub2";
        }
    }

    public class Show_product_recycler_view {
        private final Mod_tab_product_helper.List_category_product list_category_product;
        Category category;
        ArrayList<Product> list_product;
        public Show_product_recycler_view(Mod_tab_product_helper.List_category_product list_category_product) {
            this.list_category_product=list_category_product;
            try{
                category =list_category_product.getDetail();
            }catch (Exception ex){
                Timber.d("ex %s",ex.toString());
            }
            try{
                list_product=list_category_product.getList();
            }catch (Exception ex){
                Timber.d("ex %s",ex.toString());
            }


        }

        public ArrayList<Product> getList_product() {
            return list_product;
        }

        public Category getCategory() {
            return category;
        }
    }
    private class ajax_list_category_product {
        public ajax_list_category_product() {
        }

        @RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
        @JavascriptInterface
        public void showHTML(String html) {
            html= JUtilities.get_string_by_string_base64(html);
            Page page = JUtilities.getGsonParser().fromJson(html, Page.class);
            String component_content=page.getComponent_response();

            ArrayList<Mod_tab_product_helper.List_category_product> list_main_category_product_current_tab;
            Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
            list_main_category_product_current_tab = JUtilities.getGsonParser().fromJson(component_content, listType);
            //Category category_detail = list_category_product.getDetail();
            //list_main_category_product.set(Position,list_main_category_product_current_tab.get(0));
            //list_main_category_product.get(Position).setDetail(category_detail);
            //Timber.d("html response ajax_get_list_category_product %s",list_main_category_product_current_tab.toString());
            list_category_product=list_main_category_product_current_tab.get(0);
            is_loaded=true;
            app.getCurrentActivity().runOnUiThread(new Runnable()
            {
                public void run()
                {
                    re_layout();

                }

            });
        }

    }

    private class LinearLayoutLoading extends LinearLayout {

        private final View view_loading;

        public LinearLayoutLoading(Context context) {
            super(context);
            view_loading=inflate(getContext(), R.layout.modules_mod_tab_products_tmpl_default_tab_content_loading, this);
        }
    }

    private class LinearLayoutContent extends LinearLayout {
        private final View view_content;

        public LinearLayoutContent(Context context) {
            super(context);
            view_content=inflate(getContext(), R.layout.modules_mod_tab_products_tmpl_default_tab_content_layout, this);
        }
    }
}
