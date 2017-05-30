package vantinviet.core.modules.mod_cart;

import vantinviet.core.administrator.components.com_hikashop.classes.Product;

/**
 * Created by cuong on 5/27/2017.
 */

public class ItemCartProduct extends Product {
    int cart_id=0;
    int user_id=0;
    int cart_product_quantity=0;
    int cart_product_parent_id=0;
    String session_id="";
}