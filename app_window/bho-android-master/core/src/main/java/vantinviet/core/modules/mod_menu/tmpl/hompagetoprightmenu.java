package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.TextView;

import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuong on 5/10/2017.
 */

public class hompagetoprightmenu extends LinearLayout {

    public hompagetoprightmenu(Context context) {
        super(context);
        View view=inflate(getContext(), R.layout.modules_mod_menu_tmpl_hompagetoprightmenu, this);

    }
}
