package vantinviet.core.modules.mod_menu;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;

import timber.log.Timber;
import vantinviet.core.entities.module.Module;
import vantinviet.core.libraries.android.registry.JRegistry;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by cuongnd on 12/17/2015.
 */
public class mod_menu {

    public static  Context main_context;
    private final Module module;
    private final LinearLayout linear_layout;

    @SuppressLint("ValidFragment")
    public mod_menu(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        Timber.d("hello %s", this.getClass().getSimpleName());
    }
    public static void render_menu(final Context context, View parent_object, JSONObject object, int width)
    {
        main_context=context;
        try {
            String params=object.getString("params");
            JSONObject json_object_params=new JSONObject(params);
            JRegistry a_params = new JRegistry(json_object_params);
            System.out.println(json_object_params);
            String layout="";
            layout=a_params.get("layout","","string");
            if(layout.equals("sprflat:leftmenutour")){
                vertical_menu.render_menu_vertical(context, parent_object, object, width);
            }else{
                horizontal_menu.render_menu_horizontal(context, parent_object, object, width);
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }

    }





}
