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
    String color = "#000000";
    private String height = "100px";
    private String width = "100px";
    private String border_left = "0px";
    private String border_width = "0px";
    private String border_color = "0px";

    private String border_bottom_style = "solid";
    private String border_bottom_color = "solid";
    private String background_color = "#FFFFFF";
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
        int font_size = this.getFont_size();
        String height = this.getHeight();
        int width = this.getWidth();
        String color = this.getColor();
        String border_left_width = this.getBorder_left_width();
        String border_right_width = this.getBorder_right_width();
        String background_color = this.getBackground_color();
        //image_view.getLayoutParams().height = 300;
        LinearLayout.LayoutParams parent_layout_params = new LinearLayout.LayoutParams(width, WRAP_CONTENT);
        if (this.is_numeric(height, null) && this.getIntHeight() == 1) {
            parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 2);
        } else if (this.is_numeric(height, null) && this.getIntHeight() > 1) {
            parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, this.getIntHeight());
        } else if (height.equals("auto")) {
            parent_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
        }


        if (color != null && color.equals("transparent")) {
            //image_view.setColorFilter(Color.parseColor(color));
        } else if (color != null && !color.equals("")) {
            image_view.setColorFilter(Color.parseColor(color));
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
        image_view.setBackgroundDrawable(shape);

        int padding_left = this.getPadding_left();
        int padding_top = this.getPadding_top();
        int padding_right = this.getPadding_right();
        int padding_bottom = this.getPadding_bottom();

        image_view.setPadding(padding_left, padding_top, padding_right, padding_bottom);

        String margin_left = this.getMargin_left();
        String margin_top = this.getMargin_top();
        String margin_right = this.getMargin_right();
        String margin_bottom = this.getMargin_bottom();
        if (margin_left.equals("auto") && margin_right.equals("auto")) {
            parent_linear_layout.setGravity(CENTER);
            parent_layout_params = new LinearLayout.LayoutParams(WRAP_CONTENT, WRAP_CONTENT);
            parent_linear_layout.setLayoutParams(parent_layout_params);
        } else {

        }
        image_view.setLayoutParams(parent_layout_params);
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
    }

    public void apply_style_button_icon(Button current_button, LinearLayout parent_linear_layout, boolean apply_direct_class_path) {
        if (apply_direct_class_path) {
            int font_size = this.getFont_size();
            String height = this.getHeight();
            int width = this.getWidth();
            String color = this.getColor();
            String background_color = this.getBackground_color();
            //image_view.getLayoutParams().height = 300;
            LinearLayout.LayoutParams parent_layout_params = new LinearLayout.LayoutParams(width, WRAP_CONTENT);

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
            int font_size = this.getFont_size();
            String height = this.getHeight();
            int width = this.getWidth();
            JApplication app = JFactory.getApplication();
            String border_left_width = this.getBorder_left_width();
            String border_right_width = this.getBorder_right_width();
            String background_color = this.getBackground_color();
            //image_view.getLayoutParams().height = 300;
            LinearLayout.LayoutParams current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);

            if (this.is_numeric(height, null) && this.getIntHeight() == 1) {
                current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 2);
            } else if (height.equals("auto")) {
                current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
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
            if(background_color!=null&&!background_color.equals(""))
            {
                shape.setColor(Color.parseColor(background_color));
            }
            if (border_width > 0 &&background_color!=null&&!background_color.equals("")) {
                shape.setStroke(border_width, Color.parseColor(border_color));
            }
            linear_layout.setBackgroundDrawable(shape);
            if (background_color != null && !background_color.equals("")) {
                linear_layout.setBackgroundColor(Color.parseColor(background_color));
            }
            int padding_left = this.getPadding_left();
            int padding_top = this.getPadding_top();
            int padding_right = this.getPadding_right();
            int padding_bottom = this.getPadding_bottom();

            linear_layout.setPadding(padding_left, padding_top, padding_right, padding_bottom);

            String margin_left = this.getMargin_left();
            String margin_top = this.getMargin_top();
            String margin_right = this.getMargin_right();
            String margin_bottom = this.getMargin_bottom();
            if (margin_left.equals("auto") && margin_right.equals("auto")) {

                if (this.is_numeric(height, null) && this.getIntHeight() == 1) {
                    current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 2);
                } else if (this.is_numeric(height, null) && this.getIntHeight() > 1) {
                    current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, this.getIntHeight());
                } else if (height.equals("auto")) {
                    current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
                }
                linear_layout.setLayoutParams(current_layout_params);
                linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                linear_layout.setGravity(CENTER);
                //layoutParams.setMargins(margin_left,margin_top,margin_right,margin_bottom);

            } else {

            }
            linear_layout.setLayoutParams(current_layout_params);
        }
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
    }

    public void apply_style(TextView text_view, LinearLayout parent_linear_layout, boolean apply_direct_class_path) {
        int font_size = this.getFont_size();
        text_view.setTextSize(font_size);
        String height = this.getHeight();
        int width = this.getWidth();
        String color = this.getColor();
        String text_align = this.getText_align();
        LinearLayout.LayoutParams current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
        if (text_align.equals("center")) {
            parent_linear_layout.setGravity(CENTER);
        }
        if (text_align.equals("left")) {
            parent_linear_layout.setGravity(Gravity.LEFT);
            current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
        }
        if (text_align.equals("right")) {
            parent_linear_layout.setGravity(Gravity.RIGHT);
            current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
        }
        if (this.is_numeric(height, null) && this.getIntHeight() == 1) {
            current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 2);
        } else if (this.is_numeric(height, null) && this.getIntHeight() > 1) {
            current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, this.getIntHeight());
        } else if (height.equals("auto")) {
            current_layout_params = new LinearLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, WRAP_CONTENT);
        }
        parent_linear_layout.setLayoutParams(current_layout_params);

        String background_color = this.getBackground_color();
        //image_view.getLayoutParams().height = 300;
        if (color != null && !color.equals("")) {
            text_view.setTextColor(Color.parseColor(color));
        }

        String border_top_width = this.getBorder_top_width();
        String border_right_width = this.getBorder_right_width();
        String border_bottom_width = this.getBorder_bottom_width();
        String border_left_width = this.getBorder_left_width();

        int int_border_top_width = 0;
        int int_border_right_width =  0;
        int int_border_bottom_width =  0;
        int int_border_left_width =  0;

        if(is_numeric(border_top_width,null)){
            int_border_top_width=this.getInt_border_top_width(border_top_width);
        }
        if(is_numeric(border_right_width,null)){
            int_border_right_width=this.getInt_border_right_width(border_right_width);
        }

        if(is_numeric(border_bottom_width,null)){
            int_border_bottom_width=this.getInt_border_bottom_width(border_bottom_width);
        }

        if(is_numeric(border_left_width,null)){
            int_border_left_width=this.getInt_border_left_width(border_left_width);
        }


        GradientDrawable shape = new GradientDrawable();

        int border_top_left_radius = this.getBorder_top_left_radius();
        int border_top_right_radius = this.getBorder_top_right_radius();
        int border_bottom_right_radius = this.getBorder_bottom_right_radius();
        int border_bottom_left_radius = this.getBorder_bottom_left_radius();
        int border_width = this.getBorder_width();
        //shape.setCornerRadius(border_top_left_radius);
        shape.setShape(GradientDrawable.RECTANGLE);
        shape.setCornerRadii(new float[]{int_border_top_width, int_border_right_width, int_border_bottom_width, int_border_left_width, border_top_left_radius, border_top_right_radius, border_bottom_right_radius, border_bottom_left_radius});
        shape.setColor(Color.parseColor(background_color));
        if (border_width > 0) {
            String border_color = this.getBorder_color();
            shape.setStroke(border_width, Color.parseColor(border_color));
        }

        parent_linear_layout.setBackgroundDrawable(shape);

        int padding_left = this.getPadding_left();
        int padding_top = this.getPadding_top();
        int padding_right = this.getPadding_right();
        int padding_bottom = this.getPadding_bottom();

        text_view.setPadding(padding_left, padding_top, padding_right, padding_bottom);




        text_view.setPadding(padding_left, padding_top, padding_right, padding_bottom);

        String margin_left = this.getMargin_left();
        String margin_top = this.getMargin_top();
        String margin_right = this.getMargin_right();
        String margin_bottom = this.getMargin_bottom();
        if (margin_left.equals("auto") && margin_right.equals("auto")) {
            //layoutParams.setMargins(margin_left,margin_top,margin_right,margin_bottom);
        } else {

        }
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
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

    public int getWidth() {
        return Integer.parseInt(width.toLowerCase().trim().replaceAll("px", ""));
    }

    public String getColor() {
        return color != null ? color.trim() : "";
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
        return border_color != null ? String.valueOf(border_color).trim() : "#DDDDDD";
    }

    public String getBackground_color() {
        return background_color != null ? String.valueOf(background_color).trim() : "";
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
