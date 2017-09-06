package vantinviet.core.libraries.joomla.user;

import com.google.gson.annotations.SerializedName;

import java.util.HashMap;
import java.util.Map;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class JUser {
    private static JUser ourInstance = new JUser();
    public int guest;
    Map<Integer, Integer> map = new HashMap<Integer, Integer>();
    protected int id=0;
    protected String name="";
    protected String socketId="";
    protected String userName ="";
    protected String email="";
    private JUser activeUser=null;

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
                ",socketId=" + socketId +
                ", name='" + name + '\'' +
                ", userName='" + userName + '\'' +
                ", email='" + email + '\'' +
                ", guest='" + guest+ '\'' +
                //", children='" + children.toString() + '\'' +
                '}';
    }

    public String getUserName() {
        return userName;
    }

    public String getName() {
        return name;
    }

    public String getSocketId() {
        return socketId;
    }

    public void setName(String name) {
        this.name = name;
    }
}
