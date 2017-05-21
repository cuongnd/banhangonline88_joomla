package vantinviet.core.libraries.joomla.session;

import vantinviet.core.configuration.JConfig;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.cache.jstorage.JCacheFile;
import vantinviet.core.libraries.joomla.filesystem.JFile;

/**
 * Created by cuongnd on 6/8/2016.
 */
public  class JSession {
    private static JSession instance;
    public String android_ses_id =null;
    private static String formToken="";
    public Data data;

    public static JSession getInstance() {
        if(instance==null)
        {
            instance=new JSession();
        }
       return instance;
    }

    public String getFormToken() {
        return getData().getDataDefault().getSession().getToken();
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

    public  Data getData() {
        return data;
    }

    private class Data {
        public DataDefault __default;

        public DataDefault getDataDefault() {
            return __default;
        }

        private class DataDefault {
            public Session session;

            public Session getSession() {
                return session;
            }

            private class Session {
                int counter;
                String token="";

                public String getToken() {
                    return token;
                }
            }
        }
    }


}
