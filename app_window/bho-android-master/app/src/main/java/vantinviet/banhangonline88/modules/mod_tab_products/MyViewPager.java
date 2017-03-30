package vantinviet.banhangonline88.modules.mod_tab_products;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.drawable.Drawable;
import android.support.v4.view.ViewPager;
import android.text.TextPaint;
import android.util.AttributeSet;
import android.view.View;

import vantinviet.banhangonline88.R;


/**
 * TODO: document your custom view class.
 */
public class MyViewPager extends ViewPager {
    private String mExampleString; // TODO: use a default from R.string...
    private int mExampleColor = Color.RED; // TODO: use a default from R.color...
    private float mExampleDimension = 0; // TODO: use a default from R.dimen...
    private Drawable mExampleDrawable;

    private TextPaint mTextPaint;
    private float mTextWidth;
    private float mTextHeight;

    public MyViewPager(Context context) {
        super(context);
        init(null, 0);
    }

    public MyViewPager(Context context, AttributeSet attrs) {
        super(context, attrs);
        init(attrs, 0);
    }


    private void init(AttributeSet attrs, int defStyle) {
        inflate(getContext(), R.layout.dialog_login, this);
    }

}
