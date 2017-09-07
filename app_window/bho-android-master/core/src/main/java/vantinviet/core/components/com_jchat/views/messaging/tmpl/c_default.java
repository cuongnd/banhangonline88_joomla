package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.design.widget.BottomNavigationView;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;

import vantinviet.core.administrator.components.com_jchat.models.JChatModelMessages;
import vantinviet.core.components.com_hikashop.views.product.HikashopViewProduct;
import vantinviet.core.components.com_hikashop.views.product.tmpl.ShowContent;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;

import static vantinviet.core.components.com_hikashop.views.product.tmpl.show.view_product;

/**
 * Created by cuong on 8/13/2017.
 */

public class c_default {
    private ShowMessaging show_messaging = null;
    static JApplication app = JFactory.getApplication();
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public c_default(LinearLayout linear_layout) {
        JUser user=JFactory.getUser();
        JChatModelMessages jChatModelMessages=JChatModelMessages.getInstance();
        String component_response = app.getComponent_response();
        Gson gson = new Gson();
        show_messaging = new ShowMessaging(app.getCurrentActivity());
        linear_layout.addView(show_messaging);
    }
}
