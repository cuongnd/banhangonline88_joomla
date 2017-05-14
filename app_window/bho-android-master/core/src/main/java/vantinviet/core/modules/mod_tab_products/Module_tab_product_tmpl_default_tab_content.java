package vantinviet.core.modules.mod_tab_products;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.HorizontalScrollView;
import android.widget.TextView;

import java.io.IOException;
import java.sql.Time;
import java.util.ArrayList;
import java.util.Timer;

import me.everything.android.ui.overscroll.OverScrollDecoratorHelper;
import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;

/**
 * Created by neokree on 16/12/14.
 */

/**
 * Created by neokree on 16/12/14.
 */
public class Module_tab_product_tmpl_default_tab_content extends Fragment {
    public Mod_tab_product_helper.List_category_product list_category_product;
    private ArrayList<Product> list_product=new ArrayList<Product>();


    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

        View view = inflater.inflate(R.layout.modules_mod_tab_products_tmpl_default_tab_content, container, false);

        Show_product_recycler_view show_product_recycler_view=new Show_product_recycler_view(list_category_product);
        RecyclerView product_recycler_view = (RecyclerView) view.findViewById(R.id.product_recycler_view);
        product_recycler_view.setHasFixedSize(true);
        RecyclerViewDataAdapter adapter = new RecyclerViewDataAdapter(getContext(), show_product_recycler_view);
        product_recycler_view.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.VERTICAL, false));
        product_recycler_view.setAdapter(adapter);

        RecyclerView cagory_recycler_view = (RecyclerView) view.findViewById(R.id.category_recycler_view);
        cagory_recycler_view.setHasFixedSize(true);
        CategoryListDataAdapter category_adapter = new CategoryListDataAdapter(getContext(), list_category_product.getList_sub_category_detail());
        cagory_recycler_view.setLayoutManager(new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false));
        cagory_recycler_view.setAdapter(category_adapter);
        Timber.d("module list_category_product %s",list_category_product.toString());



        return view;
    }


    public class Show_product_recycler_view {
        private final Mod_tab_product_helper.List_category_product list_category_product;
        Category category;
        ArrayList<Product> list_product;
        public Show_product_recycler_view(Mod_tab_product_helper.List_category_product list_category_product) {
            this.list_category_product=list_category_product;
            try{
                category =list_category_product.getDetail();
            }catch (Exception ex){
                Timber.d("ex %s",ex.toString());
            }

            list_product=list_category_product.getList();

        }

        public ArrayList<Product> getList_product() {
            return list_product;
        }

        public Category getCategory() {
            return category;
        }
    }
}
