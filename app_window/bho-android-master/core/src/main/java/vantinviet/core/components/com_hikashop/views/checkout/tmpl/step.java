package vantinviet.core.components.com_hikashop.views.checkout.tmpl;

import android.content.Context;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.util.ArrayList;
import java.util.List;

import timber.log.Timber;
import vantinviet.core.administrator.components.com_hikashop.classes.Config;
import vantinviet.core.components.com_hikashop.views.checkout.HikashopViewCheckout;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


/**
 * Created by cuongnd on 02/04/2017.
 */

public class step {
    JApplication app= JFactory.getApplication();
    public step(LinearLayout linear_layout){
        linear_layout.setOrientation(LinearLayout.VERTICAL);
        JApplication app=JFactory.getApplication();
        String component_response=app.getComponent_response();
        Timber.d("component_response %s",component_response);
        HikashopViewCheckout view_checkout = JUtilities.getGsonParser().fromJson(component_response, HikashopViewCheckout.class);
        ArrayList<String> steps=view_checkout.getSteps();
        ArrayList<String> layouts=view_checkout.getLayouts();
        Config config=view_checkout.getConfig();
        step_show_step list_step =new step_show_step(app.getCurrentActivity());
        list_step.setSteps(steps);
        list_step.init();
        linear_layout.addView(list_step);
        for (String layout : layouts){
            layout=layout.trim();
            if(!(layout.substring(0,4)).equals("plg.")){
                Class<?> class_layout = null;
                try {
                    class_layout = Class.forName(String.format("vantinviet.core.components.com_hikashop.views.checkout.tmpl.%s",layout));
                    Constructor<?> cons = class_layout.getConstructor(Context.class);
                    Object object = cons.newInstance(app.getContext());
                    linear_layout.addView((LinearLayout)object);
                } catch (ClassNotFoundException e) {

                } catch (IllegalAccessException e) {
                    e.printStackTrace();
                } catch (NoSuchMethodException e) {
                    e.printStackTrace();
                } catch (InvocationTargetException e) {
                    e.getCause().printStackTrace();
                } catch (InstantiationException e) {
                    e.printStackTrace();
                }
            }

        }
    }

}
