package vantinviet.core.modules.mod_menu;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.squareup.picasso.Picasso;
import com.unnamed.b.atv.model.TreeNode;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.cms.menu.JMenuparams;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

import static android.view.ViewGroup.LayoutParams.MATCH_PARENT;


/**
 * Created by Bogdan Melnychuk on 2/12/15.
 */
    public class IconTreeItemHolder extends TreeNode.BaseNodeViewHolder<JMenu> {

    TreeNode node;
    public IconTreeItemHolder(Context context) {
        super(context);
    }

    @Override
    public View createNodeView(TreeNode node, JMenu menu) {
        JApplication app = JFactory.getApplication();
        this.node = node;
        MenuLinearLayout menu_linear_layout = new MenuLinearLayout(app.getContext());
        menu_linear_layout.setNode(node);
        menu_linear_layout.setMenu(menu);
        menu_linear_layout.init();
        TextView menu_title = (TextView) menu_linear_layout.findViewById(R.id.txt_menu_item);
        ImageView menu_icon = (ImageView) menu_linear_layout.findViewById(R.id.menu_icon);
        JMenuparams params = menu.getParams();
        String menu_image_path = "/" + params.getMenu_image();
        Picasso.with(app.getContext()).load(VTVConfig.rootUrl.concat(menu_image_path)).into(menu_icon);
        menu_title.setText(menu.getTitle());
        return menu_linear_layout;
    }

    private class MenuLinearLayout  extends LinearLayout{

        public TreeNode node;
        public JMenu menu;
        public JApplication app=JFactory.getApplication();
        public MenuLinearLayout(Context context) {
            super(context);
            inflate(getContext(), R.layout.modules_mod_menu_tmpl_homeverticalmenutag_menu_item, this);

        }
        public void init(){
            LayoutParams layoutparams=new LayoutParams(MATCH_PARENT,MATCH_PARENT);
            int level=menu.getLevel();
            int padding_top_button=10;
            if(level>1){
                padding_top_button=20;
            }
            this.setPadding(10,padding_top_button,0,padding_top_button);
            layoutparams.setMargins(level*30,0,0,20);
            this.setLayoutParams(layoutparams);
            this.setBackgroundColor(Color.parseColor("#039BE5"));
            if(menu.getTotalChildren()==0) {
                this.setOnClickListener(new OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        String link=menu.getLink();
                        app.setRedirect(VTVConfig.getRootUrl().concat("/"+link));
                    }
                });
            }
        }
        public void setNode(TreeNode node) {
            this.node = node;
        }

        public void setMenu(JMenu menu) {
            this.menu = menu;
        }
    }
}
