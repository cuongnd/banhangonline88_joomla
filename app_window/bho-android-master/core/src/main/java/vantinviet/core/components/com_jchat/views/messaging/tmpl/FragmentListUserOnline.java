package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.LinearLayout;
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

public class FragmentListUserOnline extends Fragment {

    static JApplication app= JFactory.getApplication();
    public View view;
    public JUser user;
    RecyclerView recyclerViewListUserOnline;
    RecyclerView.Adapter recyclerViewAdapterUserOnline;
    RecyclerView.LayoutManager recylerViewLayoutManagerUserOnline;
    ArrayList<JUser> listUserOnline =new ArrayList<JUser>();
    public JSession session;
    public LinearLayout bottom_navigation;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        view=inflater.inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_tab_content_fragment_list_user_online, null);
        // Create the AlertDialog object and return it
        Context context = app.getContext();
        recyclerViewListUserOnline = (RecyclerView) view.findViewById(R.id.recyclerViewListUserOnline);
        recylerViewLayoutManagerUserOnline = new LinearLayoutManager(context);
        recyclerViewListUserOnline.setLayoutManager(recylerViewLayoutManagerUserOnline);

        recyclerViewAdapterUserOnline = new RecyclerViewAdapterListUserOnline(context, listUserOnline);

        recyclerViewListUserOnline.setAdapter(recyclerViewAdapterUserOnline);

        recyclerViewListUserOnline.addOnItemTouchListener(
                new FragmentListSupportUserOnline.RecyclerItemClickListener(context, recyclerViewListUserOnline ,new FragmentListSupportUserOnline.RecyclerItemClickListener.OnItemClickListener() {


                    @Override public void onItemClick(View view, int position) {
                        JUser clientUser= listUserOnline.get(position);
                        LinearLayoutUserChatting linearLayoutUserChatting = new LinearLayoutUserChatting(app.getContext());
                        linearLayoutUserChatting.setClientUser(clientUser);
                        linearLayoutUserChatting.init();
                        bottom_navigation = (LinearLayout)app.getCurrentActivity().findViewById(R.id.bottom_navigation);
                        bottom_navigation.removeAllViews();
                        bottom_navigation.addView(linearLayoutUserChatting);


                        // do whatever
                    }

                    @Override public void onLongItemClick(View view, int position) {
                        // do whatever
                    }
                })
        );


        JSONObject data = new JSONObject();
        app.mSocket.emit("getListUserOnline", data);
        app.mSocket.on("getListUserOnline", updateListUserOnline);
        app.mSocket.on("update-list-user-online", updateListUserOnline);
        return view;
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



    static class RecyclerViewAdapterListUserOnline extends RecyclerView.Adapter<RecyclerViewAdapterListUserOnline.ViewHolder>{

        ArrayList<JUser> listUserOnline;
        Context context;
        View view;
        RecyclerViewAdapterListUserOnline.ViewHolder viewHolder;
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

            view = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_card_user,parent,false);

            viewHolder = new RecyclerViewAdapterListUserOnline.ViewHolder(view);

            return viewHolder;
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