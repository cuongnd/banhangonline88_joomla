package vantinviet.banhangonline88.libraries.joomla.cache;

import com.vantinviet.vtv.configuration.JConfig;
import com.vantinviet.vtv.libraries.joomla.cache.jstorage.JCacheFile;
import com.vantinviet.vtv.libraries.joomla.JFactory;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class cache {
    public static String get_content_component(String md5_link) {
        String content_component;
        JConfig config= JFactory.getConfig();
        String cache_handler=config.cache_handler;
        if(cache_handler.compareTo("file") == 0)
        {
            content_component= JCacheFile.get_content_component(md5_link);
        }
        else{
            content_component="";
        }
        return content_component;
    }

    public static void set_content_component(String md5_link, String content) {
        JConfig config= JFactory.getConfig();
        String cache_handler=config.cache_handler;
        if(cache_handler.compareTo("file") == 0)
        {
            JCacheFile.set_content_component(md5_link, content);
        }
        else{
        }
    }
    public static String get_content_website(String md5_link) {
        String content_website;
        JConfig config= JFactory.getConfig();
        String cache_handler=config.cache_handler;
        if(cache_handler.compareTo("file") == 0)
        {
            content_website= JCacheFile.get_content_website(md5_link);
        }
        else{
            content_website="";
        }
        return content_website;
    }

    public static void set_content_website(String md5_link, String content) {
        JConfig config= JFactory.getConfig();
        String cache_handler=config.cache_handler;
        if(cache_handler.compareTo("file") == 0)
        {
            JCacheFile.set_content_website(md5_link, content);
        }
        else{

        }
    }

}
