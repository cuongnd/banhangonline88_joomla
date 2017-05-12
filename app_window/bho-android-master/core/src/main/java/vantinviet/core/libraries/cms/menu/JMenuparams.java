package vantinviet.core.libraries.cms.menu;

/**
 * Created by cuongnd on 6/7/2016.
 */
public class JMenuparams {

    String menu_image;
    String jv_selection;
    @Override
    public String toString() {
        return "JMenuparams{" +
                "menu_image=" + menu_image +
                ", jv_selection='" + jv_selection + '\'' +
                '}';
    }

    public String getMenu_image() {
        return menu_image;
    }
}
