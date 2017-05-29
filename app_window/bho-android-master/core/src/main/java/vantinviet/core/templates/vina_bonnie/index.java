package vantinviet.core.templates.vina_bonnie;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import java.util.ArrayList;

import timber.log.Timber;

import vantinviet.core.R;


import vantinviet.core.libraries.cms.component.JComponentHelper;
import vantinviet.core.libraries.cms.module.JModuleHelper;
import vantinviet.core.libraries.html.bootstrap.Column;
import vantinviet.core.libraries.html.bootstrap.Row;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;


/**
 * Fragment shows a detail of the product.
 */
@SuppressLint("ValidFragment")
public class index extends LinearLayout {


    private ProgressBar progressView;

    // Fields referencing complex screen layouts.
    private View layoutEmpty;
    private RelativeLayout productContainer;
    private ScrollView contentScrollLayout;

    public static LinearLayout instance;

    private ViewTreeObserver.OnScrollChangedListener scrollViewListener;


    public  index(Context context) {
        super(context);

    }

    public static LinearLayout getInstance() {
        JApplication app=JFactory.getApplication();
        if (instance == null) {
            instance = new index(app.getContext());
        }
        return instance;
    }
    public static void buildLayout(LinearLayout root_linear_layout) {
        root_linear_layout.removeAllViews();
        JApplication app= JFactory.getApplication();
        int screen_size_width;
        int screen_size_height;
        ArrayList<Row> layout;
        layout= app.getTemplate().getParams().getAndroid_layout();
        DisplayMetrics metrics = new DisplayMetrics();
        app.getCurrentActivity().getWindowManager().getDefaultDisplay().getMetrics(metrics);
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
        render_layout(layout,root_linear_layout,screen_size_width, WRAP_CONTENT);
    }

    private static void render_layout(ArrayList<Row> layout, LinearLayout rootLinearLayout, int screen_size_width, int screen_size_heght) {
        JApplication app= JFactory.getApplication();
        LayoutParams layout_params;
        if(layout!=null)for (Row row: layout) {
            layout_params = new LayoutParams(screen_size_width,screen_size_heght  );
            layout_params.setMargins(0,10,0,10);
            LinearLayout new_row_linear_layout=new LinearLayout(app.getContext());
            new_row_linear_layout.setLayoutParams(layout_params);
            new_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

            layout_params = new LayoutParams(screen_size_width,screen_size_heght );
            LinearLayout new_wrapper_of_row_linear_layout=new LinearLayout(app.getContext());
            new_wrapper_of_row_linear_layout.setLayoutParams(layout_params);
            new_wrapper_of_row_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
            Timber.d("row name(%s)",row.getName());
            ArrayList<Column> list_column=row.getColumns();
            if(list_column!=null)for (Column column: list_column) {

                int column_width=Integer.parseInt(column.getDefault_span());
                column_width=screen_size_width*column_width/12;
                layout_params = new LayoutParams(column_width,screen_size_heght  );
                int column_offset=Integer.parseInt(column.getOffset().equals("")?"0":column.getOffset());
                column_offset=screen_size_width*column_offset/12;
                layout_params.setMargins(column_offset, 0, 0, 0);
                LinearLayout new_column_linear_layout=new LinearLayout(app.getContext());
                new_column_linear_layout.setLayoutParams(layout_params);
                new_column_linear_layout.setOrientation(LinearLayout.HORIZONTAL);

                LayoutParams new_vertical_wrapper_of_column_linear_layout_params = new LayoutParams(column_width,WRAP_CONTENT  );
                LinearLayout new_wrapper_of_column_linear_layout=new LinearLayout(app.getContext());
                new_wrapper_of_column_linear_layout.setLayoutParams(new_vertical_wrapper_of_column_linear_layout_params);
                new_wrapper_of_column_linear_layout.setOrientation(LinearLayout.VERTICAL);


                //add_text_view_test(new_column_linear_layout,column.getType());
                ArrayList<Row> list_row=column.getRows();
                String type=column.getType();
                String position=column.getPosition();
                Timber.d("position name(%s)",position);
                if(type.equals("modules")){

                    ArrayList<Module> modules=app.getModules();
                    for (Module module : modules)
                    {
                        if(module.getPosition().equals(position)){
                            LayoutParams module_layout_params = new LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
                            LinearLayout new_module_linear_layout=new LinearLayout(app.getContext());
                            new_module_linear_layout.setLayoutParams(module_layout_params);
                            new_module_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                            //add_text_view_test(new_column_linear_layout,position);
                            JModuleHelper.renderModule(app.getContext(),module, new_module_linear_layout);
                            new_wrapper_of_column_linear_layout.addView(new_module_linear_layout);
                        }
                    }

                }
                if(type.equals("component")){

                    LayoutParams component_layout_params = new LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
                    LinearLayout component_linear_layout=new LinearLayout(app.getContext());
                    component_linear_layout.setLayoutParams(component_layout_params);
                    component_linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                    //add_text_view_test(new_column_linear_layout,position);
                    app.input.set_component_linear_layout(component_linear_layout);
                    app.component_width=column_width;
                    JComponentHelper.renderComponent(app.getContext(), component_linear_layout,column_width);
                    new_wrapper_of_column_linear_layout.addView(component_linear_layout);

                }

                Timber.d("column position(%s),type(%s) span(%s)",column.getPosition(),column.getType(),column.getDefault_span());
                render_layout(list_row,new_wrapper_of_column_linear_layout,column_width,screen_size_heght);
                new_column_linear_layout.addView(new_wrapper_of_column_linear_layout);
                new_wrapper_of_row_linear_layout.addView(new_column_linear_layout);

            }

            //add_text_view_test(new_row_linear_layout,row.getProduct_name());

            new_row_linear_layout.addView(new_wrapper_of_row_linear_layout);
            rootLinearLayout.addView(new_row_linear_layout);

        }
    }
}
