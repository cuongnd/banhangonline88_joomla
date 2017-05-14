package vantinviet.core.libraries.cms.menu;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import vantinviet.core.administrator.components.com_hikashop.classes.Image;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 6/7/2016.
 */
public class JMenu {

    int id;
    int parent_id;
    String title;
    String link;
    String flink;
    JMenuparams params;
    private ArrayList<Image> images;
    private static JMenu instance;
    public ArrayList<JMenu> children=new ArrayList<JMenu>();
    private JSONObject menuActive;
    protected int level=0;
    protected int totalChildren=0;

    /* Static 'instance' method */
    public static JMenu getInstance() {

        if (instance == null) {
            instance = new JMenu();
        }
        return instance;
    }

    public JSONObject getMenuActive() {
        return menuActive;
    }

    public void setMenuActive(JSONObject menuActive) {
        this.menuActive = menuActive;
    }
    @Override
    public String toString() {
        return "JMenu{" +
                "id=" + id +
                ", parent_id='" + parent_id + '\'' +
                ", level='" + level + '\'' +
                ", title='" + title + '\'' +
                ", totalChildren='" + totalChildren + '\'' +
                ", link='" + link + '\'' +
                ", flink='" + flink + '\'' +
                ", params='" + params.toString() + '\'' +
                //", children='" + children.toString() + '\'' +
                '}';
    }

    public String getTitle() {
        return title;
    }

    public ArrayList<JMenu> getChildren() {
        return children;
    }

    public JMenuparams getParams() {
        return params;
    }

    public void setLevel(int level) {
        this.level = level;
    }

    public int getLevel() {
        return level;
    }
    public int getTotalChildren() {
        return totalChildren;
    }

    public void setTotalChildren(int totalChildren) {
        this.totalChildren = totalChildren;
    }

    public String getLink() {
        return link;
    }

    public int getId() {
        return id;
    }
}
