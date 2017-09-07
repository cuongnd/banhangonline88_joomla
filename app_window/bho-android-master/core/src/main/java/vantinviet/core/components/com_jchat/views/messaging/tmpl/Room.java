package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import java.util.ArrayList;

/**
 * Created by cuong on 9/7/2017.
 */

public class Room {
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
