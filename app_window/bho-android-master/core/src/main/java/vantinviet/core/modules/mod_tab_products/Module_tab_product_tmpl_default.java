package vantinviet.core.modules.mod_tab_products;

import android.content.Context;
import android.util.AttributeSet;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.google.gson.JsonElement;

import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;


/**
 * TODO: document your custom view class.
 */
public class Module_tab_product_tmpl_default extends LinearLayout {


    private Module module;
    private JsonElement response;

    public Module_tab_product_tmpl_default(Context context, Module module) {
        super(context);
        this.module=module;
        init(null, 0);
    }

    private void init(AttributeSet attrs, int defStyle) {
        inflate(getContext(), R.layout.modules_mod_tab_products_tmpl_default, this);


    }


}
