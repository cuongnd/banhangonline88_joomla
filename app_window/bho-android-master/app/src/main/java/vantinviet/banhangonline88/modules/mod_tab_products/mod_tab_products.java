package vantinviet.banhangonline88.modules.mod_tab_products;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v7.app.ActionBarActivity;
import android.widget.LinearLayout;

import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.ArrayList;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import timber.log.Timber;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.entities.module.Module;

import vantinviet.banhangonline88.utils.Utils;

import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;
import static vantinviet.banhangonline88.ux.MainActivity.mInstance;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products extends ActionBarActivity implements MaterialTabListener {


    private final Module module;
    private final LinearLayout linear_layout;
    private static final String KEY_DEMO = "demo";
    private MyApplication app;
    LinearLayout tab_content;
    MaterialTabHost tabHost;
    Module_tab_product_tmpl_default object_tab_product_tmpl_default;
    ArrayList<Mod_tab_product_helper.List_category_product> list_main_category_product;
    private ViewPager pager;
    private PagerAdapter adapter;

    public mod_tab_products(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        String content=this.module.getContent();
        Timber.d("module content %s",content.toString());
        Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
        list_main_category_product = Utils.getGsonParser().fromJson(content, listType);
        Timber.d("list_main_category_product %s", list_main_category_product.toString());
        init();
    }

    private void init() {
        object_tab_product_tmpl_default =new Module_tab_product_tmpl_default(mInstance,this.module);
        tabHost = (MaterialTabHost) object_tab_product_tmpl_default.findViewById(R.id.tabHost);
        pager = (ViewPager) object_tab_product_tmpl_default.findViewById(R.id.pager );

        // init view pager
        FragmentManager fragManager = mInstance.getSupportFragmentManager();
        adapter = new ViewPagerAdapter(fragManager);
        pager.setAdapter(adapter);
        pager.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                tabHost.setSelectedNavigationItem(position);

            }
        });

        // insert all tabs from pagerAdapter data
        for (int i = 0; i < adapter.getCount(); i++) {
            tabHost.addTab(
                    tabHost.newTab()
                            .setText(adapter.getPageTitle(i))
                            .setTabListener(this)
            );

        }
        LinearLayout.LayoutParams new_vertical_wrapper_of_module_linear_layout_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout new_wrapper_of_module_content_linear_layout=new LinearLayout(mInstance);
        new_wrapper_of_module_content_linear_layout.setLayoutParams(new_vertical_wrapper_of_module_linear_layout_params);
        new_wrapper_of_module_content_linear_layout.setOrientation(LinearLayout.VERTICAL);

        new_wrapper_of_module_content_linear_layout.addView(object_tab_product_tmpl_default);

        linear_layout.addView(new_wrapper_of_module_content_linear_layout);

    }
    @Override
    public void onTabSelected(MaterialTab tab) {
        pager.setCurrentItem(tab.getPosition());
    }

    @Override
    public void onTabReselected(MaterialTab tab) {

    }

    @Override
    public void onTabUnselected(MaterialTab tab) {

    }

    private class ViewPagerAdapter extends FragmentStatePagerAdapter {


        public ViewPagerAdapter(FragmentManager fm) {
            super(fm);
        }

        public Fragment getItem(int num) {
            Mod_tab_product_helper.List_category_product list_category_product=list_main_category_product.get(num);
            return new Module_tab_product_tmpl_default_tab_content(list_category_product);
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
        adapter = new ViewPagerAdapter( (getSupportFragmentManager()));
        pager.setAdapter(adapter);
        pager.setOnPageChangeListener(new MyViewPager.SimpleOnPageChangeListener() {
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                main_category_tab_host.setSelectedNavigationItem(position);

            }
        });



        for (int i = 0; i < adapter.getCount(); i++) {
            main_category_tab_host.addTab(
                    main_category_tab_host.newTab()
                            .setText(adapter.getPageTitle(i))
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

    public void onPageSelectedMainCategory(MaterialTab tab) {
        Timber.d("hello onPageSelectedMainCategory");
        pager.setCurrentItem(tab.getPosition());

        int category_index=tab.getPosition();
        MaterialTabHost sub_tab_host = new MaterialTabHost(mInstance);

        sub_tab_host.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 100));
        ArrayList<Category> list_sub_category_detail= list_main_category_product.get(category_index).getList_sub_category_detail();
      /*  if(list_sub_category_detail!=null)for (Category item: list_sub_category_detail) {
            String category_name=item.getName();
            String icon_file_path=item.getIcon();
            MaterialTab sub_tab = new MaterialTab(mInstance, false);
            sub_tab.setText(category_name);
            MaterialTabListener sub_materialTabListener= new MaterialTabListener() {
                @Override
                public void onTabSelected(MaterialTab tab) {
                    onPageSelectedSubMainCategory(tab);
                }

                @Override
                public void onTabReselected(MaterialTab tab) {

                }

                @Override
                public void onTabUnselected(MaterialTab tab) {

                }
            };
            tab.setTabListener(sub_materialTabListener);
            sub_tab_host.addTab(tab);
        }
        sub_tab_host.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 100));
        LinearLayout tmpl_tab_sub_category =(LinearLayout) object_tab_product_tmpl_default.findViewById(R.id.tab_sub_category);
        tmpl_tab_sub_category.removeAllViews();
        tmpl_tab_sub_category.addView(sub_tab_host);*/
    }

}
