package vantinviet.core.libraries.cms.menu;

import com.google.gson.annotations.SerializedName;

import java.util.ArrayList;

import vantinviet.core.administrator.components.com_hikashop.classes.Image;

/**
 * Created by cuongnd on 6/7/2016.
 */
public class JMenu {

    int id=0;
    int parent_id;
    String title;
    String link;
    String flink;
    JMenuparams params;
    private ArrayList<Image> images;
    private static JMenu instance;
    @SerializedName("children")
    public ArrayList<JMenu> children=new ArrayList<JMenu>();
    private JMenu menuactive;
    protected int level=0;
    protected int totalChildren=0;
    private String mobile_response_type;

    /* Static 'instance' method */
    public static JMenu getInstance() {

        if (instance == null) {
            instance = new JMenu();
        }
        return instance;
    }

    public JMenu getMenuactive() {
        return menuactive;
    }

    public void setMenuactive(JMenu menuactive) {
        this.menuactive = menuactive;
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

    public int getInt() {
        return id;
    }

    public String getMobile_response_type() {
        return mobile_response_type;
    }

    public static JMenu getItem(int id) {
        return null;
    }
}
