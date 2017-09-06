package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import android.content.Context;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.TextUtils;
import android.util.AttributeSet;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.github.nkzawa.emitter.Emitter;
import com.google.gson.JsonElement;
import com.google.gson.annotations.SerializedName;

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
import vantinviet.core.libraries.joomla.string.JString;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


/**
 * TODO: document your custom view class.
 */
public class ShowMessaging extends LinearLayout {


    private static final String MAIN_ROOM = "MainRoom";
    private JChatModelMessages messengers;
    private JsonElement response;
    private static JApplication app = JFactory.getApplication();
    static RecyclerView recyclerViewMessenger;
    static RecyclerView.Adapter recyclerViewAdapterMessenger;
    RecyclerView.LayoutManager recylerViewLayoutManagerMessenger;
    static List<Messenger> listMessenger =new ArrayList<Messenger>();
    private static String currentRoom=MAIN_ROOM;
    SwipeRefreshLayout swipeRefreshLayout;
    static EditText editTextName;
    static TextView textViewRoomName;
    public DialogFragmentListUserOnline dialogFragmentListUserOnline;
    public ImageButton imageButtonListUserSuportOnline;
    JUser activeUser = JFactory.getUser();
    private ArrayList<Room> listRoomExits=new ArrayList<Room>();

    public ShowMessaging(Context context, JChatModelMessages messengers) {
        super(context);
        this.messengers = messengers;
        init(null, 0);
    }

    public void createPrivateRoom(JUser clientUser) {
        Timber.d(clientUser.toString());
        String mySocketId = app.getSocketId();
        String clientSocketId = clientUser.getSocketId();
        String clientUserName = clientUser.getUserName();
        String clientName = clientUser.getUserName();
        JSession session=app.getSession();
        String token=session.getToken();

        String currentRoom="";
        if(checkExistsRoom(clientUser.getUserName(),(activeUser.getUserName()!=null &&activeUser.getUserName()!=""?activeUser.getUserName():token ))){
            currentRoom=getRoomExists(clientUser.getUserName(),(activeUser.getUserName()!=null &&activeUser.getUserName()!=""?activeUser.getUserName():token ));
        }else {
            currentRoom= createRoomAndAddToListRoomExists(clientUser.getUserName(),(activeUser.getUserName()!=null &&activeUser.getUserName()!=""?activeUser.getUserName():token ));
        }
        setCurrentRoom(currentRoom);
        textViewRoomName.setText("Room:"+currentRoom!=null?currentRoom:"");
        JSONArray rooms = new JSONArray();
        rooms.put(currentRoom);
        JSONObject data = new JSONObject();
        try {
            data.put("rooms", rooms);
            data.put("client_socket_id", clientSocketId);
            data.put("client_name", clientName);
            data.put("client_user_name", clientUserName);
            // mSocket.send(obj);
            app.mSocket.emit("subscribe", data);


            Log.d("SEND subscribe", data.toString());


        } catch (JSONException e) {
            Log.d("SEND subscribe", "ERROR");
            e.printStackTrace();
        }
        JSONObject room = new JSONObject();
        try {

            room.put("room", currentRoom);
            // mSocket.send(obj);
            app.mSocket.emit("getListMessenger", room);
            Log.d("SEND getListMessenger", room.toString());


        } catch (JSONException e) {
            Log.d("SEND getListMessenger", "ERROR");
            e.printStackTrace();
        }


    }

    private String createRoomAndAddToListRoomExists(String... listUsername) {
        ArrayList<Room> listRoomExists = getListRoomExits();
        Room room = new Room();
        String roomName = JString.generateRandomString(20);
        room.setRoomName(roomName);
        for (int i = 0; i < listUsername.length; i++) {
            String userName = listUsername[i];
            room.getListUserName().add(userName);
        }
        listRoomExists.add(room);
        return roomName;
    }

    private String getRoomExists(String... listUsername) {
        ArrayList<Room> listRoomExists = getListRoomExits();
        for (int i = 0; i < listRoomExists.size(); i++) {
            Room room = listRoomExists.get(i);
            ArrayList<String> listUserName = room.getListUserName();
            for (String userName : listUsername) {
                int indexOfUserName = listUserName.indexOf(userName);
                if (indexOfUserName > -1) {
                    listUserName.remove(indexOfUserName);
                }
            }
            if (listUserName.size() == 0) {
                return room.getRoomName();
            }
        }
        return null;
    }

    private boolean checkExistsRoom(String... listUsername) {
        ArrayList<Room> listRoomExists = getListRoomExits();
        if(listRoomExists!=null && !listRoomExists.isEmpty())
        {
            for (int i = 0; i < listRoomExists.size(); i++) {
                Room room = listRoomExists.get(i);
                ArrayList<String> listUserName=room.getListUserName();
                for (String userName : listUsername) {
                    int indexOfUserName= listUserName.indexOf(userName);
                    if(indexOfUserName>-1){
                        listUserName.remove(indexOfUserName);
                    }
                }
                if(listUserName.size()==0){
                    return true;
                }
            }
        }
        return  false;
    }

    public static void setCurrentRoom(String currentRoom) {
        ShowMessaging.currentRoom = currentRoom;
    }

    public static String getCurrentRoom() {
        return currentRoom;
    }

    private void addItem(Messenger item) {
        listMessenger.add(item);
        recyclerViewAdapterMessenger.notifyDataSetChanged();
    }

    private void init(AttributeSet attrs, int defStyle) {
        View layout_product = inflate(getContext(), R.layout.components_com_jchat_views_messeging_tmpl_c_default, this);
        ImageButton btn_send = (ImageButton) layout_product.findViewById(R.id.btn_send);
        Button buttonMainRoom = (Button) layout_product.findViewById(R.id.buttonMainRoom);
        ImageButton imageButtonListUserOnline = (ImageButton) layout_product.findViewById(R.id.imageButtonListUserOnline);
        imageButtonListUserSuportOnline = (ImageButton) layout_product.findViewById(R.id.imageButtonListUserSuportOnline);
        textViewRoomName = (TextView) layout_product.findViewById(R.id.textViewRoomName);
        editTextName = (EditText) layout_product.findViewById(R.id.editTextName);
        editTextName.setText(activeUser.getName());
        if(activeUser.getId()>0){
            editTextName.setEnabled(false);
        }
        swipeRefreshLayout = (SwipeRefreshLayout)findViewById(R.id.swiperefreshlayoutmessenger);
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


        //app.mSocket.on("getListMessenger", renderListMessenger);
        app.mSocket.on("newMessage", onNewMessage);


        app.mSocket.on("subscriptionConfirmed", subscriptionConfirmed);
        app.mSocket.on("userJoinsRoom", userJoinsRoom);
        JSONObject room = new JSONObject();
        try {

            room.put("room", getCurrentRoom());
            // mSocket.send(obj);
            app.mSocket.emit("getListMessenger", room);
            Log.d("SEND getListMessenger", room.toString());


        } catch (JSONException e) {
            Log.d("SEND getListMessenger", "ERROR");
            e.printStackTrace();
        }
        Context context = app.getContext();
        recyclerViewMessenger = (RecyclerView) layout_product.findViewById(R.id.recyclerViewMessenger);
        recylerViewLayoutManagerMessenger = new LinearLayoutManager(context);
        recyclerViewMessenger.setLayoutManager(recylerViewLayoutManagerMessenger);

        recyclerViewAdapterMessenger = new RecyclerViewAdapterMessenger(context, listMessenger);

        recyclerViewMessenger.setAdapter(recyclerViewAdapterMessenger);


        dialogFragmentListUserOnline = new DialogFragmentListUserOnline();
        dialogFragmentListUserOnline.setShowMessaging(this);


        final EditText mInputMessageView = (EditText) layout_product.findViewById(R.id.mInputMessageView);
        btn_send.setOnClickListener(new OnClickListener() {
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
                    message.put("room", getCurrentRoom());
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
        buttonMainRoom.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                setCurrentRoom(ShowMessaging.MAIN_ROOM);
                JSONObject room = new JSONObject();
                try {
                    textViewRoomName.setText(getCurrentRoom());
                    room.put("room", getCurrentRoom());
                    // mSocket.send(obj);
                    app.mSocket.emit("getListMessenger", room);
                    Log.d("SEND getListMessenger", room.toString());


                } catch (JSONException e) {
                    Log.d("SEND getListMessenger", "ERROR");
                    e.printStackTrace();
                }
            }
        });
        imageButtonListUserOnline.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {

                dialogFragmentListUserOnline.show(app.getSupportFragmentManager(), "DialogFragmentListUserOnline");
            }
        });
        imageButtonListUserSuportOnline.setOnClickListener(new OnClickListener() {
            public void onClick(View v) {
                DialogFragmentListSupportUserOnline dialogFragmentListSupportUserOnline = new DialogFragmentListSupportUserOnline();
                dialogFragmentListSupportUserOnline.show(app.getSupportFragmentManager(), "dialogFragmentListSupportUserOnline");
            }
        });
    }
    private void refresh(){

        final int pos = recyclerViewAdapterMessenger.getItemCount();
        swipeRefreshLayout.setRefreshing(true);

        //refresh long-time task in background thread
        new Thread(new Runnable() {
            @Override
            public void run() {
                try {
                    //dummy delay for 2 second
                    Thread.sleep(2000);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }

                //update ui on UI thread
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {

                        swipeRefreshLayout.setRefreshing(false);
                    }
                });

            }

            private void runOnUiThread(Runnable runnable) {

            }


        }).start();
    }
    private Emitter.Listener onNewMessage = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    String name;
                    String username;
                    String message;
                    String msg_key;
                    try {
                        name = data.getString("name");
                        username = data.getString("userName");
                        message = data.getString("msg");
                        msg_key = data.getString("msg_key");
                        Timber.d(message);
                    } catch (JSONException e) {
                        return;
                    }

                    // add the message to view
                    Messenger item = new Messenger();
                    item.setMsg(message);
                    item.setUsername(username);
                    item.setName(name);
                    item.setMsgKey(msg_key);
                    addItem(item);
                    recyclerViewMessenger.scrollToPosition(listMessenger.size() - 1);
                }
            });

        }


    };
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

    private Emitter.Listener renderListMessenger = new Emitter.Listener() {
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

        private void addListMessenger(String msg_key, String username, String message) {
            // generating text for editText Views

        }


    };
    private Emitter.Listener subscriptionConfirmed = new Emitter.Listener() {
        @Override
        public void call(final Object... ResponseData) {
            app.getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {






                }
            });

        }

        private void addListMessenger(String msg_key, String username, String message) {
            // generating text for editText Views

        }


    };

    public ArrayList<Room> getListRoomExits() {
        return listRoomExits;
    }


    static class RecyclerViewAdapterMessenger extends RecyclerView.Adapter<RecyclerViewAdapterMessenger.ViewHolder>{

        List<Messenger> SubjectValues;
        Context context;
        View view1;
        ViewHolder viewHolder1;
        TextView textView;

        public RecyclerViewAdapterMessenger(Context context1, List<Messenger> SubjectValues1){

            SubjectValues = SubjectValues1;
            context = context1;
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

            view1 = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_card_messenger,parent,false);

            viewHolder1 = new ViewHolder(view1);

            return viewHolder1;
        }

        @Override
        public void onBindViewHolder(ViewHolder holder, int position) {

            holder.textViewMessenger.setText(SubjectValues.get(position).getMsg());
            holder.textViewName.setText(SubjectValues.get(position).getName());
        }

        @Override
        public int getItemCount(){

            return SubjectValues.size();
        }


    }


    private class Messenger {

        private int id=0;
        private String name;
        private String username;
        @SerializedName("msg_key")
        private String msgKey;
        private String msg="";

        public String getMsg() {
            return msg;
        }

        public void setMsg(String msg) {
            this.msg = msg;
        }

        public void setUsername(String username) {
            this.username = username;
        }

        public void setMsgKey(String msgKey) {
            this.msgKey = msgKey;
        }
        @Override
        public String toString() {
            return "Messenger{" +
                    "id=" + id +
                    ", name='" + name + '\'' +
                    ", userName='" + username + '\'' +
                    ", msgKey='" + msgKey + '\'' +
                    ", msg='" + msg + '\'' +
                    '}';
        }

        public String getName() {
            return name!=null?name:"";
        }

        public void setName(String name) {
            this.name = name;
        }
    }

    private class Room {
        String roomName="";
        ArrayList<String> listUserName=new ArrayList<String>();

        public void setRoomName(String roomName) {
            this.roomName = roomName;
        }

        public ArrayList<String> getListUserName() {
            return listUserName;
        }

        public String getRoomName() {
            return roomName;
        }
    }
}

