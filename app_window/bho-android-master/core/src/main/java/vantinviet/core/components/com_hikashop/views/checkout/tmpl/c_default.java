package vantinviet.core.components.com_hikashop.views.checkout.tmpl;

import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


/**
 * Created by cuongnd on 02/04/2017.
 */

public class c_default {
    JApplication app= JFactory.getApplication();
    public c_default(LinearLayout linear_layout){
        String component_response=app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        Timber.d("checkout component_response %s",component_response);
        reader.setLenient(true);
    }

}
