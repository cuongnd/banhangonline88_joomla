package vantinviet.banhangonline88.components.com_hikashop.views.product.tmpl;

import android.widget.LinearLayout;
import android.widget.TextView;

import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 02/04/2017.
 */

public class listing {
    JApplication app= JFactory.getApplication();
    public listing(LinearLayout linear_layout){
        TextView  text_view=new TextView(app.context);
        text_view.setText("hello listing");
        linear_layout.addView(text_view);

    }
}
