package vantinviet.banhangonline88.libraries.joomla.filesystem;

import android.annotation.TargetApi;
import android.os.Build;
import android.os.Environment;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.UnsupportedEncodingException;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class JFile {
    @TargetApi(Build.VERSION_CODES.KITKAT)
    public static String read(String sFileName, String folder) {
        String content = "";
        File root = new File(Environment.getExternalStorageDirectory(),folder);
        if (!root.exists()) {
            root.mkdirs();
        }
        File a_cache_file = new File(root, sFileName);
        if (a_cache_file.exists()) {

            try {
                BufferedReader br = new BufferedReader(new FileReader(a_cache_file));
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
    public static void write(String sFileName, String sBody, String folder) {
        String error = "";
        try {
            File root = new File(Environment.getExternalStorageDirectory(), folder);
            if (!root.exists()) {
                root.mkdirs();
            }
            File gpxfile = new File(root, sFileName);
            FileWriter writer = new FileWriter(gpxfile);
            writer.append(sBody);
            writer.flush();
            writer.close();
            //Toast.makeText(this, "Saved", Toast.LENGTH_SHORT).show();
        } catch (IOException e) {
            e.printStackTrace();
            error = e.getMessage();

        }
    }

    public static boolean exists(String md5_link, String folder) {
        File root = new File(Environment.getExternalStorageDirectory(),folder);
        if (!root.exists()) {
            root.mkdirs();
        }
        File a_cache_file = new File(root, md5_link);
        if (a_cache_file.exists()) {
            return true;
        }
        return  false;
    }
}
