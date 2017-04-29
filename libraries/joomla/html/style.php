<?php

/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 4/29/2017
 * Time: 7:02 PM
 */
class style
{
    public static function border_radius(&$list_style,$style){
        preg_match_all ("/(.*)px/U", $style, $pat_array);
        $pat_array=$pat_array[1];
        switch (count($pat_array)) {
            case 1:
                $list_style->border_top_left_radius=$pat_array[0]."px";
                $list_style->border_top_right_radius=$pat_array[0]."px";
                $list_style->border_bottom_right_radius=$pat_array[0]."px";
                $list_style->border_bottom_left_radius=$pat_array[0]."px";
                break;
            case 2:
                $list_style->border_top_left_radius=$pat_array[0]."px";
                $list_style->border_top_right_radius=$pat_array[1]."px";
                $list_style->border_bottom_right_radius=$pat_array[0]."px";
                $list_style->border_bottom_left_radius=$pat_array[1]."px";
                break;
            case 3:
                $list_style->border_top_left_radius=$pat_array[0]."px";
                $list_style->border_top_right_radius=$pat_array[1]."px";
                $list_style->border_bottom_right_radius=$pat_array[2]."px";
                $list_style->border_bottom_left_radius=$pat_array[2]."px";
                break;
            case 4:
                $list_style->border_top_left_radius=$pat_array[0]."px";
                $list_style->border_top_right_radius=$pat_array[1]."px";
                $list_style->border_bottom_right_radius=$pat_array[2]."px";
                $list_style->border_bottom_left_radius=$pat_array[3]."px";
                break;
        }
    }
    public static function padding(&$list_style,$style){
        preg_match_all ("/(.*)px/U", $style, $pat_array);
        $pat_array=$pat_array[1];
        switch (count($pat_array)) {
            case 1:
                $list_style->padding_left=$pat_array[0]."px";
                $list_style->padding_top=$pat_array[0]."px";
                $list_style->padding_right=$pat_array[0]."px";
                $list_style->padding_bottom=$pat_array[0]."px";
                break;
            case 2:
                $list_style->padding_left=$pat_array[0]."px";
                $list_style->padding_top=$pat_array[1]."px";
                $list_style->padding_right=$pat_array[0]."px";
                $list_style->padding_bottom=$pat_array[1]."px";
                break;
            case 3:
                $list_style->padding_left=$pat_array[0]."px";
                $list_style->padding_top=$pat_array[1]."px";
                $list_style->padding_right=$pat_array[2]."px";
                $list_style->padding_bottom=$pat_array[2]."px";
                break;
            case 4:
                $list_style->padding_left=$pat_array[0]."px";
                $list_style->padding_top=$pat_array[1]."px";
                $list_style->padding_right=$pat_array[2]."px";
                $list_style->padding_bottom=$pat_array[3]."px";
                break;
        }
    }
    public static function margin(&$list_style,$style){
        preg_match_all ("/(.*)px/U", $style, $pat_array);
        $pat_array=$pat_array[1];
        switch (count($pat_array)) {
            case 1:
                $list_style->margin_left=$pat_array[0]."px";
                $list_style->margin_top=$pat_array[0]."px";
                $list_style->margin_right=$pat_array[0]."px";
                $list_style->margin_bottom=$pat_array[0]."px";
                break;
            case 2:
                $list_style->margin_left=$pat_array[0]."px";
                $list_style->margin_top=$pat_array[1]."px";
                $list_style->margin_right=$pat_array[0]."px";
                $list_style->margin_bottom=$pat_array[1]."px";
                break;
            case 3:
                $list_style->margin_left=$pat_array[0]."px";
                $list_style->margin_top=$pat_array[1]."px";
                $list_style->margin_right=$pat_array[2]."px";
                $list_style->margin_bottom=$pat_array[2]."px";
                break;
            case 4:
                $list_style->margin_left=$pat_array[0]."px";
                $list_style->margin_top=$pat_array[1]."px";
                $list_style->margin_right=$pat_array[2]."px";
                $list_style->margin_bottom=$pat_array[3]."px";
                break;
        }
    }
    public static function border(&$list_style,$style)
    {
        $style = preg_split('/\s+/', trim($style));
        $list_style->border_width = $style[0];
        $style[1]?$list_style->border_style= $style[1]:"";
        $style[2]?$list_style->border_color=$style[2]:"";
    }
}