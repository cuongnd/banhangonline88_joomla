package vantinviet.core.modules.mod_easysocial_login.mod_menu;

import android.os.Bundle;
import android.widget.LinearLayout;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.core.libraries.cms.module.JModuleHelper;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.html.module.Params;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.modules.mod_slideshowck.Slider;

import static android.widget.ListPopupWindow.MATCH_PARENT;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_easysocial_login {


    private final Module module;
    private final LinearLayout linear_layout;
    JApplication app= JFactory.getApplication();
    public mod_easysocial_login(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        String content=this.module.getContent();
        if(content.isEmpty()){
            Timber.d("content module %s(%d) is empty",module.getModuleName(),module.getId());
            return;
        }
        Params params=module.getParams();

        String layout=params.getLayout();

        JModuleHelper.render_layout(this.module,this.linear_layout,layout);
        Timber.d("module menu param %s",module.getParams().toString());
        //Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
        //list_main_category_product = JUtilities.getGsonParser().fromJson(content, listType);
        //Timber.d("list_main_category_product %s", list_main_category_product.toString());
        //init();
    }

    private void init() {
        String response=this.module.getResponse();
        Type listType = new TypeToken<ArrayList<Slider>>() {}.getType();
        ArrayList<Slider> list_slide = JUtilities.getGsonParser().fromJson(response, listType);
        Timber.d("mod_slideshowck list_slide %s",list_slide.toString());
        SliderLayout mDemoSlider =new SliderLayout(app.getContext());
        mDemoSlider.setLayoutParams(new LinearLayout.LayoutParams(MATCH_PARENT, 400));
        if(list_slide!=null)for (Slider item: list_slide) {
            TextSliderView textSliderView = new TextSliderView(app.getContext());
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
