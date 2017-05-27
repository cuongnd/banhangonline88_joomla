package vantinviet.core.modules.mod_cart;

import java.util.ArrayList;

import vantinviet.core.administrator.components.com_hikashop.classes.Product;

/**
 * Created by cuong on 5/27/2017.
 */

public class ModCartView {
    private ArrayList<ItemProductCart> list_item_product_cart;

    public ArrayList<ItemProductCart> getList_item_product_cart() {
        return list_item_product_cart;
    }

    public class ItemProductCart extends Product {
    }
}
