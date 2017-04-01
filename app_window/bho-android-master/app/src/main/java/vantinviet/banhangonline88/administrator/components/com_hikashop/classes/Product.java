package vantinviet.banhangonline88.administrator.components.com_hikashop.classes;

import android.widget.TextView;

import java.util.ArrayList;

/**
 * Created by cuongnd on 30/03/2017.
 */

public class Product {
    int product_id=0;
    int category_id=0;
    String product_name="";
    String product_code="";
    private ArrayList<Image> list_image;
    int price_value=0;
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
}
