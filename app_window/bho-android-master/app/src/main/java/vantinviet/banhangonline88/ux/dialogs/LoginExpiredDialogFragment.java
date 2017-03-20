package vantinviet.banhangonline88.ux.dialogs;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.v4.app.DialogFragment;

import vantinviet.banhangonline88.ux.MainActivity;
import vantinviet.banhangonline88.R;
import timber.log.Timber;

/**
 * Dialog informs user about session timeout.
 */
public class LoginExpiredDialogFragment extends DialogFragment {

    @NonNull
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        Timber.d("%s - OnCreateView", this.getClass().getSimpleName());

        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity(), R.style.myAlertDialogStyle);
        builder.setTitle(R.string.Oops_login_expired);
        builder.setMessage(R.string.Your_session_has_expired_Please_log_in_again);



        return builder.create();
    }
}
