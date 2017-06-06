package vantinviet.core.modules.mod_tab_products;

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
import android.widget.Button;
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

public class ProductListDataAdapter extends RecyclerView.Adapter<ProductListDataAdapter.SingleProductRowHolder> {

    private ArrayList<Product> list_product;
    private Context mContext;

    public ProductListDataAdapter(Context context, ArrayList<Product> list_product) {
        this.list_product = list_product;
        this.mContext = context;
    }

    @Override
    public SingleProductRowHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.module_tab_product_tmpl_default_list_single_product_card, null);
        SingleProductRowHolder mh = new SingleProductRowHolder(v);
        return mh;
    }

    @Override
    public void onBindViewHolder(SingleProductRowHolder holder, int i) {

        Product product = list_product.get(i);
        holder.productName.setText(product.getProduct_name());
        holder.link=product.getLink();

        holder.price_value.setText(String.valueOf(product.getPrice_value()));
        ArrayList<Image> list_image=product.getList_image();
        if(list_image!=null && list_image.size()>0) {
            Image first_image = list_image.get(0);
            String url = first_image.getUrl();
            if(!url.isEmpty())
            {
                url=VTVConfig.rootUrl.concat(url);
                Picasso
                        .with(mContext)
                        .load(url)
                        .resize(400,400)
                        .into((ImageView) holder.ProductImage);
            }
        }
        holder.productName.setGravity(Gravity.CENTER_VERTICAL | Gravity.CENTER_HORIZONTAL);
    }

    @Override
    public int getItemCount() {
        return (null != list_product ? list_product.size() : 0);
    }

    public class SingleProductRowHolder extends RecyclerView.ViewHolder {
        private Button btn_add_to_cart;
        protected TextView productName;
        protected TextView price_value;
        protected ImageView ProductImage;
        public String link;
        private JApplication app= JFactory.getApplication();
        public SingleProductRowHolder(View view) {
            super(view);

            this.productName = (TextView) view.findViewById(R.id.productName);
            this.ProductImage = (ImageView) view.findViewById(R.id.productImage);
            this.price_value = (TextView) view.findViewById(R.id.price_value);
            this.btn_add_to_cart = (Button) view.findViewById(R.id.btn_add_to_cart);
            view.setOnClickListener(new View.OnClickListener() {
                public String getLink() {
                    return link;
                }
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onClick(View v) {

                    //Toast.makeText(v.getContext(), this.getLink_redirect(), Toast.LENGTH_SHORT).show();
                    app.setRedirect(this.getLink());
                }
            });
            this.btn_add_to_cart.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                }
            });


        }

    }

}