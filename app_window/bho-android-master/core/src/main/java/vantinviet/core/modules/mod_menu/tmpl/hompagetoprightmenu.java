package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;

import com.google.gson.reflect.TypeToken;
import com.unnamed.b.atv.model.TreeNode;
import com.unnamed.b.atv.view.AndroidTreeView;

import java.lang.reflect.Type;
import java.util.ArrayList;

import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.modules.mod_menu.IconTreeItemHolder;
import vantinviet.core.modules.mod_menu.JCustomMenu;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class hompagetoprightmenu extends LinearLayout {
    LinearLayout linear_layout_wrapper_menu;
    boolean show =true;
    JApplication app= JFactory.getApplication();
    TreeNode root;
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public hompagetoprightmenu(Context context, Module module) {
        super(context);
        inflate(getContext(), R.layout.modules_mod_menu_tmpl_homeverticalmenutag, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        Button button=(Button)this.findViewById(R.id.show_menu);
        linear_layout_wrapper_menu =(LinearLayout)this.findViewById(R.id.wrapper_menu);
        String module_content=module.getContent();
        Type listType = new TypeToken<ArrayList<JCustomMenu>>() {}.getType();
        ArrayList<JCustomMenu> list_menu = JUtilities.getGsonParser().fromJson(module_content, listType);
        root = TreeNode.root();
        for (JCustomMenu menu_item: list_menu) {
            menu_item.setLevel(0);
            TreeNode node = new TreeNode(menu_item).setViewHolder(new IconTreeItemHolder(app.getContext()));
            root.addChild(node);
            ArrayList<JCustomMenu> children=menu_item.getChildrenCustomMenu();
            menu_item.setTotalChildren(children.size());
            tree_recurse_menu(children,node,0);
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


    public void tree_recurse_menu(ArrayList<JCustomMenu> list_menu, TreeNode root,int level){
        if(list_menu!=null)for (JCustomMenu menu_item: list_menu) {
            ArrayList<JCustomMenu> children=menu_item.getChildrenCustomMenu();
            menu_item.setLevel(level+1);
            menu_item.setTotalChildren(children.size());
            TreeNode node = new TreeNode(menu_item).setViewHolder(new IconTreeItemHolder(app.getContext()));

            root.addChildren(node);

            tree_recurse_menu(children,node,level+1);
        }
        else{

        }

    }


}
