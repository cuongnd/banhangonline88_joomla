package vantinviet.banhangonline88.modules.mod_tab_products;

import java.util.ArrayList;

import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.entities.module.Module;

/**
 * Created by cuongnd on 30/03/2017.
 */

public class Mod_tab_product_helper {
    ArrayList<List_category_product> list_category_product=new ArrayList<List_category_product>();

    public class List_category_product {
        ArrayList<Category> list_sub_category_detail=new ArrayList<Category>();
        ArrayList<Integer> list_category=new ArrayList<Integer>();
        Category detail;
        ArrayList<Product> list=new ArrayList<Product>();
        ArrayList<Product> list_small_product=new ArrayList<Product>();
        @Override
        public String toString() {
            return "List_category_product{" +
                    "list_sub_category_detail=" + list_sub_category_detail +
                    ", list_category='" + list_category + '\'' +
                    ", detail='" + detail + '\'' +
                    ", list='" + list + '\'' +
                    ", list_small_product='" + list_small_product + '\'' +
                    '}';
        }

        public Category getDetail() {
            return detail;
        }

        public ArrayList<Category> getList_sub_category_detail() {
            return list_sub_category_detail;
        }
    }
}