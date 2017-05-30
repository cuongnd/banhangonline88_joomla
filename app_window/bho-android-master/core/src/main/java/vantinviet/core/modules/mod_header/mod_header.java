package vantinviet.core.modules.mod_header;

import android.widget.LinearLayout;

import timber.log.Timber;
import vantinviet.core.libraries.cms.module.JModuleHelper;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.html.module.Params;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_header {


    private final Module module;
    private final LinearLayout linear_layout;
    JApplication app= JFactory.getApplication();
    public mod_header(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        String content=this.module.getContent();
        if(content.isEmpty()){
            Timber.d("content module %s(%d) is empty",module.getModuleName(),module.getId());
            return;
        }
        Params params=module.getParams();

        String layout=params.getLayout();

        JModuleHelper.render_layout(this.module,this.linear_layout,layout);
    }

}
