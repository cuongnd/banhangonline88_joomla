package vantinviet.banhangonline88.media.element.slider.banner_rotator;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.daimajia.slider.library.Animations.DescriptionAnimation;
import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by cuongnd on 12/18/2015.
 */
public class elementBanner_RotatorHelper {



    public static void render_banner_rotator(Context context, View parent_object, JSONObject object, int width) {

        SliderLayout mDemoSlider =new SliderLayout(context);
        try{
            int id = object.getInt("id");
            mDemoSlider.setId(id);
            mDemoSlider.setLayoutParams(new ViewGroup.LayoutParams(width, 400));
            String params=object.getString("params");
            JSONObject json_object_params=new JSONObject(params);
            JSONArray list_image=new JSONArray();
            if(object.has("list_image"))
            {
                list_image=object.getJSONArray("list_image");
            }
            for (int i=0;i<list_image.length();i++) {
                JSONObject image=list_image.getJSONObject(i);
                String source="";
                if(image.has("source"))
                {
                    source=image.getString("source");
                    TextSliderView textSliderView = new TextSliderView(context);
                    // initialize a SliderLayout
                    textSliderView
                            .description("")
                            .image(source)
                            .setScaleType(BaseSliderView.ScaleType.Fit)
                    ;

                    //add your extra information
                    textSliderView.bundle(new Bundle());
                    textSliderView.getBundle()
                            .putString("extra","");

                    mDemoSlider.addSlider(textSliderView);
                }
            }






            mDemoSlider.setPresetTransformer(SliderLayout.Transformer.Accordion);
            mDemoSlider.setPresetIndicator(SliderLayout.PresetIndicators.Center_Bottom);
            mDemoSlider.setCustomAnimation(new DescriptionAnimation());
            mDemoSlider.setDuration(4000);

            ((LinearLayout) parent_object).addView(mDemoSlider);
        }catch (JSONException e)
        {

        }

    }
}
