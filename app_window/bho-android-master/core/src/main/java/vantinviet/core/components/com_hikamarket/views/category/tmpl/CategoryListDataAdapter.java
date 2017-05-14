package vantinviet.core.components.com_hikamarket.views.category.tmpl;

/**
 * Created by cuongnd on 31/03/2017.
 */

/**
 * Created by pratap.kesaboyina on 24-12-2014.
 */

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Image;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

public class CategoryListDataAdapter extends RecyclerView.Adapter<CategoryListDataAdapter.SingleCategoryRowHolder> {

    private ArrayList<Category> list_category;
    private Context mContext;

    public CategoryListDataAdapter(Context context, ArrayList<Category> list_category) {
        this.list_category = list_category;
        this.mContext = context;
    }

    @Override
    public SingleCategoryRowHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.components_com_hikashop_views_category_tmpl_listing_content_single_category_card, null);
        SingleCategoryRowHolder mh = new SingleCategoryRowHolder(v);
        return mh;
    }

    @Override
    public void onBindViewHolder(SingleCategoryRowHolder holder, int i) {

        Category category = list_category.get(i);
        holder.categoryName.setText(category.getName());
        holder.link=category.getLink();
        Image medium_image=category.getMedium_image();
        String url=medium_image.getUrl();
        Picasso.with(mContext).load(VTVConfig.rootUrl.concat(url)).into((ImageView) holder.categoryImage);
    }

    @Override
    public int getItemCount() {
        return (null != list_category ? list_category.size() : 0);
    }

    public class SingleCategoryRowHolder extends RecyclerView.ViewHolder {
        protected TextView categoryName;
        protected ImageView categoryImage;
        protected String link;
        private JApplication app= JFactory.getApplication();
        public SingleCategoryRowHolder(View view) {
            super(view);

            this.categoryName = (TextView) view.findViewById(R.id.categoryName);
            this.categoryImage = (ImageView) view.findViewById(R.id.categoryImage);
            view.setOnClickListener(new View.OnClickListener() {
                public String getLink() {
                    return link;
                }
                @Override
                public void onClick(View v) {
                    //Toast.makeText(v.getContext(), this.getLink_redirect(), Toast.LENGTH_SHORT).show();
                    app.setRedirect(this.getLink());
                }
            });


        }

    }

}