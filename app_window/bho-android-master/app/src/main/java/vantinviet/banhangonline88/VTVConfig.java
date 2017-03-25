package vantinviet.banhangonline88;

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
    //public static String root_url ="http://www.banhangonline88.com";
    //public static String root_url ="http://www.countdown.vantinviet.com";
    //public static String root_url ="http://www.phatthanhnghean.vantinviet.com";
    public  String root_url;

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

    public void set_root_url(String a_root_url) {
        this.root_url=a_root_url;
        System.out.println("this.root_url:"+this.root_url);
    }
}
