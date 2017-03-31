package vantinviet.banhangonline88.modules.mod_tab_products;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.ViewPager;
import android.util.AttributeSet;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import java.util.ArrayList;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.components.com_users.views.profile.view;

import static vantinviet.banhangonline88.ux.MainActivity.mInstance;

/**
 * Created by neokree on 16/12/14.
 */

/**
 * Created by neokree on 16/12/14.
 */
public class Module_tab_product_tmpl_default_tab_content extends Fragment implements MaterialTabListener {
    private final Mod_tab_product_helper.List_category_product list_category_product;
    private MaterialTabHost tabHost;
    private ViewPager pager;
    private ViewPagerAdapter adapter;
    private static ArrayList<Category> list_sub_category_detail;

    public Module_tab_product_tmpl_default_tab_content(Mod_tab_product_helper.List_category_product list_category_product) {
        this.list_category_product=list_category_product;
        list_sub_category_detail=list_category_product.getList_sub_category_detail();
    }

    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.modules_mod_tab_products_tmpl_default_tab_content, container, false);
        tabHost = (MaterialTabHost) view.findViewById(R.id.tabHost);
        pager = (ViewPager) view.findViewById(R.id.pager );

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

        return view;
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

    public static class ViewPagerAdapter extends FragmentStatePagerAdapter {


        public ViewPagerAdapter(FragmentManager fm) {
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
