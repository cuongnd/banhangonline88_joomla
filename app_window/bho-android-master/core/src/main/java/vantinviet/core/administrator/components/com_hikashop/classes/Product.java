package vantinviet.core.administrator.components.com_hikashop.classes;

import android.widget.TextView;

import java.util.ArrayList;

import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikamarket.classes.Vendor;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 30/03/2017.
 */

public class Product {
    int product_id=0;
    int category_id=0;
    String product_name="";
    String product_code="";
    String html_price="";
    String html_product="";
    String style_product="";
    private ArrayList<Image> list_image;
    int price_value=0;
    String link="";
    Vendor vendor;
    private ArrayList<Image> images;
    private String product_description;

    @Override
    public String toString() {
        return "Product{" +
                "product_id=" + product_id +
                ", category_id='" + category_id + '\'' +
                ", product_name='" + product_name + '\'' +
                ", product_code='" + product_code + '\'' +
                ", list_image='" + list_image + '\'' +
                ", price_value='" + price_value + '\'' +
                '}';
    }

    public String getName() {
        return product_name;
    }

    public ArrayList<Image> getList_image() {
        return list_image;
    }

    public ArrayList<Image> getImages() {
        return images;
    }

    public String getHtml_price() {
        return html_price;
    }

    public String getLink() {

        JApplication app=JFactory.getApplication();
        return VTVConfig.rootUrl.concat(link);
    }

    public String getProduct_description() {
        return product_description;
    }

    public String getHtml_product() {
        return html_product;
    }

    public String getStyle_product() {
        return style_product;
    }

    public int getProduct_id() {
        return product_id;
    }
}
