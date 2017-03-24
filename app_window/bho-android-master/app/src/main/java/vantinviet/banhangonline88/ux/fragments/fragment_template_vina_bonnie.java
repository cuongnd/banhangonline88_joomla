package vantinviet.banhangonline88.ux.fragments;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.PorterDuff;
import android.net.Uri;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.design.widget.Snackbar;
import android.support.v4.app.Fragment;
import android.support.v4.content.ContextCompat;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.text.method.LinkMovementMethod;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.Spinner;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.facebook.share.model.ShareLinkContent;
import com.facebook.share.widget.MessageDialog;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import mbanje.kurt.fabbutton.FabButton;
import timber.log.Timber;
import vantinviet.banhangonline88.BuildConfig;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.SettingsMy;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.api.JsonRequest;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.User;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.entities.product.Product;
import vantinviet.banhangonline88.entities.product.ProductColor;
import vantinviet.banhangonline88.entities.product.ProductSize;
import vantinviet.banhangonline88.entities.product.ProductVariant;
import vantinviet.banhangonline88.entities.template.bootstrap.Column;
import vantinviet.banhangonline88.entities.template.bootstrap.Row;
import vantinviet.banhangonline88.interfaces.LoginDialogInterface;
import vantinviet.banhangonline88.interfaces.ProductImagesRecyclerInterface;
import vantinviet.banhangonline88.interfaces.RelatedProductsRecyclerInterface;
import vantinviet.banhangonline88.interfaces.RequestListener;
import vantinviet.banhangonline88.listeners.OnSingleClickListener;
import vantinviet.banhangonline88.utils.Analytics;
import vantinviet.banhangonline88.utils.JsonUtils;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.RecyclerMarginDecorator;
import vantinviet.banhangonline88.utils.Utils;
import vantinviet.banhangonline88.ux.MainActivity;
import vantinviet.banhangonline88.ux.adapters.ColorSpinnerAdapter;
import vantinviet.banhangonline88.ux.adapters.ProductImagesRecyclerAdapter;
import vantinviet.banhangonline88.ux.adapters.RelatedProductsRecyclerAdapter;
import vantinviet.banhangonline88.ux.adapters.SizeVariantSpinnerAdapter;
import vantinviet.banhangonline88.ux.dialogs.LoginDialogFragment;
import vantinviet.banhangonline88.ux.dialogs.LoginExpiredDialogFragment;
import vantinviet.banhangonline88.ux.dialogs.ProductImagesDialogFragment;

import static vantinviet.banhangonline88.ux.MainActivity.mInstance;

/**
 * Fragment shows a detail of the product.
 */
@SuppressLint("ValidFragment")
public class fragment_template_vina_bonnie extends Fragment {

    private final DrawerMenuItem drawerMenuItem;
    private final Page page;

    private ProgressBar progressView;

    // Fields referencing complex screen layouts.
    private View layoutEmpty;
    private RelativeLayout productContainer;
    private ScrollView contentScrollLayout;

    ArrayList<Row> layout;
    private ViewTreeObserver.OnScrollChangedListener scrollViewListener;
    private MyApplication app;
    private int screen_size_width;
    private int screen_size_height;

    @SuppressLint("ValidFragment")
    public fragment_template_vina_bonnie(DrawerMenuItem drawerMenuItem, Page page) {
        this.drawerMenuItem=drawerMenuItem;
        this.page=page;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Timber.d("%s - onCreateView", this.getClass().getSimpleName());
        MainActivity.setActionBarTitle("hello title");
        layout= page.getTemplate().getParams().getLayout();
        Timber.d("page layout %s", layout.toString());
        View view = inflater.inflate(R.layout.fragment_template_vina_bonnie, container, false);
        LinearLayout rootLinearLayout=(LinearLayout)view.findViewById(R.id.root_layout);
        DisplayMetrics metrics = new DisplayMetrics();
        mInstance.getWindowManager().getDefaultDisplay().getMetrics(metrics);
        int screenDensity = (int) metrics.density;
        int screenDensityDPI = metrics.densityDpi;
        float screenscaledDensity = metrics.scaledDensity;
        int width = metrics.widthPixels;
        int height = metrics.heightPixels;


        System.out.println("Screen Density=" + screenDensity + "\n"
                + "Screen DensityDPI=" + screenDensityDPI + "\n"
                + "Screen Scaled DensityDPI=" + screenscaledDensity + "\n"
                + "Height=" + height + "\n"
                + "Width=" + width);

        screen_size_width = width;
        screen_size_height = height;
        if (screenDensity == 0) {
            screenDensity = 1;
        }
        String screenSize = Integer.toString(width / screenDensity) + "x" + Integer.toString(height);
        System.out.println(width / screenDensity);

        render_layout(layout,rootLinearLayout);
        return view;
    }

    private void render_layout(ArrayList<Row> layout, LinearLayout rootLinearLayout) {
        LayoutParams layout_params;
        if(layout!=null && !layout.isEmpty())
        {
            for (int i = 0; i < layout.size(); i++) {
                layout_params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT);
                LinearLayout new_row_linear_layout=new LinearLayout(getContext());
                new_row_linear_layout.setLayoutParams(layout_params);
                new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

                layout_params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT);
                LinearLayout new_wrapper_of_row_linear_layout=new LinearLayout(getContext());
                new_wrapper_of_row_linear_layout.setLayoutParams(layout_params);
                new_wrapper_of_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                Row row=layout.get(i);
                add_text_view_test(new_wrapper_of_row_linear_layout,"hello row "+row.getName());
                ArrayList<Column> list_column=row.getColumns();
                for (int j = 0; j < list_column.size(); j++) {
                    Column column=list_column.get(j);
                    int column_width=Integer.parseInt(column.getSpan());
                    column_width=screen_size_width*column_width/12;
                    layout_params = new LayoutParams(column_width, LayoutParams.MATCH_PARENT);
                    int column_offset=Integer.parseInt(column.getOffset().equals("")?"0":column.getOffset());
                    column_offset=screen_size_width*column_offset/12;
                    layout_params.setMargins(column_offset, 0, 0, 0);
                    LinearLayout new_column_linear_layout=new LinearLayout(getContext());
                    new_column_linear_layout.setLayoutParams(layout_params);
                    new_column_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                    ArrayList<Row> list_row=column.getRows();
                    add_text_view_test(new_column_linear_layout,"hello "+column.getSpan()+" ( offset:"+column.getOffset()+") ");
                    if(list_row!=null && list_row.isEmpty()){


                    }else{
                        render_layout(list_row,new_column_linear_layout);
                    }
                    new_wrapper_of_row_linear_layout.addView(new_column_linear_layout);


                }
                new_row_linear_layout.addView(new_wrapper_of_row_linear_layout);
                rootLinearLayout.addView(new_row_linear_layout);

            }
        }



    }
    private void add_text_view_test(LinearLayout view_linear_layout,String text) {
        TextView new_item_text_view=new TextView(getContext());
        new_item_text_view.setText(text);
        view_linear_layout.addView(new_item_text_view);
    }
    private void setContentVisible(CONST.VISIBLE visible) {
        if (layoutEmpty != null && contentScrollLayout != null && progressView != null) {
            switch (visible) {
                case EMPTY:
                    layoutEmpty.setVisibility(View.VISIBLE);
                    contentScrollLayout.setVisibility(View.INVISIBLE);
                    progressView.setVisibility(View.GONE);
                    break;
                case PROGRESS:
                    layoutEmpty.setVisibility(View.GONE);
                    contentScrollLayout.setVisibility(View.INVISIBLE);
                    progressView.setVisibility(View.VISIBLE);
                    break;
                default: // Content
                    layoutEmpty.setVisibility(View.GONE);
                    contentScrollLayout.setVisibility(View.VISIBLE);
                    progressView.setVisibility(View.GONE);
            }
        } else {
            Timber.e(new RuntimeException(), "Setting content visibility with null views.");
        }
    }

    @Override
    public void onResume() {
        if (contentScrollLayout != null) contentScrollLayout.getViewTreeObserver().addOnScrollChangedListener(scrollViewListener);
        super.onResume();
    }

    @Override
    public void onPause() {
        if (contentScrollLayout != null) contentScrollLayout.getViewTreeObserver().removeOnScrollChangedListener(scrollViewListener);
        super.onPause();
    }

    @Override
    public void onStop() {
        setContentVisible(CONST.VISIBLE.CONTENT);
        super.onStop();
    }
}
