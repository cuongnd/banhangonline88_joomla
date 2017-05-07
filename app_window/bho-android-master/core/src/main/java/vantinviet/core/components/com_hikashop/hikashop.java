package vantinviet.core.components.com_hikashop;

import android.widget.LinearLayout;

import java.sql.Time;
import java.util.Timer;

import timber.log.Timber;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.cache.controller;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.legacy.controller.JControllerLegacy;

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

