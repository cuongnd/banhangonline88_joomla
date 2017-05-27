package vantinviet.core.components.com_hikashop.views.product;

import java.util.ArrayList;

import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;

/**
 * Created by cuongnd on 03/04/2017.
 */

public class HikashopViewProduct {

    private PageShowProduct product;
    String url_checkout="";

    public PageShowProduct getProduct_response() {
        return product;
    }

    public String getUrl_checkout() {
        return VTVConfig.getRootUrl()+"/"+url_checkout;
    }

    public PageShowProduct getProduct() {
        return product;
    }

    public class PageShowProduct extends Product {
        public ArrayList<Category> categories = new ArrayList<Category>();


        public ArrayList<Category> getCategories() {
            return categories;
        }

    }

}
