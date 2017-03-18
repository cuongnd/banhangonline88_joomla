package vantinviet.banhangonline88.entities.messenger;

import com.google.gson.annotations.SerializedName;
import com.google.gson.internal.Streams;

import java.util.ArrayList;
import java.util.List;

import vantinviet.banhangonline88.entities.Metadata;

public class MessengerListResponse {

    private String my_username;
    private String my_avatar;
    private String my_email;
    private String users_inmyroom;
    private Metadata metadata;
    private Userstatus userstatus;
    private Paramslist paramslist;
    private Initialize initialize;
    private Webrtc_signaling_channel webrtc_signaling_channel;

    @SerializedName("records")
    private ArrayList<Buddylist> buddylist=new ArrayList<Buddylist>();
    private ArrayList<Typing_status> typing_status=new ArrayList<Typing_status>();
    private ArrayList<Buddylist> buddylist_ids=new ArrayList<Buddylist>();
    private ArrayList<Chatrooms> chatrooms=new ArrayList<Chatrooms>();
    private ArrayList<Messenger> wallmessages=new ArrayList<Messenger>();

    public ArrayList<Messenger> getMessengers() {
        return wallmessages;
    }

    public void setMessengers(ArrayList<Messenger> Messengers) {
        this.wallmessages = Messengers;
    }


    @Override
    public String toString() {
        return "MessengerListResponse{" +
                "paramslist=" + paramslist +
                ",my_avatar=" + my_avatar +
                ",buddylist=" + buddylist +
                ",buddylist_ids=" + buddylist_ids +
                ",my_username=" + my_username +
                ",my_email=" + my_email +
                ",chatrooms=" + chatrooms +
                ",users_inmyroom=" + users_inmyroom +
                ",userstatus=" + userstatus +
                ",initialize=" + initialize +
                ",typing_status=" + typing_status +
                ",webrtc_signaling_channel=" + webrtc_signaling_channel +
                ",wallmessages=" + wallmessages +
                '}';
    }
}
