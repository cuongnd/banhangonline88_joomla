package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.LinearLayout;

import com.google.gson.reflect.TypeToken;
import com.unnamed.b.atv.model.TreeNode;
import com.unnamed.b.atv.view.AndroidTreeView;

import java.lang.reflect.Type;
import java.util.ArrayList;

import vantinviet.core.R;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.modules.mod_menu.DialogFragmentSubMenu;
import vantinviet.core.modules.mod_menu.DialogFragmentSubMenuLeft;
import vantinviet.core.modules.mod_menu.IconTreeItemHolder;


import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class hompagetopleftmenu extends LinearLayout {
    JApplication app= JFactory.getApplication();
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public hompagetopleftmenu(Context context, Module module) {
        super(context);
        View view=inflate(getContext(), R.layout.modules_mod_menu_tmpl_hometopleftmenu, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        ImageButton show_menu=(ImageButton)view.findViewById(R.id.show_menu);
        final String module_content=module.getContent();
        show_menu.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                DialogFragmentSubMenuLeft newFragmentLeft = new DialogFragmentSubMenuLeft();
                Bundle args = new Bundle();
                args.putString("module_content",module_content);
                newFragmentLeft.setArguments(args);

                //newFragment.setList_menu(list_menu);
                //newFragment.init();
                newFragmentLeft.show(app.getSupportFragmentManager(), "DialogFragmentSubMenuLeft");
                app.getProgressDialog().dismiss();
            }
        });


    }




}
