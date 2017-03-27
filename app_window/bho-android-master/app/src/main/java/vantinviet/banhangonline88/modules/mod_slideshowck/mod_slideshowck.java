package vantinviet.banhangonline88.modules.mod_slideshowck;

import android.content.Context;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.List;

import com.daimajia.slider.library.Animations.DescriptionAnimation;
import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.reflect.TypeToken;
import vantinviet.banhangonline88.entities.module.Module;

import android.os.Bundle;
import android.view.ViewGroup;
import android.widget.LinearLayout;


import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import timber.log.Timber;
import vantinviet.banhangonline88.entities.template.bootstrap.Column;
import vantinviet.banhangonline88.utils.Utils;

import static android.view.ViewGroup.LayoutParams.MATCH_PARENT;
import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_slideshowck {


    private final Context context;
    private final Module module;
    private final LinearLayout linear_layout;

    public mod_slideshowck(Context context, Module module, LinearLayout linear_layout) {
        this.context=context;
        this.module=module;
        this.linear_layout=linear_layout;
        init();
    }

    private void init() {
        String response=this.module.getResponse();
        Type listType = new TypeToken<ArrayList<Slider>>() {}.getType();
        ArrayList<Slider> list_slide = Utils.getGsonParser().fromJson(response, listType);
        Timber.d("mod_slideshowck list_slide %s",list_slide.toString());
        SliderLayout mDemoSlider =new SliderLayout(context);
        mDemoSlider.setLayoutParams(new LinearLayout.LayoutParams(400, 400));
        if(listType!=null)for (Slider item: list_slide) {
            TextSliderView textSliderView = new TextSliderView(context);
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
