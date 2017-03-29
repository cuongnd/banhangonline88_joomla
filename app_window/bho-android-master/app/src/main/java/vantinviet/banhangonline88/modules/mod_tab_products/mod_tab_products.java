package vantinviet.banhangonline88.modules.mod_tab_products;

import android.app.Activity;
import android.app.TabActivity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v7.app.ActionBarActivity;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.TabHost;
import android.widget.TabWidget;
import android.widget.TextView;

import timber.log.Timber;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.libraries.tab.materialtabs.MaterialTab;
import vantinviet.banhangonline88.libraries.tab.materialtabs.MaterialTabHost;
import vantinviet.banhangonline88.libraries.tab.materialtabs.MaterialTabListener;

import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;
import static vantinviet.banhangonline88.libraries.joomla.JFactory.getContext;
import static vantinviet.banhangonline88.ux.MainActivity.mInstance;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products extends ActionBarActivity implements MaterialTabListener {


    private final Module module;
    private final LinearLayout linear_layout;
    private static final String KEY_DEMO = "demo";
    private MaterialTabHost tabHost;
    private ViewPager pager;
    private ViewPagerAdapter adapter;
    private MyApplication app;
    LinearLayout tab_content;
    public mod_tab_products(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        init();
    }

    private void init() {
        app= MyApplication.getInstance();

        String response=this.module.getResponse();
        Timber.d("mod_tab_products response %s",response.toString());


        LinearLayout.LayoutParams new_vertical_wrapper_of_module_linear_layout_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout new_wrapper_of_module_content_linear_layout=new LinearLayout(mInstance);
        new_wrapper_of_module_content_linear_layout.setLayoutParams(new_vertical_wrapper_of_module_linear_layout_params);
        new_wrapper_of_module_content_linear_layout.setOrientation(LinearLayout.VERTICAL);



        tabHost = new MaterialTabHost(mInstance);

        tabHost.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 100));
        // insert all tabs from pagerAdapter data
        pager = new ViewPager(mInstance);

        pager.setLayoutParams(new LinearLayout.LayoutParams(WRAP_CONTENT, 100));


        // init view pager
        adapter = new ViewPagerAdapter( (getSupportFragmentManager()));
        pager.setAdapter(adapter);
        pager.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                tabHost.setSelectedNavigationItem(position);

            }
        });



        for (int i = 0; i < adapter.getCount(); i++) {
            tabHost.addTab(
                    tabHost.newTab()
                            .setText(adapter.getPageTitle(i))
                            .setTabListener(this)
            );

        }

        tab_content=new LinearLayout(mInstance);
        tab_content.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 500));


        tabHost.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 100));


        new_wrapper_of_module_content_linear_layout.addView(tabHost);
        new_wrapper_of_module_content_linear_layout.addView(tab_content);




        linear_layout.addView(new_wrapper_of_module_content_linear_layout);






    }

    @Override
    public void onTabSelected(MaterialTab tab) {
        Timber.d("hello onTabSelected");
        TextView text = new TextView(mInstance);
        text.setText("Fragment content");
        text.setGravity(Gravity.CENTER);
        tab_content.addView(text);
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
        @Override
        public Fragment getItem(int num) {
            Timber.d("hello Fragment");
            return new FragmentText();
        }

        @Override
        public int getCount() {
            return 10;
        }

        @Override
        public CharSequence getPageTitle(int position) {
            return "Sezione " + position;
        }

    }
}
