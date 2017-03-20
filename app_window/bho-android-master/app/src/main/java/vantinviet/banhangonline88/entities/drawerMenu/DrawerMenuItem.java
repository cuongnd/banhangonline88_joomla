package vantinviet.banhangonline88.entities.drawerMenu;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class DrawerMenuItem {

    private long menu_item_id;

    @SerializedName("original_id")
    private long originalId;
    private String name;
    private String link;
    private List<DrawerMenuItem> children;
    private String type;

    public DrawerMenuItem() {
    }

    public DrawerMenuItem(long menu_item_id, long originalId, String name) {
        this.menu_item_id = menu_item_id;
        this.originalId = originalId;
        this.name = name;
    }

    public long getMenu_item_id() {
        return menu_item_id;
    }

    public void setMenu_item_id(long menu_item_id) {
        this.menu_item_id = menu_item_id;
    }

    public long getOriginalId() {
        return originalId;
    }

    public void setOriginalId(long originalId) {
        this.originalId = originalId;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public List<DrawerMenuItem> getChildren() {
        return children;
    }

    public void setChildren(List<DrawerMenuItem> children) {
        this.children = children;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        DrawerMenuItem that = (DrawerMenuItem) o;

        if (menu_item_id != that.menu_item_id) return false;
        if (originalId != that.originalId) return false;
        if (name != null ? !name.equals(that.name) : that.name != null) return false;
        if (children != null ? !children.equals(that.children) : that.children != null)
            return false;
        return !(type != null ? !type.equals(that.type) : that.type != null);

    }

    @Override
    public int hashCode() {
        int result = (int) (menu_item_id ^ (menu_item_id >>> 32));
        result = 31 * result + (int) (originalId ^ (originalId >>> 32));
        result = 31 * result + (name != null ? name.hashCode() : 0);
        result = 31 * result + (children != null ? children.hashCode() : 0);
        result = 31 * result + (type != null ? type.hashCode() : 0);
        return result;
    }

    @Override
    public String toString() {
        return "DrawerMenuItem{" +
                "menu_item_id=" + menu_item_id +
                ", originalId=" + originalId +
                ", name='" + name + '\'' +
                ", children=" + children +
                ", type='" + type + '\'' +
                ", link='" + link + '\'' +
                '}';
    }

    public boolean hasChildren() {
        return children != null && !children.isEmpty();
    }

    public String getLink() {
        return link;
    }
}
