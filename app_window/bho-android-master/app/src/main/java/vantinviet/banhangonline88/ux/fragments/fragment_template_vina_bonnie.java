package vantinviet.banhangonline88.ux.fragments;

import android.annotation.SuppressLint;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.widget.HorizontalScrollView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.Page;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.entities.module.Module;
import vantinviet.banhangonline88.entities.template.bootstrap.Column;
import vantinviet.banhangonline88.entities.template.bootstrap.Row;
import vantinviet.banhangonline88.libraries.cms.module.JModuleHelper;
import vantinviet.banhangonline88.ux.MainActivity;

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
        layout= page.getTemplate().getParams().getAndroid_layout();
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

        render_layout(layout,rootLinearLayout,screen_size_width,LayoutParams.WRAP_CONTENT);
        return view;
    }

    private void render_layout(ArrayList<Row> layout, LinearLayout rootLinearLayout,int screen_size_width,int screen_size_heght) {
        LayoutParams layout_params;
        for (int i = 0; i < layout.size(); i++) {
            layout_params = new LayoutParams(screen_size_width,screen_size_heght  );
            LinearLayout new_row_linear_layout=new LinearLayout(getContext());
            new_row_linear_layout.setLayoutParams(layout_params);
            new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

            layout_params = new LayoutParams(screen_size_width,screen_size_heght );
            LinearLayout new_wrapper_of_row_linear_layout=new LinearLayout(getContext());
            new_wrapper_of_row_linear_layout.setLayoutParams(layout_params);
            new_wrapper_of_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
            Row row=layout.get(i);
            Timber.d("row name(%s)",row.getName());
            ArrayList<Column> list_column=row.getColumns();
            for (int j = 0; j < list_column.size(); j++) {
                Column column=list_column.get(j);
                int column_width=Integer.parseInt(column.getSpan());
                column_width=screen_size_width*column_width/12;
                layout_params = new LayoutParams(screen_size_width,screen_size_heght  );
                int column_offset=Integer.parseInt(column.getOffset().equals("")?"0":column.getOffset());
                column_offset=screen_size_width*column_offset/12;
                layout_params.setMargins(column_offset, 0, 0, 0);
                LinearLayout new_column_linear_layout=new LinearLayout(getContext());
                new_column_linear_layout.setLayoutParams(layout_params);
                new_column_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                add_text_view_test(new_column_linear_layout,column.getPosition());
                ArrayList<Row> list_row=column.getRows();
                String type=column.getType();
                String position=column.getPosition();
                if(type.equals("modules")){
                    ArrayList<Module> modules=page.getModules();
                    for (Module module : modules)
                    {
                        if(module.getPosition().equals(position)){
                            //add_text_view_test(new_column_linear_layout,"hello");
                            JModuleHelper.renderModule(getContext(),module, new_column_linear_layout);
                        }
                    }

                }
                Timber.d("column position(%s),type(%s) span(%s)",column.getPosition(),column.getType(),column.getSpan());
                render_layout(list_row,new_column_linear_layout,column_width,screen_size_heght);
                ScrollView column_scroll_view=new ScrollView(getContext());
                column_scroll_view.addView(new_column_linear_layout);
                new_wrapper_of_row_linear_layout.addView(column_scroll_view);


            }
            add_text_view_test(new_row_linear_layout,row.getName());

            new_row_linear_layout.addView(new_wrapper_of_row_linear_layout);
            HorizontalScrollView horizontal_scrollview=new HorizontalScrollView(getContext());
            horizontal_scrollview.addView(new_row_linear_layout);
            rootLinearLayout.addView(horizontal_scrollview);

        }




    }
    private void add_text_view_test(LinearLayout view_linear_layout,String text) {
        TextView new_item_text_view=new TextView(getContext());
        new_item_text_view.setText(text);
        view_linear_layout.addView(new_item_text_view);
    }
    private void setContentVisible(CONST.VISIBLE visible) {

    }

    @Override
    public void onResume() {
        super.onResume();
    }

    @Override
    public void onPause() {
        super.onPause();
    }

    @Override
    public void onStop() {
        super.onStop();
    }
}
