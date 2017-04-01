package vantinviet.banhangonline88.modules.mod_tab_products;

/**
 * Created by cuongnd on 31/03/2017.
 */

/**
 * Created by pratap.kesaboyina on 24-12-2014.
 */

import android.content.Context;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;

public class RecyclerViewDataAdapter extends RecyclerView.Adapter<RecyclerViewDataAdapter.ItemRowHolder> {
    private final Category category;
    private ArrayList<Product> list_product;
    private Context mContext;
    public RecyclerViewDataAdapter(Context context, Module_tab_product_tmpl_default_tab_content.Show_product_recycler_view show_product_recycler_view) {
        this.list_product = show_product_recycler_view.getList_product();
        this.category = show_product_recycler_view.getCategory();
        this.mContext = context;
    }
    @Override
    public ItemRowHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.modules_mod_tab_products_list_category_product_item, null);
        ItemRowHolder mh = new ItemRowHolder(v);
        return mh;
    }
    @Override
    public void onBindViewHolder(ItemRowHolder itemRowHolder, int i) {
        ProductListDataAdapter productListDataAdapter = new ProductListDataAdapter(mContext, list_product);
        itemRowHolder.recycler_view_list.setHasFixedSize(true);
        itemRowHolder.recycler_view_list.setLayoutManager(new LinearLayoutManager(mContext, LinearLayoutManager.HORIZONTAL, false));
        itemRowHolder.recycler_view_list.setAdapter(productListDataAdapter);
        itemRowHolder.btnMore.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Toast.makeText(v.getContext(), "click event on more, " , Toast.LENGTH_SHORT).show();
            }
        });
        itemRowHolder.itemCategoryName.setText(category.getName());
    }

    @Override
    public int getItemCount() {
        return 1;
    }

    public class ItemRowHolder extends RecyclerView.ViewHolder {

        protected TextView itemCategoryName;

        protected RecyclerView recycler_view_list;

        protected Button btnMore;



        public ItemRowHolder(View view) {
            super(view);

            this.itemCategoryName = (TextView) view.findViewById(R.id.itemTitle);
            this.recycler_view_list = (RecyclerView) view.findViewById(R.id.recycler_view_list);
            this.btnMore= (Button) view.findViewById(R.id.btnMore);


        }

    }

}
