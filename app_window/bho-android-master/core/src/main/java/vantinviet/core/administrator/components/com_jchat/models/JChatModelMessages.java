package vantinviet.core.administrator.components.com_jchat.models;

import java.util.ArrayList;

import vantinviet.core.components.com_jchat.views.messaging.tmpl.Room;
import vantinviet.core.libraries.joomla.string.JString;

/**
 * Created by cuong on 8/13/2017.
 */

public class JChatModelMessages {
    private static JChatModelMessages instance;
    public static JChatModelMessages getInstance() {
        if(instance==null)
        {
            instance=new JChatModelMessages();
        }
        return instance;
    }
    public ArrayList<Room> listRoomExits=new ArrayList<Room>();

    public String createRoomAndAddToListRoomExists(String... listUsername) {
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

    public String getRoomExists(String... listUsername) {
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
    public ArrayList<Room> getListRoomExits() {
        return listRoomExits;
    }
    public boolean checkExistsRoom(String... listUsername) {
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
}
