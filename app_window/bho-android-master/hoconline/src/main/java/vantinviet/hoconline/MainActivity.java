package vantinviet.hoconline;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

import java.util.ArrayList;

import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

public class MainActivity extends AppCompatActivity {

    private MainActivity mInstance;
    private JApplication app= JFactory.getApplication();
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        mInstance = this;
        init(mInstance);
    }
    private void init(MainActivity mInstance) {


    }

}
