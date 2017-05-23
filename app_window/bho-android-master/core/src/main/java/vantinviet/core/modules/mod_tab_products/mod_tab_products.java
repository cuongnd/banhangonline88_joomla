package vantinviet.core.modules.mod_tab_products;

import android.graphics.Color;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.webkit.JavascriptInterface;
import android.widget.LinearLayout;
import android.widget.TextView;


import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.google.gson.reflect.TypeToken;

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
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;



/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products extends FragmentActivity implements MaterialTabListener {


    public final Module module;
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
    int sum_load=0;


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


                    ArrayList<Mod_tab_product_helper.List_category_product> list_main_category_product_current_tab;
                    Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
                    list_main_category_product_current_tab = JUtilities.getGsonParser().fromJson(component_content, listType);
                    Category category_detail = list_main_category_product.get(position).getDetail();
                    list_main_category_product.set(position,list_main_category_product_current_tab.get(0));
                    list_main_category_product.get(position).setDetail(category_detail);

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
                tabHost.setSelectedNavigationItem(position);

/*
                vtv_WebView web_browser = JFactory.getWebBrowser();
                web_browser_setup(web_browser,position);
                app.getProgressDialog().show();
                web_browser.addJavascriptInterface(new ajax_list_category_product_PageSelected(position), "HtmlViewer");
*/




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
    private class ScreenSlidePagerAdapter extends FragmentStatePagerAdapter {
        private boolean is_first=true;
        public ScreenSlidePagerAdapter(FragmentManager fm) {
            super(fm);
        }


        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        @Override
        public Fragment getItem(int position) {
            Mod_tab_product_helper.List_category_product list_category_product = list_main_category_product != null ? list_main_category_product.get(position) : null;
            Module_tab_product_tmpl_default_tab_content module_tab_product_tmpl_default_tab_content = new Module_tab_product_tmpl_default_tab_content();

            module_tab_product_tmpl_default_tab_content.list_category_product = list_category_product;
            module_tab_product_tmpl_default_tab_content.module = module;
            Timber.d("hello Request_ajax");
            Timber.d("mdule_id %s ,list_category_product %s", module.getId(), list_category_product.toString());
            if (list_category_product.getIs_loaded() == 1) {

            } else {

                //vtv_WebView web_browser = JFactory.getWebBrowser();
                //web_browser_setup(web_browser);
                //app.getProgressDialog().show();
                //web_browser.addJavascriptInterface(new ajax_list_category_product(), "HtmlViewer");
            }

            if (list_category_product.getIs_loaded() != 1) {
                //list_main_category_product.get(position).setIs_loaded(true);
            }
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

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public void onTabSelected(MaterialTab tab) {
        int position=tab.getPosition();
        vpPager.setCurrentItem(tab.getPosition());

/*
        vtv_WebView web_browser = JFactory.getWebBrowser();
        web_browser_setup(web_browser,position);
        app.getProgressDialog().show();
        web_browser.addJavascriptInterface(new mod_tab_products.ajax_list_category_product(tab), "HtmlViewer");
*/


    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    private void web_browser_setup(vtv_WebView web_browser, int position) {
        Category category=list_main_category_product.get(position).getDetail();
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
    }

    @Override
    public void onTabReselected(MaterialTab tab) {

    }


    @Override
    public void onTabUnselected(MaterialTab tab) {

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

            ArrayList<Mod_tab_product_helper.List_category_product> list_main_category_product_current_tab;
            Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
            list_main_category_product_current_tab = JUtilities.getGsonParser().fromJson(component_content, listType);
            int Position=tab.getPosition();
            Category category_detail = list_main_category_product.get(Position).getDetail();
            list_main_category_product.set(Position,list_main_category_product_current_tab.get(0));
            list_main_category_product.get(Position).setDetail(category_detail);
            //Timber.d("html response ajax_get_list_category_product %s",list_main_category_product_current_tab.toString());

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

    public class Params {
        String layout="_:products.app";
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
