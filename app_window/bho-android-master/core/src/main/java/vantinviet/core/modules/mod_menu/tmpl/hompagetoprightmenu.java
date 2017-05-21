package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.LinearLayout;

import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.modules.mod_menu.DialogFragmentSubMenuRight;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class hompagetoprightmenu extends LinearLayout {
    JApplication app= JFactory.getApplication();
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public hompagetoprightmenu(Context context, Module module) {
        super(context);
        View view=inflate(getContext(), R.layout.modules_mod_menu_tmpl_hometoprightmenu, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        ImageButton show_menu=(ImageButton)view.findViewById(R.id.show_menu);
        final String module_content=module.getContent();
        show_menu.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                DialogFragmentSubMenuRight newFragmentLeft = new DialogFragmentSubMenuRight();
                Bundle args = new Bundle();
                args.putString("module_content",module_content);
                newFragmentLeft.setArguments(args);

                //newFragment.setList_menu(list_menu);
                //newFragment.init();
                newFragmentLeft.show(app.getSupportFragmentManager(), "DialogFragmentSubMenuRight");
                app.getProgressDialog().dismiss();
            }
        });


    }




}
