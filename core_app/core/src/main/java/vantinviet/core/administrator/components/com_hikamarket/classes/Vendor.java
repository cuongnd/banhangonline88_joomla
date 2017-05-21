package vantinviet.core.administrator.components.com_hikamarket.classes;

import java.util.ArrayList;

import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 30/03/2017.
 */

public class Vendor {
    int vendor_id=0;
    int vendor_admin_id=0;
    String vendor_name="";
    String vendor_domain_id="";
    String vendor_alias="";
    private ArrayList<Image> list_image;
    int price_value=0;
    String link="";
    private ArrayList<Image> images;
    private String product_description;

    @Override
    public String toString() {
        return "Vendor{" +
                "vendor_id=" + vendor_id +
                ", vendor_admin_id='" + vendor_admin_id + '\'' +
                ", vendor_name='" + vendor_name + '\'' +
                ", vendor_domain_id='" + vendor_domain_id + '\'' +
                ", list_image='" + list_image + '\'' +
                ", price_value='" + price_value + '\'' +
                '}';
    }


    public ArrayList<Image> getList_image() {
        return list_image;
    }

    public ArrayList<Image> getImages() {
        return images;
    }


    public String getLink() {

        JApplication app=JFactory.getApplication();
        return VTVConfig.rootUrl.concat(link);
    }

    public String getProduct_description() {
        return product_description;
    }
}
