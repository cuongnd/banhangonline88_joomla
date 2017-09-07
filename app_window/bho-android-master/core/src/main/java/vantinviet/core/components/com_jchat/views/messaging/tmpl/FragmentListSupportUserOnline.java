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

import com.github.aakira.expandablelayout.ExpandableRelativeLayout;
import com.github.nkzawa.emitter.Emitter;
import com.google.gson.reflect.TypeToken;

import org.json.JSONObject;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuong on 5/19/2017.
 */

public class FragmentListSupportUserOnline extends Fragment {

    static JApplication app= JFactory.getApplication();
    public View view;
    public static AlertDialog mAlertDialog;
    public JUser user;
    RecyclerView recyclerViewGroupSupport;
    RecyclerView.LayoutManager recylerViewLayoutManagerSupportUserOnline;


    ArrayList<GroupSupportUser> listGroupSupport =new ArrayList<GroupSupportUser>();
    private AdapterGroupSupport adapterGroupSupport;
    Integer[] listUserIdSupport={1373,993};
    GroupSupportUser groupSupportUser;
    public static String EXPANDABLERELATIVELAYOUTGROUP = "expandableRelativeLayoutGroup_%d";
    public static String RECYCLERVIEWLISTSUPPORTUSERONLINE = "recyclerviewlistsupportuseronline_%d";
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
    }

    // Inflate the fragment layout we defined above for this fragment
    // Set the associated text for the title
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_list_support_user_online, null);
        groupSupportUser = new GroupSupportUser();
        groupSupportUser.setGroupName("General support");
        listGroupSupport.add(groupSupportUser);
        groupSupportUser = new GroupSupportUser();
        groupSupportUser.setGroupName("Technical support");
        listGroupSupport.add(groupSupportUser);
        groupSupportUser = new GroupSupportUser();
        groupSupportUser.setGroupName("Business support");
        listGroupSupport.add(groupSupportUser);
        groupSupportUser = new GroupSupportUser();
        groupSupportUser.setGroupName("Support collaborators");
        listGroupSupport.add(groupSupportUser);
        groupSupportUser = new GroupSupportUser();
        groupSupportUser.setGroupName("Support vendor");
        listGroupSupport.add(groupSupportUser);

        // Use the Builder class for convenient dialog construction

        Context context = app.getContext();
        //now get list user online
        JSONObject data = new JSONObject();
        app.mSocket.emit("getListUserOnline", data);
        app.mSocket.on("update-list-user-online", updateListSupportUserOnline);
        app.mSocket.on("getListUserOnline", updateListSupportUserOnline);
        recyclerViewGroupSupport = (RecyclerView) view.findViewById(R.id.recyclerViewGroupSupportUserOnline);
        recylerViewLayoutManagerSupportUserOnline = new LinearLayoutManager(context);
        recyclerViewGroupSupport.setLayoutManager(recylerViewLayoutManagerSupportUserOnline);
        adapterGroupSupport = new AdapterGroupSupport(context, listGroupSupport);
        recyclerViewGroupSupport.setAdapter(adapterGroupSupport);
/*
        recyclerViewGroupSupport.addOnItemTouchListener(
                new RecyclerItemClickListener(context, recyclerViewGroupSupport ,new RecyclerItemClickListener.OnItemClickListener() {


                    @Override public void onItemClick(View view, int position) {
                        ExpandableRelativeLayout expandableRelativeLayoutGroup;
                        for(int i=0; i<listGroupSupport.size(); i++){
                            expandableRelativeLayoutGroup = (ExpandableRelativeLayout)recyclerViewGroupSupport.findViewWithTag(String.format(EXPANDABLERELATIVELAYOUTGROUP,i));
                            if(position==i){
                                expandableRelativeLayoutGroup.toggle();
                            }
                            if(expandableRelativeLayoutGroup.isExpanded())
                            {
                                expandableRelativeLayoutGroup.setExpanded(false);
                            }

                        }
                    }

                    @Override public void onLongItemClick(View view, int position) {
                        // do whatever
                    }
                })
        );
*/


        return view;


    }



    private Emitter.Listener updateListSupportUserOnline;

    {
        updateListSupportUserOnline = new Emitter.Listener() {
            @Override
            public void call(final Object... ResponseData) {
                app.getCurrentActivity().runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        String mySocketId= app.getSocketId();
                        Type listType = new TypeToken<Map<String, JUser>>() {}.getType();
                        Map<String, JUser> mapListSupportUserOnline = JUtilities.getGsonParser().fromJson(ResponseData[0].toString(), listType);
                        Timber.d(mapListSupportUserOnline.toString());
                        for (GroupSupportUser groupSupportUser: listGroupSupport) {
                            groupSupportUser.getListUserSupport().clear();
                            for (Map.Entry<String, JUser> entry : mapListSupportUserOnline.entrySet())
                            {
                                JUser user=entry.getValue();
                                String userSocketId=user.getSocketId();
                                if(!userSocketId.equals(mySocketId) && Arrays.asList(listUserIdSupport).contains(user.getId())){
                                    groupSupportUser.getListUserSupport().add(user);
                                }
                            }
                            groupSupportUser.getListrecyclerViewAdapterListUserOnline().notifyDataSetChanged();

                        }
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



    static class RecyclerItemClickListener implements RecyclerView.OnItemTouchListener {
        private OnItemClickListener mListener;

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
        View view;
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

            view = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_card_user,parent,false);

            viewHolder1 = new RecyclerViewAdapterListUserOnline.ViewHolder(view);

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

    }
    static class AdapterGroupSupport extends RecyclerView.Adapter<AdapterGroupSupport.ViewHolder>{


        static ArrayList<GroupSupportUser> listGroupSupport;
        Context context;
        View view;
        AdapterGroupSupport.ViewHolder viewHolder;



        public AdapterGroupSupport(Context context, ArrayList<GroupSupportUser> listGroupSupport){

            this.listGroupSupport = listGroupSupport;
            this.context = context;
        }

        public static class ViewHolder extends RecyclerView.ViewHolder{

            public LinearLayoutManager recylerViewLayoutManagerListSupportUserOnline;
            public RecyclerView recyclerViewListSupportUserOnline;
            public TextView textViewGroupName;
            public TextView textViewLastMessenger;
            public ExpandableRelativeLayout expandableRelativeLayoutGroup;


            public ViewHolder(View view){

                super(view);

                recyclerViewListSupportUserOnline = (RecyclerView)view.findViewById(R.id.recyclerViewListSupportUserOnline);

                Context context=app.getContext();
                recylerViewLayoutManagerListSupportUserOnline = new LinearLayoutManager(context);
                recyclerViewListSupportUserOnline.setLayoutManager(recylerViewLayoutManagerListSupportUserOnline);


                textViewGroupName = (TextView)view.findViewById(R.id.textViewGroupName);
                textViewLastMessenger = (TextView)view.findViewById(R.id.textViewName);
                expandableRelativeLayoutGroup = (ExpandableRelativeLayout)view.findViewById(R.id.expandableRelativeLayoutGroup);
            }

        }

        @Override
        public AdapterGroupSupport.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType){

            view = LayoutInflater.from(context).inflate(R.layout.components_com_jchat_views_messeging_tmpl_c_default_list_support_user_online_group_template,parent,false);

            viewHolder = new AdapterGroupSupport.ViewHolder(view);

            return viewHolder;
        }

        @Override
        public void onBindViewHolder(AdapterGroupSupport.ViewHolder holder, int position) {
            String groupName = listGroupSupport.get(position).getGroupName();
            holder.textViewGroupName.setText(groupName);
            holder.recyclerViewListSupportUserOnline.setAdapter(listGroupSupport.get(position).getListrecyclerViewAdapterListUserOnline());
            holder.expandableRelativeLayoutGroup.setTag(String.format(EXPANDABLERELATIVELAYOUTGROUP,position));
            holder.recyclerViewListSupportUserOnline.setTag(String.format(RECYCLERVIEWLISTSUPPORTUSERONLINE,position));
        }

        @Override
        public int getItemCount(){

            return listGroupSupport.size();
        }

    }

    private class GroupSupportUser {
        String groupName;
        ArrayList<JUser> listUserSupport=new ArrayList<JUser>();
        private RecyclerViewAdapterListUserOnline listrecyclerViewAdapterListUserOnline=null;

        public GroupSupportUser() {
        }

        public void setGroupName(String groupName) {
            this.groupName = groupName;
        }

        public String getGroupName() {
            return groupName;
        }

        public ArrayList<JUser> getListUserSupport() {
            return listUserSupport;
        }


        public RecyclerViewAdapterListUserOnline getListrecyclerViewAdapterListUserOnline() {
            if(listrecyclerViewAdapterListUserOnline==null)
            {
                listrecyclerViewAdapterListUserOnline=new RecyclerViewAdapterListUserOnline(app.getContext(),this.getListUserSupport());
            }
            return listrecyclerViewAdapterListUserOnline;
        }
    }
}