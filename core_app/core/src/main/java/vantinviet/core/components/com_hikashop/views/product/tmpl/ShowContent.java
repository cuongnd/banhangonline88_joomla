package vantinviet.core.components.com_hikashop.views.product.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.util.AttributeSet;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewTreeObserver;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.stream.JsonReader;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Image;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.libraries.html.StyleSheet;
import vantinviet.core.libraries.html.TagHtml;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

import static vantinviet.core.ux.MainActivity.mInstance;

/**
 * TODO: document your custom view class.
 */
public class ShowContent extends LinearLayout {


    private show.PageShowProduct product_response;
    private JsonElement response;
    private JApplication app= JFactory.getApplication();
    public ShowContent(Context context, show.PageShowProduct product_response) {
        super(context);
        this.product_response =product_response;
        init(null, 0);
    }

    private void init(AttributeSet attrs, int defStyle) {
        View view =inflate(getContext(), R.layout.components_com_hikashop_views_product_tmpl_show_content, this);

        SliderLayout product_slider =(SliderLayout) view.findViewById(R.id.product_slider);
        Product product=product_response.getProduct();
        ArrayList<Image> images=product.getImages();
        if(images!=null)for (Image image: images) {
            TextSliderView textSliderView = new TextSliderView(mInstance);
            // initialize a SliderLayout
            textSliderView
                    .image(VTVConfig.rootUrl.concat(image.getUrl()))
                    .setScaleType(BaseSliderView.ScaleType.Fit)
            ;
            //add your extra information
            textSliderView.bundle(new Bundle());
            textSliderView.getBundle()
                    .putString("extra","");

            product_slider.addSlider(textSliderView);

        }
        TextView productName= (TextView) view.findViewById(R.id.productName);
        productName.setText(product.getName());

        TextView html_price= (TextView) view.findViewById(R.id.html_price);
        html_price.setText(Html.fromHtml(product.getHtml_price()));



        String html_product=product.getHtml_product();
        Timber.d("html_product %s",html_product.toString());
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(html_product));
        reader.setLenient(true);
        TagHtml html= gson.fromJson(reader, TagHtml.class);

        Map<String, StyleSheet> list_style_sheet = new HashMap<String, StyleSheet>();
        String style_product=product.getStyle_product();
        list_style_sheet=StyleSheet.get_list_style_sheet(style_product);

        LinearLayout html_product_linear_layout= (LinearLayout) view.findViewById(R.id.html_product);
        TagHtml.get_html_linear_layout(html,html_product_linear_layout,list_style_sheet);


        LinearLayout product_description= (LinearLayout) view.findViewById(R.id.product_description);


        String content=product.getProduct_description();
        String header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">"
                + "<html>  <head>  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">"
                +"<link type=\"text/css\" "+VTVConfig.rootUrl+"/templates/"+app.getTemplate().getTemplateName()+"/bootstrap-3.3.7/css/bootstrap.min.css\" rel=\"stylesheet\">"
                +"<link type=\"text/css\" "+VTVConfig.rootUrl+"/templates/"+app.getTemplate().getTemplateName()+"/less/custom.css\" rel=\"stylesheet\">"
                + "</head>  <body>";
        String footer = "</body></html>";

        android.webkit.WebView pageContent=new android.webkit.WebView(app.getCurrentActivity());
        content=header + content + footer;
        pageContent.loadData(content, "text/html; charset=UTF-8", null);



        final ScrollView main_scroll_view= app.getMain_scroll_view();
        main_scroll_view.getViewTreeObserver().addOnScrollChangedListener(new ViewTreeObserver.OnScrollChangedListener() {

            @Override
            public void onScrollChanged() {

            }
        });

        main_scroll_view.setOnTouchListener(new OnTouchListener() {
            boolean reload=false;
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                if (event.getAction() == MotionEvent.ACTION_UP) {
                    int scrollY = main_scroll_view.getScrollY(); // For ScrollView
                    int scrollX = main_scroll_view.getScrollX(); // For HorizontalScrollView
                    // DO SOMETHING WITH THE SCROLL COORDINATES
                    Timber.d("scrollY %d scrollX %d",scrollY,scrollX);
                    if(reload==true){
                        String link=app.getLink();
                        app.setRedirect(link);
                        Log.i("Touche", "ScrollView ACTION_DOWN");
                    }
                    if(scrollY==0){
                        reload=true;
                    }else{

                    }

                    return true;
                }
                return false;
            }
        });






        ((LinearLayout) product_description).addView(pageContent);




       /* RecyclerView cagory_recycler_view = (RecyclerView) view.findViewById(R.id.category_recycler_view);
        CategoryListDataAdapter category_adapter = new CategoryListDataAdapter(getContext(), product_response.getCategories());
        GridLayoutManager gridLayoutManager = new GridLayoutManager(getContext(), 2, GridLayoutManager.HORIZONTAL, false);
        cagory_recycler_view.setLayoutManager(gridLayoutManager);
        cagory_recycler_view.setAdapter(category_adapter);*/



    }
    protected void start() {
        // Perform initialization (bindings, timers, etc) here
    }
    protected void stop() {
        Timber.d("hello 22222222222222222222");
        // Unbind, destroy timers, yadda yadda
    }


}
