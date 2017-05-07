package vantinviet.core.libraries.cms.menu;

import org.json.JSONArray;
import org.json.JSONObject;

/**
 * Created by cuongnd on 6/7/2016.
 */
public class JMenu {

    private static JMenu singleton;
    private int menuActiveId;
    private JSONObject menuActive;
    private JSONArray items;

    /* A private Constructor prevents any other
     * class from instantiating.
     */
    private JMenu(){

    }

    /* Static 'instance' method */
    public static JMenu getInstance( ) {
        if (singleton == null) {
            singleton = new JMenu();
        }
        System.out.println(singleton);
        return singleton;
    }
    /* Other methods protected by singleton-ness */
    protected static void demoMethod( ) {
        System.out.println("demoMethod for singleton");
    }


    public int geMenuActiveId() {
        return menuActiveId;
    }

    public void setMenuActiveId(int menuActiveId) {
        this.menuActiveId = menuActiveId;
    }

    public JSONObject getMenuActive() {
        return menuActive;
    }

    public void setMenuActive(JSONObject menuActive) {
        this.menuActive = menuActive;
    }

    public void setItems(JSONArray items) {
        this.items = items;
    }
}
