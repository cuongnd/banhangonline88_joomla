package vantinviet.banhangonline88.components.com_hikashop.views.category.tmpl;

import android.widget.LinearLayout;
import android.widget.TextView;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;

import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.modules.mod_tab_products.Module_tab_product_tmpl_default;

import static vantinviet.banhangonline88.ux.MainActivity.mInstance;

/**
 * Created by cuongnd on 02/04/2017.
 */

public class listing  {
    private final ListingContent listingcontent;
    JApplication app= JFactory.getApplication();
    public listing(LinearLayout linear_layout){
        String component_response=app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        ListingResponse listingresponse = gson.fromJson(reader, ListingResponse.class);
        listingcontent =new ListingContent(app.getCurrentActivity(),listingresponse);
        linear_layout.addView(listingcontent);
    }

    public class ListingResponse {
        public ArrayList<Category> categories=new ArrayList<Category>();
        public ArrayList<Product> products=new ArrayList<Product>();

        public ArrayList<Category> getCategories() {
            return categories;
        }

        public ArrayList<Product> getProducts() {
            return products;
        }
    }
}
