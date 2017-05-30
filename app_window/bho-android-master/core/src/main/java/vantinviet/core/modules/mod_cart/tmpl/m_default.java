package vantinviet.core.modules.mod_cart.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.LinearLayout;

import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.modules.mod_cart.DialogFragmentCart;
import vantinviet.core.modules.mod_menu.DialogFragmentSubMenuLeft;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class m_default extends LinearLayout {
    JApplication app= JFactory.getApplication();
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public m_default(Context context, Module module) {
        super(context);
        View view=inflate(getContext(), R.layout.modules_mod_cart_tmpl_default, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        ImageButton show_menu=(ImageButton)view.findViewById(R.id.show_cart);
        final String module_content=module.getContent();
        show_menu.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                DialogFragmentCart newFragmentLeft = new DialogFragmentCart();
                Bundle args = new Bundle();
                args.putString("module_content",module_content);
                newFragmentLeft.setArguments(args);

                //newFragment.setList_menu(list_menu);
                //newFragment.init();
                newFragmentLeft.show(app.getSupportFragmentManager(), "DialogFragmentCart");
                app.getProgressDialog().dismiss();
            }
        });


    }




}
