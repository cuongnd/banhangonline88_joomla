package vantinviet.banhangonline88.components.com_hikashop.views.product.tmpl;

import android.content.Context;
import android.os.Bundle;
import android.support.design.widget.BottomNavigationView;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Html;
import android.util.AttributeSet;
import android.view.Gravity;
import android.view.View;
import android.webkit.WebViewClient;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.google.gson.JsonElement;

import java.util.ArrayList;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Image;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.libraries.cms.application.WebView;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

import static vantinviet.banhangonline88.libraries.joomla.JFactory.getContext;
import static vantinviet.banhangonline88.ux.MainActivity.mInstance;

/**
 * TODO: document your custom view class.
 */
public class product_show_footer extends BottomNavigationView {
    public product_show_footer(Context context) {
        super(context);
        init(null, 0);
    }

    private void init(AttributeSet attrs, int defStyle) {
        View view =inflate(getContext(), R.layout.components_com_hikashop_views_product_tmpl_show_content_footer, this);



    }
}
