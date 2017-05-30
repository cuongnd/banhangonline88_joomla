package vantinviet.core.libraries.joomla.language;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class JLanguage {
    private static JLanguage ourInstance = null;

    public static JLanguage getInstance(String language, boolean debug_lang) {
        if(ourInstance==null)
        {
            ourInstance=new JLanguage(language,debug_lang);
        }
        return ourInstance;
    }

    public JLanguage(String language, boolean debug_lang) {
    }
}
