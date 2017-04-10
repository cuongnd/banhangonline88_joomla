package vantinviet.banhangonline88.components.com_hikashop.views.product.tmpl;

import android.graphics.Color;
import android.support.v4.content.ContextCompat;
import android.widget.LinearLayout;

import com.aurelhubert.ahbottomnavigation.AHBottomNavigation;
import com.aurelhubert.ahbottomnavigation.AHBottomNavigationItem;
import com.aurelhubert.ahbottomnavigation.notification.AHNotification;
import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 02/04/2017.
 */

public class show {
    private final ShowContent show_content;
    JApplication app= JFactory.getApplication();
    public show(LinearLayout linear_layout){
        String component_response=app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        PageShowProduct product_response = gson.fromJson(reader, PageShowProduct.class);
        show_content =new ShowContent(app.getCurrentActivity(),product_response);

        AHBottomNavigation bottomNavigation = new AHBottomNavigation(app.getCurrentActivity());

        // Create items
        AHBottomNavigationItem item1 = new AHBottomNavigationItem("test123", R.drawable.abc_ab_share_pack_holo_dark, R.color.bootstrap_well_border_color);

        // Add items
        bottomNavigation.addItem(item1);

        // Set background color
        bottomNavigation.setDefaultBackgroundColor(Color.parseColor("#FEFEFE"));

        // Disable the translation inside the CoordinatorLayout
        bottomNavigation.setBehaviorTranslationEnabled(false);

        // Change colors
        bottomNavigation.setAccentColor(Color.parseColor("#F63D2B"));
        bottomNavigation.setInactiveColor(Color.parseColor("#747474"));

        // Force to tint the drawable (useful for font with icon for example)
        bottomNavigation.setForceTint(true);

        // Display color under navigation bar (API 21+)
        // Don't forget these lines in your style-v21
        // <item name="android:windowTranslucentNavigation">true</item>
        // <item name="android:fitsSystemWindows">true</item>
        bottomNavigation.setTranslucentNavigationEnabled(true);

        // Manage titles
        bottomNavigation.setTitleState(AHBottomNavigation.TitleState.SHOW_WHEN_ACTIVE);
        bottomNavigation.setTitleState(AHBottomNavigation.TitleState.ALWAYS_SHOW);
        bottomNavigation.setTitleState(AHBottomNavigation.TitleState.ALWAYS_HIDE);

        // Use colored navigation with circle reveal effect
        bottomNavigation.setColored(true);

        // Set current item programmatically
        bottomNavigation.setCurrentItem(1);

        // Customize notification (title, background, typeface)
        bottomNavigation.setNotificationBackgroundColor(Color.parseColor("#F63D2B"));

        // Add or remove notification for ech item
        bottomNavigation.setNotification("1", 3);
        // OR
        AHNotification notification = new AHNotification.Builder()
                .setText("1")
                .build();
        bottomNavigation.setNotification(notification, 1);

        // Set listeners
        bottomNavigation.setOnTabSelectedListener(new AHBottomNavigation.OnTabSelectedListener() {
            @Override
            public boolean onTabSelected(int position, boolean wasSelected) {
                // Do something cool here...
                return true;
            }
        });
        bottomNavigation.setOnNavigationPositionListener(new AHBottomNavigation.OnNavigationPositionListener() {
            @Override public void onPositionChange(int y) {
                // Manage the new y position
            }
        });
        app.rootLinearLayout.addView(bottomNavigation);
        linear_layout.addView(show_content);
    }

    public class PageShowProduct {
        public ArrayList<Category> categories=new ArrayList<Category>();
        public Product product;

        public ArrayList<Category> getCategories() {
            return categories;
        }

        public Product getProduct() {
            return product;
        }
    }
}
