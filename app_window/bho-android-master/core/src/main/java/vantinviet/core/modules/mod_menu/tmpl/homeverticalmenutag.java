package vantinviet.core.modules.mod_menu.tmpl;

import android.content.Context;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.PopupWindow;

import vantinviet.core.R;
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
    LayoutParams wrapper_menu_params = new LayoutParams(MATCH_PARENT,MATCH_PARENT );
    public homeverticalmenutag(Context context) {
        super(context);
        inflate(getContext(), R.layout.modules_mod_menu_tmpl_homeverticalmenutag, this);
        this.setLayoutParams( new LayoutParams(MATCH_PARENT,MATCH_PARENT ));
        Button button=(Button)this.findViewById(R.id.show_menu);
        linear_layout_wrapper_menu =(LinearLayout)this.findViewById(R.id.wrapper_menu);
        PopupWindow mPopupWindow;
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
