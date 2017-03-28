package vantinviet.banhangonline88.modules.mod_slideshowck;

import vantinviet.banhangonline88.api.EndPoints;

/**
 * Created by cuongnd on 27/03/2017.
 */

public class Slider  {
    String imgname;
    String imgcaption;
    String imgtitle;
    String imgthumb;
    String imglink="";
    String imgtarget;
    String imgalignment;
    String imgvideo;
    String slidearticleid;
    String slidearticlename;
    String imgtime;
    String state;
    String startdate;
    String enddate;
    String article;
    private int source;

    @Override
    public String toString() {
        return "Slider{" +
                "imgname=" + imgname +
                ", imgcaption='" + imgcaption + '\'' +
                ", imgtitle='" + imgtitle + '\'' +
                ", imgthumb='" + imgthumb + '\'' +
                ", imglink='" + imglink + '\'' +
                ", imgtarget='" + imgtarget + '\'' +
                ", imgalignment='" + imgalignment + '\'' +
                ", imgvideo='" + imgvideo + '\'' +
                ", slidearticleid='" + slidearticleid + '\'' +
                ", slidearticlename='" + slidearticlename + '\'' +
                ", imgtime='" + imgtime + '\'' +
                ", state='" + state + '\'' +
                ", startdate='" + startdate + '\'' +
                ", enddate='" + enddate + '\'' +
                ", article='" + article + '\'' +
                '}';
    }

    public String getTitle() {
        return imgtitle;
    }

    public String getSource() {
        return EndPoints.API_URL1.concat(imgname);
    }
}
