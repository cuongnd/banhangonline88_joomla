package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.app.AlertDialog;
import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.view.ViewPager;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.TextUtils;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.github.nkzawa.emitter.Emitter;
import com.google.gson.JsonElement;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.administrator.components.com_jchat.models.JChatModelMessages;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.session.JSession;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuong on 5/19/2017.
 */

public class FragmentChatting extends Fragment {

    static JApplication app= JFactory.getApplication();
    public View view;
    public JUser user;
    ArrayList<JUser> listUserOnline =new ArrayList<JUser>();
    public JSession session;
    private static final String MAIN_ROOM = "MainRoom";
    private JsonElement response;
    static RecyclerView recyclerViewMessenger;
    static RecyclerView.Adapter recyclerViewAdapterMessenger;
    RecyclerView.LayoutManager recylerViewLayoutManagerMessenger;
    static List<Messenger> listMessenger =new ArrayList<Messenger>();
    SwipeRefreshLayout swipeRefreshLayout;
    JUser activeUser = JFactory.getUser();
    private ViewPager pager;
    EditText mInputMessageView;
    Button btn_send;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        view=inflater.inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_tab_content_fragment_chatting, null);
        swipeRefreshLayout = (SwipeRefreshLayout)view.findViewById(R.id.swiperefreshlayoutmessenger);
        swipeRefreshLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                Toast.makeText(app.getContext(), "Refresh", Toast.LENGTH_SHORT).show();
                app.getCurrentActivity().runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        if (swipeRefreshLayout.isRefreshing()) {
                            swipeRefreshLayout.setRefreshing(false);
                        }
                    }
                });
            }
        });
        app.mSocket.on("getListMessenger", renderListMessenger);
        app.mSocket.on("newMessage", onNewMessageMainRoom);
        app.mSocket.on("subscriptionConfirmed", subscriptionConfirmedMainRoom);
        app.mSocket.on("userJoinsRoom", userJoinsRoomMainRoom);
        app.mSocket.on("sendDocumentPage", sendDocumentPage);
        Context context = app.getContext();
        recyclerViewMessenger = (RecyclerView) view.findViewById(R.id.recyclerViewMessenger);
        recylerViewLayoutManagerMessenger = new LinearLayoutManager(context);
        recyclerViewMessenger.setLayoutManager(recylerViewLayoutManagerMessenger);

        recyclerViewAdapterMessenger = new RecyclerViewAdapterMessengerMainRoom(context, listMessenger);

        recyclerViewMessenger.setAdapter(recyclerViewAdapterMessenger);


        btn_send = (Button) view.findViewById(R.id.btn_send);


        mInputMessageView = (EditText) view.findViewById(R.id.mInputMessageView);
        btn_send.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                // TODO Auto-generated method stub
                String txt_message = mInputMessageView.getText().toString().trim();
                if (TextUtils.isEmpty(txt_message)) {
                    return;
                }

                mInputMessageView.setText("");
                JSONObject message = new JSONObject();
                try {
                    JUser user=JFactory.getUser();
                    message.put("room", MAIN_ROOM);
                    message.put("client_socket_id", 0);
                    message.put("userName", user.getUserName());
                    message.put("name", user.getName());
                    message.put("msg", txt_message);
                    // mSocket.send(obj);
                    app.mSocket.emit("newMessage", message);


                    Log.d("SEND newMessage", message.toString());


                } catch (JSONException e) {
                    Log.d("SEND newMessage", "ERROR");
                    e.printStackTrace();
                }
            }
        });
        return view;
    }



    private void addItem(Messenger item) {
        listMessenger.add(item);
        recyclerViewAdapterMessenger.notifyDataSetChanged();
    }

    private Emitter.Listener sendDocumentPage = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    String name="";
                    String currentRoomName = "";
                    String username="";
                    String message="";
                    String msg_key="";
                    try {
                        name = data.getString("name");
                        currentRoomName = data.getString("room");
                        username = data.getString("userName");
                        message = data.getString("msg");
                        msg_key = data.getString("msg_key");
                        Timber.d(message);
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                    if(currentRoomName.equals(MAIN_ROOM)) {
                        // add the message to view
                        Messenger item = new Messenger();
                        item.setMsg(message);
                        item.setUsername(username);
                        item.setName(name);
                        item.setMsgKey(msg_key);
                        addItem(item);
                        recyclerViewMessenger.scrollToPosition(listMessenger.size() - 1);
                    }
                }
            });

        }


    };
    private Emitter.Listener onNewMessageMainRoom = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    String name;
                    String currentRoomName;
                    String username;
                    String message;
                    String msg_key;
                    try {
                        name = data.getString("name");
                        currentRoomName = data.getString("room");
                        username = data.getString("userName");
                        message = data.getString("msg");
                        msg_key = data.getString("msg_key");
                        Timber.d(message);
                    } catch (JSONException e) {
                        return;
                    }
                    if(currentRoomName.equals(MAIN_ROOM)) {
                        // add the message to view
                        Messenger item = new Messenger();
                        item.setMsg(message);
                        item.setUsername(username);
                        item.setName(name);
                        item.setMsgKey(msg_key);
                        addItem(item);
                        recyclerViewMessenger.scrollToPosition(listMessenger.size() - 1);
                    }
                }
            });

        }
    };
    private Emitter.Listener userJoinsRoomMainRoom = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    //app.mSocket.emit("newMessage", data);
                }
            });

        }


    };

    private Emitter.Listener renderListMessenger = new Emitter.Listener() {
        @Override
        public void call(final Object... ResponseData) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    listMessenger.clear();

                    JSONArray data=new JSONArray();
                    try {
                        data = (JSONArray) ResponseData[0];
                    }catch (Exception e){
                        e.printStackTrace();
                    }

                    for (int i=0; i<data.length(); i++) {
                        String str_messenger="";
                        try {
                            str_messenger=data.getString(i);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }Timber.d("str_messenger: "+str_messenger);
                        Messenger item = JUtilities.getGsonParser().fromJson(str_messenger, Messenger.class);
                        Timber.d("item messenger:"+item.toString());
                        if(item.getRoom().equals(MAIN_ROOM)) {
                            listMessenger.add(item);
                        }
                    }
                    Timber.d("listMessenger:"+listMessenger.toString());
                    recyclerViewAdapterMessenger.notifyDataSetChanged();
                }
            });

        }
    };
    private Emitter.Listener subscriptionConfirmedMainRoom = new Emitter.Listener() {
        @Override
        public void call(final Object... ResponseData) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                }
            });

        }



    };
    static class RecyclerViewAdapterMessengerMainRoom extends RecyclerView.Adapter<RecyclerViewAdapterMessengerMainRoom.ViewHolder>{

        List<Messenger> SubjectValues;
        Context context;
        View view1;
        RecyclerViewAdapterMessengerMainRoom.ViewHolder viewHolder;
        TextView textView;

        public RecyclerViewAdapterMessengerMainRoom(Context context, List<Messenger> SubjectValues1){

            SubjectValues = SubjectValues1;
            this.context = context;
        }

        public static class ViewHolder extends RecyclerView.ViewHolder{

            public TextView textViewMessenger;
            public TextView textViewName;

            public ViewHolder(View v){

                super(v);

                textViewMessenger = (TextView)v.findViewById(R.id.subject_textview);
                textViewName = (TextView)v.findViewById(R.id.textViewName);
            }
        }

        @Override
        public RecyclerViewAdapterMessengerMainRoom.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType){

            view1 = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_card_messenger,parent,false);

            viewHolder = new RecyclerViewAdapterMessengerMainRoom.ViewHolder(view1);

            return viewHolder;
        }

        @Override
        public void onBindViewHolder(RecyclerViewAdapterMessengerMainRoom.ViewHolder holder, int position) {

            holder.textViewMessenger.setText(SubjectValues.get(position).getMsg());
            holder.textViewName.setText(SubjectValues.get(position).getName());
        }

        @Override
        public int getItemCount(){

            return SubjectValues.size();
        }


    }



}