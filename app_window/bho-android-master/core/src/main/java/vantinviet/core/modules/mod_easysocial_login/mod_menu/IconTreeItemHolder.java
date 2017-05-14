package vantinviet.core.modules.mod_easysocial_login.mod_menu;

import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.squareup.picasso.Picasso;
import com.unnamed.b.atv.model.TreeNode;

import java.util.HashMap;
import java.util.Map;

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
    private JMenu redirectMenu;

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

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void setRedirectMenu(JMenu redirectMenu) {
        JApplication app=JFactory.getApplication();
        String link = redirectMenu.getLink();
        Timber.d("menu item %s", redirectMenu.toString());
        Map<String, String> post = new HashMap<String, String>();
        post.put("Itemid", String.valueOf(redirectMenu.getId()));
        app.setRedirect(VTVConfig.getRootUrl().concat("/" + link), post);
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
                padding_top_button=10;
            }
            this.setPadding(level*30,padding_top_button,0,padding_top_button);
            layoutparams.setMargins(level*30,0,0,20);
            this.setLayoutParams(layoutparams);
            this.setBackgroundColor(Color.parseColor("#039BE5"));
            final Button btn_go_to_page = (Button) this.findViewById(R.id.btn_go_to_page);
            if(menu.getTotalChildren()==0) {
                btn_go_to_page.setVisibility(INVISIBLE);
                this.setOnClickListener(new OnClickListener() {
                    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                    @Override
                    public void onClick(View view) {
                        setRedirectMenu(menu);


                    }
                });
            }else{

                btn_go_to_page.setOnClickListener(OnClickGoToPageListener());
            }
        }

        private OnClickListener OnClickGoToPageListener() {
            return new OnClickListener() {
                @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                @Override
                public void onClick(View view) {
                    setRedirectMenu(menu);
                }
            };
        }

        public void setNode(TreeNode node) {
            this.node = node;
        }

        public void setMenu(JMenu menu) {
            this.menu = menu;
        }
    }
}
