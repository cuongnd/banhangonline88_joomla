package vantinviet.core.modules.mod_menu;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.TextView;

import com.unnamed.b.atv.model.TreeNode;

import vantinviet.core.R;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;


/**
 * Created by Bogdan Melnychuk on 2/12/15.
 */
    public class IconTreeItemHolder extends TreeNode.BaseNodeViewHolder<JMenu> {


    public IconTreeItemHolder(Context context) {
        super(context);
    }

    @Override
    public View createNodeView(TreeNode node, JMenu menu) {
        JApplication app= JFactory.getApplication();
        final LayoutInflater inflater = LayoutInflater.from(app.getContext());
        final View view = inflater.inflate(R.layout.layout_profile_node, null, false);
        TextView tvValue = (TextView) view.findViewById(R.id.node_value);
        tvValue.setText(menu.getTitle());

        return view;
    }

}
