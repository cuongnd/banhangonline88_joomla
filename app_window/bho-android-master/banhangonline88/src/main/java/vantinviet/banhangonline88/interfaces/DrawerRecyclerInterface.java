package vantinviet.banhangonline88.interfaces;

import android.view.View;

import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemCategory;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemPage;

public interface DrawerRecyclerInterface {

    void onCategorySelected(View v, DrawerItemCategory drawerItemCategory);

    void onPageSelected(View v, DrawerItemPage drawerItemPage);

    void onHeaderSelected();
}
