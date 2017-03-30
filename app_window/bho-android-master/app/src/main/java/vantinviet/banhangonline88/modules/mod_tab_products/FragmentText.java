package vantinviet.banhangonline88.modules.mod_tab_products;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.util.AttributeSet;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.components.com_users.views.profile.view;

/**
 * Created by neokree on 16/12/14.
 */

/**
 * Created by neokree on 16/12/14.
 */
public class FragmentText extends Fragment{
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        TextView text = new TextView(container.getContext());
        text.setText("Fragment content");
        text.setGravity(Gravity.CENTER);

        return text;
    }
}
