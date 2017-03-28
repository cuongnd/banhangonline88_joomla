package vantinviet.banhangonline88.modules.mod_tab_products;

import android.app.Activity;
import android.app.TabActivity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.TabHost;
import android.widget.TabWidget;
import android.widget.TextView;

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.module.Module;

import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;
import static vantinviet.banhangonline88.libraries.joomla.JFactory.getContext;
import static vantinviet.banhangonline88.ux.MainActivity.mInstance;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products extends TabActivity {


    private final Module module;
    private final LinearLayout linear_layout;
    private static final String KEY_DEMO = "demo";
    public mod_tab_products(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        init();
    }

    private void init() {
        String response=this.module.getResponse();
        Timber.d("mod_tab_products response %s",response.toString());
        TextView tv=new TextView(mInstance);
        tv.setText("Tab1Activity");
        tv.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, WRAP_CONTENT));
        linear_layout.addView(tv);





    }
    private void setNewTab(Context context, TabHost tabHost, String tag, int title, int icon, int contentID ){
        TabHost.TabSpec tabSpec = tabHost.newTabSpec(tag);
        String titleString = "title";
        tabSpec.setIndicator(titleString, context.getResources().getDrawable(android.R.drawable.star_on));
        tabSpec.setContent(contentID);
        tabHost.addTab(tabSpec);
    }
    public static TabHost createTabHost(Context context) {
        // Create the TabWidget (the tabs)
        TabWidget tabWidget = new TabWidget(context);
        tabWidget.setId(android.R.id.tabs);

        // Create the FrameLayout (the content area)
        FrameLayout frame = new FrameLayout(context);
        frame.setId(android.R.id.tabcontent);
        LinearLayout.LayoutParams frameLayoutParams = new LinearLayout.LayoutParams(
                LinearLayout.LayoutParams.FILL_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT, 1);
        frameLayoutParams.setMargins(4, 4, 4, 4);
        frame.setLayoutParams(frameLayoutParams);

        // Create the container for the above widgets
        LinearLayout tabHostLayout = new LinearLayout(context);
        tabHostLayout.setOrientation(LinearLayout.VERTICAL);
        tabHostLayout.addView(tabWidget);
        tabHostLayout.addView(frame);

        // Create the TabHost and add the container to it.
        TabHost tabHost = new TabHost(context, null);
        tabHost.addView(tabHostLayout);
        tabHost.setup();

        return tabHost;
    }

}
