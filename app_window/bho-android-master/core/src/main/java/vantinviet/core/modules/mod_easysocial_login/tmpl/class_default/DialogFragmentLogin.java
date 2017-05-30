package vantinviet.core.modules.mod_easysocial_login.tmpl.class_default;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v4.app.DialogFragment;
import android.view.LayoutInflater;
import android.view.View;
import android.webkit.JavascriptInterface;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;

import com.unnamed.b.atv.model.TreeNode;

import java.lang.reflect.Method;
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
import vantinviet.core.libraries.utilities.JAlert;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.libraries.utilities.MessageType;
import vantinviet.core.modules.mod_easysocial_login.tmpl.m_default_logout;

import static android.app.ProgressDialog.show;

/**
 * Created by cuong on 5/19/2017.
 */

public class DialogFragmentLogin extends DialogFragment {

    private TreeNode root;
    static JApplication app= JFactory.getApplication();
    public LinearLayout linear_layout_wrapper_menu;
    public View view;
    public AlertDialog mAlertDialog;
    public EditText txt_username;
    public EditText txt_password;
    public JUser user;
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        // Use the Builder class for convenient dialog construction
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        LayoutInflater inflater = getActivity().getLayoutInflater();
        view=inflater.inflate(R.layout.modules_mod_easysocial_login_tmpl_m_default_dialog_login, null);
        builder.setTitle(R.string.str_login);
        builder.setCancelable(false);
        builder
                .setPositiveButton(R.string.str_login, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        if(validate()){

                        }
                        Boolean wantToCloseDialog = false;
                        //Do stuff, possibly set wantToCloseDialog to true then...
                        if(wantToCloseDialog)
                            dialog.dismiss();

                        //else dialog stays open. Make sure you have an obvious way to close the dialog especially if you set cancellable to false.

                    }
                })
                .setNegativeButton(R.string.str_close, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                    }
                })
        ;


        builder.setView(view);
        mAlertDialog = builder.create();
        // Create the AlertDialog object and return it

        return mAlertDialog;
    }

    //onStart() is where dialog.show() is actually called on
//the underlying dialog, so we have to do it there or
//later in the lifecycle.
//Doing it in onResume() makes sure that even if there is a config change
//environment that skips onStart then the dialog will still be functioning
//properly after a rotation.
    @Override
    public void onResume()
    {
        super.onResume();
        if(mAlertDialog != null)
        {
            Button positiveButton = (Button) mAlertDialog.getButton(Dialog.BUTTON_POSITIVE);

            positiveButton.setOnClickListener(new View.OnClickListener()
            {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onClick(View v)
                {
                    Boolean enableCloseDialog = false;
                    if (validate()) {
                        enableCloseDialog = true;
                    }

                    //Do stuff, possibly set wantToCloseDialog to true then...
                    if (enableCloseDialog) {
                        vtv_WebView web_browser = JFactory.getWebBrowser();
                        Map<String, String> post = new HashMap<String, String>();
                        JMenu menu = JFactory.getMenu();
                        int active_menu_item_id = menu.getMenuactive().getId();
                        post.put("option", "com_users");
                        post.put("task", "user.ajax_login");
                        post.put("username", txt_username.getText().toString());
                        post.put("password", txt_password.getText().toString());
                        post.put(app.getSession().getFormToken(), "1");
                        post.put("tmpl", "component");
                        post.put("Itemid", String.valueOf(active_menu_item_id));
                        web_browser.vtv_postUrl(VTVConfig.rootUrl, post);
                        app.getProgressDialog(app.getContext().getString(R.string.str_login_processing)).show();
                        web_browser.addJavascriptInterface(new ajax_login(), "HtmlViewer");
                    }
                }
            });
        }
    }
    private boolean validate() {

        txt_username = (EditText)view.findViewById(R.id.txt_username);

        txt_password = (EditText)view.findViewById(R.id.txt_password);
        if(txt_username.getText().toString().isEmpty()){
            JUtilities.alert(MessageType.ERROR,R.string.username_requirement);
            txt_username.setSelected(true);
            return  false;
        }else if(txt_password.getText().toString().isEmpty()){
            JUtilities.alert(MessageType.ERROR,R.string.password_requirement);
            return  false;
        }
        return true;
    }

    private class ajax_login {
        public ajax_login() {
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
                final m_default_logout logout_view=new m_default_logout(app.getContext());
                final LinearLayout wrapper_content=(LinearLayout)app.getMain_relative_layout().findViewById(R.id.mod_easysocial_login_wrapper_content);
                app.getCurrentActivity().runOnUiThread(new Runnable()
                {
                    public void run()
                    {
                        wrapper_content.removeAllViews();
                        wrapper_content.addView(logout_view);

                    }

                });
                JFactory.getUser().setActiveUser(user);
                mAlertDialog.dismiss();
                app.getCurrentActivity().runOnUiThread(new Runnable() {
                    @Override
                    public void run() {

                        JAlert.show(MessageType.INFO,R.string.str_login_successful,app,"refresh_page");
                    }
                });


            }else{
                JUtilities.alert(MessageType.ERROR,R.string.str_login_false);
            }



        }

    }

}