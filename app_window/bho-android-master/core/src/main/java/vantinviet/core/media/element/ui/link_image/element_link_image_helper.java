package vantinviet.core.media.element.ui.link_image;

import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;

import com.beardedhen.androidbootstrap.BootstrapThumbnail;
import com.squareup.picasso.Picasso;
import vantinviet.core.libraries.android.registry.JRegistry;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by cuongnd on 12/18/2015.
 */
public class element_link_image_helper {





    public static void render_element(Context context, View parent_object, JSONObject object, int width) {
        BootstrapThumbnail image=new BootstrapThumbnail(context);
        try{
            int id = object.getInt("id");
            image.setId(id);
            String params=object.getString("params");
            JSONObject json_object_params=new JSONObject(params);
            JRegistry a_params = new JRegistry(json_object_params);

            System.out.println(json_object_params);
            String image_url="";
            image_url=a_params.get("element_config.image_source","","string");

            Picasso.with(context).load(image_url).resize(300,100).centerCrop().into(image);
            ((LinearLayout) parent_object).addView(image);
        }catch (JSONException e)
        {

        } catch (Exception e) {
            e.printStackTrace();
        }

    }
}
