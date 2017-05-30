package vantinviet.core.libraries.joomla.environment;

import android.webkit.WebView;
import android.webkit.WebViewClient;

/**
 * Created by cuongnd on 6/20/2016.
 */
public class JBrowser {
    private static WebViewClient web_view_client=null;

    public static WebViewClient getWebViewClient() {
        if(web_view_client==null){
            WebViewClient web_view_client = new WebViewClient() {

                @Override
                public boolean shouldOverrideUrlLoading(WebView view, String url) {
                    return false;
                }

                @Override
                public void onPageFinished(WebView view, String url) {
                    view.loadUrl("javascript:HtmlViewer.showHTML" +
                            "(document.getElementsByTagName('body')[0].innerHTML);");
                }
            };
        }
        return web_view_client;
    }
}
