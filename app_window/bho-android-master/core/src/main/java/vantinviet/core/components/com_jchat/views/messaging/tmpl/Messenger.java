package vantinviet.core.components.com_jchat.views.messaging.tmpl;

import com.google.gson.annotations.SerializedName;

/**
 * Created by cuong on 9/7/2017.
 */

public class Messenger {
    private int id=0;
    private String name;
    private String username;
    @SerializedName("msg_key")
    private String msgKey;
    private String msg="";
    private Object room;

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

    public Object getRoom() {
        return room;
    }
}
