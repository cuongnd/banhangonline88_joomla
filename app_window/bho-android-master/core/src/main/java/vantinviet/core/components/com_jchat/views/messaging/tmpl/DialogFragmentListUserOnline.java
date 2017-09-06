package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v4.app.DialogFragment;
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

public class DialogFragmentListUserOnline extends DialogFragment {

    static JApplication app= JFactory.getApplication();
    public View view;
    public AlertDialog mAlertDialog;
    public JUser user;
    RecyclerView recyclerViewListUserOnline;
    RecyclerView.Adapter recyclerViewAdapterUserOnline;
    RecyclerView.LayoutManager recylerViewLayoutManagerUserOnline;
    ArrayList<JUser> listUserOnline =new ArrayList<JUser>();
    public static DialogFragmentListUserOnline dialogFragmentListUserOnline;
    private ShowMessaging showMessaging;
    public JSession session;
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        // Use the Builder class for convenient dialog construction
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        LayoutInflater inflater = getActivity().getLayoutInflater();
        view=inflater.inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_list_user_online, null);
        builder.setTitle(R.string.str_list_user_online);
        builder.setCancelable(false);
        builder
                .setPositiveButton(R.string.strChatWithPerson, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {

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
        Context context = app.getContext();
        recyclerViewListUserOnline = (RecyclerView) view.findViewById(R.id.recyclerViewListUserOnline);
        recyclerViewListUserOnline.addOnItemTouchListener(
                new RecyclerItemClickListener(context, recyclerViewListUserOnline ,new RecyclerItemClickListener.OnItemClickListener() {
                    @Override public void onItemClick(View view, int position) {
                        JUser clientUser=listUserOnline.get(position);
                        mAlertDialog.dismiss();
                        showMessaging.createPrivateRoom(clientUser);
                        //createRoom(clientUser);

                        // do whatever
                    }

                    @Override public void onLongItemClick(View view, int position) {
                        // do whatever
                    }
                })
        );

        recylerViewLayoutManagerUserOnline = new LinearLayoutManager(context);
        recyclerViewListUserOnline.setLayoutManager(recylerViewLayoutManagerUserOnline);

        recyclerViewAdapterUserOnline = new RecyclerViewAdapterListUserOnline(context, listUserOnline);

        recyclerViewListUserOnline.setAdapter(recyclerViewAdapterUserOnline);


        JSONObject data = new JSONObject();
        app.mSocket.emit("getListUserOnline", data);
        app.mSocket.on("getListUserOnline", updateListUserOnline);
        app.mSocket.on("update-list-user-online", updateListUserOnline);
        return mAlertDialog;
    }

    private Emitter.Listener updateListUserOnline;

    {
        updateListUserOnline = new Emitter.Listener() {
            @Override
            public void call(final Object... ResponseData) {
                app.getCurrentActivity().runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        listUserOnline.clear();
                        Type listType = new TypeToken<Map<String, JUser>>() {
                        }.getType();
                        Map<String, JUser> mapListUserOnline = JUtilities.getGsonParser().fromJson(ResponseData[0].toString(), listType);
                        String mySocketId= app.getSocketId();
                        session=JFactory.getSession();
                        JUser activeUser=JFactory.getUser();
                        String userName=activeUser.getUserName();
                        String token=session.getToken();
                        userName=userName!=null&&userName!=""?userName:token;
                        for (Map.Entry<String, JUser> entry : mapListUserOnline.entrySet())
                        {
                            JUser clientUser=entry.getValue();
                            String clientUserName=clientUser.getUserName();
                            Timber.d("clientUserName:"+clientUserName);
                            Timber.d("userName:"+userName);
                            if(!clientUserName.equals(userName))
                            {
                                listUserOnline.add(clientUser);
                            }

                        }
                        recyclerViewAdapterUserOnline.notifyDataSetChanged();
                    }
                });

            }
        };
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

                }
            });
        }
    }


    public void setShowMessaging(ShowMessaging showMessaging) {
        this.showMessaging = showMessaging;
    }


    static class RecyclerItemClickListener implements RecyclerView.OnItemTouchListener {
        private OnItemClickListener mListener;

        public RecyclerItemClickListener(Context context, RecyclerView recyclerViewListSupportUserOnline, DialogFragmentListSupportUserOnline.RecyclerItemClickListener.OnItemClickListener onItemClickListener) {

        }

        public interface OnItemClickListener {
            public void onItemClick(View view, int position);

            public void onLongItemClick(View view, int position);
        }

        GestureDetector mGestureDetector;

        public RecyclerItemClickListener(Context context, final RecyclerView recyclerView, OnItemClickListener listener) {
            mListener = listener;
            mGestureDetector = new GestureDetector(context, new GestureDetector.SimpleOnGestureListener() {
                @Override
                public boolean onSingleTapUp(MotionEvent e) {
                    return true;
                }

                @Override
                public void onLongPress(MotionEvent e) {
                    View child = recyclerView.findChildViewUnder(e.getX(), e.getY());
                    if (child != null && mListener != null) {
                        mListener.onLongItemClick(child, recyclerView.getChildAdapterPosition(child));
                    }
                }
            });
        }

        @Override public boolean onInterceptTouchEvent(RecyclerView view, MotionEvent e) {
            View childView = view.findChildViewUnder(e.getX(), e.getY());
            if (childView != null && mListener != null && mGestureDetector.onTouchEvent(e)) {
                mListener.onItemClick(childView, view.getChildAdapterPosition(childView));
                return true;
            }
            return false;
        }

        @Override public void onTouchEvent(RecyclerView view, MotionEvent motionEvent) { }

        @Override
        public void onRequestDisallowInterceptTouchEvent (boolean disallowIntercept){}
    }
    static class RecyclerViewAdapterListUserOnline extends RecyclerView.Adapter<RecyclerViewAdapterListUserOnline.ViewHolder>{

        ArrayList<JUser> listUserOnline;
        Context context;
        View view1;
        RecyclerViewAdapterListUserOnline.ViewHolder viewHolder1;
        TextView textView;

        public RecyclerViewAdapterListUserOnline(Context context1, ArrayList<JUser> listUserOnline){

            this.listUserOnline = listUserOnline;
            context = context1;
        }

        public static class ViewHolder extends RecyclerView.ViewHolder{

            private final ImageButton imageButtonUser;
            public TextView textViewName;
            public TextView textViewLastMessenger;

            public ViewHolder(View v){

                super(v);

                textViewName = (TextView)v.findViewById(R.id.subject_textview);
                textViewLastMessenger = (TextView)v.findViewById(R.id.textViewName);
                imageButtonUser = (ImageButton)v.findViewById(R.id.imageButtonUser);

            }
        }

        @Override
        public RecyclerViewAdapterListUserOnline.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType){

            view1 = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_card_user,parent,false);

            viewHolder1 = new RecyclerViewAdapterListUserOnline.ViewHolder(view1);

            return viewHolder1;
        }

        @Override
        public void onBindViewHolder(RecyclerViewAdapterListUserOnline.ViewHolder holder, int position) {

            holder.textViewName.setText(listUserOnline.get(position).getName());
            holder.textViewLastMessenger.setText(listUserOnline.get(position).getName());
        }

        @Override
        public int getItemCount(){

            return listUserOnline.size();
        }
        public void updateData(ArrayList<JUser> listUserOnline) {
            this.listUserOnline.clear();
            this.listUserOnline.addAll(listUserOnline);
            notifyDataSetChanged();
        }

    }

}