package vantinviet.banhangonline88.modules.mod_slideshowck;

import android.content.Context;
import java.lang.reflect.Type;
import java.util.ArrayList;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.reflect.TypeToken;
import vantinviet.banhangonline88.entities.module.Module;

import android.os.Bundle;
import android.widget.LinearLayout;


import timber.log.Timber;
import vantinviet.banhangonline88.utils.Utils;

import static android.view.ViewGroup.LayoutParams.FILL_PARENT;
import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;
import static vantinviet.banhangonline88.ux.MainActivity.mInstance;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_slideshowck {


    private final Module module;
    private final LinearLayout linear_layout;

    public mod_slideshowck( Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        init();
    }

    private void init() {
        String response=this.module.getResponse();
        Type listType = new TypeToken<ArrayList<Slider>>() {}.getType();
        ArrayList<Slider> list_slide = Utils.getGsonParser().fromJson(response, listType);
        Timber.d("mod_slideshowck list_slide %s",list_slide.toString());
        SliderLayout mDemoSlider =new SliderLayout(mInstance);
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

        }
        linear_layout.addView(mDemoSlider);
    }
}
