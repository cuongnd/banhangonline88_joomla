package vantinviet.banhangonline88.styles;

import android.view.Gravity;
import android.widget.LinearLayout;

import timber.log.Timber;

/**
 * Created by cuong on 5/30/2017.
 */

public class top_right_menu_style {
    public top_right_menu_style(LinearLayout linear_layout){
        linear_layout.setOrientation(LinearLayout.HORIZONTAL);
        linear_layout.setGravity(Gravity.RIGHT);
    }
}
