package vantinviet.core.components.com_hikamarket.views.product.tmpl;

import android.content.Context;
import android.support.design.widget.BottomNavigationView;
import android.util.AttributeSet;
import android.view.View;

import vantinviet.core.R;

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
