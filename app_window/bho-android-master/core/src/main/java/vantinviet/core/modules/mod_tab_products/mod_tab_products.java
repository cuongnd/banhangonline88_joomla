package vantinviet.core.modules.mod_tab_products;

import android.app.Activity;
import android.graphics.Color;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v7.app.ActionBarActivity;
import android.util.SparseArray;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.JavascriptInterface;
import android.widget.LinearLayout;
import android.widget.TextView;


import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.google.gson.JsonObject;
import com.google.gson.reflect.TypeToken;

import org.json.JSONObject;

import java.lang.ref.WeakReference;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import timber.log.Timber;

import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.libraries.utilities.MessageType;
import vantinviet.core.modules.mod_easysocial_login.tmpl.class_default.DialogFragmentLogin;
import vantinviet.core.modules.mod_easysocial_login.tmpl.m_default_logout;


import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;



/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products extends FragmentActivity implements MaterialTabListener {


    private final Module module;
    private final LinearLayout linear_layout;
    private static final String KEY_DEMO = "demo";
    LinearLayout tab_content;
    MaterialTabHost tabHost;
    JApplication app= JFactory.getApplication();
    Module_tab_product_tmpl_default object_tab_product_tmpl_default;
    ArrayList<Mod_tab_product_helper.List_category_product> list_main_category_product;
    ArrayList<Module_tab_product_tmpl_default_tab_content> list_module_tab_product_tmpl_default_tab_content;
    private ViewPager pager;
    private PagerAdapter pager_adapter;
    ScreenSlidePagerAdapter adapterViewPager;
    ViewPager vpPager;

    public mod_tab_products(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        String content=this.module.getContent();
        if(content.isEmpty()){
            Timber.d("content module %s(%d) is empty",module.getModuleName(),module.getId());
            return;
        }
        Timber.d("module content %s",content.toString());
        Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
        list_main_category_product = JUtilities.getGsonParser().fromJson(content, listType);
        //Timber.d("list_main_category_product %s", list_main_category_product.toString());
        init();
    }

    private void init() {
        object_tab_product_tmpl_default =new Module_tab_product_tmpl_default(app.getContext(),this.module);
        tabHost = (MaterialTabHost) object_tab_product_tmpl_default.findViewById(R.id.tabHost);
        vpPager= (ViewPager) object_tab_product_tmpl_default.findViewById(R.id.pager);

        adapterViewPager = new ScreenSlidePagerAdapter(app.getSupportFragmentManager());
        vpPager.setAdapter(adapterViewPager);
        vpPager.setId(module.getId());

       /* pager = (ViewPager) object_tab_product_tmpl_default.findViewById(R.id.pager );
        pager.setId(module.getId());
        // init view pager

        FragmentManager fragmentManager = getSupportFragmentManager();

        pager_adapter = new ViewPagerAdapter(fragmentManager);
        pager.setAdapter(pager_adapter);*/
        vpPager.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {
            class ajax_list_category_product_PageSelected {
                int position;
                public ajax_list_category_product_PageSelected(int position) {
                    this.position=position;
                }
                @JavascriptInterface
                public void showHTML(String html_data) {
                    html_data= JUtilities.get_string_by_string_base64(html_data);
                    Page page = JUtilities.getGsonParser().fromJson(html_data, Page.class);
                    String component_content=page.getComponent_response();
                    Timber.d("html response ajax_get_list_category_product %s",component_content);
                    app.getCurrentActivity().runOnUiThread(new Runnable()
                    {
                        public void run()
                        {
                            // when user do a swipe the selected tab change
                            tabHost.setSelectedNavigationItem(position);
                        }

                    });
                    app.getProgressDialog().dismiss();
                }
                @JavascriptInterface
                public void HtmlViewer(String html) {
                    html= JUtilities.get_string_by_string_base64(html);
                    Page page = JUtilities.getGsonParser().fromJson(html, Page.class);
                    String component_content=page.getComponent_response();
                    Timber.d("html response ajax_get_list_category_product %s",component_content);
                    app.getCurrentActivity().runOnUiThread(new Runnable()
                    {
                        public void run()
                        {
                            // when user do a swipe the selected tab change
                            tabHost.setSelectedNavigationItem(position);
                        }

                    });
                    app.getProgressDialog().dismiss();
                }

            }

            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onPageSelected(int position) {
                vtv_WebView web_browser = JFactory.getWebBrowser();
                Map<String, String> post = new HashMap<String, String>();
                JMenu menu = JFactory.getMenu();
                int active_menu_item_id = menu.getMenuactive().getId();
                post.put("option", "com_modules");
                post.put("task", "module.app_ajax_render_module");
                post.put("module_id",String.valueOf(module.getId()));
                post.put(app.getSession().getFormToken(), "1");
                post.put("tmpl", "component");
                post.put("Itemid", String.valueOf(active_menu_item_id));
                web_browser.vtv_postUrl(VTVConfig.rootUrl, post);
                app.getProgressDialog().show();
                web_browser.addJavascriptInterface(new ajax_list_category_product_PageSelected(position), "HtmlViewer");




            }
        });

        // insert all tabs from pagerAdapter data
        for (int i = 0; i < adapterViewPager.getCount(); i++) {
            tabHost.addTab(
                    tabHost.newTab()
                            .setText(adapterViewPager.getPageTitle(i))
                            .setTabListener(this)
            );

        }
        LinearLayout.LayoutParams module_title_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout module_title=(LinearLayout)object_tab_product_tmpl_default.findViewById(R.id.module_title );
        module_title.setBackgroundColor(Color.parseColor("#eff0f1"));
        module_title_params.setMargins(0,0,0,20);
        module_title.setLayoutParams(module_title_params);
        TextView title=(TextView) module_title.findViewById(R.id.title);
        title.setText(module.getTitle());
        title.setTextAppearance(app.getContext(), R.style.module_title_text);

        LinearLayout.LayoutParams new_vertical_wrapper_of_module_linear_layout_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout new_wrapper_of_module_content_linear_layout=new LinearLayout(app.getContext());
        new_wrapper_of_module_content_linear_layout.setLayoutParams(new_vertical_wrapper_of_module_linear_layout_params);
        new_wrapper_of_module_content_linear_layout.setOrientation(LinearLayout.VERTICAL);

        new_wrapper_of_module_content_linear_layout.addView(object_tab_product_tmpl_default);

        linear_layout.addView(new_wrapper_of_module_content_linear_layout);

    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public void onTabSelected(MaterialTab tab) {
        int position=tab.getPosition();
        Category category=list_main_category_product.get(position).getDetail();
        vtv_WebView web_browser = JFactory.getWebBrowser();
        Map<String, String> post = new HashMap<String, String>();
        JMenu menu = JFactory.getMenu();
        int active_menu_item_id = menu.getMenuactive().getId();
        post.put("option", "com_modules");
        post.put("task", "module.app_ajax_render_module");
        post.put("module_id",String.valueOf(module.getId()));
        post.put("category_id",String.valueOf(category.getCategory_id()));
        Params params=new Params();
        ObjectMapper mapper = new ObjectMapper();
        String paramsjsonInString="";
        try {
            paramsjsonInString = mapper.writeValueAsString(params);
        } catch (JsonProcessingException e) {
            e.printStackTrace();
        }
        post.put("params",paramsjsonInString);
        post.put(app.getSession().getFormToken(), "1");
        post.put("tmpl", "component");
        post.put("Itemid", String.valueOf(active_menu_item_id));
        web_browser.vtv_postUrl(VTVConfig.rootUrl, post);
        app.getProgressDialog().show();
        web_browser.addJavascriptInterface(new mod_tab_products.ajax_list_category_product(tab), "HtmlViewer");


    }

    @Override
    public void onTabReselected(MaterialTab tab) {

    }

    @Override
    public void onTabUnselected(MaterialTab tab) {

    }


    private class ScreenSlidePagerAdapter extends FragmentStatePagerAdapter {
        public ScreenSlidePagerAdapter(FragmentManager fm) {
            super(fm);
        }


        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        @Override
        public Fragment getItem(int position) {


            Mod_tab_product_helper.List_category_product list_category_product=list_main_category_product!=null?list_main_category_product.get(position):null;
            Module_tab_product_tmpl_default_tab_content module_tab_product_tmpl_default_tab_content = new Module_tab_product_tmpl_default_tab_content();
            module_tab_product_tmpl_default_tab_content.list_category_product=list_category_product;
            return module_tab_product_tmpl_default_tab_content;
        }
        @Override
        public int getCount() {
            return list_main_category_product.size();
        }
        @Override
        public CharSequence getPageTitle(int position) {
            Category category=list_main_category_product.get(position).getDetail();
            return category.getName();
        }


    }
    private class ajax_list_category_product {
        private  MaterialTab tab;
        public ajax_list_category_product(MaterialTab tab) {
            this.tab=tab;
        }

        @RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
        @JavascriptInterface
        public void showHTML(String html) {
            html= JUtilities.get_string_by_string_base64(html);
            Page page = JUtilities.getGsonParser().fromJson(html, Page.class);
            String component_content=page.getComponent_response();
            Timber.d("html response ajax_get_list_category_product %s",component_content);
            app.getCurrentActivity().runOnUiThread(new Runnable()
            {
                public void run()
                {
                    vpPager.setCurrentItem(tab.getPosition());
                }

            });
            app.getProgressDialog().dismiss();
        }

    }

    private class Params {
        String layout="_:products";
        ClassParent parent=new ClassParent();

        private class ClassParent {
            String sub1="sub1";
            String sub2="sub2";
        }
    }

/*
    private void init() {
        app= MyApplication.getInstance();

        String response=this.module.getResponse();
        Timber.d("mod_tab_products response %s",response.toString());
        object_tab_product_tmpl_default =new Module_tab_product_tmpl_default(mInstance,this.module);

        LinearLayout.LayoutParams new_vertical_wrapper_of_module_linear_layout_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout new_wrapper_of_module_content_linear_layout=new LinearLayout(mInstance);
        new_wrapper_of_module_content_linear_layout.setLayoutParams(new_vertical_wrapper_of_module_linear_layout_params);
        new_wrapper_of_module_content_linear_layout.setOrientation(LinearLayout.VERTICAL);

        new_wrapper_of_module_content_linear_layout.addView(object_tab_product_tmpl_default);

        linear_layout.addView(new_wrapper_of_module_content_linear_layout);
        main_category_tab_host = new MaterialTabHost(mInstance);
        main_category_tab_host.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 100));
        // insert all tabs from pagerAdapter data
        pager = new MyViewPager(mInstance);
        pager.setId(R.id.account_address);
        pager.setLayoutParams(new LinearLayout.LayoutParams(WRAP_CONTENT, 100));


        // init view pager
        pager_adapter = new ViewPagerAdapter( (getSupportFragmentManager()));
        pager.setAdapter(pager_adapter);
        pager.setOnPageChangeListener(new MyViewPager.SimpleOnPageChangeListener() {
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                main_category_tab_host.setSelectedNavigationItem(position);

            }
        });



        for (int i = 0; i < pager_adapter.getCount(); i++) {
            main_category_tab_host.addTab(
                    main_category_tab_host.newTab()
                            .setText(pager_adapter.getPageTitle(i))
                            .setTabListener(this)
            );

        }


        tab_content=new LinearLayout(mInstance);
        tab_content.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 500));
        main_category_tab_host.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 100));

        LinearLayout tab_main_category_linear_layout_tmpl_default =(LinearLayout) object_tab_product_tmpl_default.findViewById(R.id.list_main_category);
        tab_main_category_linear_layout_tmpl_default.addView(main_category_tab_host);





    }
*/


}
