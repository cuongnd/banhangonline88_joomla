package vantinviet.core.modules.mod_slideshowck;

import android.content.Context;
import java.lang.reflect.Type;
import java.util.ArrayList;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.reflect.TypeToken;

import android.os.Bundle;
import android.widget.LinearLayout;


import timber.log.Timber;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

import static android.view.ViewGroup.LayoutParams.FILL_PARENT;
import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_slideshowck {


    private final Module module;
    private final LinearLayout linear_layout;
    JApplication app= JFactory.getApplication();
    public mod_slideshowck( Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        init();
    }

    private void init() {
        String response=this.module.getResponse();
        Type listType = new TypeToken<ArrayList<Slider>>() {}.getType();
        ArrayList<Slider> list_slide = JUtilities.getGsonParser().fromJson(response, listType);
        Timber.d("mod_slideshowck list_slide %s",list_slide.toString());
        SliderLayout mDemoSlider =new SliderLayout(app.getBaseContext());
        mDemoSlider.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 400));
        if(list_slide!=null)for (Slider item: list_slide) {
            TextSliderView textSliderView = new TextSliderView(app.getBaseContext());
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
