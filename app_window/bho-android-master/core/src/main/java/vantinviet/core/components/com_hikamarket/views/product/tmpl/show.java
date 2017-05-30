package vantinviet.core.components.com_hikamarket.views.product.tmpl;

import android.support.design.widget.BottomNavigationView;
import android.view.Menu;
import android.view.View;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;

import vantinviet.core.R;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.language.JText;
import vantinviet.core.libraries.legacy.application.JApplication;

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

        product_show_footer = (BottomNavigationView)app.getCurrentActivity().findViewById(R.id.bottom_navigation);
        Menu menu= product_show_footer.getMenu();
        menu.clear();
        menu.add(JText._("Chatting")).setIcon(R.drawable.com_facebook_send_button_icon);
        menu.add(JText._("Call")).setIcon(R.drawable.ic_smiles_car);
        menu.add(JText._("add to cart")).setIcon(R.drawable.cart_add);
        menu.add(JText._("buy now")).setIcon(R.drawable.cart_add);
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
