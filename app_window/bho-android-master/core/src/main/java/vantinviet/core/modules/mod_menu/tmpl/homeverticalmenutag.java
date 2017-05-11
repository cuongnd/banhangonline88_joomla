package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.PopupWindow;

import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.google.gson.stream.JsonReader;
import com.unnamed.b.atv.model.TreeNode;
import com.unnamed.b.atv.view.AndroidTreeView;

import java.io.StringReader;
import java.lang.reflect.Type;
import java.sql.Time;
import java.util.ArrayList;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.components.com_hikashop.views.category.tmpl.listing;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.html.bootstrap.Row;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.modules.mod_menu.IconTreeItemHolder;
import vantinviet.core.modules.mod_slideshowck.Slider;

import static android.widget.ListPopupWindow.MATCH_PARENT;
import static android.widget.ListPopupWindow.WRAP_CONTENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class homeverticalmenutag extends LinearLayout {
    LinearLayout linear_layout_wrapper_menu;
    boolean show =true;
    JApplication app= JFactory.getApplication();
    TreeNode root;
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public homeverticalmenutag(Context context, Module module) {
        super(context);
        inflate(getContext(), R.layout.modules_mod_menu_tmpl_homeverticalmenutag, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        Button button=(Button)this.findViewById(R.id.show_menu);
        linear_layout_wrapper_menu =(LinearLayout)this.findViewById(R.id.wrapper_menu);
        String module_content=module.getContent();
        Type listType = new TypeToken<ArrayList<JMenu>>() {}.getType();
        ArrayList<JMenu> list_menu = JUtilities.getGsonParser().fromJson(module_content, listType);
        Timber.d("menu list_menu %s",list_menu);
        root = TreeNode.root();

        for (JMenu menu_item: list_menu) {
            TreeNode node = new TreeNode(menu_item).setViewHolder(new IconTreeItemHolder(app.getContext()));
            root.addChild(node);
            tree_recurse_menu(menu_item.getChildren(),node);
        }
        AndroidTreeView tView = new AndroidTreeView(app.getCurrentActivity(), root);

        linear_layout_wrapper_menu.addView(tView.getView());
        wrapper_menu_params.height=0;
        linear_layout_wrapper_menu.setLayoutParams( wrapper_menu_params);
        button.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                if(show)
                {
                    wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT);
                }else{
                    wrapper_menu_params.height=0;
                }
                linear_layout_wrapper_menu.setLayoutParams( wrapper_menu_params);
                show =!show;


            }
        });
        //this.setVisibility(LinearLayout.GONE);


    }
    public void tree_recurse_menu(ArrayList<JMenu> list_menu, TreeNode root){
        for (JMenu menu_item: list_menu) {
            TreeNode node = new TreeNode(menu_item.getTitle());
            root.addChildren(node);
            ArrayList<JMenu> children=menu_item.getChildren();
            tree_recurse_menu(children,node);
        }

    }


}
