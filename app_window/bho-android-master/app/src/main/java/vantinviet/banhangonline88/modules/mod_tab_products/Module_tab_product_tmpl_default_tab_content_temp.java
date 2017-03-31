package vantinviet.banhangonline88.modules.mod_tab_products;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.LinearLayout;

import com.google.gson.JsonElement;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.entities.module.Module;

/**
 * TODO: document your custom view class.
 */
public class Module_tab_product_tmpl_default_tab_content_temp extends LinearLayout {


    private Module module;
    private JsonElement response;

    public Module_tab_product_tmpl_default_tab_content_temp(Context context, Module module) {
        super(context);
        this.module=module;
        init(null, 0);
    }

    private void init(AttributeSet attrs, int defStyle) {
        inflate(getContext(), R.layout.modules_mod_tab_products_tmpl_default_tab_content, this);

        //Timber.d("mod_slideshowck list_slide %s",list_slide.toString());
        /*SliderLayout mDemoSlider= (SliderLayout)findViewById(R.id.product_slider);
        mDemoSlider.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 400));
        if(list_slide!=null)for (Slider item: list_slide) {
            TextSliderView textSliderView = new TextSliderView(mInstance);
            // initialize a SliderLayout
            textSliderView
                    .description(item.getTitle())
                    .image(item.getSource())
                    .setScaleType(BaseSliderView.ScaleType.Fit)
            ;

            //add your extra information
            textSliderView.bundle(new Bundle());
            textSliderView.getBundle()
                    .putString("extra","");

            mDemoSlider.addSlider(textSliderView);

        }*/

    }


}
