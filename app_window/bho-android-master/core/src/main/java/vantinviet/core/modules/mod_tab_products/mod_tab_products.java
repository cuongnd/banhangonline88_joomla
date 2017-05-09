package vantinviet.core.modules.mod_tab_products;

import android.app.Activity;
import android.graphics.Color;
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
import android.widget.LinearLayout;
import android.widget.TextView;

import com.google.gson.reflect.TypeToken;

import java.lang.ref.WeakReference;
import java.lang.reflect.Type;
import java.util.ArrayList;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import timber.log.Timber;

import vantinviet.core.R;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
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
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                tabHost.setSelectedNavigationItem(position);

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


    private class ScreenSlidePagerAdapter extends FragmentStatePagerAdapter {
        private static final int NUM_PAGES = 3;
        private SparseArray<WeakReference<Fragment>> currentFragments = new SparseArray<WeakReference<Fragment>>();
        public ScreenSlidePagerAdapter(FragmentManager fm) {
            super(fm);
        }


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
        @Override
        public Object instantiateItem(ViewGroup container, int position) {
            Object item = super.instantiateItem(container, position);
            currentFragments.append(position, new WeakReference<Fragment>(
                    (Fragment) item));
            return item;
        }

        @Override
        public void destroyItem(ViewGroup container, int position, Object object) {
            currentFragments.put(position, null);
            super.destroyItem(container, position, object);
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
