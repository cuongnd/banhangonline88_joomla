package vantinviet.core.components.com_users.views.login.tmpl;

import android.os.Build;
import android.support.annotation.RequiresApi;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;

import vantinviet.core.administrator.components.com_jchat.models.JChatModelMessages;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuong on 8/13/2017.
 */

public class c_default {
    private ShowLogin show_messaging = null;
    JChatModelMessages messengers;
    static JApplication app = JFactory.getApplication();
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public c_default(LinearLayout linear_layout) {
        JUser user=JFactory.getUser();

        String component_response = app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        messengers= gson.fromJson(reader, JChatModelMessages.class);
        show_messaging = new ShowLogin(app.getCurrentActivity(), messengers);
        linear_layout.addView(show_messaging);
    }
}
