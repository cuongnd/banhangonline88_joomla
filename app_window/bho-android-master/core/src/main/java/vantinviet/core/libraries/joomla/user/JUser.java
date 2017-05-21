package vantinviet.core.libraries.joomla.user;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import vantinviet.core.libraries.cms.menu.JMenu;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class JUser {
    private static JUser ourInstance = new JUser();
    public int guest;
    Map<Integer, Integer> map = new HashMap<Integer, Integer>();
    protected int id=0;
    protected String name;
    protected String username;
    protected String email;
    private JUser activeUser;

    public static JUser getInstance() {
        return ourInstance;
    }
    public static JUser getInstance(int id) {
        return ourInstance;
    }

    public JUser() {
    }

    public int getId() {
        return id;
    }

    public JUser getActiveUser() {
        return activeUser;
    }

    public void setActiveUser(JUser activeUser) {
        this.activeUser = activeUser;
    }
    @Override
    public String toString() {
        return "JUser{" +
                "id=" + id +
                ", name='" + name + '\'' +
                ", username='" + username + '\'' +
                ", email='" + email + '\'' +
                ", guest='" + guest+ '\'' +
                //", children='" + children.toString() + '\'' +
                '}';
    }

    public String getUsername() {
        return username;
    }
}
