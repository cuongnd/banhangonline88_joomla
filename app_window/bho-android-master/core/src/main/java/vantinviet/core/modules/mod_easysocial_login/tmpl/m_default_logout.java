package vantinviet.core.modules.mod_easysocial_login.tmpl;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.view.View;
import android.webkit.JavascriptInterface;
import android.widget.Button;
import android.widget.LinearLayout;

import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.libraries.utilities.MessageType;
import vantinviet.core.modules.mod_easysocial_login.tmpl.class_default.DialogFragmentLogout;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class m_default_logout extends LinearLayout {
    private JUser user;
    private View view;
    JApplication app= JFactory.getApplication();
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public m_default_logout(Context context) {
        super(context);
        user = JFactory.getUser();
        view=inflate(getContext(), R.layout.modules_mod_easysocial_login_tmpl_m_default_logout, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        Button btn_show_profile=(Button)view.findViewById(R.id.btn_show_profile);
        btn_show_profile.setText(app.getContext().getString(R.string.hello_user,user.getUsername()));
        btn_show_profile.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                DialogFragmentLogout newFragmentLeft = new DialogFragmentLogout();
                newFragmentLeft.show(app.getSupportFragmentManager(), "DialogFragmentLogout");
            }
        });
        Button btn_logout=(Button)view.findViewById(R.id.btn_logout);

        btn_logout.setOnClickListener(new OnClickListener() {
            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onClick(View view) {
                AlertDialog.Builder builder = new AlertDialog.Builder(app.getCurrentActivity());
                builder.setMessage(R.string.str_logout_confirm);
                builder.setPositiveButton(R.string.str_yes, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        //TODO
                        vtv_WebView web_browser = JFactory.getWebBrowser();
                        Map<String, String> post = new HashMap<String, String>();
                        JMenu menu = JFactory.getMenu();
                        int active_menu_item_id = menu.getMenuactive().getId();
                        post.put("option", "com_users");
                        post.put("task", "user.ajax_logout");
                        post.put(app.getSession().getFormToken(), "1");
                        post.put("tmpl", "component");
                        post.put("Itemid", String.valueOf(active_menu_item_id));
                        web_browser.vtv_postUrl(VTVConfig.rootUrl, post);
                        app.getProgressDialog(app.getContext().getString(R.string.str_logout_processing)).show();
                        web_browser.addJavascriptInterface(new m_default_logout.ajax_logout(), "HtmlViewer");

                        dialog.dismiss();
                    }
                });
                builder.setNegativeButton(R.string.str_cancel, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        //TODO
                        dialog.dismiss();
                    }
                });
                AlertDialog dialog = builder.create();
                dialog.show();

            }
        });


    }

    private class ajax_logout {
        public ajax_logout() {
        }

        @RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
        @JavascriptInterface
        public void showHTML(String html) {
            html= JUtilities.get_string_by_string_base64(html);
            Page page = JUtilities.getGsonParser().fromJson(html, Page.class);
            String component_content=page.getComponent_response();
            Timber.d("html response %s",component_content);
            JUser user = JUtilities.getGsonParser().fromJson(component_content, JUser.class);
            app.getProgressDialog().dismiss();
            if(user.getId()>0){
                JUtilities.alert(MessageType.ERROR,R.string.str_logout_false);
            }else{
                final m_default_login login_view=new m_default_login(app.getContext());
                final LinearLayout wrapper_content=(LinearLayout)app.getMain_relative_layout().findViewById(R.id.mod_easysocial_login_wrapper_content);
                app.getCurrentActivity().runOnUiThread(new Runnable()
                {
                    public void run()
                    {
                        wrapper_content.removeAllViews();
                        wrapper_content.addView(login_view);

                    }

                });
                JUtilities.alert(MessageType.INFO,R.string.str_logout_successful);
            }
        }

    }



}
