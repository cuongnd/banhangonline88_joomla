package vantinviet.core.libraries.html.module;

import vantinviet.core.libraries.cms.module.JModuleHelper;

/**
 * Created by cuongnd on 25/03/2017.
 */

public class Params {
    String layout;
    String cache;
    String cache_time;
    String module_image;
    String module_image_tip;
    String lazyload;
    private String androidRender="auto";
    private String _android_render_form_type= JModuleHelper.ANDROID_RENDER_FORM_HTML;

    @Override
    public String toString() {
        return "Params{" +
                ", layout='" + layout + '\'' +
                ", cache='" + cache + '\'' +
                ", cache_time='" + cache_time + '\'' +
                ", module_image='" + module_image + '\'' +
                ", module_image_tip='" + module_image_tip + '\'' +
                ", lazyload='" + lazyload + '\'' +
                '}';
    }
    @Override
    public boolean equals(Object o) {
        return  true;

    }

    public String getAndroidRender() {
        return androidRender;
    }

    public String get_android_render_form_type() {
        return _android_render_form_type;
    }
}
