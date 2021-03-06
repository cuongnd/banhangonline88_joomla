package vantinviet.core.components.com_hikashop.views.category.tmpl;

import android.content.Context;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.AttributeSet;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.google.gson.JsonElement;

import vantinviet.core.R;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * TODO: document your custom view class.
 */
public class ListingContent extends LinearLayout {


    private listing.ListingResponse listingresponse;
    private JsonElement response;
    public View view;
    public ListingContent(Context context, listing.ListingResponse listingresponse) {
        super(context);
        view = inflate(getContext(), R.layout.components_com_hikashop_views_category_tmpl_listing_content, this);
        LayoutParams component_layout_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT  );
        view.setLayoutParams(component_layout_params);
        this.listingresponse=listingresponse;
        init(null, 0);
    }

    private void init(AttributeSet attrs, int defStyle) {

        RecyclerView cagory_recycler_view = (RecyclerView) view.findViewById(R.id.category_recycler_view);
        CategoryListDataAdapter category_adapter = new CategoryListDataAdapter(getContext(), listingresponse.getCategories());
        GridLayoutManager gridLayoutManager = new GridLayoutManager(getContext(), 2, GridLayoutManager.HORIZONTAL, false);
        cagory_recycler_view.setLayoutManager(gridLayoutManager);
        cagory_recycler_view.setAdapter(category_adapter);

        RecyclerView product_recycler_view = (RecyclerView) view.findViewById(R.id.product_recycler_view);




        ProductListDataAdapter product_adapter = new ProductListDataAdapter(getContext(), listingresponse.getProducts());
        product_recycler_view.setLayoutManager(new GridLayoutManager(getContext(), 2));
        product_recycler_view.setAdapter(product_adapter);
        ViewGroup.LayoutParams params=product_recycler_view.getLayoutParams();
        params.height=(listingresponse.getProducts().size()/2)*500;
        product_recycler_view.setLayoutParams(params);

    }


}
