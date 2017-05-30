package vantinviet.core.libraries.legacy.application;

import android.content.SharedPreferences;

import java.util.Map;
import java.util.Timer;

import timber.log.Timber;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuong on 5/29/2017.
 */

class CacheLinkAndDataPost {
    String link="";
    Map<String,String> data_post;

    public String getLink() {
        return link!=null?link:"";
    }

    public Map<String, String> getData_post() {
        return data_post;
    }


    public void setLink(String link) {
        this.link = link;
    }

    public void setData_post(Map<String, String> data_post) {
        this.data_post = data_post;
    }

    public void save() {
        JApplication app= JFactory.getApplication();
        SharedPreferences cache_link_and_data = app.getCurrentActivity().getSharedPreferences("cache_link_and_data", app.getCurrentActivity().MODE_PRIVATE);
        SharedPreferences.Editor editor = cache_link_and_data.edit();
        String current_data= JUtilities.getGsonParser().toJson(this);
        editor.putString("cache_link_and_data", current_data);
        editor.apply();

    }
}
