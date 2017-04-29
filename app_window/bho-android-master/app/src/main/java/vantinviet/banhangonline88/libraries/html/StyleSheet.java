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
import android.widget.ImageView;
import android.widget.LinearLayout;
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

/**
 * Created by cuongnd on 17/04/2017.
 */

    public class StyleSheet {
    String class_path;
    String border;
    String border_radius="0px";
    String text_align="left";
    String border_top_left_radius="0px";
    String border_top_right_radius="0px";
    String border_bottom_right_radius="0px";
    String border_bottom_left_radius="0px";
    String test_style;
    String font_size="12px";
    String color="#000000";
    private String height="100px";
    private String width="100px";
    private String border_left="0px";
    private String border_width="0px";
    private String border_color="0px";
    private String background_color="#FFFFFF";
    private String padding_left="0px";
    private String padding_top="0px";
    private String padding_right="0px";
    private String padding_bottom="0px";
    private String margin_left="0px";
    private String margin_top="0px";
    private String margin_right="0px";
    private String margin_bottom="0px";

    public static Map<String,StyleSheet> get_list_style_sheet(String style) {
        Map<String, StyleSheet> list_style_sheet = new HashMap<String, StyleSheet>();
        //Timber.d("style_product object %s",style.toString());
        try {
            JSONObject jsonObject= new JSONObject(style);
            Iterator<?> keys = jsonObject.keys();
            while(keys.hasNext() ) {
                String key = (String)keys.next();
                //Timber.d("key %s",key.toString());
                if ( jsonObject.get(key) instanceof JSONObject ) {
                    String value_of_key=jsonObject.get(key).toString();
                    Gson gson=new Gson();
                    StyleSheet style_sheet= gson.fromJson(value_of_key, StyleSheet.class);
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

    public void apply_style(ImageView image_view,LinearLayout parent_linear_layout) {
        int font_size = this.getFont_size();
        int height = this.getHeight();
        int width = this.getWidth();
        String color = this.getColor();
        String border_left_top = this.getBorder_left();
        String border_right = this.getBorder_left();
        String background_color = this.getBackground_color();
        //image_view.getLayoutParams().height = 300;
        LinearLayout.LayoutParams layoutParams = new LinearLayout.LayoutParams(height, width);

        if (color != null && !color.equals("")) {
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

        image_view.setPadding(padding_left,padding_top,padding_right,padding_bottom);

        int margin_left = this.getMargin_left();
        int margin_top = this.getMargin_top();
        int margin_right = this.getMargin_right();
        int margin_bottom = this.getMargin_bottom();
        layoutParams.setMargins(margin_left,margin_top,margin_right,margin_bottom);
        image_view.setLayoutParams(layoutParams);
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
    }
    public void apply_style(LinearLayout linear_layout) {
        int font_size = this.getFont_size();
        int height = this.getHeight();
        int width = this.getWidth();
        String border_left_top = this.getBorder_left();
        String border_right = this.getBorder_left();
        String background_color = this.getBackground_color();
        //image_view.getLayoutParams().height = 300;
        LinearLayout.LayoutParams layoutParams = new LinearLayout.LayoutParams(height, width);

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
        linear_layout.setBackgroundDrawable(shape);

        int padding_left = this.getPadding_left();
        int padding_top = this.getPadding_top();
        int padding_right = this.getPadding_right();
        int padding_bottom = this.getPadding_bottom();

        linear_layout.setPadding(padding_left,padding_top,padding_right,padding_bottom);

        int margin_left = this.getMargin_left();
        int margin_top = this.getMargin_top();
        int margin_right = this.getMargin_right();
        int margin_bottom = this.getMargin_bottom();
        layoutParams.setMargins(margin_left,margin_top,margin_right,margin_bottom);
        linear_layout.setLayoutParams(layoutParams);
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
    }

    public void apply_style(TextView text_view, LinearLayout parent_linear_layout) {
        int font_size = this.getFont_size();
        text_view.setTextSize(font_size);
        int height = this.getHeight();
        int width = this.getWidth();
        String color = this.getColor();
        String text_align = this.getText_align();
        if(text_align.equals("center"))
        {
            text_view.setGravity(Gravity.CENTER_VERTICAL | Gravity.CENTER_HORIZONTAL);
        }
        String border_left_top = this.getBorder_left();
        String border_right = this.getBorder_left();
        String background_color = this.getBackground_color();
        //image_view.getLayoutParams().height = 300;
        LinearLayout.LayoutParams layoutParams = (LinearLayout.LayoutParams) text_view.getLayoutParams();
        if (color != null && !color.equals("")) {
            text_view.setTextColor(Color.parseColor(color));
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
        //text_view.setBackgroundDrawable(shape);

        int padding_left = this.getPadding_left();
        int padding_top = this.getPadding_top();
        int padding_right = this.getPadding_right();
        int padding_bottom = this.getPadding_bottom();

        text_view.setPadding(padding_left, padding_top, padding_right, padding_bottom);

        int margin_left = this.getMargin_left();
        int margin_top = this.getMargin_top();
        int margin_right = this.getMargin_right();
        int margin_bottom = this.getMargin_bottom();
        layoutParams.setMargins(margin_left, margin_top, margin_right, margin_bottom);
        text_view.setLayoutParams(layoutParams);
        //image_view.setLayoutParams(new ViewGroup.LayoutParams));
    }

    public int getFont_size() {
        return Integer.parseInt(font_size.toLowerCase().trim().replaceAll("px",""));
    }

    public int getHeight() {
        return Integer.parseInt(height.toLowerCase().trim().replaceAll("px",""));
    }
    public int getWidth() {
        return Integer.parseInt(width.toLowerCase().trim().replaceAll("px",""));
    }

    public String getColor() {
        return color!=null?color.trim():"";
    }

    public String getBorder_left() {
        return border_left;
    }

    public int getBorder_top_left_radius() {
        return Integer.parseInt(border_top_left_radius.toLowerCase().trim().replaceAll("px",""));
    }
    public int getBorder_top_right_radius() {
        return Integer.parseInt(border_top_right_radius.toLowerCase().trim().replaceAll("px",""));
    }
    public int getBorder_bottom_right_radius() {
        return Integer.parseInt(border_bottom_right_radius.toLowerCase().trim().replaceAll("px",""));
    }
    public int getBorder_bottom_left_radius() {
        return Integer.parseInt(border_bottom_left_radius.toLowerCase().trim().replaceAll("px",""));
    }

    public int getBorder_width() {
        return Integer.parseInt(String.valueOf(border_width).toLowerCase().trim().replaceAll("px",""));
    }

    public String getBorder_color() {
        return border_color!=null?String.valueOf(border_color).trim():"#DDDDDD";
    }

    public String getBackground_color() {
        return background_color!=null?String.valueOf(background_color).trim():"#DDDDDD";
    }

    public int getPadding_left() {
        return Integer.parseInt(String.valueOf(padding_left).toLowerCase().trim().replaceAll("px",""));
    }

    public int getPadding_top() {
        return Integer.parseInt(String.valueOf(padding_top).toLowerCase().trim().replaceAll("px",""));
    }

    public int getPadding_right() {
        return Integer.parseInt(String.valueOf(padding_right).toLowerCase().trim().replaceAll("px",""));
    }

    public int getPadding_bottom() {
        return Integer.parseInt(String.valueOf(padding_bottom).toLowerCase().trim().replaceAll("px",""));
    }

    public int getMargin_left() {
        return Integer.parseInt(String.valueOf(margin_left).toLowerCase().trim().replaceAll("px",""));
    }

    public int getMargin_top() {
        return Integer.parseInt(String.valueOf(margin_top).toLowerCase().trim().replaceAll("px",""));
    }

    public int getMargin_right() {
        return Integer.parseInt(String.valueOf(margin_right).toLowerCase().trim().replaceAll("px",""));
    }

    public int getMargin_bottom() {
        return Integer.parseInt(String.valueOf(margin_bottom).toLowerCase().trim().replaceAll("px",""));
    }

    public String getText_align() {
        return text_align!=null?String.valueOf(text_align).trim():"";
    }
}
