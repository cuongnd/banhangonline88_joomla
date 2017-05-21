package vantinviet.core.libraries.legacy.application;

import android.content.Context;
import android.webkit.JavascriptInterface;

/**
 * Created by cuongnd on 6/20/2016.
 */
public class MyJavaScriptInterface {
    private Context ctx;

    public MyJavaScriptInterface(Context ctx) {
        this.ctx = ctx;
    }

    @JavascriptInterface
    public void showHTML(String html) {



        /*final Pattern pattern = Pattern.compile("<android_response>(.+?)</android_response>");
        final Matcher matcher = pattern.matcher(html);
        matcher.find();
        System.out.println("html:" + html);
        System.out.println(matcher.group(1)); // Prints String I want to extract
        html=matcher.group(1);
        System.out.println("html:"+html);
        JApplication app= JFactory.getApplication();
        app.execute(html);*/
    }
}

