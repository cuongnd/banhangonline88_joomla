package vantinviet.banhangonline88.modules.mod_tab_products;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.drawable.Drawable;
import android.text.TextPaint;
import android.util.AttributeSet;
import android.view.View;
import android.widget.RelativeLayout;

import vantinviet.banhangonline88.R;

import static vantinviet.banhangonline88.libraries.joomla.JFactory.getContext;

/**
 * TODO: document your custom view class.
 */
public class tab_content extends RelativeLayout {


    public tab_content(Context context) {
        super(context);
        init(null, 0);
    }

    public tab_content(Context context, AttributeSet attrs) {
        super(context, attrs);
        init(attrs, 0);
    }

    public tab_content(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        init(attrs, defStyle);
    }

    private void init(AttributeSet attrs, int defStyle) {
        inflate(getContext(), R.layout.payment_dialog, this);
    }


}
