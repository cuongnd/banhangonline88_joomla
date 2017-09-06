package vantinviet.core.components.com_users;

import android.widget.LinearLayout;

import timber.log.Timber;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.legacy.controller.JControllerLegacy;

/**
 * Created by cuongnd on 6/18/2016.
 */
public class users {
    JApplication app= JFactory.getApplication();
    public  users(LinearLayout linear_layout, int component_width){
        JControllerLegacy controller =  JControllerLegacy.getInstance(this.getClass().getSimpleName());
        String task=app.input.getString("task");
        controller.execute(task);
        controller.redirect();
        Timber.d("hello jchat");
    }
}
