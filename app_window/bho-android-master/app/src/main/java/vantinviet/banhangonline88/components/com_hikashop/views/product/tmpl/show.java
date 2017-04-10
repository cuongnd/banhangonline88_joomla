package vantinviet.banhangonline88.components.com_hikashop.views.product.tmpl;

import android.graphics.Color;
import android.support.annotation.NonNull;
import android.support.design.widget.BottomNavigationView;
import android.support.v4.content.ContextCompat;
import android.view.MenuItem;
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
    JApplication app = JFactory.getApplication();

    public show(LinearLayout linear_layout) {
        String component_response = app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        PageShowProduct product_response = gson.fromJson(reader, PageShowProduct.class);
        show_content = new ShowContent(app.getCurrentActivity(), product_response);
        BottomNavigationView bottomNavigationView = (BottomNavigationView) app.main_linear_layout.findViewById(R.id.bottom_navigation);

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
