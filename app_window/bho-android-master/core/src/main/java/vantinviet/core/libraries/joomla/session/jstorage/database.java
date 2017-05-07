package vantinviet.core.libraries.joomla.session.jstorage;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class database {
    private static database ourInstance = new database();

    public static database getInstance() {
        return ourInstance;
    }

    private database() {
    }
}
