package vantinviet.banhangonline88.modules.mod_tab_products;

/**
 * Created by cuongnd on 31/03/2017.
 */

/**
 * Created by pratap.kesaboyina on 24-12-2014.
 */

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Image;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

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
        holder.productName.setText(product.getName());
        holder.link=product.getLink();
        holder.html_price.setText(Html.fromHtml(product.getHtml_price()));
        ArrayList<Image> list_image=product.getList_image();
        if(list_image!=null && list_image.size()>0) {
            Image first_image = list_image.get(0);
            String url = first_image.getUrl();

            Picasso.with(mContext).load(EndPoints.API_URL1.concat(url)).into((ImageView) holder.ProductImage);
        }
        holder.productName.setGravity(Gravity.CENTER_VERTICAL | Gravity.CENTER_HORIZONTAL);
    }

    @Override
    public int getItemCount() {
        return (null != list_product ? list_product.size() : 0);
    }

    public class SingleProductRowHolder extends RecyclerView.ViewHolder {
        protected TextView productName;
        protected TextView html_price;
        protected ImageView ProductImage;
        public String link;
        private JApplication app= JFactory.getApplication();
        public SingleProductRowHolder(View view) {
            super(view);

            this.productName = (TextView) view.findViewById(R.id.productName);
            this.ProductImage = (ImageView) view.findViewById(R.id.productImage);
            this.html_price = (TextView) view.findViewById(R.id.html_price);
            view.setOnClickListener(new View.OnClickListener() {
                public String getLink() {
                    return link;
                }
                @Override
                public void onClick(View v) {

                    //Toast.makeText(v.getContext(), this.getLink(), Toast.LENGTH_SHORT).show();
                    app.setRedirect(this.getLink());
                }
            });


        }

    }

}