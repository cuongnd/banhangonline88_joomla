package vantinviet.banhangonline88.modules.mod_tab_products;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.view.ViewPager;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TabHost;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.reflect.TypeToken;
import com.ogaclejapan.smarttablayout.SmartTabLayout;

import java.lang.reflect.Type;
import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.modules.mod_slideshowck.Slider;
import vantinviet.banhangonline88.utils.Utils;

import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static vantinviet.banhangonline88.ux.MainActivity.mInstance;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products {


    private final Context context;
    private final Module module;
    private final LinearLayout linear_layout;
    private static final String KEY_DEMO = "demo";
    public final int layoutResId;
    public mod_tab_products(Context context, Module module, LinearLayout linear_layout, int layoutResId) {
        this.context=context;
        this.module=module;
        this.linear_layout=linear_layout;
        this.layoutResId = layoutResId;
        init();
    }

    private void init() {
        String response=this.module.getResponse();
        Timber.d("mod_slideshowck response %s",response.toString());
        // create the TabHost that will contain the Tabs
        TabHost tabHost = new TabHost(context);
        TabHost.TabSpec tab1 = tabHost.newTabSpec("tab 1");
        TabHost.TabSpec tab2 = tabHost.newTabSpec("tab 2");
        TabHost.TabSpec tab3 = tabHost.newTabSpec("tab 3");
        // Set the Tab name and Activity
        // that will be opened when particular Tab will be selected
        tab1.setIndicator("tab1");
        tab1.setContent(new Intent(context,Tab1Activity.class));

        tab2.setIndicator("tab2");
        tab2.setContent(new Intent(context,Tab2Activity.class));
        tab3.setIndicator("tab2");
        tab3.setContent(new Intent(context,Tab3Activity.class));

        /** Add the tabs  to the TabHost to display. */
        tabHost.addTab(tab1);
        tabHost.addTab(tab2);
        tabHost.addTab(tab3);
        linear_layout.addView(tabHost);


    }



}
