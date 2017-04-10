package vantinviet.banhangonline88.administrator.components.com_hikashop.classes;

import android.widget.TextView;

import java.util.ArrayList;

import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 30/03/2017.
 */

public class Product {
    int product_id=0;
    int category_id=0;
    String product_name="";
    String product_code="";
    String html_price="";
    private ArrayList<Image> list_image;
    int price_value=0;
    String link="";
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
}
