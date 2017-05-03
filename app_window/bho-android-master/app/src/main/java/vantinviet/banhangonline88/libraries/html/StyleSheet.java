package vantinviet.banhangonline88.libraries.html;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.BitmapShader;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Shader;
import android.graphics.drawable.GradientDrawable;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.google.gson.Gson;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.libraries.utilities.ImageConverter;

import static android.R.attr.bitmap;
import static android.R.attr.numberPickerStyle;
import static android.R.attr.theme;
import static android.view.Gravity.CENTER;
import static android.view.Gravity.LEFT;
import static android.view.ViewGroup.LayoutParams.MATCH_PARENT;
import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;

/**
 * Created by cuongnd on 17/04/2017.
 */

public class StyleSheet {
    String class_path;
    String border;
    String border_radius = "0px";
    String text_align = "left";
    String border_top_left_radius = "0px";
    String border_top_right_radius = "0px";
    String border_bottom_right_radius = "0px";
    String border_bottom_left_radius = "0px";
    String test_style;
    String font_size = "12px";
    String color = "";
    private String height = "100px";
    private String width = "100px";
    private String border_left = "0px";
    private String border_width = "";
    private String border_color = "";

    private String border_bottom_style = "solid";
    private String border_bottom_color = "solid";
    private String background_color = "";
    private String padding_left = "0px";
    private String padding_top = "0px";
    private String padding_right = "0px";
    private String padding_bottom = "0px";
    private String margin_left = "0px";
    private String margin_top = "0px";
    private String margin_right = "0px";
    private String margin_bottom = "0px";
    private String border_bottom_width = "0px";
    private String border_left_width="0px";
    private String border_right_width="0px";
    private String border_top_width="0px";

    public static Map<String, StyleSheet> get_list_style_sheet(String style) {
        Map<String, StyleSheet> list_style_sheet = new HashMap<String, StyleSheet>();
        //Timber.d("style_product object %s",style.toString());
        try {
            JSONObject jsonObject = new JSONObject(style);
            Iterator<?> keys = jsonObject.keys();
            while (keys.hasNext()) {
                String key = (String) keys.next();
                //Timber.d("key %s",key.toString());
                if (jsonObject.get(key) instanceof JSONObject) {
                    String value_of_key = jsonObject.get(key).toString();
                    Gson gson = new Gson();
                    StyleSheet style_sheet = gson.fromJson(value_of_key, StyleSheet.class);
                    list_style_sheet.put(key, style_sheet);
                }
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return list_style_sheet;
    }

    @Override
    public String toString() {
        return "StyleSheet{" +
                "class_path=" + class_path +
                ",border=" + border +
                ", color=" + color +
                '}';
    }

    public void setClass_path(String class_path) {
        this.class_path = class_path;
    }

    public void apply_style(ImageView image_view, LinearLayout parent_linear_layout, boolean apply_direct_class_path) {
        if(apply_direct_class_path) {
            apply_style_image_view(image_view,apply_direct_class_path);
            apply_style_linear_layout(parent_linear_layout,apply_direct_class_path);
        }else {

        }
    }

    public void apply_style_button_icon(Button current_button, LinearLayout parent_linear_layout, boolean apply_direct_class_path) {
        if (apply_direct_class_path) {
            int font_size = this.getFont_size();
            String height = this.getHeight();
            String width = this.getWidth();
            String color = this.getColor();
            String background_color = this.getBackground_color();
            //image_view.getLayoutParams().height = 300;
            LinearLayout.LayoutParams parent_layout_params;
            if(is_numeric(width,"%"))
            {
                parent_layout_params = new LinearLayout.LayoutParams(this.getIntWidth("%")/100, WRAP_CONTENT);
            }else if(is_numeric(width,"px")){
                parent_layout_params = new LinearLayout.LayoutParams(this.getIntWidth("px"), WRAP_CONTENT);
            }

            if (this.is_numeric(height, null) && this.getIntHeight() == 1) {
                parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 2);
            } else if (this.is_numeric(height, null) && this.getIntHeight() > 1) {
                parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, this.getIntHeight());
            } else if (height.equals("auto")) {
                parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
            }

            current_button.setBackgroundColor(0);
            if (color != null && color.equals("transparent")) {

            } else if (color != null && !color.equals("")) {
                //image_view.setColorFilter(Color.parseColor(color));
            }
            GradientDrawable shape = new GradientDrawable();

            int border_top_left_radius = this.getBorder_top_left_radius();
            int border_top_right_radius = this.getBorder_top_right_radius();
            int border_bottom_right_radius = this.getBorder_bottom_right_radius();
            int border_bottom_left_radius = this.getBorder_bottom_left_radius();
            int border_width = this.getBorder_width();
            //shape.setCornerRadius(border_top_left_radius);
            shape.setShape(GradientDrawable.RECTANGLE);
            shape.setCornerRadii(new float[]{border_top_left_radius, border_top_right_radius, border_bottom_right_radius, border_bottom_left_radius, border_top_left_radius, border_top_right_radius, border_bottom_right_radius, border_bottom_left_radius});
            shape.setColor(Color.parseColor(background_color));
            if (border_width > 0) {
                String border_color = this.getBorder_color();
                shape.setStroke(border_width, Color.parseColor(border_color));
            }
            //current_button.setBackgroundDrawable(shape);

            int padding_left = this.getPadding_left();
            int padding_top = this.getPadding_top();
            int padding_right = this.getPadding_right();
            int padding_bottom = this.getPadding_bottom();

            current_button.setPadding(padding_left, padding_top, padding_right, padding_bottom);

            String margin_left = this.getMargin_left();
            String margin_top = this.getMargin_top();
            String margin_right = this.getMargin_right();
            String margin_bottom = this.getMargin_bottom();
            if (margin_left.equals("auto") && margin_right.equals("auto")) {
                parent_linear_layout.setGravity(CENTER);
                parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
                parent_linear_layout.setLayoutParams(parent_layout_params);
                current_button.setLayoutParams(new LinearLayout.LayoutParams(
                        LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT));
            } else {

            }
            //current_button.setLayoutParams(parent_layout_params);
        }
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
    }

    public void apply_style(LinearLayout linear_layout, boolean apply_direct_class_path) {
        if (apply_direct_class_path) {
            apply_style_linear_layout(linear_layout,apply_direct_class_path);
        }else{

        }
    }

    public void apply_style(TextView text_view, LinearLayout parent_linear_layout, boolean apply_direct_class_path) {
        if(apply_direct_class_path) {
            apply_style_text_view(text_view,apply_direct_class_path);
            apply_style_linear_layout(parent_linear_layout,apply_direct_class_path);
        }else{

        }

    }
    public void apply_style_text_view(TextView text_view, boolean apply_direct_class_path) {
        if(apply_direct_class_path) {
            //set for textview
            int text_view_font_size = this.getFont_size();
            int text_view_layout_params_width = WRAP_CONTENT;
            int text_view_layout_params_height = WRAP_CONTENT;
            int text_view_gravity = LEFT;
            //set font size text_view
            text_view.setTextSize(text_view_font_size);
            //set text align text_view
            String text_view_text_align = this.getText_align();
            if (text_view_text_align.equals("center")) {
                text_view_gravity = Gravity.CENTER;
                text_view_layout_params_width = MATCH_PARENT;
            }
            if (text_view_text_align.equals("left")) {
                text_view_gravity = Gravity.LEFT;
                text_view_layout_params_width = WRAP_CONTENT;
            }
            if (text_view_text_align.equals("right")) {
                text_view_gravity = Gravity.RIGHT;
                text_view_layout_params_width = WRAP_CONTENT;
            }
            text_view.setGravity(text_view_gravity);
            LinearLayout.LayoutParams text_view_layout_params = new LinearLayout.LayoutParams(text_view_layout_params_width, text_view_layout_params_height);
            text_view.setLayoutParams(text_view_layout_params);
            String text_view_color = this.getColor();
            if (text_view_color != null && !text_view_color.equals("")) {
                text_view.setTextColor(Color.parseColor(text_view_color));
            }
        }

    }
    public void apply_style_image_view(ImageView image_view, boolean apply_direct_class_path) {
        if(apply_direct_class_path) {
            //set for textview
            int image_view_layout_params_width = WRAP_CONTENT;
            int image_view_layout_params_height = WRAP_CONTENT;
            int image_view_gravity = LEFT;
            //set text align image_view
            String image_view_text_align = this.getText_align();
            if (image_view_text_align.equals("center")) {
                image_view_gravity = Gravity.CENTER;
                image_view_layout_params_width = MATCH_PARENT;
            }
            if (image_view_text_align.equals("left")) {
                image_view_gravity = Gravity.LEFT;
                image_view_layout_params_width = WRAP_CONTENT;
            }
            if (image_view_text_align.equals("right")) {
                image_view_gravity = Gravity.RIGHT;
                image_view_layout_params_width = WRAP_CONTENT;
            }
            LinearLayout.LayoutParams image_view_layout_params = new LinearLayout.LayoutParams(image_view_layout_params_width, image_view_layout_params_height);
            image_view.setLayoutParams(image_view_layout_params);

        }

    }
    public void apply_style_linear_layout(LinearLayout linear_layout, boolean apply_direct_class_path) {
        if(apply_direct_class_path) {
            String linear_width = this.getWidth();
            int linear_layout_params_width = MATCH_PARENT;
            int linear_layout_params_height = MATCH_PARENT;
            int linear_gravity = LEFT;
            LinearLayout.LayoutParams layout_params = new LinearLayout.LayoutParams(linear_layout_params_width, linear_layout_params_height);
            String linear_border_top_width = this.getBorder_top_width();
            String linear_border_right_width = this.getBorder_right_width();
            String linear_border_bottom_width = this.getBorder_bottom_width();
            String linear_border_left_width = this.getBorder_left_width();

            int int_linear_border_top_width = 0;
            int int_linear_border_right_width = 0;
            int int_linear_border_bottom_width = 0;
            int int_linear_border_left_width = 0;

            if (is_numeric(linear_border_top_width, null)) {
                int_linear_border_top_width = this.getInt_border_top_width(linear_border_top_width);
            }
            if (is_numeric(linear_border_right_width, null)) {
                int_linear_border_right_width = this.getInt_border_right_width(linear_border_right_width);
            }

            if (is_numeric(linear_border_bottom_width, null)) {
                int_linear_border_bottom_width = this.getInt_border_bottom_width(linear_border_bottom_width);
            }

            if (is_numeric(linear_border_left_width, null)) {
                int_linear_border_left_width = this.getInt_border_left_width(linear_border_left_width);
            }


            GradientDrawable linear_shape = new GradientDrawable();

            int linear_border_top_left_radius = this.getBorder_top_left_radius();
            int linear_border_top_right_radius = this.getBorder_top_right_radius();
            int linear_border_bottom_right_radius = this.getBorder_bottom_right_radius();
            int linear_border_bottom_left_radius = this.getBorder_bottom_left_radius();
            int linear_border_width = this.getBorder_width();
            linear_shape.setShape(GradientDrawable.RECTANGLE);
            linear_shape.setCornerRadii(new float[]{int_linear_border_top_width, int_linear_border_right_width, int_linear_border_bottom_width, int_linear_border_left_width, linear_border_top_left_radius, linear_border_top_right_radius, linear_border_bottom_right_radius, linear_border_bottom_left_radius});
            String linear_border_color = this.getBorder_color();
            if(linear_border_color!=null&&!linear_border_color.equals("")) {
                linear_shape.setColor(Color.parseColor(linear_border_color));
                linear_shape.setStroke(linear_border_width, Color.parseColor(linear_border_color));
            }
            linear_layout.setBackgroundDrawable(linear_shape);

            //set height
            String linear_layout_height = this.getHeight();
            if (this.is_numeric(linear_layout_height, null) && this.getIntHeight() == 1) {
                linear_layout_params_height = 2;
            } else if (this.is_numeric(linear_layout_height, null) && this.getIntHeight() > 1) {
                linear_layout_params_height = this.getIntHeight();
            } else if (linear_layout_height.equals("auto")) {
                linear_layout_params_height = WRAP_CONTENT;
            }


            int linear_padding_left = this.getPadding_left();
            int linear_padding_top = this.getPadding_top();
            int linear_padding_right = this.getPadding_right();
            int linear_padding_bottom = this.getPadding_bottom();
            linear_layout.setPadding(linear_padding_left, linear_padding_top, linear_padding_right, linear_padding_bottom);

            String linear_layout_margin_left = this.getMargin_left();
            String linear_layout_margin_top = this.getMargin_top();
            String linear_layout_margin_right = this.getMargin_right();
            String linear_layout_margin_bottom = this.getMargin_bottom();
            if (linear_layout_margin_left.equals("auto") && linear_layout_margin_right.equals("auto")) {
                linear_gravity=CENTER;
            } else if(is_numeric(linear_layout_margin_top,"px")&&is_numeric(linear_layout_margin_right,"px")&&is_numeric(linear_layout_margin_bottom,"px")&&is_numeric(linear_layout_margin_right,"px")) {
                layout_params.setMargins(getInt(linear_layout_margin_left,"px"),getInt(linear_layout_margin_top,"px"),getInt(linear_layout_margin_right,"px"),getInt(linear_layout_margin_bottom,"px"));
            }
            if (is_numeric(linear_width, "%")) {
                linear_layout_params_width = this.getIntWidth("%") / 100;
            } else if (is_numeric(linear_width, "px")) {
                linear_layout_params_width = this.getIntWidth("px") / 100;
            }
            layout_params = new LinearLayout.LayoutParams(linear_layout_params_width, linear_layout_params_height);
            String linear_background_color = this.getBackground_color();
            if(linear_background_color!=null&&!linear_background_color.equals(""))
            {
                linear_layout.setBackgroundColor(Color.parseColor(linear_background_color));
            }
            linear_layout.setGravity(linear_gravity);
            linear_layout.setLayoutParams(layout_params);
        }else{

        }

    }

    private int getInt(String var, String type) {
        return Integer.parseInt(var.toLowerCase().trim().replaceAll(type, ""));
    }

    private int getInt_border_top_width(String border_top_width) {
        return Integer.parseInt(border_top_width.toLowerCase().trim().replaceAll("px", ""));
    }

    private int getInt_border_right_width(String border_right_width) {
        return Integer.parseInt(border_right_width.toLowerCase().trim().replaceAll("px", ""));
    }

    private int getInt_border_bottom_width(String border_bottom_width) {
        return Integer.parseInt(border_bottom_width.toLowerCase().trim().replaceAll("px", ""));
    }

    private int getInt_border_left_width(String border_left_width) {
        return Integer.parseInt(border_left_width.toLowerCase().trim().replaceAll("px", ""));
    }

    public int getFont_size() {
        return Integer.parseInt(font_size.toLowerCase().trim().replaceAll("px", ""));
    }

    public String getHeight() {
        return height;
    }

    public int getIntHeight() {
        return Integer.parseInt(height.toLowerCase().trim().replaceAll("px", ""));
    }

    public boolean is_numeric(String var, String type) {
        if (type == null) {
            type = "px";
        }
        return var.contains(type);
    }

    public String getWidth() {
        return width;
    }
    public int getIntWidth(String type) {
        if(type==null)
            type="px";
        return Integer.parseInt(width.toLowerCase().trim().replaceAll(type, ""));
    }

    public String getColor() {
        return color;
    }

    public String getBorder_left_wdith() {
        return border_left_width;
    }

    public int getBorder_top_left_radius() {
        return Integer.parseInt(border_top_left_radius.toLowerCase().trim().replaceAll("px", ""));
    }

    public int getBorder_top_right_radius() {
        return Integer.parseInt(border_top_right_radius.toLowerCase().trim().replaceAll("px", ""));
    }

    public int getBorder_bottom_right_radius() {
        return Integer.parseInt(border_bottom_right_radius.toLowerCase().trim().replaceAll("px", ""));
    }

    public int getBorder_bottom_left_radius() {
        return Integer.parseInt(border_bottom_left_radius.toLowerCase().trim().replaceAll("px", ""));
    }

    public int getBorder_width() {
        return Integer.parseInt(String.valueOf(border_width).toLowerCase().trim().replaceAll("px", ""));
    }

    public String getBorder_color() {
        return border_color;
    }

    public String getBackground_color() {
        return background_color;
    }

    public int getPadding_left() {
        return Integer.parseInt(String.valueOf(padding_left).toLowerCase().trim().replaceAll("px", ""));
    }

    public int getPadding_top() {
        return Integer.parseInt(String.valueOf(padding_top).toLowerCase().trim().replaceAll("px", ""));
    }

    public int getPadding_right() {
        return Integer.parseInt(String.valueOf(padding_right).toLowerCase().trim().replaceAll("px", ""));
    }

    public int getPadding_bottom() {
        return Integer.parseInt(String.valueOf(padding_bottom).toLowerCase().trim().replaceAll("px", ""));
    }

    public String getMargin_left() {
        return margin_left;
    }

    public int getIntMargin_left() {
        return Integer.parseInt(String.valueOf(margin_left).toLowerCase().trim().replaceAll("px", ""));
    }

    public String getMargin_top() {
        return margin_top;
    }

    public int getIntMargin_top() {
        return Integer.parseInt(String.valueOf(margin_top).toLowerCase().trim().replaceAll("px", ""));
    }

    public String getMargin_right() {
        return margin_right;
    }

    public int getIntMargin_right() {
        return Integer.parseInt(String.valueOf(margin_right).toLowerCase().trim().replaceAll("px", ""));
    }

    public String getMargin_bottom() {
        return margin_bottom;
    }

    public int getIntMargin_bottom() {
        return Integer.parseInt(String.valueOf(margin_bottom).toLowerCase().trim().replaceAll("px", ""));
    }

    public String getText_align() {
        return text_align != null ? String.valueOf(text_align).trim() : "";
    }

    public String getClass_path() {
        return class_path;
    }

    public String getBorder_left_width() {
        return border_left_width;
    }

    public String getBorder_right_width() {
        return border_right_width;
    }

    public String getBorder_top_width() {
        return border_top_width;
    }

    public String getBorder_bottom_width() {
        return border_bottom_width;
    }
}
