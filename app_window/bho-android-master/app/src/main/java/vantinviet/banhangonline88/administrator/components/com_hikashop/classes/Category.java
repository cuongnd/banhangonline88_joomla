package vantinviet.banhangonline88.administrator.components.com_hikashop.classes;

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
    @Override
    public String toString() {
        return "Category{" +
                "category_id=" + category_id +
                ", category_parent_id='" + category_parent_id + '\'' +
                ", category_type='" + category_type + '\'' +
                ", category_name='" + category_name + '\'' +
                ", category_description='" + category_description + '\'' +
                ", file_path='" + file_path + '\'' +
                '}';
    }

    public String getName() {
        return category_name;
    }

    public String getIcon() {
        return file_path;
    }
}
