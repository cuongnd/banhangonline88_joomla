package vantinviet.core.modules.mod_menu;

import android.content.Context;
import android.graphics.Color;
import android.os.Handler;
import android.os.Looper;
import android.util.Base64;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.JsonParseException;
import com.google.gson.reflect.TypeToken;
import com.google.gson.stream.JsonReader;
import com.unnamed.b.atv.model.TreeNode;
import com.unnamed.b.atv.view.AndroidTreeView;

import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.lang.reflect.Type;
import java.util.ArrayList;

import timber.log.Timber;

import vantinviet.core.R;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.modules.mod_menu.IconTreeItemHolder;

import static android.widget.ListPopupWindow.MATCH_PARENT;

/**
 * Created by cuong on 5/10/2017.
 */

public class Pouphomeverticalmenutag extends LinearLayout {
    LinearLayout linear_layout_wrapper_menu;
    static JApplication app= JFactory.getApplication();
    TreeNode root;
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public Pouphomeverticalmenutag(Context context,String json_menu) {
        super(context);
        inflate(getContext(), R.layout.modules_mod_menu_tmpl_popup_homeverticalmenutag, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        final Button button=(Button)this.findViewById(R.id.show_menu);
        linear_layout_wrapper_menu =(LinearLayout)this.findViewById(R.id.wrapper_menu);
        Type listType = new TypeToken<ArrayList<JMenu>>() {}.getType();
        ArrayList<JMenu> list_menu = JUtilities.getGsonParser().fromJson(json_menu, listType);
        Timber.d("list_menu %s",list_menu.toString());
        root = TreeNode.root();


        for (JMenu menu_item: list_menu) {
            menu_item.setLevel(0);
            TreeNode node = new TreeNode(menu_item).setViewHolder(new IconTreeItemHolderPopup(app.getContext()));
            root.addChild(node);
            ArrayList<JMenu> children=menu_item.getChildren();
            tree_recurse_menu(children,node,0);
        }
        final AndroidTreeView tView = new AndroidTreeView(app.getContext(), root);
        linear_layout_wrapper_menu.addView(tView.getView());





    }


    public static void tree_recurse_menu(ArrayList<JMenu> list_menu, TreeNode root, int level){
        if(list_menu!=null)for (JMenu menu_item: list_menu) {
            ArrayList<JMenu> children=menu_item.getChildren();
            menu_item.setLevel(level+1);
            TreeNode node = new TreeNode(menu_item).setViewHolder(new IconTreeItemHolderPopup(app.getContext()));
            root.addChildren(node);

            tree_recurse_menu(children,node,level+1);
        }
        else{

        }

    }


}
