package vantinviet.banhangonline88.components.com_hikamarket.views.product.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.util.AttributeSet;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.Gson;
import com.google.gson.JsonElement;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Image;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.libraries.html.ParseHtml;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

import static vantinviet.banhangonline88.ux.MainActivity.mInstance;

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
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(html_product));
        reader.setLenient(true);
        ParseHtml html= gson.fromJson(reader, ParseHtml.class);
        LinearLayout html_linear_layout=ParseHtml.get_html_linear_layout(html);

        LinearLayout product_description= (LinearLayout) view.findViewById(R.id.product_description);
        LinearLayout html_product_linear_layout= (LinearLayout) view.findViewById(R.id.html_product);
        ((LinearLayout) html_product_linear_layout).addView(html_linear_layout);

        String content=product.getProduct_description();
        String header = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">"
                + "<html>  <head>  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">"
                +"<link type=\"text/css\" "+VTVConfig.rootUrl+"/templates/"+app.getTemplate().getTemplateName()+"/bootstrap-3.3.7/css/bootstrap.min.css\" rel=\"stylesheet\">"
                + "</head>  <body>";
        String footer = "</body></html>";

        android.webkit.WebView pageContent=new android.webkit.WebView(app.getCurrentActivity());
        content=header + content + footer;
        pageContent.loadData(content, "text/html; charset=UTF-8", null);


        ((LinearLayout) product_description).addView(pageContent);




        RecyclerView cagory_recycler_view = (RecyclerView) view.findViewById(R.id.category_recycler_view);
        CategoryListDataAdapter category_adapter = new CategoryListDataAdapter(getContext(), product_response.getCategories());
        GridLayoutManager gridLayoutManager = new GridLayoutManager(getContext(), 2, GridLayoutManager.HORIZONTAL, false);
        cagory_recycler_view.setLayoutManager(gridLayoutManager);
        cagory_recycler_view.setAdapter(category_adapter);



    }
    protected void start() {
        // Perform initialization (bindings, timers, etc) here
    }
    protected void stop() {
        Timber.d("hello 22222222222222222222");
        // Unbind, destroy timers, yadda yadda
    }


}
