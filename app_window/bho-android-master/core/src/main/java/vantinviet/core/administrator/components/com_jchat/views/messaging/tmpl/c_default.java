package vantinviet.core.administrator.components.com_jchat.views.messaging.tmpl;

import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;

import vantinviet.core.administrator.components.com_jchat.views.messaging.JchatViewMessenging;
import vantinviet.core.components.com_hikashop.views.product.HikashopViewProduct;
import vantinviet.core.components.com_jchat.views.messaging.tmpl.ShowMessaging;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuong on 8/13/2017.
 */

public class c_default {
    public  JchatViewMessenging messengers;
    static JApplication app = JFactory.getApplication();
    public c_default(LinearLayout linear_layout) {
        String component_response = app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        messengers = gson.fromJson(reader, JchatViewMessenging.class);
        //show_messaging = new ShowMessaging(app.getCurrentActivity(), messengers);
        //linear_layout.addView(show_messaging);
    }
}
