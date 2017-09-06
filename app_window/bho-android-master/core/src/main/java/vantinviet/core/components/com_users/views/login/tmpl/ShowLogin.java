package vantinviet.core.components.com_users.views.login.tmpl;

import android.app.Fragment;
import android.app.FragmentTransaction;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.AttributeSet;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.LinearLayout;

import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.FacebookSdk;
import com.facebook.GraphRequest;
import com.facebook.GraphResponse;
import com.facebook.login.LoginResult;
import com.facebook.login.widget.LoginButton;
import com.google.gson.JsonElement;

import org.json.JSONException;
import org.json.JSONObject;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.administrator.components.com_jchat.models.JChatModelMessages;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


/**
 * TODO: document your custom view class.
 */
public class ShowLogin extends LinearLayout {


    private JChatModelMessages messengers;
    private JsonElement response;
    private static JApplication app= JFactory.getApplication();
    private LoginButton btn_login_facebook;


    public ShowLogin(Context context, JChatModelMessages messengers) {
        super(context);
        this.messengers =messengers;
        init(null, 0);
    }

    private login_facebook mFragment;

    private void init(AttributeSet attrs, int defStyle) {
        View layout_login=inflate(getContext(), R.layout.components_com_users_views_login_tmpl_c_default, this);

        FrameLayout frame = new FrameLayout(app.getContext());
        mFragment = new login_facebook();
        FragmentTransaction ft = app.getFragmentManager().beginTransaction();
        ft.add(R.id.frame_containerone, mFragment).commit();




    }

    protected void start() {
        // Perform initialization (bindings, timers, etc) here
    }
    protected void stop() {
        Timber.d("hello 22222222222222222222");
        // Unbind, destroy timers, yadda yadda
    }
    public static class login_facebook extends Fragment {
        private CallbackManager callbackManager;
        View view;
        @Override
        public void onCreate(Bundle savedInstanceState) {
            super.onCreate(savedInstanceState);

        }
        @Override
        public View onCreateView(LayoutInflater inflater,
                                 ViewGroup container,
                                 Bundle savedInstanceState) {
            FacebookSdk.sdkInitialize(app.getContext());
            view = inflater.inflate(R.layout.fragment_components_com_users_views_login_tmpl_c_default, container, false);
            callbackManager = CallbackManager.Factory.create();
            LoginButton loginButton = (LoginButton) view.findViewById(R.id.btn_login_facebook);
            loginButton.setReadPermissions("email");
            loginButton.setReadPermissions("user_friends");
            loginButton.setReadPermissions("public_profile");
            loginButton.setReadPermissions("email");
            loginButton.setReadPermissions("user_birthday");
            // If using in a fragment
            loginButton.setFragment(this);
            // If using in a fragment
            loginButton.registerCallback(callbackManager,
                    new FacebookCallback<LoginResult>() {
                        @Override
                        public void onSuccess(LoginResult loginResult) {
                            // App code
                            GraphRequest request = GraphRequest.newMeRequest(
                                    loginResult.getAccessToken(),
                                    new GraphRequest.GraphJSONObjectCallback() {
                                        @Override
                                        public void onCompleted(JSONObject object, GraphResponse response) {
                                            Log.v("LoginActivity", response.toString());

                                            // Application code
                                            try {
                                                String email = object.getString("email");
                                                String birthday = object.getString("birthday"); // 01/31/1980 format
                                                Timber.d("email:"+email);
                                            } catch (JSONException e) {
                                                e.printStackTrace();
                                            }

                                        }
                                    });
                            Timber.d("hello123");
                        }

                        @Override
                        public void onCancel() {
                            // App code
                        }

                        @Override
                        public void onError(FacebookException exception) {
                            // App code
                        }
                    });


            return  view;
        }
        @Override
        public void onActivityResult(int requestCode, int resultCode, Intent data) {
            super.onActivityResult(requestCode, resultCode, data);
            callbackManager.onActivityResult(requestCode, resultCode, data);
        }
    }

}
