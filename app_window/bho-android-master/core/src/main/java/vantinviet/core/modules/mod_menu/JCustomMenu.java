package vantinviet.core.modules.mod_menu;

import android.test.suitebuilder.annotation.Suppress;

import java.util.ArrayList;

import vantinviet.core.libraries.cms.menu.JMenu;

/**
 * Created by cuong on 5/18/2017.
 */

public class JCustomMenu extends JMenu {
    public int total_sub_menu=0;
    public int getTotalChildren() {
        return total_sub_menu;
    }
    public ArrayList<JCustomMenu> getChildrenCustomMenu() {
        ArrayList<JMenu> children= super.getChildren();
        ArrayList<JCustomMenu> childrenCustomMenu=new ArrayList<JCustomMenu>();
        for (JMenu menu_item: children) {
            childrenCustomMenu.add((JCustomMenu)menu_item);
        }
        return childrenCustomMenu;
    }


}
