package vantinviet.core.components.com_hikamarket.views.category.tmpl;

import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;

import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

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
