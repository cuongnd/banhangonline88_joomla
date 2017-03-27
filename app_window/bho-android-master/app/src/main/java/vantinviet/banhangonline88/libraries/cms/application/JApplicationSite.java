package vantinviet.banhangonline88.libraries.cms.application;

import android.annotation.TargetApi;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.res.Resources;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Environment;
import android.support.v7.app.AppCompatActivity;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.JavascriptInterface;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ScrollView;

import com.beardedhen.androidbootstrap.BootstrapEditText;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.util.Hashtable;
import java.util.Map;
import java.util.Random;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.VTVConfig;
import vantinviet.banhangonline88.libraries.cms.component.JPluginHelper;
import vantinviet.banhangonline88.libraries.cms.menu.JMenu;
import vantinviet.banhangonline88.libraries.joomla.JFactory;
import vantinviet.banhangonline88.libraries.joomla.user.JUser;
import vantinviet.banhangonline88.libraries.legacy.application.JApplication;
import vantinviet.banhangonline88.libraries.legacy.request.JRequest;
import vantinviet.banhangonline88.media.element.slider.banner_rotator.elementBanner_RotatorHelper;
import vantinviet.banhangonline88.media.element.ui.grid.element_grid_helper;
import vantinviet.banhangonline88.media.element.ui.link_image.element_link_image_helper;
import vantinviet.banhangonline88.modules.mod_menu.mod_menu;
import vantinviet.banhangonline88.modules.mod_virtuemart_category.mod_virtuemart_category_helper;

/**
 * Created by cuongnd on 6/10/2016.
 */
public class JApplicationSite extends JApplicationCms {
    private static JApplicationSite ourInstance;
    public static ProgressDialog dialog;
    public  String client="";
    public static int screen_size_width;
    public static int screen_size_height;
    public static String host="";
    public static boolean debug;
    public static JSONArray modules;
    public static String component_content;
    public static JApplicationSite getInstance(String client) {
        if (ourInstance == null) {
            ourInstance = new JApplicationSite(client);
        }
        return ourInstance;
    }

    public JApplicationSite(String client) {
        this.client=client;
    }

    public static void execute(JSONObject json_object) {
        doExecute(json_object);
    }

    public static void doExecute(JSONObject json_object) {
        initialiseApp(json_object);
    }

    public static void initialiseApp(JSONObject json_object) {
        try {
            JSONObject request = null;
            request = json_object.has("request")?json_object.getJSONObject("request"):new JSONObject();
            JRequest.request=request;
        } catch (JSONException e) {
            e.printStackTrace();
        }

        JUser user = JFactory.getUser();
        int guestUserGroup=1;
        JPluginHelper.importPlugin("system", "languagefilter");
        Map<String,String> option=new Hashtable<>();

    }


    public static void start(AppCompatActivity mainActivity) {
        dialog=new ProgressDialog(mainActivity);
        if (dialog == null) {
            dialog = new ProgressDialog(mainActivity);
        }
        dialog.setMessage("Downloading element...");
        dialog.show();
        //Remove title bar
        JApplication app = JFactory.getApplication();
        app.context = mainActivity;
        JFactory.setContext(mainActivity);
        DisplayMetrics metrics = new DisplayMetrics();
        mainActivity.getWindowManager().getDefaultDisplay().getMetrics(metrics);
        int screenDensity = (int) metrics.density;
        int screenDensityDPI = metrics.densityDpi;
        float screenscaledDensity = metrics.scaledDensity;
        int width = metrics.widthPixels;
        int height = metrics.heightPixels;


        System.out.println("Screen Density=" + screenDensity + "\n"
                + "Screen DensityDPI=" + screenDensityDPI + "\n"
                + "Screen Scaled DensityDPI=" + screenscaledDensity + "\n"
                + "Height=" + height + "\n"
                + "Width=" + width);

        screen_size_width = width;
        screen_size_height = height;
        if (screenDensity == 0) {
            screenDensity = 1;
        }
        String screenSize = Integer.toString(width / screenDensity) + "x" + Integer.toString(height);
        System.out.println(width / screenDensity);
        VTVConfig vtv_config=JFactory.getVTVConfig();
        System.out.println("vtv_config.root_url"+vtv_config.root_url);
        String local_version = vtv_config.get_version();
        //initChatting();
        VTVConfig.screen_size_width = screen_size_width;
        VTVConfig.screen_size_height = screen_size_height;

        VTVConfig.screenDensity = screenDensity;
        String root_url=vtv_config.root_url;
        if (host.equals("")) {
            String test_page = "&Itemid=433";
            test_page = "";
            host = root_url + "/index.php?os=android&screenSize=" + screenSize + "&version=" + local_version + test_page;
        } else if (!host.contains(root_url)) {
            host = root_url + "/" + host;
        }
        start_remote(host);

    }
    public static void start_remote(String host){
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


        WebView web_browser = JFactory.getWebBrowser();
        web_browser.getSettings().setJavaScriptEnabled(true);
        web_browser.getSettings().setSupportZoom(true);
        web_browser.getSettings().setBuiltInZoomControls(true);
        web_browser.setWebViewClient(web_view_client);


        web_browser.clearHistory();
        web_browser.clearFormData();
        web_browser.clearCache(true);

        System.out.println("-------host---------");
        System.out.println(host);
        System.out.println("-------host---------");
        web_browser.loadUrl(host);
        web_browser.addJavascriptInterface(new MyJavaScriptInterfaceWebsite(), "HtmlViewer");
    }
    private static class MyJavaScriptInterfaceWebsite {
        public MyJavaScriptInterfaceWebsite() {
        }

        @JavascriptInterface
        public void showHTML(String html) {
            final Pattern pattern = Pattern.compile("<android_response>(.+?)</android_response>");
            final Matcher matcher = pattern.matcher(html);

            matcher.find();
            html = matcher.group(1);
            JApplication app = JFactory.getApplication();
            (new AsyncJsonElementViewLoader()).execute(html);

        }
    }

    private static class AsyncJsonElementViewLoader extends AsyncTask<String, Void, String> {


        @Override
        protected void onPostExecute(String json_string) {
            JApplication app = JFactory.getApplication();
            if (json_string.equals("")) {
                return;
            }
            super.onPostExecute(json_string);
            dialog.dismiss();
            try {
                JSONObject json_object = new JSONObject(json_string);
                JApplicationSite.execute(json_object);
                if (json_object.has("link_redirect")) {
                    String link_redirect = json_object.getString("link_redirect");
                    link_redirect=link_redirect.replaceAll("&amp;", "&");
                    System.out.println(link_redirect);
                    app.setRedirect(link_redirect);
                    return;
                }
                component_content = json_object.has("component_content") ? json_object.getString("component_content") : "";

                String version = json_object.has("version") ? json_object.getString("version") : "";
                String local_version = VTVConfig.get_version();

                if (version != local_version) {
                    int root_id = json_object.getInt("root_id");
                    JSONObject children = json_object.getJSONObject("children");
                    modules= json_object.has("modules")?json_object.getJSONArray("modules"):new JSONArray();
                    JSONObject active_menu_item = json_object.getJSONObject("active_menu_item");
                    JSONArray list_menu_item = json_object.getJSONArray("list_menu_item");
                    JMenu JMenu = JFactory.getMenu();
                    JMenu.setMenuActive(active_menu_item);
                    JMenu.setItems(list_menu_item);
                    set_cache_json_by_screen_size(screen_size_width, screen_size_height, 0, json_object.toString());
                    tree_recurse(root_id, children, null, null, null, 0, 0);

                } else {
                    String cache_json = get_cache_json_by_screen_size(screen_size_width, screen_size_height, 0);
                    if (cache_json != "") {
                        JSONObject json = new JSONObject(cache_json);
                        int root_id = json.getInt("root_id");
                        modules = json_object.getJSONArray("modules");
                        JSONObject children = json.getJSONObject("children");
                        JSONObject active_menu_item = json.getJSONObject("active_menu_item");
                        JSONArray list_menu_item = json_object.getJSONArray("list_menu_item");
                        JMenu JMenu = JFactory.getMenu();
                        JMenu.setItems(list_menu_item);
                        JMenu.setMenuActive(active_menu_item);
                        tree_recurse(root_id, children, null, null, null, 0, 0);
                    }

                }

            } catch (Throwable t) {
                t.printStackTrace();
            }


        }

        @TargetApi(Build.VERSION_CODES.KITKAT)
        private String get_cache_json_by_screen_size(int screen_size_width, int screen_size_height, int menu_item_id) throws UnsupportedEncodingException {
            String cache_file = "android_" + String.valueOf(menu_item_id) + "_" + String.valueOf(screen_size_width) + "X" + String.valueOf(screen_size_height);
        /*byte[] data = cache_file.getBytes("UTF-8");
        cache_file = Base64.encodeToString(data, Base64.DEFAULT);*/
            cache_file += ".txt";
            String content = "";
            File root = new File(Environment.getExternalStorageDirectory(), "cache");
            if (!root.exists()) {
                root.mkdirs();
            }
            File a_cache_file = new File(root, cache_file);
            //System.out.println(a_cache_file);
            if (a_cache_file.exists()) {

                try {
                    BufferedReader br = new BufferedReader(new FileReader(a_cache_file));
                    try {
                        StringBuilder sb = new StringBuilder();
                        String line = br.readLine();

                        while (line != null) {
                            sb.append(line);
                            sb.append(System.lineSeparator());
                            line = br.readLine();
                        }
                        content = sb.toString();
                    } finally {
                        br.close();
                    }


                } catch (FileNotFoundException e) {
                    return "";
                } catch (UnsupportedEncodingException e) {
                    return "";
                } catch (IOException e) {
                    return "";
                }

            }
            return content;
        }

        private void set_cache_json_by_screen_size(int screen_size_width, int screen_size_height, int menu_item_id, String content) throws UnsupportedEncodingException {
            String cache_file = "android_" + String.valueOf(menu_item_id) + "_" + String.valueOf(screen_size_width) + "X" + String.valueOf(screen_size_height);
       /* byte[] data = cache_file.getBytes("UTF-8");
        cache_file = Base64.encodeToString(data, Base64.DEFAULT);*/
            //this.generateNoteOnSD(cache_file + ".txt", content, "cache");
        }
        public void generateNoteOnSD(String sFileName, String sBody, String folder) {
            String error = "";
            try {
                File root = new File(Environment.getExternalStorageDirectory(), folder);
                if (!root.exists()) {
                    root.mkdirs();
                }
                File gpxfile = new File(root, sFileName);
                FileWriter writer = new FileWriter(gpxfile);
                writer.append(sBody);
                writer.flush();
                writer.close();
                //Toast.makeText(this, "Saved", Toast.LENGTH_SHORT).show();
            } catch (IOException e) {
                e.printStackTrace();
                error = e.getMessage();

            }
        }

        private void writeToFile(String data) {
            JApplication app=JFactory.getApplication();
            try {
                OutputStreamWriter outputStreamWriter = new OutputStreamWriter(app.context.openFileOutput("VTVConfig.txt", Context.MODE_PRIVATE));
                outputStreamWriter.write(data);
                outputStreamWriter.close();
            } catch (IOException e) {
                Log.e("Exception", "File write failed: " + e.toString());
            }
        }
        public void tree_recurse(int root_id, JSONObject root, JSONObject children, JSONObject prev_children, LinearLayout object_parent, int width, int level) {
            JApplication app=JFactory.getApplication();
            if (width == 0) {
                width = screen_size_width;
            }
            if (object_parent == null) {
                View main_linear_layout = app.context.findViewById(R.id.info);


/*
            //add icon chattingfrom
            RelativeLayout.LayoutParams params_chatting = new RelativeLayout.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, ViewGroup.LayoutParams.FILL_PARENT);
            params_chatting.setMargins(0, 100, 0, 10);
            LayoutInflater factory = LayoutInflater.from(context);
            View chattingfrom = factory.inflate(R.layout.chattingfrom, null);
            chattingfrom.setBackgroundColor(0);
            chattingfrom.setLayoutParams(params_chatting);
            ((LinearLayout) main_linear_layout).addView(chattingfrom);

*/




                Random rnd = new Random();
                int currentStrokeColor = 0;
                int scroll_view_id = root_id;
                //add new  scrollview
                ScrollView scroll_view = new ScrollView(app.context);
                scroll_view.setId(scroll_view_id);
                scroll_view.setLayoutParams(new ViewGroup.LayoutParams(screen_size_width, screen_size_height));
                if (debug) {
                    currentStrokeColor = Color.argb(255, rnd.nextInt(256), rnd.nextInt(256), rnd.nextInt(256));
                    scroll_view.setBackgroundColor(currentStrokeColor);

                }

                ((LinearLayout) main_linear_layout).addView(scroll_view);

                //add new row liner layout
                LinearLayout row_linear_layout = new LinearLayout(app.context);
                if (debug) {
                    currentStrokeColor = Color.argb(255, rnd.nextInt(256), rnd.nextInt(256), rnd.nextInt(256));
                    row_linear_layout.setBackgroundColor(currentStrokeColor);

                }

                row_linear_layout.setLayoutParams(new ViewGroup.LayoutParams(width, ViewGroup.LayoutParams.WRAP_CONTENT));
                row_linear_layout.setOrientation(LinearLayout.VERTICAL);
                scroll_view.addView(row_linear_layout);
                object_parent = ((LinearLayout) row_linear_layout);
                object_parent.setOrientation(LinearLayout.VERTICAL);
            }
            int level1 = level + 1;
            if (level == 0) {


                JSONArray jsonArray = new JSONArray();
                try {
                    jsonArray = root.getJSONArray(Integer.toString(root_id));
                } catch (JSONException e) {
                }


                for (int i = 0; i < jsonArray.length(); i++) {
                    JSONObject a_children;
                    try {
                        JSONObject a_object = jsonArray.getJSONObject(i);

                        JSONObject prev_object = new JSONObject();
                        if (i != 0) {
                            prev_object = jsonArray.getJSONObject(i - 1);
                        } else {
                            prev_object = new JSONObject();
                        }
                        int a_root_id = a_object.getInt("id");
                        LinearLayout a_object_parent = this.render_element_row(object_parent, a_object, width);
                        this.tree_recurse(root_id, root, a_object, prev_object, a_object_parent, width, level1);
                    } catch (JSONException e) {
                        System.out.println(e.toString());
                    }
                }
            } else {

                int a_root_id = 0;
                LinearLayout a_object_parent = new LinearLayout(app.context);
                if (children.has("id")) {
                    try {
                        a_root_id = children.getInt("id");
                        String type = children.getString("type");
                        switch (type) {
                            case "row":
                                a_object_parent = this.render_element_row(object_parent, children, width);
                                break;
                            case "column":
                                a_object_parent = this.render_element_column(object_parent, children, prev_children, width / 12);
                                int object_width = children.getInt("width");
                                width = (width / 12) * object_width;
                                break;
                        /*case "input":
                            EditText input_object = this.render_element_edit_text(object_parent, children);*/
                            case "input":
                                EditText input_object = this.render_element_edit_text(object_parent, children);
                                break;
                            case "banner_rotator":
                                elementBanner_RotatorHelper.render_banner_rotator(app.context, object_parent, children, width);
                                break;
                            case "link_image":
                                element_link_image_helper.render_element(app.context, object_parent, children, width);
                                break;
                            case "grid":
                                element_grid_helper.render_element(app.context, object_parent, children, width);
                                break;
                            default:
                                Button return_object = this.render_element_button(object_parent, children);
                                //code here
                                break;
                        }
                    } catch (JSONException e) {
                        System.out.println(e.toString());
                    }
                }
                try {
                    JSONArray a_children = root.getJSONArray(Integer.toString(a_root_id));

                    if (a_children != null && a_children.length() > 0) {
                        for (int i = 0; i < a_children.length(); i++) {
                            try {
                                JSONObject prev_object = new JSONObject();
                                if (i != 0) {
                                    prev_object = a_children.getJSONObject(i - 1);
                                } else {
                                    prev_object = new JSONObject();
                                }
                                JSONObject a_object = a_children.getJSONObject(i);
                                this.tree_recurse(a_root_id, root, a_object, prev_object, a_object_parent, width, level1);
                            } catch (JSONException e) {
                                System.out.println(e.toString());
                            }
                        }
                    }
                } catch (JSONException e) {
                    System.out.println(e.toString());
                }

            }
        }


        public LinearLayout render_element_row(LinearLayout parent_object, JSONObject object, int width) {
            JApplication app=JFactory.getApplication();
            LinearLayout linear_layout = new LinearLayout(app.context);
            try {
                int id = object.getInt("id");
                String type = object.getString("type");
                linear_layout.setId(id);
                Random rnd = new Random();
                if (debug) {
                    int currentStrokeColor = Color.argb(255, rnd.nextInt(256), rnd.nextInt(256), rnd.nextInt(256));
                    linear_layout.setBackgroundColor(currentStrokeColor);
                }
                linear_layout.setLayoutParams(new ViewGroup.LayoutParams(width, ViewGroup.LayoutParams.WRAP_CONTENT));
                linear_layout.setOrientation(LinearLayout.HORIZONTAL);
                if (debug) {
                    //linear_layout=a_render_element_text_view("row-"+id+":"+Integer.toString(width),id,linear_layout);
                }
                ((LinearLayout) parent_object).addView(linear_layout);

            } catch (JSONException e) {
                e.printStackTrace();
            }
            return linear_layout;
        }

        private static class component_params {
            String link;
            LinearLayout linear_layout;

            component_params(String link, LinearLayout linear_layout) {
                this.link = link;
                this.linear_layout = linear_layout;
            }
        }

        private static class component_response {
            String link;
            LinearLayout linear_layout;
            String content;

            component_response(String link, LinearLayout linear_layout, String content) {
                this.link = link;
                this.linear_layout = linear_layout;
                this.content = content;
            }
        }

        public LinearLayout render_element_column(LinearLayout parent_object, JSONObject object, JSONObject prev_object, int width) {
            JApplication app=JFactory.getApplication();
            LinearLayout linear_layout = new LinearLayout(app.context);
            try {
                int id = object.getInt("id");
                int object_width = object.getInt("width");
                int a_width = width * object_width;
                String type = object.getString("type");
                linear_layout.setId(id);
                Resources resource = app.context.getResources();
                Random rnd = new Random();

                if (debug) {
                    int currentStrokeColor = Color.argb(255, rnd.nextInt(256), rnd.nextInt(256), rnd.nextInt(256));
                    linear_layout.setBackgroundColor(currentStrokeColor);
                }
                linear_layout.setOrientation(LinearLayout.VERTICAL);
                //offset
                try {
                    int prev_id = 0;
                    if (prev_object.has("id")) {
                        prev_id = prev_object.getInt("id");
                    }
                    String prev_type = "";

                    if (prev_object.has("type")) {
                        prev_type = prev_object.getString("type");
                    }
                    int offset = 0;
                    if (prev_type.toLowerCase().contains("column")) {
                        offset = object.getInt("gs_x") - (prev_object.getInt("gs_x") + prev_object.getInt("width"));
                        linear_layout.setPadding(width * offset, 0, 0, 0);
                    } else {
                        offset = object.getInt("gs_x");
                        linear_layout.setPadding(width * offset, 0, 0, 0);
                    }
                    a_width += width * offset;
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                //linear_layout = a_render_element_text_view("column-" + id + ":" + Integer.toString(a_width), id, linear_layout);
                linear_layout.setLayoutParams(new ViewGroup.LayoutParams(a_width, ViewGroup.LayoutParams.WRAP_CONTENT));

                String position = object.getString("position");
                if (position.toLowerCase().contains("position-component")) {

                    String host_tmpl_component = host.replaceAll("os=android&", "");
                    host_tmpl_component = host_tmpl_component + "&tmpl=android&layout=android";
                    try {
                        JApplicationCms.execute_component(app.context, linear_layout, host, component_content);
                    } catch (vantinviet.banhangonline88.libraries.legacy.exception.exception exception) {
                        exception.printStackTrace();
                    }
                    //((LinearLayout) linear_layout).addView(edit_text);
                } else {

                    for (int i = 0; i < modules.length(); i++) {
                        JSONObject module = modules.getJSONObject(i);
                        String module_position = module.getString("position");
                        if (module_position.toLowerCase().contains("position-" + String.valueOf(id))) {
                            String module_name = module.getString("module");
                            switch (module_name) {
                                case "mod_menu":
                                    mod_menu.render_menu(app.context, linear_layout, module, width);
                                    break;
                                case "mod_virtuemart_category":
                                    mod_virtuemart_category_helper.render_module(app.context, linear_layout, module, width);
                                    break;
                                default:
                                    //Button return_object = this.render_element_button(object_parent, children);
                                    //code here
                                    break;
                            }
                        }
                    }
                }

                ((LinearLayout) parent_object).addView(linear_layout);

            } catch (JSONException e) {
                e.printStackTrace();
            }
            return linear_layout;
        }

        public Button render_element_button(View parent_object, JSONObject object) {
            JApplication app=JFactory.getApplication();
            Button btn = new Button(app.context);
            try {

                int id = object.getInt("id");
                String type = object.getString("type");
                btn.setId(id);
                btn.setText(type + "-" + Integer.toString(id));
                Resources resource = app.context.getResources();
                ((LinearLayout) parent_object).addView(btn);

            } catch (JSONException e) {
                e.printStackTrace();
            }
            return btn;
        }

        public BootstrapEditText render_element_edit_text(View parent_object, JSONObject object) {
            JApplication app=JFactory.getApplication();
            BootstrapEditText edit_text = new BootstrapEditText(app.context);

            try {

                int id = object.getInt("id");
                String type = object.getString("type");
                edit_text.setId(id);
                edit_text.setBootstrapBrand(DefaultBootstrapBrand.PRIMARY);
                edit_text.setText(type + "-" + Integer.toString(id));
                Resources resource = app.context.getResources();
                ((LinearLayout) parent_object).addView(edit_text);

            } catch (JSONException e) {
                e.printStackTrace();
            }
            return edit_text;
        }
        @Override
        protected void onPreExecute() {
            super.onPreExecute();

        }

        @Override
        protected String doInBackground(String... params) {
            String link = params[0];


            String return_json = "";
            //String return_json = JApplication.get_content_website(link);
            return params[0];
        }


    }

    private static void initChatting() {

    }
}
