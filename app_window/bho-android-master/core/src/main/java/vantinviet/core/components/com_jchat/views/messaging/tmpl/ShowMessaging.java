package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.content.Context;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentPagerAdapter;
import android.support.v4.view.ViewPager;
import android.view.View;
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
public class ShowMessaging extends LinearLayout {


    private static final String HOME = "Home";
    private static JApplication app = JFactory.getApplication();
    private final VTVConfig vTVConfig;
    LinearLayout bottom_navigation;
    private ArrayList<Fragment> listFragment=new ArrayList<Fragment>();
    View view;
    public ShowMessaging(Context context) {
        super(context);
        view = inflate(getContext(), R.layout.components_com_jchat_views_messeging_tmpl_c_default, this);
        FragmentListUserOnline fragmentListUserOnline=new FragmentListUserOnline();
        listFragment.add(fragmentListUserOnline);

        FragmentChatting fragmentChatting=new FragmentChatting();
        listFragment.add(fragmentChatting);

        FragmentListSupportUserOnline fragmentListSupportUserOnline=new FragmentListSupportUserOnline();
        listFragment.add(fragmentListSupportUserOnline);

        FragmentMore fragmentMore=new FragmentMore();
        listFragment.add(fragmentMore);


        // Get the ViewPager and set it's PagerAdapter so that it can display items
        ViewPager viewPager = (ViewPager) view.findViewById(R.id.viewPagerTabContent);
        vTVConfig =VTVConfig.getInstance();
        int heightViewPager=0;
        heightViewPager = vTVConfig.getScreen_size_height()-345;
        viewPager.setLayoutParams(new LayoutParams(
                LayoutParams.MATCH_PARENT, heightViewPager));

        viewPager.setAdapter(new ChattingFragmentPagerAdapter(app.getSupportFragmentManager()));

        // Give the PagerSlidingTabStrip the ViewPager
        PagerSlidingTabStrip tabsStrip = (PagerSlidingTabStrip) view.findViewById(R.id.tabs);
        // Attach the view pager to the tab strip
        tabsStrip.setViewPager(viewPager);

        bottom_navigation = (LinearLayout)app.getCurrentActivity().findViewById(R.id.bottom_navigation);
        bottom_navigation.removeAllViews();
        FooterShowMessaging footerShowMessaging=new FooterShowMessaging(app.getContext());
        bottom_navigation.addView(footerShowMessaging);

    }
    public class ChattingFragmentPagerAdapter extends FragmentPagerAdapter implements PagerSlidingTabStrip.IconTabProvider {
        final int PAGE_COUNT = 4;
        private int icons[] = {R.drawable.account_multiple, R.drawable.wechat, R.drawable.professional_hexagon,R.drawable.dots_horizontal};

        public ChattingFragmentPagerAdapter(FragmentManager fm) {
            super(fm);
        }

        @Override
        public int getCount() {
            return PAGE_COUNT;
        }

        @Override
        public Fragment getItem(int position) {
            //return PageFragment.newInstance(position + 1);
            return listFragment.get(position);
        }


        @Override
        public int getPageIconResId(int position) {
            return  icons[position];
        }
    }


 }

