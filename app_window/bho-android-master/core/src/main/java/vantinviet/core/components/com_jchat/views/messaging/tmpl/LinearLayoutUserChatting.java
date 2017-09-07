package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.content.Context;
import android.os.Build;
import android.support.annotation.RequiresApi;
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
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.github.nkzawa.emitter.Emitter;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import it.neokree.materialtabs.MaterialTabHost;
import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.administrator.components.com_jchat.models.JChatModelMessages;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.session.JSession;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


/**
 * TODO: document your custom view class.
 */
public class LinearLayoutUserChatting extends LinearLayout {


    private static JApplication app = JFactory.getApplication();
    Button btn_back;
    LinearLayout bottom_navigation;
    public JUser user;
    public JSession session;
    static RecyclerView recyclerViewMessenger;
    static RecyclerView.Adapter recyclerViewAdapterMessenger;
    RecyclerView.LayoutManager recylerViewLayoutManagerMessenger;
    static List<Messenger> listMessenger =new ArrayList<Messenger>();
    SwipeRefreshLayout swipeRefreshLayout;
    JUser activeUser = JFactory.getUser();
    public MaterialTabHost tabHost;
    EditText mInputMessageView;
    Button btn_send;
    View view;
    public String token="";
    public JUser clientUser;
    public String roomName="";

    public LinearLayoutUserChatting(Context context) {
        super(context);
        view = inflate(getContext(), R.layout.components_com_jchat_views_messeging_tmpl_c_default_user_chatting, this);
    }

    private Emitter.Listener onNewMessageUserChatting = new Emitter.Listener() {
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
                    if(currentRoomName.equals(roomName)) {
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
    private void addItem(Messenger item) {
        listMessenger.add(item);
        recyclerViewAdapterMessenger.notifyDataSetChanged();
    }
    private Emitter.Listener userJoinsRoom = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    app.mSocket.emit("newMessage", data);
                }
            });

        }


    };

    private Emitter.Listener renderListMessengerUserChatting = new Emitter.Listener() {
        @Override
        public void call(final Object... ResponseData) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    listMessenger.clear();
                    JSONArray data = (JSONArray) ResponseData[0];
                    for (int i=0; i<data.length(); i++) {
                        String str_messenger="";
                        try {
                            str_messenger=data.getString(i);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }Timber.d("str_messenger: "+str_messenger);
                        Messenger item = JUtilities.getGsonParser().fromJson(str_messenger, Messenger.class);
                        Timber.d("item messenger:"+item.toString());
                        listMessenger.add(item);
                    }
                    Timber.d("listMessenger:"+listMessenger.toString());
                    recyclerViewAdapterMessenger.notifyDataSetChanged();
                }
            });

        }
    };

    public void setClientUser(JUser clientUser) {
        this.clientUser = clientUser;
    }


    public void init() {
        btn_back = (Button)view.findViewById(R.id.btn_back);
        btn_back.setOnClickListener(new OnClickListener() {
            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onClick(View v) {
                bottom_navigation = (LinearLayout)app.getCurrentActivity().findViewById(R.id.bottom_navigation);
                bottom_navigation.removeAllViews();
                FooterShowMessaging footerShowMessaging=new FooterShowMessaging(app.getContext());
                bottom_navigation.addView(footerShowMessaging);
            }
        });
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


        app.mSocket.on("getListMessenger", renderListMessengerUserChatting);
        app.mSocket.on("newMessage", onNewMessageUserChatting);


        JChatModelMessages jChatModelMessages=JChatModelMessages.getInstance();
        String var1=clientUser.getUserName();
        JUser activeUser=JFactory.getUser();
        String userName=activeUser.getUserName();

        token = JFactory.getSession().getToken();
        String var2=userName!=null&&userName!=""?userName:token;

        if(jChatModelMessages.checkExistsRoom(var1,var2)){
            roomName=jChatModelMessages.getRoomExists(var1,var2);
        }else{
            roomName=jChatModelMessages.createRoomAndAddToListRoomExists(var1,var2);
        }

        JSONObject dataRoom = new JSONObject();
        JSONArray rooms = new JSONArray();
        rooms.put(roomName);
        try {
            dataRoom.put("rooms",rooms);
            dataRoom.put("clientToken",token);
            dataRoom.put("clientUserName",clientUser.getUserName());
            app.mSocket.emit("subscribe", dataRoom);
        } catch (JSONException e) {
            Log.d("SEND subscribe", "ERROR");
            e.printStackTrace();
        }
        Context context = app.getContext();
        recyclerViewMessenger = (RecyclerView) view.findViewById(R.id.recyclerViewMessenger);
        recylerViewLayoutManagerMessenger = new LinearLayoutManager(context);
        recyclerViewMessenger.setLayoutManager(recylerViewLayoutManagerMessenger);

        recyclerViewAdapterMessenger = new RecyclerViewAdapterMessenger(context, listMessenger);

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
                    String userName=user.getUserName();
                    message.put("room", roomName);
                    message.put("client_socket_id", 0);
                    message.put("userName", userName!=null&&userName!=""?userName:token);
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
    }


    static class RecyclerViewAdapterMessenger extends RecyclerView.Adapter<RecyclerViewAdapterMessenger.ViewHolder>{

        List<Messenger> listMessenger;
        Context context;
        View view;
        RecyclerViewAdapterMessenger.ViewHolder viewHolder;
        TextView textView;

        public RecyclerViewAdapterMessenger(Context context, List<Messenger> listMessenger){

            this.listMessenger = listMessenger;
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
        public RecyclerViewAdapterMessenger.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType){

            view = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_card_messenger,parent,false);

            viewHolder = new RecyclerViewAdapterMessenger.ViewHolder(view);

            return viewHolder;
        }

        @Override
        public void onBindViewHolder(RecyclerViewAdapterMessenger.ViewHolder holder, int position) {

            holder.textViewMessenger.setText(listMessenger.get(position).getMsg());
            holder.textViewName.setText(listMessenger.get(position).getName());
        }

        @Override
        public int getItemCount(){

            return listMessenger.size();
        }


    }




 }

