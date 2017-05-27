package vantinviet.core.components.com_hikashop.views.checkout.tmpl;

import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;
import java.util.List;

import timber.log.Timber;
import vantinviet.core.administrator.components.com_hikashop.classes.Config;
import vantinviet.core.components.com_hikashop.views.checkout.HikashopViewCheckout;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


/**
 * Created by cuongnd on 02/04/2017.
 */

public class step {
    JApplication app= JFactory.getApplication();
    public step(LinearLayout linear_layout){
        JApplication app=JFactory.getApplication();
        String component_response=app.getComponent_response();
        HikashopViewCheckout view_checkout = JUtilities.getGsonParser().fromJson(component_response, HikashopViewCheckout.class);
        ArrayList<String> steps=view_checkout.getSteps();
        Config config=view_checkout.getConfig();
        step_show_step list_step =new step_show_step(app.getCurrentActivity());
        list_step.setSteps(steps);
        list_step.init();
        linear_layout.addView(list_step);
    }

}
