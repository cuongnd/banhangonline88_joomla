package vantinviet.core.configuration;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class JConfig_countdown extends JConfig {
    private static JConfig_countdown instance;
    public  String FILE_NAME_OF_FILE_CONFIG = "VTVConfig.ini";
    public float cachetime=150000;
    public int caching=0;
    public String cache_handler="file";
    public String android_ses_id=null;
    public static JConfig_countdown getInstance() {
        if(instance==null)
        {
            instance=new JConfig_countdown();
        }
        return instance;
    }
}
