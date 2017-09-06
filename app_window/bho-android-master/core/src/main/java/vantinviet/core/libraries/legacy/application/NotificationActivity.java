package vantinviet.core.libraries.legacy.application;

import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.NotificationCompat;
import android.support.v4.app.RemoteInput;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;

import org.json.JSONException;
import org.json.JSONObject;

import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.joomla.JFactory;

public class NotificationActivity extends BroadcastReceiver {

    // Key for the string that's delivered in the action's intent.
    private static final String KEY_TEXT_REPLY = "key_text_reply";

    // mRequestCode allows you to update the notification.
    int mRequestCode = 1000;


    private void move_main_activity(Intent intent){
        JApplication app = JFactory.getApplication();
        VTVConfig vtvConfig=VTVConfig.getInstance();

        String msg=(String) getMessageText(intent);
        if (msg != null && !msg.equals("")) {

            JSONObject obj = new JSONObject();
            try {

                obj.put("msg",msg);
                obj.put("userName","sanjay".trim());
                obj.put("room","MainRoom".trim());
                // mSocket.send(obj);
                app.mSocket.emit("newMessage", obj);
                Log.d("SEND MESSAGE",obj.toString());


            } catch (JSONException e) {
                Log.d("SEND MESSAGE","ERROR");
                e.printStackTrace();
            }
        }



    }

    private CharSequence getMessageText(Intent intent) {
        Bundle remoteInput = RemoteInput.getResultsFromIntent(intent);
        if (remoteInput != null) {
            return remoteInput.getCharSequence(KEY_TEXT_REPLY);
        }
        return null;
    }

    @Override
    public void onReceive(Context context, Intent intent) {

    }
}