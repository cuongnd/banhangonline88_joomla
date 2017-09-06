package vantinviet.core.administrator.components.com_jchat;

import android.widget.LinearLayout;

import timber.log.Timber;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.legacy.controller.JControllerLegacy;

/**
 * Created by cuong on 8/13/2017.
 */

public class jchat {
    JApplication app= JFactory.getApplication();
    public  jchat(LinearLayout linear_layout, int component_width){
        JControllerLegacy controller =  JControllerLegacy.getInstance(this.getClass().getSimpleName());
        String task=app.input.getString("task");
        controller.execute(task);
        controller.redirect();
        Timber.d("hello jchat");
    }
}
