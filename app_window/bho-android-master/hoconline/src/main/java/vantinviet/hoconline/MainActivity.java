package vantinviet.hoconline;

import android.app.Activity;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v7.app.AppCompatActivity;

import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

public class MainActivity extends AppCompatActivity {
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        JFactory.getApplication().execute(this,"http://hoctructuyen88.com");
    }

    @Override
    protected void onStop() {
        super.onStop();
    }
}
