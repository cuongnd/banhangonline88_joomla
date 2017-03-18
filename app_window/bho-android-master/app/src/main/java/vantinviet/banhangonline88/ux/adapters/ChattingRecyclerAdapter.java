package vantinviet.banhangonline88.ux.adapters;

import android.content.Context;
import android.content.res.Configuration;
import android.support.v7.widget.RecyclerView;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

import timber.log.Timber;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.messenger.Messenger;
import vantinviet.banhangonline88.interfaces.ChattingRecyclerInterface;
import vantinviet.banhangonline88.views.ResizableImageView;

/**
 * Adapter handling list of product items.
 */
public class ChattingRecyclerAdapter extends RecyclerView.Adapter<ChattingRecyclerAdapter.ViewHolder> {

    private final Context context;
    private final ChattingRecyclerInterface chattingRecyclerInterface;
    private List<Messenger> messengers = new ArrayList<>();
    private LayoutInflater layoutInflater;

    private boolean loadHighRes = false;

    /**
     * Creates an adapter that handles a list of product items.
     *  @param context                   activity context.
     * @param chattingRecyclerInterface listener indicating events that occurred.
     */
    public ChattingRecyclerAdapter(Context context, ChattingRecyclerInterface chattingRecyclerInterface) {
        this.context = context;
        this.chattingRecyclerInterface = chattingRecyclerInterface;

        defineImagesQuality(false);
    }

    private Messenger getItem(int position) {
        return messengers.get(position);
    }

    // Return the size of your dataset (invoked by the layout manager)
    @Override
    public int getItemCount() {
        return messengers.size();
    }

    public void addMessengers(List<Messenger> messengers) {
        messengers.addAll(messengers);
        notifyDataSetChanged();
    }

    // Create new views (invoked by the layout manager)
    @Override
    public ChattingRecyclerAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        if (layoutInflater == null)
            layoutInflater = LayoutInflater.from(parent.getContext());

        View view = layoutInflater.inflate(R.layout.list_item_products, parent, false);
        return new ViewHolder(view, chattingRecyclerInterface);
    }

    // Replace the contents of a view (invoked by the layout manager)
    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        Messenger messenger = getItem(position);
        holder.bindContent(messenger);
        // - replace the contents of the view with that element
        holder.messengerNameTV.setText(holder.messenger.getMessage());

        if (loadHighRes && messenger.getMainImageHighRes() != null) {
            Picasso.with(context).load(messenger.getMainImageHighRes())
                    .fit().centerInside()
                    .placeholder(R.drawable.placeholder_loading)
                    .error(R.drawable.placeholder_error)
                    .into(holder.messengerImage);
        } else {
            Picasso.with(context).load(holder.messenger.getAvatar())
                    .fit().centerInside()
                    .placeholder(R.drawable.placeholder_loading)
                    .error(R.drawable.placeholder_error)
                    .into(holder.messengerImage);
        }

    }

    public void clear() {
        messengers.clear();
    }

    /**
     * Define required image quality. On really big screens is higher quality always requested.
     *
     * @param highResolution true for better quality.
     */
    public void defineImagesQuality(boolean highResolution) {
        DisplayMetrics dm = context.getResources().getDisplayMetrics();
        int densityDpi = dm.densityDpi;
        // On big screens and wifi connection load high res images always
        if (((context.getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) >= Configuration.SCREENLAYOUT_SIZE_XLARGE)
                && MyApplication.getInstance().isWiFiConnection()) {
            loadHighRes = true;
        } else if (highResolution) {
            // Load high resolution images only on better screens.
            loadHighRes = ((context.getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) >= Configuration.SCREENLAYOUT_SIZE_NORMAL)
                    && densityDpi >= DisplayMetrics.DENSITY_XHIGH;
        } else {
            // otherwise
            loadHighRes = false;
        }

        Timber.d("Image high quality selected: %s", loadHighRes);
        notifyDataSetChanged();
    }



    // Provide a reference to the views for each data item
    public static class ViewHolder extends RecyclerView.ViewHolder {
        public ResizableImageView messengerImage;
        public TextView messengerNameTV;
        public TextView messengerPriceTV;
        public TextView productPriceDiscountTV;
        public ImageView btn_image_view_chatting;
        private Messenger messenger;

        public ViewHolder(View v, final ChattingRecyclerInterface ChattingRecyclerInterface) {
            super(v);
            messengerNameTV = (TextView) v.findViewById(R.id.product_item_name);
            messengerPriceTV = (TextView) v.findViewById(R.id.product_item_price);
            productPriceDiscountTV = (TextView) v.findViewById(R.id.product_item_discount);
            messengerImage = (ResizableImageView) v.findViewById(R.id.product_item_image);

            btn_image_view_chatting.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    ChattingRecyclerInterface.onChattingSelected(v, messenger);
                }
            });
        }

        public void bindContent(Messenger messenger) {
            this.messenger = messenger;
        }
    }
}
