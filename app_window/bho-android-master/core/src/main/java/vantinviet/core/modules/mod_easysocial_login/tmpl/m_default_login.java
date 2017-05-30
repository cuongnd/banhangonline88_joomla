package vantinviet.core.modules.mod_easysocial_login.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.LinearLayout;

import vantinviet.core.R;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.modules.mod_easysocial_login.tmpl.class_default.DialogFragmentLogin;
import vantinviet.core.modules.mod_easysocial_login.tmpl.class_default.DialogFragmentLogout;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class m_default_login extends LinearLayout {
    private JUser user;
    private View view;
    JApplication app= JFactory.getApplication();
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public m_default_login(Context context) {
        super(context);
        user =JFactory.getUser();
        view=inflate(getContext(), R.layout.modules_mod_easysocial_login_tmpl_m_default_login, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        Button btn_login=(Button)view.findViewById(R.id.btn_login);

        btn_login.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                DialogFragmentLogin newFragmentLeft = new DialogFragmentLogin();
                newFragmentLeft.show(app.getSupportFragmentManager(), "DialogFragmentLogin");
            }
        });


    }



}
