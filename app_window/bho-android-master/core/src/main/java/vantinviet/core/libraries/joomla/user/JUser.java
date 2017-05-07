package vantinviet.core.libraries.joomla.user;

import java.util.List;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class JUser {
    private static JUser ourInstance = new JUser();
    public boolean guest;
    public List<Integer> groups;
    public static JUser getInstance() {
        return ourInstance;
    }
    public static JUser getInstance(int id) {
        return ourInstance;
    }

    public JUser() {
    }
}
