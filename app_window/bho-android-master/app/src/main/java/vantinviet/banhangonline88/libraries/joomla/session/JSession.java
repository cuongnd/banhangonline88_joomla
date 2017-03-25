package vantinviet.banhangonline88.libraries.joomla.session;

import vantinviet.banhangonline88.configuration.JConfig;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.cache.jstorage.JCacheFile;
import vantinviet.banhangonline88.libraries.joomla.filesystem.JFile;

/**
 * Created by cuongnd on 6/8/2016.
 */
public  class JSession {
    private static JSession instance;
    public String android_ses_id =null;
    public static JSession getInstance() {
        if(instance==null)
        {
            instance=new JSession();
        }
       return instance;
    }
    public  void setId(String android_ses_id) {
        this.android_ses_id = android_ses_id;
        JConfig config= JFactory.getConfig();
        config.android_ses_id= android_ses_id;
        String str_config=config.toStringConfig();
        JFile.write(config.FILE_NAME_OF_FILE_CONFIG, str_config, JCacheFile.CACHE_PATH);
    }

    public String getId() {
        JConfig config= JFactory.getConfig();
        String android_ses_id= this.android_ses_id;
        if (android_ses_id ==null)
        {
            android_ses_id=config.get("android_ses_id","");
        }
        return android_ses_id;
    }


}
