package vantinviet.core.modules.mod_easysocial_login.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class m_default extends LinearLayout {
    private JUser user;
    private View m_default_view;
    JApplication app= JFactory.getApplication();
    LayoutParams layout_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public m_default(Context context, Module module) {
        super(context);
        final String module_content=module.getContent();
        user = JFactory.getUser();
        Timber.d("current user %s",user.toString());
        m_default_view = inflate(getContext(), R.layout.modules_mod_easysocial_login_tmpl_m_default, this);
        if(user.getId()>0){
            m_default_logout logout_view=new m_default_logout(app.getContext());
            LinearLayout wrapper_content=(LinearLayout) m_default_view.findViewById(R.id.mod_easysocial_login_wrapper_content);
            wrapper_content.addView(logout_view);
        }else{
            m_default_login login_view=new m_default_login(app.getContext());
            LinearLayout wrapper_content=(LinearLayout) m_default_view.findViewById(R.id.mod_easysocial_login_wrapper_content);
            wrapper_content.addView(login_view);
        }
        this.setLayoutParams(layout_params);
    }




}
