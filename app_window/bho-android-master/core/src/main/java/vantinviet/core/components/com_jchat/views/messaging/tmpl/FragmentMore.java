package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.GestureDetector;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.TextView;

import com.github.nkzawa.emitter.Emitter;
import com.google.gson.reflect.TypeToken;

import org.json.JSONObject;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.session.JSession;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuong on 5/19/2017.
 */

public class FragmentMore extends Fragment {

    static JApplication app= JFactory.getApplication();
    public View view;
    public AlertDialog mAlertDialog;
    public JUser user;
    RecyclerView recyclerViewListUserOnline;
    RecyclerView.Adapter recyclerViewAdapterUserOnline;
    RecyclerView.LayoutManager recylerViewLayoutManagerUserOnline;
    ArrayList<JUser> listUserOnline =new ArrayList<JUser>();
    public static FragmentMore dialogFragmentListUserOnline;
    private ShowMessaging showMessaging;
    public JSession session;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        view=inflater.inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_tab_content_fragment_more, null);
        return view;
    }
}