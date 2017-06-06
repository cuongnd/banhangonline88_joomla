package vantinviet.banhangonline88.styles;

import android.graphics.Color;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import static android.widget.ListPopupWindow.MATCH_PARENT;
import static android.widget.ListPopupWindow.WRAP_CONTENT;

/**
 * Created by cuong on 5/30/2017.
 */

public class row_main_menu_style {
    public row_main_menu_style(LinearLayout linear_layout){
        LinearLayout.LayoutParams layout_params = new LinearLayout.LayoutParams(MATCH_PARENT, WRAP_CONTENT);
        layout_params.setMargins(0,10,0,10);
        linear_layout.setLayoutParams(layout_params);
    }
}
