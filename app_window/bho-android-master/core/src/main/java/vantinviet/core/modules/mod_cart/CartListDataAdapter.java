package vantinviet.core.modules.mod_cart;

/**
 * Created by cuongnd on 31/03/2017.
 */

/**
 * Created by pratap.kesaboyina on 24-12-2014.
 */

import android.content.Context;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v7.widget.RecyclerView;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Image;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

public class CartListDataAdapter extends RecyclerView.Adapter<CartListDataAdapter.SingleItemProductCartRowHolder> {

    private ArrayList<ModCartView.ItemProductCart> list_item_product_cart;
    private Context mContext;

    public CartListDataAdapter(Context context, ArrayList<ModCartView.ItemProductCart> list_item_product_cart) {
        this.list_item_product_cart = list_item_product_cart;
        this.mContext = context;
    }

    @Override
    public SingleItemProductCartRowHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.modules_mod_cart_tmpl_default_content_single_product_cart_card, null);
        SingleItemProductCartRowHolder mh = new SingleItemProductCartRowHolder(v);
        return mh;
    }

    @Override
    public void onBindViewHolder(SingleItemProductCartRowHolder holder, int i) {

        Product product = list_item_product_cart.get(i);

        holder.productName.setText(product.getName());
        holder.link=product.getLink();
        ArrayList<Image> images=product.getImages();
        if(images!=null && images.size()>0) {
            Image first_image = images.get(0);
            String url = first_image.getUrl();

            Picasso.with(mContext).load(VTVConfig.rootUrl.concat(url)).into((ImageView) holder.ProductImage);
        }
        holder.productName.setGravity(Gravity.CENTER_VERTICAL | Gravity.CENTER_HORIZONTAL);
    }

    @Override
    public int getItemCount() {
        Timber.d("list_item_product_cart size %d",list_item_product_cart.size());
        return (null != list_item_product_cart ? list_item_product_cart.size() : 0);
    }

    public class SingleItemProductCartRowHolder extends RecyclerView.ViewHolder {

        protected TextView productName;
        protected ImageView ProductImage;
        public String link;
        private JApplication app= JFactory.getApplication();

        public SingleItemProductCartRowHolder(View view) {
            super(view);
            this.productName = (TextView) view.findViewById(R.id.productName);
            this.ProductImage = (ImageView) view.findViewById(R.id.productImage);
            view.setOnClickListener(new View.OnClickListener() {
                public String getLink() {
                    return link;
                }

                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onClick(View v) {
                    //Toast.makeText(v.getContext(), productName.getText(), Toast.LENGTH_SHORT).show();
                    //Toast.makeText(v.getContext(), this.getLink_redirect(), Toast.LENGTH_SHORT).show();
                    app.setRedirect(this.getLink());
                }
            });


        }

    }

}