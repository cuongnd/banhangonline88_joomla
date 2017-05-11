package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.PopupWindow;

import com.unnamed.b.atv.model.TreeNode;
import com.unnamed.b.atv.view.AndroidTreeView;

import java.sql.Time;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

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
        Timber.d("menu module_content %s",module_content);
        PopupWindow mPopupWindow;
        root = TreeNode.root();
        TreeNode parent = new TreeNode("MyParentNode");
        TreeNode child0 = new TreeNode("ChildNode0");
        TreeNode child1 = new TreeNode("ChildNode1");
        parent.addChildren(child0, child1);
        root.addChild(parent);

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
}
