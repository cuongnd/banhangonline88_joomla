package vantinviet.core.components.com_hikashop.views.product.tmpl;

import android.annotation.TargetApi;
import android.os.Build;
import android.os.Handler;
import android.os.Looper;
import android.support.design.widget.BottomNavigationView;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.webkit.JavascriptInterface;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.stream.JsonReader;

import java.io.StringReader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.administrator.components.com_hikashop.classes.Category;
import vantinviet.core.administrator.components.com_hikashop.classes.Product;
import vantinviet.core.api.EndPoints;
import vantinviet.core.entities.ShopResponse;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.language.JText;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuongnd on 02/04/2017.
 */

public class show {
    private final ShowContent show_content;
    BottomNavigationView product_show_footer;
    JApplication app = JFactory.getApplication();

    public show(LinearLayout linear_layout) {
        String component_response = app.getComponent_response();
        Gson gson = new Gson();
        JsonReader reader = new JsonReader(new StringReader(component_response));
        reader.setLenient(true);
        PageShowProduct product_response = gson.fromJson(reader, PageShowProduct.class);
        show_content = new ShowContent(app.getCurrentActivity(), product_response);

        product_show_footer = (BottomNavigationView)app.root_relative_layout.findViewById(R.id.bottom_navigation);
        Menu menu= product_show_footer.getMenu();
        menu.clear();
        final String chatting=JText._("Chatting");
        final String add_to_cart=JText._("Add to cart");
        final String buy_now=JText._("buy now");
        menu.add(chatting).setIcon(R.drawable.com_facebook_send_button_icon).setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
            @Override
            public boolean onMenuItemClick(MenuItem menuItem) {
                Timber.d("hello %s",chatting);
                return false;
            }
        });
        menu.add(add_to_cart).setIcon(R.drawable.cart_add).setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
            @Override
            public boolean onMenuItemClick(MenuItem menuItem) {
                ajax_add_to_cart();
                return false;
            }
        });
        menu.add(buy_now).setIcon(R.drawable.cart_add).setOnMenuItemClickListener(new MenuItem.OnMenuItemClickListener() {
            @Override
            public boolean onMenuItemClick(MenuItem menuItem) {
                Timber.d("hello %s",buy_now);
                return false;
            }
        });
        product_show_footer.setVisibility(View.VISIBLE);




        linear_layout.addView(show_content);
    }

    @TargetApi(Build.VERSION_CODES.KITKAT)
    private void ajax_add_to_cart() {
        app.getProgressDialog().show();
        vtv_WebView web_browser = JFactory.getWebBrowser();
        Map<String, String> post = new HashMap<String, String>();
        post.put("option", "com_hikashop");
        post.put("ctrl", "product");
        post.put("task", "updatecart");
        post.put("add", "1");
        post.put("cart_type", "cart");
        post.put("from", "module");
        post.put("hikashop_ajax", "1");
        post.put("product_id", "14");
        post.put("quantity", "1");
        post.put("return_url", "aHR0cDovL2JhbmhhbmdvbmxpbmU4OC5jb20vaW5kZXgucGhwL2NvbXBvbmVudC9oaWthc2hvcC9wcm9kdWN0L2NpZC04Lmh0bWw=");
        web_browser.vtv_postUrl(VTVConfig.rootUrl,post);
        app.getProgressDialog().dismiss();
        web_browser.addJavascriptInterface(new response_ajax_add_to_cart(), "HtmlViewer");
    }

    public class PageShowProduct {
        public ArrayList<Category> categories = new ArrayList<Category>();

        public Product product;

        public ArrayList<Category> getCategories() {
            return categories;
        }

        public Product getProduct() {
            return product;
        }
    }
    private class response_ajax_add_to_cart {

        public response_ajax_add_to_cart() {
        }

        @JavascriptInterface
        public void showHTML(String html) {
            html= JUtilities.get_string_by_string_base64(html);
            Timber.d("html response: %s",html);
            Gson gson = new Gson();
            JsonReader reader = new JsonReader(new StringReader(html));
            reader.setLenient(true);







        }

    }

}
