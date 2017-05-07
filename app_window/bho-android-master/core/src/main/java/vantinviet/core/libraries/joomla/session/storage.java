package vantinviet.core.libraries.joomla.session;

/**
 * Created by cuongnd on 6/8/2016.
 */
class storage {
    private static storage ourInstance = new storage();

    public static storage getInstance() {
        return ourInstance;
    }

    private storage() {
    }
}
