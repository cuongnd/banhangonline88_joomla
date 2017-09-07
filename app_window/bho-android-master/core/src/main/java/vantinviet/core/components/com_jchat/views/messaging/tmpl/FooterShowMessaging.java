package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.content.Context;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.view.ViewPager;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;

import com.astuetz.PagerSlidingTabStrip;

import java.util.ArrayList;

import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


/**
 * TODO: document your custom view class.
 */
public class FooterShowMessaging extends LinearLayout {
    private static final String HOME = "Home";
    private static JApplication app = JFactory.getApplication();
    LinearLayout bottom_navigation;
    Button buttonGoToHome;
    private ArrayList<Fragment> listFragment=new ArrayList<Fragment>();
    View view;
    public FooterShowMessaging(Context context) {
        super(context);
        view = inflate(getContext(), R.layout.components_com_jchat_views_messeging_tmpl_c_default_footer, this);
        buttonGoToHome = (Button)view.findViewById(R.id.buttonGoToHome);
        buttonGoToHome.setOnClickListener(new OnClickListener() {
            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onClick(View v) {
                app.setRedirect("");
            }
        });


    }


 }

