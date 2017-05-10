package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;

import vantinviet.core.R;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class homeverticalmenutag extends LinearLayout {

    public homeverticalmenutag(Context context) {
        super(context);
        inflate(getContext(), R.layout.modules_mod_menu_tmpl_homeverticalmenutag, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        this.setVisibility(LinearLayout.GONE);


    }
}
