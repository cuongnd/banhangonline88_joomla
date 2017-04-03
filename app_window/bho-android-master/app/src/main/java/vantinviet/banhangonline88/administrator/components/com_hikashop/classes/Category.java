package vantinviet.banhangonline88.administrator.components.com_hikashop.classes;

import java.util.ArrayList;

/**
 * Created by cuongnd on 30/03/2017.
 */

public class Category {
    int category_id=0;
    int category_parent_id=0;
    String category_type="";
    String category_name="";
    String category_description="";
    String file_path="";
    Image icon;
    Image medium_image;

    @Override
    public String toString() {
        return "Category{" +
                "category_id=" + category_id +
                ", category_parent_id='" + category_parent_id + '\'' +
                ", category_type='" + category_type + '\'' +
                ", category_name='" + category_name + '\'' +
                ", category_description='" + category_description + '\'' +
                ", icon='" + icon + '\'' +
                ", medium_image='" + medium_image + '\'' +
                '}';
    }

    public String getName() {
        return category_name;
    }

    public Image getIcon() {
        return icon;
    }

    public Image getMedium_image() {
        return medium_image;
    }
}
