package vantinviet.core.components.com_hikashop.views.checkout.tmpl;

import android.content.Context;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.AttributeSet;
import android.view.View;
import android.widget.LinearLayout;

import com.google.gson.JsonElement;

import java.util.ArrayList;
import java.util.List;

import vantinviet.core.R;
import vantinviet.core.components.com_hikashop.views.category.tmpl.CategoryListDataAdapter;
import vantinviet.core.components.com_hikashop.views.category.tmpl.ProductListDataAdapter;
import vantinviet.core.components.com_hikashop.views.category.tmpl.listing;
import vantinviet.core.components.com_users.views.profile.view;

/**
 * TODO: document your custom view class.
 */
public class step_show_step extends LinearLayout {


    private vantinviet.core.components.com_hikashop.views.category.tmpl.listing.ListingResponse listingresponse;
    private JsonElement response;
    private ArrayList<String> steps;
    private View view;

    public step_show_step(Context context) {
        super(context);
        this.view=inflate(getContext(), R.layout.components_com_hikashop_views_checkout_tmpl_step_step, this);
    }

    public void init() {

        RecyclerView steps_recycler_view = (RecyclerView) this.view.findViewById(R.id.steps_recycler_view);
        StepListDataAdapter steps_adapter = new StepListDataAdapter(getContext(), this.steps);
        GridLayoutManager gridLayoutManager = new GridLayoutManager(getContext(), 1, GridLayoutManager.HORIZONTAL, false);
        steps_recycler_view.setLayoutManager(gridLayoutManager);
        steps_recycler_view.setAdapter(steps_adapter);

    }
    public void setSteps(ArrayList<String> steps) {
        this.steps = steps;
    }
}
