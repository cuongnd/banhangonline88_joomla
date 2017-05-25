package vantinviet.core.modules.mod_header.tmpl;

import android.content.Context;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.view.View;
import android.webkit.URLUtil;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.squareup.picasso.Picasso;

import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.html.StyleSheet;
import vantinviet.core.libraries.html.TagHtml;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class m_default extends LinearLayout {
    private LinearLayout m_default_view;
    JApplication app= JFactory.getApplication();
    LayoutParams layout_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );

    public m_default(Context context, Module module) {
        super(context);
        final String module_content = module.getContent();

        m_default_view = (LinearLayout) inflate(getContext(), R.layout.modules_mod_header_tmpl_m_default, this);
        ModuleResponse moduleresponse = JUtilities.getGsonParser().fromJson(module_content, ModuleResponse.class);

        String logo_url = moduleresponse.getLogo();
        if(!URLUtil.isValidUrl(logo_url)){
            logo_url= VTVConfig.getRootUrl().concat("/"+logo_url);
        }
        Timber.d("logo_url %s",logo_url);
        ImageView image_view_logo=(ImageView)m_default_view.findViewById(R.id.image_view_logo);
        image_view_logo.setOnClickListener(new OnClickListener() {
            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onClick(View v) {
                app.setRedirect(VTVConfig.getRootUrl());
            }
        });
        Picasso.with(app.getCurrentActivity()).load(logo_url).into(image_view_logo);
        image_view_logo.getLayoutParams().height = 400;
        this.setLayoutParams(layout_params);
    }


    private class ModuleResponse {
        String logo="";

        public String getLogo() {
            return logo;
        }
        @Override
        public String toString() {
            return "ModuleResponse{" +
                    "logo=" + logo +
                    '}';
        }
    }
}
