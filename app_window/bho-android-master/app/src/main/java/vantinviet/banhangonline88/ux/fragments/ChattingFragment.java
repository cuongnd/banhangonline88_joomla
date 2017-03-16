package vantinviet.banhangonline88.ux.fragments;

import android.app.ProgressDialog;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;

import timber.log.Timber;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.SettingsMy;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.entities.User;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.Utils;
import vantinviet.banhangonline88.ux.MainActivity;



/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class ChattingFragment extends Fragment {

    private ProgressDialog pDialog;

    /**
     * Indicates if user data should be loaded from server or from memory.
     */
    private boolean mAlreadyLoaded = false;

    // User information
    private LinearLayout userInfoLayout;
    private TextView tvUserName;
    private TextView tvAddress;
    private TextView tvPhone;
    private TextView tvEmail;

    // Actions
    private Button loginLogoutBtn;
    private Button updateUserBtn;
    private Button myOrdersBtn;

    @Override
    public View onCreateView(LayoutInflater inflater, final ViewGroup container, Bundle savedInstanceState) {
        Timber.d("%s - OnCreateView", this.getClass().getSimpleName());
        MainActivity.setActionBarTitle(getString(R.string.Profile));

        View view = inflater.inflate(R.layout.fragment_chatting, container, false);

        pDialog = Utils.generateProgressDialog(getActivity(), false);


        return view;
    }

    private void syncUserData(@NonNull User user) {
        String url = String.format(EndPoints.USER_SINGLE, SettingsMy.getActualNonNullShop(getActivity()).getId(), user.getId());
        pDialog.show();

        GsonRequest<User> getUser = new GsonRequest<>(Request.Method.GET, url, null, User.class,
                new Response.Listener<User>() {
                    @Override
                    public void onResponse(@NonNull User response) {
                        Timber.d("response: %s", response.toString());
                        SettingsMy.setActiveUser(response);
                        refreshScreen(SettingsMy.getActiveUser());
                        if (pDialog != null) pDialog.cancel();
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                if (pDialog != null) pDialog.cancel();
                MsgUtils.logAndShowErrorMessage(getActivity(), error);
            }
        }, getFragmentManager(), user.getAccessToken());
        getUser.setRetryPolicy(MyApplication.getDefaultRetryPolice());
        getUser.setShouldCache(false);
        MyApplication.getInstance().addToRequestQueue(getUser, CONST.ACCOUNT_REQUESTS_TAG);
    }

    private void refreshScreen(User user) {
        if (user == null) {
            loginLogoutBtn.setText(getString(R.string.Log_in));
            userInfoLayout.setVisibility(View.GONE);
            updateUserBtn.setVisibility(View.GONE);
            myOrdersBtn.setVisibility(View.GONE);
        } else {
            loginLogoutBtn.setText(getString(R.string.Log_out));
            userInfoLayout.setVisibility(View.VISIBLE);
            updateUserBtn.setVisibility(View.VISIBLE);
            myOrdersBtn.setVisibility(View.VISIBLE);

            tvUserName.setText(user.getName());

            String address = user.getStreet();
            address = appendCommaText(address, user.getHouseNumber(), false);
            address = appendCommaText(address, user.getCity(), true);
            address = appendCommaText(address, user.getZip(), true);

            tvAddress.setText(address);
            tvEmail.setText(user.getEmail());
            tvPhone.setText(user.getPhone());
        }
    }

    /**
     * The method combines two strings. As the string separator is used space or comma.
     *
     * @param result   first part of final string.
     * @param append   second part of final string.
     * @param addComma true if comma with space should be used as separator. Otherwise is used space.
     * @return concatenated string.
     */
    private String appendCommaText(String result, String append, boolean addComma) {
        if (result != null && !result.isEmpty()) {
            if (append != null && !append.isEmpty()) {
                if (addComma)
                    result += getString(R.string.format_comma_prefix, append);
                else
                    result += getString(R.string.format_space_prefix, append);
            }
            return result;
        } else {
            return append;
        }
    }

    @Override
    public void onStop() {
        MyApplication.getInstance().getRequestQueue().cancelAll(CONST.ACCOUNT_REQUESTS_TAG);
        super.onStop();
    }
}
