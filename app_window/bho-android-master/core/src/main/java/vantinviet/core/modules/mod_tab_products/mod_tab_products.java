package vantinviet.core.modules.mod_tab_products;

import android.graphics.Color;
import android.os.Build;
import android.support.annotation.RequiresApi;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.widget.LinearLayout;
import android.widget.TextView;


import com.google.gson.reflect.TypeToken;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;
import timber.log.Timber;

import vantinviet.core.R;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;


import static android.view.ViewGroup.LayoutParams.WRAP_CONTENT;
import static android.widget.ListPopupWindow.MATCH_PARENT;



/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class mod_tab_products extends FragmentActivity implements MaterialTabListener {


    public final Module module;
    private final LinearLayout linear_layout;
    private static final String KEY_DEMO = "demo";
    LinearLayout tab_content;
    MaterialTabHost tabHost;
    JApplication app= JFactory.getApplication();
    Module_tab_product_tmpl_default object_tab_product_tmpl_default;
    ArrayList<Mod_tab_product_helper.List_category_product> list_main_category_product;
    ArrayList<Module_tab_product_tmpl_default_tab_content> list_module_tab_product_tmpl_default_tab_content;
    private ViewPager pager;
    private PagerAdapter pager_adapter;
    ScreenSlidePagerAdapter adapterViewPager;
    ViewPager vpPager;
    int sum_load=0;


    public mod_tab_products(Module module, LinearLayout linear_layout) {
        this.module=module;
        this.linear_layout=linear_layout;
        String content=this.module.getContent();
        if(content.isEmpty()){
            Timber.d("content module %s(%d) is empty",module.getModuleName(),module.getId());
            return;
        }
        Type listType = new TypeToken<ArrayList<Mod_tab_product_helper.List_category_product>>() {}.getType();
        list_main_category_product = JUtilities.getGsonParser().fromJson(content, listType);
        //Timber.d("list_main_category_product %s", list_main_category_product.toString());
        init();
    }

    private void init() {
        object_tab_product_tmpl_default =new Module_tab_product_tmpl_default(app.getContext(),this.module);
        tabHost = (MaterialTabHost) object_tab_product_tmpl_default.findViewById(R.id.tabHost);
        vpPager= (ViewPager) object_tab_product_tmpl_default.findViewById(R.id.tab_content);
        adapterViewPager = new ScreenSlidePagerAdapter(app.getSupportFragmentManager());
        vpPager.setAdapter(adapterViewPager);
        vpPager.setId(module.getId());
        vpPager.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {

            @RequiresApi(api = Build.VERSION_CODES.KITKAT)
            @Override
            public void onPageSelected(int position) {
                tabHost.setSelectedNavigationItem(position);

                Module_tab_product_tmpl_default_tab_content module_tab_product_tmpl_default_tab_content=(Module_tab_product_tmpl_default_tab_content)adapterViewPager.getItem(position);
                if(!module_tab_product_tmpl_default_tab_content.is_loaded)
                {
                    module_tab_product_tmpl_default_tab_content.getAjax_load_data();
                }

            }
        });

        // insert all tabs from pagerAdapter data
        for (int i = 0; i < adapterViewPager.getCount(); i++) {
            tabHost.addTab(
                    tabHost.newTab()
                            .setText(adapterViewPager.getPageTitle(i))
                            .setTabListener(this)
            );

        }
        LinearLayout.LayoutParams module_title_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout module_title=(LinearLayout)object_tab_product_tmpl_default.findViewById(R.id.module_title );
        module_title.setBackgroundColor(Color.parseColor("#eff0f1"));
        module_title_params.setMargins(0,0,0,20);
        module_title.setLayoutParams(module_title_params);
        TextView title=(TextView) module_title.findViewById(R.id.title);
        title.setText(module.getTitle());
        title.setTextAppearance(app.getContext(), R.style.module_title_text);

        LinearLayout.LayoutParams new_vertical_wrapper_of_module_linear_layout_params = new LinearLayout.LayoutParams(MATCH_PARENT,WRAP_CONTENT  );
        LinearLayout new_wrapper_of_module_content_linear_layout=new LinearLayout(app.getContext());
        new_wrapper_of_module_content_linear_layout.setLayoutParams(new_vertical_wrapper_of_module_linear_layout_params);
        new_wrapper_of_module_content_linear_layout.setOrientation(LinearLayout.VERTICAL);

        new_wrapper_of_module_content_linear_layout.addView(object_tab_product_tmpl_default);

        linear_layout.addView(new_wrapper_of_module_content_linear_layout);

    }
    private class ScreenSlidePagerAdapter extends FragmentStatePagerAdapter {
        private boolean is_first=true;
        private Module_tab_product_tmpl_default_tab_content module_tab_product_tmpl_default_tab_content;
        Map<Integer, Module_tab_product_tmpl_default_tab_content> list_objectSet = new HashMap<Integer,Module_tab_product_tmpl_default_tab_content>();
        public ScreenSlidePagerAdapter(FragmentManager fm) {
            super(fm);
        }


        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
        @Override
        public Fragment getItem(int position) {
            Mod_tab_product_helper.List_category_product list_category_product = list_main_category_product != null ? list_main_category_product.get(position) : null;
            Module_tab_product_tmpl_default_tab_content module_tab_product_tmpl_default_tab_content=list_objectSet.get(position);
            if(module_tab_product_tmpl_default_tab_content==null){
                module_tab_product_tmpl_default_tab_content = new Module_tab_product_tmpl_default_tab_content();
                module_tab_product_tmpl_default_tab_content.list_category_product = list_category_product;
                module_tab_product_tmpl_default_tab_content.module = module;
                list_objectSet.put(position,module_tab_product_tmpl_default_tab_content);
            }
            return module_tab_product_tmpl_default_tab_content;
        }
        public String getTest(){
            return  "ddddddddddd";
        }
        @Override
        public int getCount() {
            return list_main_category_product.size();
        }
        @Override
        public CharSequence getPageTitle(int position) {
            Category category=list_main_category_product.get(position).getDetail();
            return category.getName();
        }


    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public void onTabSelected(MaterialTab tab) {
        int position=tab.getPosition();
        vpPager.setCurrentItem(tab.getPosition());

    }


    @Override
    public void onTabReselected(MaterialTab tab) {

    }


    @Override
    public void onTabUnselected(MaterialTab tab) {

    }



}
