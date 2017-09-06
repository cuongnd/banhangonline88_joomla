package vantinviet.banhangonline2;

import android.content.Intent;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.utilities.JUtilities;

public class MainActivity extends AppCompatActivity {

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        JFactory.getApplication().execute(this,"http://banhangonline88.com");
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        JUtilities.send_socket_messenger(intent,NOTIFICATION_SERVICE);

    }
}
