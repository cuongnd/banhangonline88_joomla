package vantinviet.core.modules.mod_menu;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.FragmentManager;
import android.content.DialogInterface;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.support.annotation.Nullable;
import android.support.annotation.RequiresApi;
import android.support.v4.app.DialogFragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.squareup.picasso.Picasso;
import com.unnamed.b.atv.model.TreeNode;
import com.unnamed.b.atv.view.AndroidTreeView;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuong on 5/19/2017.
 */

public class FireMissilesDialogFragment extends DialogFragment {

    private ArrayList<JMenu> list_menu;
    private TreeNode root;
    static JApplication app= JFactory.getApplication();
    public LinearLayout linear_layout_wrapper_menu;

    public FireMissilesDialogFragment(ArrayList<JMenu> list_menu) {
        this.list_menu=list_menu;
    }

    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        // Use the Builder class for convenient dialog construction
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        LayoutInflater inflater = getActivity().getLayoutInflater();
        View a_view=inflater.inflate(R.layout.modules_mod_menu_tmpl_popup_homeverticalmenutag, null);

        this.linear_layout_wrapper_menu =(LinearLayout)a_view.findViewById(R.id.wrapper_menu);
        builder.setView(a_view);
        builder
                .setPositiveButton("ok", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        // FIRE ZE MISSILES!
                    }
                })
                .setNegativeButton("cancel", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        // User cancelled the dialog
                    }
                });
        root = TreeNode.root();
        for (JMenu menu_item: list_menu) {
            menu_item.setLevel(0);
            TreeNode node = new TreeNode(menu_item).setViewHolder(new IconTreeItemHolderPopup(app.getContext()));
            root.addChild(node);
            ArrayList<JMenu> children=menu_item.getChildren();
            tree_recurse_menu(children,node,0);
        }
        AndroidTreeView tView = new AndroidTreeView(app.getContext(), root);
        tView.setDefaultNodeClickListener(nodeClickListener);

        this.linear_layout_wrapper_menu.addView(tView.getView());

        // Create the AlertDialog object and return it
        return builder.create();
    }
    public void setList_menu(ArrayList<JMenu> list_menu){
        this.list_menu=list_menu;
    }

    public void init() {

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
    private TreeNode.TreeNodeClickListener nodeClickListener = new TreeNode.TreeNodeClickListener() {
        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        @Override
        public void onClick(TreeNode node, Object value) {
            JMenu menu_item = (JMenu) value;
            final String link = menu_item.getLink();
            Timber.d("menu item %s", menu_item.toString());
            final Map<String, String> post = new HashMap<String, String>();
            post.put("Itemid", String.valueOf(menu_item.getId()));
            if(menu_item.getTotalChildren()==0) {
                app.getAlertDialog().dismiss();
                Handler refresh2 = new Handler(Looper.getMainLooper());
                refresh2.post(new Runnable() {
                    public void run()
                    {
                        app.setRedirect(VTVConfig.getRootUrl().concat("/" + link), post);
                    }});

            }

        }
    };


}