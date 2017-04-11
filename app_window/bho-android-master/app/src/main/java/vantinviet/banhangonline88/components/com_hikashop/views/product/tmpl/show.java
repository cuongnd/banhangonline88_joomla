package vantinviet.banhangonline88.components.com_hikashop.views.product.tmpl;

import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.design.widget.BottomNavigationView;
import android.support.v4.content.ContextCompat;
import android.view.Gravity;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
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
    BottomNavigationView product_show_footer;
    JApplication app = JFactory.getApplication();

    public show(LinearLayout linear_layout) {
        String component_response = app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        PageShowProduct product_response = gson.fromJson(reader, PageShowProduct.class);
        show_content = new ShowContent(app.getCurrentActivity(), product_response);

        product_show_footer = (BottomNavigationView)app.root_relative_layout.findViewById(R.id.bottom_navigation);

        product_show_footer.setVisibility(View.VISIBLE);


        linear_layout.addView(show_content);
    }

    public class PageShowProduct {
        public ArrayList<Category> categories = new ArrayList<Category>();

        public Product product;

        public ArrayList<Category> getCategories() {
            return categories;
        }

        public Product getProduct() {
            return product;
        }
    }
}
