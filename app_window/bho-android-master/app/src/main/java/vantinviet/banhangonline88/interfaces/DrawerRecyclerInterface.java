package vantinviet.banhangonline88.interfaces;

import android.view.View;

import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemPage;

public interface DrawerRecyclerInterface {

    void onMenuItemSelected(View v, DrawerMenuItem drawerMenuItem);


    void onHeaderSelected();
}
