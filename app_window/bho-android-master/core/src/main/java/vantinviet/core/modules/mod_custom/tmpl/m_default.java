package vantinviet.core.modules.mod_custom.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.LinearLayout;

import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.libraries.html.StyleSheet;
import vantinviet.core.libraries.html.TagHtml;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.user.JUser;
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
        final String module_content=module.getContent();

        m_default_view = (LinearLayout)inflate(getContext(), R.layout.modules_mod_custom_tmpl_m_default, this);
        return;
        /*TagHtml html= JUtilities.getGsonParser().fromJson(module_content, TagHtml.class);
        Timber.d("html module custom %s",module_content);
        Timber.d("html tag module custom %s",html.toString());
        Map<String, StyleSheet> list_style_sheet = new HashMap<String, StyleSheet>();
        String style_module=module.getStyle_module();
        list_style_sheet=StyleSheet.get_list_style_sheet(style_module);
        LinearLayout root=new LinearLayout(app.getContext());
        TagHtml.get_html_linear_layout(html,root,list_style_sheet);
        m_default_view.addView(root);
        this.setLayoutParams(layout_params);*/
    }




}
