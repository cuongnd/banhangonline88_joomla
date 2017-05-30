package vantinviet.core.libraries.joomla.cache.jstorage;

import android.os.Environment;

import vantinviet.core.libraries.joomla.filesystem.JFile;

import java.io.File;

/**
 * Created by cuongnd on 6/8/2016.
 */
public class JCacheFile {
    public static final String CACHE_PATH = "cache";
    private static final String EXTENSION_FILE_CACHE = ".cache";
    public static File root=Environment.getExternalStorageDirectory();
    public static String get_content_component(String md5_link) {
        String content="";
        String md5_link_file=md5_link + JCacheFile.EXTENSION_FILE_CACHE;
        if(!JFile.exists(md5_link_file,JCacheFile.CACHE_PATH))
        {
            return content;
        }
        content= JFile.read(md5_link_file, JCacheFile.CACHE_PATH);
        return content;
    }

    public static void set_content_component(String md5_link, String content) {
        String md5_link_file=md5_link + JCacheFile.EXTENSION_FILE_CACHE;
        JFile.write(md5_link_file,content, JCacheFile.CACHE_PATH);
    }
    public static void set_content_website(String md5_link, String content) {
        String md5_link_file=md5_link + JCacheFile.EXTENSION_FILE_CACHE;
        JFile.write(md5_link_file,content, JCacheFile.CACHE_PATH);
    }

    public static String get_content_website(String md5_link) {
        String content="";
        String md5_link_file=md5_link + JCacheFile.EXTENSION_FILE_CACHE;
        System.out.println(md5_link_file);
        if(!JFile.exists(md5_link_file,JCacheFile.CACHE_PATH))
        {
            return content;
        }
        content= JFile.read(md5_link_file, JCacheFile.CACHE_PATH);
        return content;
    }



}
