package vantinviet.core.modules.mod_cart;

import java.util.ArrayList;
import java.util.List;

import vantinviet.core.R;
import vantinviet.core.modules.mod_cart.CartListDataAdapter.RecyclerViewHolder;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

public class CartListDataAdapter extends RecyclerView.Adapter<RecyclerViewHolder> {

    private List<ItemCartProduct> listData = new ArrayList<ItemCartProduct>();

    public CartListDataAdapter(List<ItemCartProduct> listData) {
        this.listData = listData;
    }

    public void updateList(List<ItemCartProduct> data) {
        listData = data;
        notifyDataSetChanged();
    }

    @Override
    public int getItemCount() {
        return listData.size();
    }

    @Override
    public RecyclerViewHolder onCreateViewHolder(ViewGroup viewGroup,
                                                 int position) {
        LayoutInflater inflater = LayoutInflater.from(viewGroup.getContext());
        View itemView = inflater.inflate(R.layout.modules_mod_cart_tmpl_default_content_single_cart_product_card, viewGroup, false);
        return new RecyclerViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(RecyclerViewHolder viewHolder, int position) {
        viewHolder.product_name.setText(listData.get(position).getProduct_name());
    }

    public void addItem(int position, ItemCartProduct data) {
        listData.add(position, data);
        notifyItemInserted(position);
    }

    public void removeItem(int position) {
        listData.remove(position);
        notifyItemRemoved(position);
    }

    /**
     * ViewHolder for item view of list
     * */

    public class RecyclerViewHolder extends RecyclerView.ViewHolder implements
            OnClickListener {

        public TextView product_name;
        public ImageButton btnDelete;

        public RecyclerViewHolder(View itemView) {
            super(itemView);
            product_name = (TextView) itemView.findViewById(R.id.product_name);
            btnDelete = (ImageButton) itemView.findViewById(R.id.btn_delete);

            // set listener for button delete
            btnDelete.setOnClickListener(this);
        }

        // remove item when click button delete
        @Override
        public void onClick(View v) {
            removeItem(getAdapterPosition());
        }
    }

}