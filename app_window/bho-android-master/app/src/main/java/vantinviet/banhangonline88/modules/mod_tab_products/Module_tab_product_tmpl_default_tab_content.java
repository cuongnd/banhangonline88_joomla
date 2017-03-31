package vantinviet.banhangonline88.modules.mod_tab_products;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.view.ViewPager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import java.util.ArrayList;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;

import static vantinviet.banhangonline88.ux.MainActivity.mInstance;

/**
 * Created by neokree on 16/12/14.
 */

/**
 * Created by neokree on 16/12/14.
 */
public class Module_tab_product_tmpl_default_tab_content extends Fragment implements MaterialTabListener {
    private final Mod_tab_product_helper.List_category_product list_category_product;
    private MaterialTabHost sub_tab_host;
    private ViewPager sub_page_view;
    private SubViewPagerAdapter sub_adapter;
    private static ArrayList<Category> list_sub_category_detail;

    public Module_tab_product_tmpl_default_tab_content(Mod_tab_product_helper.List_category_product list_category_product) {
        this.list_category_product=list_category_product;
        list_sub_category_detail=list_category_product.getList_sub_category_detail();
    }

    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.modules_mod_tab_products_tmpl_default_tab_content, container, false);
        sub_tab_host = (MaterialTabHost) view.findViewById(R.id.sub_tab_host);
        sub_page_view = (ViewPager) view.findViewById(R.id.sub_page_view );

        // init view sub_page_view
        FragmentManager fragManager = mInstance.getSupportFragmentManager();
        sub_adapter = new SubViewPagerAdapter(fragManager);
        sub_page_view.setAdapter(sub_adapter);
        sub_page_view.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                sub_tab_host.setSelectedNavigationItem(position);

            }
        });

        // insert all tabs from pagerAdapter data
        for (int i = 0; i < sub_adapter.getCount(); i++) {
            sub_tab_host.addTab(
                    sub_tab_host.newTab()
                            .setText(sub_adapter.getPageTitle(i))
                            .setTabListener(this)
            );

        }

        return view;
    }

    @Override
    public void onTabSelected(MaterialTab tab) {
        sub_page_view.setCurrentItem(tab.getPosition());
    }

    @Override
    public void onTabReselected(MaterialTab tab) {

    }

    @Override
    public void onTabUnselected(MaterialTab tab) {

    }

    public static class SubViewPagerAdapter extends FragmentStatePagerAdapter {


        public SubViewPagerAdapter(FragmentManager fm) {
            super(fm);
        }

        public Fragment getItem(int num) {
            Category category= list_sub_category_detail.get(num);
            return new sub_category_content(category);
        }

        @Override
        public int getCount() {
            if(list_sub_category_detail!=null)
            {
                return list_sub_category_detail.size();
            }else{
                return 0;
            }
        }

        @Override
        public CharSequence getPageTitle(int position) {
            Category category=list_sub_category_detail.get(position);
            return category.getName();
        }
        @Override
        public void destroyItem(View container, int position, Object object) {
            super.destroyItem(container, position, object);

            if (position <= getCount()) {
                FragmentManager manager = ((Fragment) object).getFragmentManager();
                FragmentTransaction trans = manager.beginTransaction();
                trans.remove((Fragment) object);
                trans.commit();
            }
        }

    }
    public static   class sub_category_content extends Fragment {
        public sub_category_content(Category category) {

        }
        @Override
        public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
            View view = inflater.inflate(R.layout.modules_mod_tab_products_tmpl_default_sub_tab_content, container, false);
            return view;
        }

    }

}
