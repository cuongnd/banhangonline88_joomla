package vantinviet.core.administrator.components.com_hikamarket.classes;

/**
 * Created by cuongnd on 01/04/2017.
 */

public class Image {
    String path;
    String filename;
    String url;
    String origin_url;
    public String toString() {
        return "Image{" +
                "path=" + path +
                ", filename='" + filename + '\'' +
                ", url='" + url + '\'' +
                ", origin_url='" + origin_url + '\'' +
                '}';
    }

    public String getUrl() {
        return url;
    }
}
