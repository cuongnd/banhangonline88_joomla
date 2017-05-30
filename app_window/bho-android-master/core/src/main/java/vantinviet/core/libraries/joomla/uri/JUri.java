package vantinviet.core.libraries.joomla.uri;

/**
 * Created by cuongnd on 6/15/2016.
 */
public class JUri {
    private static JUri ourInstance = new JUri();

    public static JUri getInstance(String link) {
        return ourInstance;
    }

    private JUri() {
    }
}
