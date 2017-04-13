package vantinviet.banhangonline88.components.com_hikashop;

import android.widget.LinearLayout;

import java.sql.Time;
import java.util.Timer;

import timber.log.Timber;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.cache.controller;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.libraries.legacy.controller.JControllerLegacy;

/**
 * Created by cuongnd on 02/04/2017.
 */

public class hikashop {
    JApplication app= JFactory.getApplication();
    public  hikashop(LinearLayout linear_layout,int component_width){
        JControllerLegacy controller = JControllerLegacy.getInstance(hikashop.class.getSimpleName());
        String task=app.input.getString("task");
        controller.execute(task);
        controller.redirect();
        Timber.d("hello hikashop");
    }
}

