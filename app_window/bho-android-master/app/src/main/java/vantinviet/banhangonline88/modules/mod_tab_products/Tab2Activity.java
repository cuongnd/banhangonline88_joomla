package vantinviet.banhangonline88.modules.mod_tab_products;

import android.app.Activity;
import android.os.Bundle;
import android.view.Gravity;
import android.widget.TextView;

/**
 * Created by cuongnd on 27/03/2017.
 */

public class Tab2Activity extends Activity
{
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);

        TextView tv=new TextView(this);
        tv.setTextSize(25);
        tv.setGravity(Gravity.CENTER_VERTICAL);
        tv.setText("Tab2Activity");

        setContentView(tv);
    }
}
