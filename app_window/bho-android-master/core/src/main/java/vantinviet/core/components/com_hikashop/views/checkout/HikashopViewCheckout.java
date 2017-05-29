package vantinviet.core.components.com_hikashop.views.checkout;

import java.util.ArrayList;
import java.util.List;

import vantinviet.core.administrator.components.com_hikashop.classes.Config;
import vantinviet.core.libraries.legacy.view.JViewLegacy;

/**
 * Created by cuongnd on 03/04/2017.
 */

public class HikashopViewCheckout extends JViewLegacy {
    private ArrayList<String> steps;
    private ArrayList<String> layouts;
    int step=1;
    private Config config;

    public ArrayList<String> getSteps() {
        return steps;
    }

    public Config getConfig() {
        return config;
    }

    public ArrayList<String> getLayouts() {
        return layouts;
    }
}
