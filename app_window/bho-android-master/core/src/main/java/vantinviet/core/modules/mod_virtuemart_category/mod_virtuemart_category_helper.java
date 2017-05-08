package vantinviet.core.modules.mod_virtuemart_category;

import android.content.Context;
import android.content.Intent;
import android.content.res.Resources;
import android.view.View;
import android.view.ViewGroup;
import android.widget.HorizontalScrollView;
import android.widget.LinearLayout;

import com.beardedhen.androidbootstrap.BootstrapButton;
import com.beardedhen.androidbootstrap.BootstrapButtonGroup;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.application.JApplicationSite;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


/**
 * Created by cuongnd on 12/17/2015.
 */
public class mod_virtuemart_category_helper {
    public static void render_module(Context context, View parent_object, JSONObject object, int width)
    {
        if(1==1)
        {
            return;
        }
        LinearLayout linear_layout = new LinearLayout(context);
        BootstrapButtonGroup bootstrap_button_group=new BootstrapButtonGroup(context);
        HorizontalScrollView scroll_view = new HorizontalScrollView(context);
        String scroll="";
        try {

            int id = object.getInt("id");
            String title = "";
            bootstrap_button_group.setId(id);

            bootstrap_button_group.setBootstrapBrand(DefaultBootstrapBrand.SUCCESS);
            bootstrap_button_group.setOrientation(LinearLayout.HORIZONTAL);
            bootstrap_button_group.setRounded(false);
            JSONArray list_menu_item=object.getJSONArray("list_menu_item");
            System.out.println(list_menu_item);
            String params=object.getString("params");
            JSONObject json_object_params=new JSONObject(params);
            JSONObject menu_config=json_object_params.getJSONObject("menu_config");
            if(menu_config.has("scroll")) {
                scroll =menu_config.getString("scroll");
            }else
            {
                scroll="on";
            }
            if(scroll.equals("on"))
            {
                scroll_view.setId(id);

                scroll_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));
                //scroll_view.setBackgroundColor(currentStrokeColor);
            }
            for (int i=0;i<list_menu_item.length();i++) {
                BootstrapButton bootstrap_button = new BootstrapButton(context);
                JSONObject menu_item=list_menu_item.getJSONObject(i);
                id = menu_item.getInt("id");
                title = menu_item.getString("title");
                bootstrap_button.setId(id);
                bootstrap_button.setText(title);
                Resources resource = context.getResources();
                bootstrap_button_group.addView(bootstrap_button);
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }
        if(scroll.equals("on"))
        {
            scroll_view.addView(bootstrap_button_group);
            ((LinearLayout) parent_object).addView(scroll_view);

        }
        else
        {
            ((LinearLayout) parent_object).addView(bootstrap_button_group);
        }

    }

}
