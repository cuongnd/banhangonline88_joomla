package vantinviet.core.modules.mod_cart;

import java.util.ArrayList;
import java.util.Collection;

import vantinviet.core.VTVConfig;

/**
 * Created by cuong on 5/27/2017.
 */

public class ModCartView {
    private ArrayList<ItemCartProduct> list_item_cart_product=new ArrayList<ItemCartProduct>();
    private  String url_checkout="";

    public ArrayList<ItemCartProduct> getList_item_cart_product() {
        return list_item_cart_product!=null?list_item_cart_product:new ArrayList<ItemCartProduct>();
    }

    public  String getUrl_checkout() {
        return VTVConfig.getRootUrl()+url_checkout;
    }
}
