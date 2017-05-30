package vantinviet.core.modules.mod_tab_products;

import java.util.ArrayList;

import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;


/**
 * Created by cuongnd on 30/03/2017.
 */

public class Mod_tab_product_helper {
    ArrayList<List_category_product> list_category_product=new ArrayList<List_category_product>();

    public class List_category_product {
        ArrayList<Category> list_sub_category_detail=new ArrayList<Category>();
        ArrayList<Integer> list_category=new ArrayList<Integer>();
        Category detail=new Category();
        int is_loaded =1;
        ArrayList<Product> list=new ArrayList<Product>(0);
        ArrayList<Product> list_small_product=new ArrayList<Product>();
        @Override
        public String toString() {
            return "List_category_product{" +
                    "list_sub_category_detail=" + list_sub_category_detail +
                    ", list_category='" + list_category + '\'' +
                    ", is_loaded='" + is_loaded + '\'' +
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
        public void setList(ArrayList<Product> list) {
            if(list!=null)
            {
                this.list=list;
            }
        }
        public ArrayList<Product> getList() {
            return list;
        }

        public void setDetail(Category detail) {
            this.detail = detail;
        }

        public int getIs_loaded() {
            return is_loaded;
        }

        public void setIs_loaded(int is_loaded) {
            this.is_loaded = is_loaded;
        }
    }
}
