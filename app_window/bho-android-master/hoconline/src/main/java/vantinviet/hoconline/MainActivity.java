package vantinviet.hoconline;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.Toolbar;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

public class MainActivity extends Activity {
    public static MainActivity mInstance = null;
    public static VTVConfig vtvconfig= VTVConfig.getInstance();
    private JApplication app= JFactory.getApplication();
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(app.get_layout_activity_main());
        mInstance = this;
        init(mInstance);
    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    private void init(MainActivity mInstance) {
        Timber.d("%s onCreate", MainActivity.class.getSimpleName());
        app.setCurrentActivity(mInstance);
        app.setContext(getBaseContext());
        app.setResources(getResources());


        //app.main_linear_layout =(LinearLayout) findViewById(R.id.main_linear_layout);
        //app.root_relative_layout =(RelativeLayout) findViewById(R.id.root_relative_layout);
        app.execute();

    }

}
