package vantinviet.core.modules.mod_menu;

import android.content.Context;
import android.content.res.Resources;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.HorizontalScrollView;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.beardedhen.androidbootstrap.BootstrapButton;
import com.beardedhen.androidbootstrap.BootstrapButtonGroup;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapSize;
import vantinviet.core.R;
import vantinviet.core.libraries.android.registry.JRegistry;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by cuongnd on 13/01/2016.
 */
public class horizontal_menu {

    private static final int ID_UP     = 1;
    private static final int ID_DOWN   = 2;
    private static final int ID_SEARCH = 3;
    private static final int ID_INFO   = 4;
    private static final int ID_ERASE  = 5;
    private static final int ID_OK     = 6;
    public static  Context main_context;

    static void render_menu_horizontal(final Context context, View parent_object, JSONObject object, int width) {
        ActionItem nextItem 	= new ActionItem(ID_DOWN, "Next", context.getResources().getDrawable(R.drawable.menu_down_arrow));
        ActionItem prevItem 	= new ActionItem(ID_UP, "Prev", context.getResources().getDrawable(R.drawable.menu_up_arrow));
        ActionItem searchItem 	= new ActionItem(ID_SEARCH, "Find", context.getResources().getDrawable(R.drawable.menu_search));
        ActionItem infoItem 	= new ActionItem(ID_INFO, "Info", context.getResources().getDrawable(R.drawable.menu_info));
        ActionItem eraseItem 	= new ActionItem(ID_ERASE, "Clear", context.getResources().getDrawable(R.drawable.menu_eraser));
        ActionItem okItem 		= new ActionItem(ID_OK, "OK", context.getResources().getDrawable(R.drawable.menu_ok));

        //use setSticky(true) to disable QuickAction dialog being dismissed after an item is clicked
        prevItem.setSticky(true);
        nextItem.setSticky(true);

        //create QuickAction. Use QuickAction.VERTICAL or QuickAction.HORIZONTAL param to define layout
        //orientation
        final QuickAction quickAction = new QuickAction(context, QuickAction.VERTICAL);

        //add action items into QuickAction
        quickAction.addActionItem(nextItem);
        quickAction.addActionItem(prevItem);
        quickAction.addActionItem(searchItem);
        quickAction.addActionItem(infoItem);
        quickAction.addActionItem(eraseItem);
        quickAction.addActionItem(okItem);

        //Set listener for action item clicked
        quickAction.setOnActionItemClickListener(new QuickAction.OnActionItemClickListener() {
            @Override
            public void onItemClick(QuickAction source, int pos, int actionId) {
                ActionItem actionItem = quickAction.getActionItem(pos);

                //here we can filter which action item was clicked with pos or actionId parameter
                if (actionId == ID_SEARCH) {
                    Toast.makeText(context.getApplicationContext(), "Let's do some search action", Toast.LENGTH_SHORT).show();
                } else if (actionId == ID_INFO) {
                    Toast.makeText(context.getApplicationContext(), "I have no info this time", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(context.getApplicationContext(), actionItem.getTitle() + " selected", Toast.LENGTH_SHORT).show();
                }
            }
        });

        //set listnener for on dismiss event, this listener will be called only if QuickAction dialog was dismissed
        //by clicking the area outside the dialog.
        quickAction.setOnDismissListener(new QuickAction.OnDismissListener() {
            @Override
            public void onDismiss() {
                Toast.makeText(context.getApplicationContext(), "Dismissed", Toast.LENGTH_SHORT).show();
            }
        });

        //show on btn1
        Button btn1 = new Button(context);
        btn1.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                quickAction.show(v);
            }
        });

        Button btn2 =  new Button(context);
        btn2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                quickAction.show(v);
            }
        });

        Button btn3 = (Button)  new Button(context);
        btn3.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                quickAction.show(v);
                quickAction.setAnimStyle(QuickAction.ANIM_REFLECT);
            }
        });
/*

        ((LinearLayout) parent_object).addView(btn1);
        ((LinearLayout) parent_object).addView(btn2);
        ((LinearLayout) parent_object).addView(btn3);
*/


        LinearLayout linear_layout = new LinearLayout(context);
        BootstrapButtonGroup bootstrap_button_group=new BootstrapButtonGroup(context);
        HorizontalScrollView scroll_view = new HorizontalScrollView(context);
        String scroll="";
        try {

            int id = object.getInt("id");
            String title = "";
            bootstrap_button_group.setId(id);

            bootstrap_button_group.setBootstrapBrand(DefaultBootstrapBrand.SUCCESS);
            bootstrap_button_group.setOrientation(LinearLayout.HORIZONTAL);
            bootstrap_button_group.setRounded(false);
            bootstrap_button_group.setBootstrapSize(DefaultBootstrapSize.LG);
            JSONArray list_menu_item=object.getJSONArray("list_menu_item");

            String params=object.getString("params");
            JSONObject json_object_params=new JSONObject(params);

            JRegistry a_params = new JRegistry(json_object_params);
            JSONObject menu_config=json_object_params.has("menu_config")?json_object_params.getJSONObject("menu_config"):new JSONObject();
            if(menu_config.has("scroll")) {
                scroll =menu_config.getString("scroll");
            }else
            {
                scroll="on";
            }
            if(scroll.equals("on"))
            {
                scroll_view.setId(id);

                scroll_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));
                //scroll_view.setBackgroundColor(currentStrokeColor);
            }
            for (int i=0;i<list_menu_item.length();i++) {
                BootstrapButton bootstrap_button = new BootstrapButton(context);
                JSONObject menu_item=list_menu_item.getJSONObject(i);
                id = menu_item.getInt("id");
                title = menu_item.getString("title");
                bootstrap_button.setId(id);
                bootstrap_button.setText(title);
                bootstrap_button.setBootstrapSize(DefaultBootstrapSize.LG);
                bootstrap_button.setOnClickListener(getOnClickDoSomething(bootstrap_button,menu_item));
                Resources resource = context.getResources();

               /* bootstrap_button.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        quickAction.show(v);
                    }
                });*/
                bootstrap_button_group.addView(bootstrap_button);
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }
        if(scroll.equals("on"))
        {
            scroll_view.addView(bootstrap_button_group);
            ((LinearLayout) parent_object).addView(scroll_view);

        }
        else
        {
            ((LinearLayout) parent_object).addView(bootstrap_button_group);
        }
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.FILL_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
        params.setMargins(0, 10, 0, 10);
        parent_object.setLayoutParams(params);


    }
    static View.OnClickListener getOnClickDoSomething(final BootstrapButton button, final JSONObject menu_item)  {
        return new View.OnClickListener() {
            public void onClick(View v) {
                try {

                    String link=menu_item.getString("link");
                    String id=menu_item.getString("id");
                    String title=menu_item.getString("title");
                    link=link+"&Itemid="+id;
                    JApplication app=JFactory.getApplication();
                    app.setRedirect(link);
                    JMenu JMenu = JFactory.getMenu();
                    JMenu.setMenuActive(menu_item);
                    //(new AsyncJsonElementViewLoader()).execute(link);
                    System.out.println(link);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        };
    }






}
