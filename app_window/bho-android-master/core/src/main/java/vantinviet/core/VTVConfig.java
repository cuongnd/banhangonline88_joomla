package vantinviet.core;

import android.annotation.TargetApi;
import android.os.Build;
import android.os.Environment;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.UnsupportedEncodingException;


/**
 * Created by cuongnd on 12/17/2015.
 */
public class VTVConfig {
    public static int screen_size_width;
    public static int screen_size_height;
    public static int screenDensity;
    private static VTVConfig instance;
    //public static String rootUrl ="http://www.banhangonline88.com";
    //public static String rootUrl ="http://www.countdown.vantinviet.com";
    //public static String rootUrl ="http://www.phatthanhnghean.vantinviet.com";
    public static String rootUrl="http://banhangonline88.com";
    private static boolean debug=true;
    private String local_version="2.5";
    private int caching=1;

    /* Static 'instance' method */
    public static VTVConfig getInstance() {

        if (instance == null) {
            instance = new VTVConfig();
        }
        return instance;
    }
    public static boolean getDebug(){
        return debug;
    }
    @TargetApi(Build.VERSION_CODES.KITKAT)
    public  static  String get_version()
    {

        String content="1";
        if(content!="")
        {
            return content;
        }
        File root = new File(Environment.getExternalStorageDirectory(), "cache");
        if (!root.exists()) {
            root.mkdirs();
        }
        File cofig_file = new File(root, "VTVConfig.xml");
        if (cofig_file.exists()) {

            try {
                BufferedReader br = new BufferedReader(new FileReader(cofig_file));
                try {
                    StringBuilder sb = new StringBuilder();
                    String line = br.readLine();

                    while (line != null) {
                        sb.append(line);
                        sb.append(System.lineSeparator());
                        line = br.readLine();
                    }
                    content = sb.toString();
                } finally {
                    br.close();
                }


            } catch (FileNotFoundException e) {
                return "";
            } catch (UnsupportedEncodingException e) {
                return "";
            } catch (IOException e) {
                return "";
            }

        }
        return content;
    }

    public void setScreenDensity(int screenDensity) {
        VTVConfig.screenDensity = screenDensity;
    }

    public void setScreen_size_height(int screen_size_height) {
        VTVConfig.screen_size_height = screen_size_height;
    }

    public void setRootUrl(String rootUrl) {
        this.rootUrl =rootUrl;
    }
    public String getRootUrl() {
        return rootUrl;
    }

    public void setScreen_size_width(int screen_size_width) {
        this.screen_size_width = screen_size_width;
    }
    public int getScreen_size_width() {
        return this.screen_size_width;
    }

    public String getLocal_version() {
        return local_version;
    }

    public int getCaching() {
        return caching;
    }
}
