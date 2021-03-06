package vantinviet.core.modules.mod_cart;

import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.RequiresApi;
import android.support.v4.app.DialogFragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.EditText;

import com.unnamed.b.atv.model.TreeNode;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuong on 5/19/2017.
 */

public class DialogFragmentCart extends DialogFragment {

    static JApplication app= JFactory.getApplication();
    private List<ItemCartProduct> listData = new ArrayList<ItemCartProduct>();
    public static ModCartView mod_cart_view;
    @Override
    public Dialog onCreateDialog(Bundle savedInstanceState) {
        // Use the Builder class for convenient dialog construction
        AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
        LayoutInflater inflater = getActivity().getLayoutInflater();
        View cart_popup_default=inflater.inflate(R.layout.modules_mod_cart_tmpl_popup_default, null);
        String module_content=getArguments().getString("module_content");


        mod_cart_view = JUtilities.getGsonParser().fromJson(module_content, ModCartView.class);
        ArrayList<ItemCartProduct> list_item_product_cart=mod_cart_view.getList_item_cart_product();
        //Timber.d("list_item_product_cart %s",list_item_product_cart.toString());
        /*CartListDataAdapter cart_adapter = new CartListDataAdapter(getActivity(), list_item_product_cart);
        RecyclerView recycler_view_cart = (RecyclerView) cart_popup_default.findViewById(R.id.recycler_view_cart);
        recycler_view_cart.setHasFixedSize(true);
        recycler_view_cart.setAdapter(cart_adapter);*/
        RecyclerView recyclerView = (RecyclerView) cart_popup_default.findViewById(R.id.recycler_view_cart);

        // If the size of views will not change as the data changes.
        recyclerView.setHasFixedSize(true);

        // Setting the LayoutManager.
        LinearLayoutManager layoutManager = new LinearLayoutManager(getContext());
        recyclerView.setLayoutManager(layoutManager);

        // Setting the adapter.
        final CartListDataAdapter adapter = new CartListDataAdapter(listData);
        for(int i=0;i<list_item_product_cart.size();i++){
            ItemCartProduct item_cart_product =list_item_product_cart.get(i);

            // Update adapter.
            adapter.addItem(listData.size(), item_cart_product);
        }
        recyclerView.setAdapter(adapter);
        builder.setView(cart_popup_default);
        builder
                .setTitle(R.string.str_your_cart)
                .setPositiveButton(R.string.close, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        // FIRE ZE MISSILES!
                    }
                })
                .setNegativeButton(R.string.str_pay_order, new DialogInterface.OnClickListener() {
                    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        go_to_pay_now();
                    }
                })
        ;
        // Create the AlertDialog object and return it
        return builder.create();
    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public static void go_to_pay_now() {
        Map<String, String> post = new HashMap<String, String>();
        post.put("option", "com_hikashop");
        post.put("ctrl", "checkout");
        post.put("task", "step");
        String url_checkout=mod_cart_view.getUrl_checkout();
        app.setRedirect(url_checkout,post);
    }
    public void init() {

    }


}