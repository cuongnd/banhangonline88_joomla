package vantinviet.banhangonline88.ux.adapters;

import android.content.Context;
import android.os.Handler;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.List;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.Banner;
import vantinviet.banhangonline88.interfaces.BannersRecyclerInterface;
import vantinviet.banhangonline88.listeners.OnSingleClickListener;
import vantinviet.banhangonline88.views.ResizableImageView;
import timber.log.Timber;

/**
 * Adapter handling list of banner items.
 */
public class BannersRecyclerAdapter extends RecyclerView.Adapter<BannersRecyclerAdapter.ViewHolder> {

    private final BannersRecyclerInterface bannersRecyclerInterface;
    private final Context context;
    private LayoutInflater layoutInflater;
    private List<Banner> banners = new ArrayList<>();
    public TextView textview_title;
    /**
     * Creates an adapter that handles a list of banner items.
     *
     * @param context                  activity context.
     * @param bannersRecyclerInterface listener indicating events that occurred.
     */
    public BannersRecyclerAdapter(Context context, BannersRecyclerInterface bannersRecyclerInterface) {
        this.context = context;
        this.bannersRecyclerInterface = bannersRecyclerInterface;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        if (layoutInflater == null)
            layoutInflater = LayoutInflater.from(parent.getContext());

        View view = layoutInflater.inflate(R.layout.list_item_banners, parent, false);


        return new ViewHolder(view, bannersRecyclerInterface);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {



        Banner banner = getBannerItem(position);
        holder.bindContent(banner);

        Picasso.with(context).load(banner.getImageUrl())
                .placeholder(R.drawable.placeholder_loading)
                .fit().centerInside()
                .into(holder.bannerImage);
    }

    private Banner getBannerItem(int position) {
        return banners.get(position);
    }

    @Override
    public int getItemCount() {
        return banners.size();
    }

    public void addBanners(List<Banner> bannerList) {
        if (bannerList != null && !bannerList.isEmpty()) {
            banners.addAll(bannerList);
            notifyDataSetChanged();
        } else {
            Timber.e("Adding empty banner list.");
        }
    }

    /**
     * Clear all data.
     */
    public void clear() {
        banners.clear();
    }

    /**
     * Check if some banners exist.
     *
     * @return true if content is empty.
     */
    public boolean isEmpty() {
        return banners == null || banners.isEmpty();
    }

    // Provide a reference to the views for each data item
    public static class ViewHolder extends RecyclerView.ViewHolder {
        public ResizableImageView bannerImage;
        public TextView textview_title;
        public View itemView;

        private Banner banner;

        public ViewHolder(View itemView, final BannersRecyclerInterface bannersRecyclerInterface) {
            super(itemView);
            this.itemView=itemView;
            bannerImage = (ResizableImageView) itemView.findViewById(R.id.banner_image);
            itemView.setOnClickListener(new OnSingleClickListener() {
                @Override
                public void onSingleClick(final View view) {
                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            bannersRecyclerInterface.onBannerSelected(banner);
                        }
                    }, 200);
                }
            });


        }

        public void bindContent(Banner banner) {
            this.banner = banner;
            textview_title = (TextView) this.itemView.findViewById(R.id.textview_title);
            textview_title.setText(banner.getName());
        }
    }
}
