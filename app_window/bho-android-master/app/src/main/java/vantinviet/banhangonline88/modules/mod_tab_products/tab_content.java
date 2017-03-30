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
import vantinviet.banhangonline88.entities.module.Module;

import static vantinviet.banhangonline88.libraries.joomla.JFactory.getContext;

/**
 * TODO: document your custom view class.
 */
public class tab_content extends RelativeLayout {


    private Module module;

    public tab_content(Context context, Module module) {
        super(context);
        this.module=module;
        init(null, 0);
    }

    private void init(AttributeSet attrs, int defStyle) {
        inflate(getContext(), R.layout.dialog_login, this);

    }


}
