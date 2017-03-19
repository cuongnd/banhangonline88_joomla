package vantinviet.banhangonline88.ux.adapters;

import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.messenger.Messenger;

import java.util.List;

public class ChattingRecyclerAdapter extends RecyclerView.Adapter<ChattingRecyclerAdapter.MyViewHolder> {

    private List<Messenger> messengerList;

    public class MyViewHolder extends RecyclerView.ViewHolder {
        public TextView full_name, message;

        public MyViewHolder(View view) {
            super(view);
            full_name = (TextView) view.findViewById(R.id.full_name);
            message = (TextView) view.findViewById(R.id.message);
        }
    }


    public ChattingRecyclerAdapter(List<Messenger> messengerList) {
        this.messengerList = messengerList;
    }

    @Override
    public MyViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.fragment_chatting_item, parent, false);

        return new MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(MyViewHolder holder, int position) {
        Messenger messenger = messengerList.get(position);
        holder.full_name.setText(messenger.get_full_name());
        holder.message.setText(messenger.getMessage());
    }

    @Override
    public int getItemCount() {
        return messengerList.size();
    }
}


