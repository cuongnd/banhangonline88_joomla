package vantinviet.core.libraries.html;

import android.view.Gravity;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import java.util.ArrayList;


/**
 * Created by cuongnd on 14/04/2017.
 */

public class Style {

    public static String[] get_list_styles() {
        String[] list_styles = new String[]{
                "text-center",
                "text-left",
                "text-right",
                "pull-left",
                "pull-right"
        };
        return  list_styles;
    }
    public void style_text_center(TagHtml tag, LinearLayout linear_layout){
        linear_layout.setGravity(Gravity.CENTER | Gravity.CENTER_HORIZONTAL);
    }
    public void style_text_left(TagHtml tag, LinearLayout linear_layout){
        linear_layout.setGravity(Gravity.LEFT | Gravity.CENTER_HORIZONTAL);
    }
    public void style_text_right(TagHtml tag, LinearLayout linear_layout){
        linear_layout.setGravity(Gravity.RIGHT | Gravity.CENTER_HORIZONTAL);
    }
    public void style_pull_left(TagHtml tag, LinearLayout linear_layout){
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.FILL_PARENT,
                LinearLayout.LayoutParams.WRAP_CONTENT);
        //linear_layout.setLayoutParams(params);
    }
    public void style_pull_right(TagHtml tag, LinearLayout linear_layout){
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT,
                LinearLayout.LayoutParams.MATCH_PARENT);
        //linear_layout.setLayoutParams(params);
    }
}
