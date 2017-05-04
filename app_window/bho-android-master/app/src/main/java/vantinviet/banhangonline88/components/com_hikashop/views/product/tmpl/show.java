package vantinviet.banhangonline88.components.com_hikashop.views.product.tmpl;

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

import timber.log.Timber;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Category;
import vantinviet.banhangonline88.administrator.components.com_hikashop.classes.Product;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.entities.ShopResponse;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.language.JText;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;

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

    private void ajax_add_to_cart() {
        Timber.d("Now request add to cart %s", EndPoints.ADD_TO_CART);
        app.getProgressDialog().show();
        android.webkit.WebView web_browser = JFactory.getWebBrowser();
        web_browser.postUrl(EndPoints.ADD_TO_CART,app.getPostBrowser());
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
            Timber.d("html response: %s",html);
            Gson gson = new Gson();
            JsonReader reader = new JsonReader(new StringReader(html));
            reader.setLenient(true);
            final ShopResponse response_shop = gson.fromJson(reader, ShopResponse.class);
            Timber.d("Get shops response: %s", response_shop.toString());
            Handler refresh = new Handler(Looper.getMainLooper());
            refresh.post(new Runnable() {
                public void run()
                {
                    //setSpinShops(response_shop.getShopList());
                    //animateContentVisible();
                }
            });






        }

    }

}
