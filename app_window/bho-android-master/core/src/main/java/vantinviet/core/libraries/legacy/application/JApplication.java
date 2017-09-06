package vantinviet.core.libraries.legacy.application;

import android.annotation.TargetApi;
import android.app.AlertDialog;
import android.app.FragmentManager;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.media.RingtoneManager;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Build;
import android.preference.PreferenceManager;
import android.support.annotation.RequiresApi;
import android.support.v4.app.RemoteInput;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.app.NotificationCompat;
import android.text.TextUtils;
import android.util.Base64;
import android.util.DisplayMetrics;
import android.util.Log;
import android.webkit.CookieManager;
import android.webkit.URLUtil;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.ScrollView;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;


import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.document.JDocument;
import vantinviet.core.libraries.html.bootstrap.Template;
import vantinviet.core.libraries.html.module.Module;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.input.JInput;
import vantinviet.core.libraries.joomla.language.JText;
import vantinviet.core.libraries.joomla.session.JSession;
import vantinviet.core.libraries.joomla.user.JUser;
import vantinviet.core.libraries.utilities.JUtilities;
import vantinviet.core.libraries.utilities.MessageType;
import com.github.nkzawa.socketio.client.IO;
import com.github.nkzawa.socketio.client.Socket;
import com.github.nkzawa.emitter.Emitter;

import org.json.JSONException;
import org.json.JSONObject;


/**
 * Created by cuongnd on 6/7/2016.
 */
public class JApplication {
    private static final String CURRENT_LINK = "current_link";
    private static final String VTV_PREFERENCES = "vtv_preferences";
    public static JApplication instance;
    private static CacheLinkAndDataPost cachelinkanddatapost;
    private String redirect;
    public VTVConfig vtvConfig=VTVConfig.getInstance();
    Map<String, String> list_input = new HashMap<String, String>();

    private String title;
    private String component_response;
    SharedPreferences shared_preferences;
    public LinearLayout root_linear_layout;
    public int component_width;
    private ScrollView main_scroll_view;
    private byte[] setPostBrowser;
    private String socketId;
    private Resources resources;
    private FragmentManager fragmentManager;
    private android.support.v4.app.FragmentManager supportFragmentManager;
    private RelativeLayout main_relative_layout;
    private SharedPreferences webcache_sharedpreferences;
    public Template template;
    public ArrayList<Module> modules=new ArrayList<Module>();
    public JInput input;
    public static AppCompatActivity currentActivity;
    private String link_redirect;
    public static final String LIST_DATA_RESPONSE_BY_URL = "list_data_response_by_url";
    private ProgressDialog progressDialog;
    private Context context;
    private Map<String, String> data_post;
    private AlertDialog AlertDialog;
    private AlertDialog.Builder alertBuilderDialog;
    private JSession session;
    public Socket mSocket=null;
    private boolean reloadPage=true;

    /* Static 'instance' method */
    public static JApplication getInstance() {

        if (instance == null) {
            instance = new JApplication();
        }
        return instance;
    }

    public JApplication(){
        input=JInput.getInstance();
    }
    public JMenu getMenu() {
        JMenu menu = JMenu.getInstance();
        return menu;
    }
    public Template getTemplate() {
        return template;
    }

    public ArrayList<Module> getModules() {
        return modules;
    }

    public String get_session() {
        //final SharedPreferences sharedpreferences = getSharedPreferences(MyPREFERENCES, Context.MODE_PRIVATE);
        //String session=sharedpreferences.getString(SESSION,"");
        String session="";
        return session;
    }
    public String get_token_android_link(String url) {
        String session=get_session();
        url=url+"&ignoreMessages=true&format=json&os=android&token="+session+"&"+session+"=1";
        return url;

    }
    public String get_page_config_app(String url) {
        String session=get_session();
        url=url+"&get_page_config_app=1&ignoreMessages=true&format=json&os=android&token="+session+"&"+session+"=1";
        return url;

    }
    public void setLink_redirect(String link_redirect) {
        this.link_redirect = link_redirect;
    }

    public String getLink_redirect() {
        return link_redirect;
    }

    public void setProgressDialog(ProgressDialog progressDialog) {
        this.progressDialog = progressDialog;
    }
    public void setAlertDialog(AlertDialog AlertDialog) {
        this.AlertDialog = AlertDialog;
    }
    public AlertDialog getAlertDialog() {
        return  AlertDialog;
    }

    public ProgressDialog getProgressDialog() {
        if(!progressDialog.isShowing())
        {
            progressDialog.setMessage(getContext().getString(R.string.Loading));
        }
        return progressDialog;
    }
    public ProgressDialog getProgressDialog(String messenger) {
        progressDialog.setMessage(messenger);
        return progressDialog;
    }

    public ProgressDialog getProgressDialog(int messenger) {
        progressDialog.setMessage(getContext().getString(messenger));
        return progressDialog;
    }

    public Context getContext() {
        return context;
    }

    public void setContext(Context context) {
        this.context = context;
    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void refresh_page() {
        doExecute();
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void doExecute() {
        Timber.plant(new Timber.DebugTree());
        SharedPreferences webcache_sharedpreferences = getCurrentActivity().getSharedPreferences(LIST_DATA_RESPONSE_BY_URL, getCurrentActivity().MODE_PRIVATE);
        setWebcache_sharedpreferences(webcache_sharedpreferences);

        config_screen_size();
        VTVConfig vtv_config = JFactory.getVTVConfig();
        int caching = vtv_config.getCaching();
        CacheLinkAndDataPost cache_link_and_data_post = getCacheLinkAndDataPostInstance();
        String link = cache_link_and_data_post.getLink();
        Map<String, String> data_post = cache_link_and_data_post.getData_post();
        if (link.isEmpty()) {
            data_post = getData_post();
            link = getLink_redirect();
            if (link == null || link.isEmpty()) {
                link = VTVConfig.getRootUrl();
            }
        }


        if (!URLUtil.isValidUrl(link)) {
            // URL is valid
            link = vtvConfig.getRootUrl() + "/" + link;
        }
        cache_link_and_data_post.setLink(link);
        cache_link_and_data_post.setData_post(data_post);
        cache_link_and_data_post.save();
        setRoot_linear_layout((LinearLayout) getCurrentActivity().findViewById(R.id.root_linear_layout));
        setMain_scroll_view((ScrollView) getCurrentActivity().findViewById(R.id.main_scroll_view));
        setLink_redirect(link);
        final WebView webview = WebView.getInstance();
        getProgressDialog().show();
        if (caching == 1) {
            webcache_sharedpreferences = getWebcache_sharedpreferences();// getCurrentActivity().getSharedPreferences(LIST_DATA_RESPONSE_BY_URL, getCurrentActivity().MODE_PRIVATE);
            String response_data = (String) webcache_sharedpreferences.getString(link, "");
            Timber.d(" webcache_sharedpreferences response_data %s", response_data);
            if (response_data.equals("")) {
                webview.create_browser(link, data_post);

            } else {
                webview.go_to_page(response_data);
            }
        } else {
            final String finalLink = link;
            final Map<String, String> final_Data_post = data_post;
            getCurrentActivity().runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    webview.create_browser(finalLink, final_Data_post);
                }
            });


        }
    }


    public static void config_screen_size() {
        //set screen size
        DisplayMetrics metrics = new DisplayMetrics();
        getCurrentActivity().getWindowManager().getDefaultDisplay().getMetrics(metrics);
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

        int screen_size_width = width;
        int screen_size_height = height;
        if (screenDensity == 0) {
            screenDensity = 1;
        }
        String screenSize = Integer.toString(width / screenDensity) + "x" + Integer.toString(height);
        System.out.println(width / screenDensity);
        VTVConfig vtv_config = JFactory.getVTVConfig();
        System.out.println("vtv_config.rootUrl" + vtv_config.rootUrl);
        String local_version = vtv_config.get_version();
        //initChatting();
        VTVConfig.getInstance().setScreen_size_width(screen_size_width);
        VTVConfig.getInstance().setScreen_size_height(screen_size_height);
        VTVConfig.getInstance().setScreenDensity(screenDensity);
    }


    public void setCurrentActivity(AppCompatActivity currentActivity) {
        this.currentActivity = currentActivity;
    }
    public static AppCompatActivity getCurrentActivity() {
        return  currentActivity;
    }
    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void setRedirect(String link) {
        /*String test_page = "&Itemid=433";
        test_page = "";
        if (link_redirect.equals("")) {
            link_redirect =  vtvConfig.getRootUrl()+ "/index.php?os=android&screenSize=" + vtvConfig.getScreen_size_width() + "&version=" +vtvConfig.getLocal_version() + test_page+"&format=json&get_page_config_app=1";
        } else if (!link_redirect.contains( vtvConfig.getRootUrl())) {
            link_redirect =  vtvConfig.getRootUrl() + "/" + link_redirect;
        }else if(link_redirect.equals(vtvConfig.getRootUrl())){
            link_redirect =  vtvConfig.getRootUrl()+ "/index.php?os=android&screenSize=" + vtvConfig.getScreen_size_width() + "&version=" +vtvConfig.getLocal_version() + test_page+"&format=json&get_page_config_app=1";
        }else if (link_redirect.contains( vtvConfig.getRootUrl())) {
            link_redirect =  link_redirect+ "&os=android&screenSize=" + vtvConfig.getScreen_size_width() + "&version=" +vtvConfig.getLocal_version() + test_page+"&format=json&get_page_config_app=1";
        }*/

        setData_post(null);
        setLink_redirect(link);
        saveCurrentLink(link);
        CacheLinkAndDataPost cache_link_and_data_post=getCacheLinkAndDataPostInstance();
        cache_link_and_data_post.setLink(link);
        cache_link_and_data_post.setData_post(null);
        cache_link_and_data_post.save();
        doExecute();


    }

    private void saveCurrentLink(String link) {
        shared_preferences = PreferenceManager
                .getDefaultSharedPreferences(getCurrentActivity());
        SharedPreferences.Editor editor = shared_preferences.edit();
        editor.putString(CURRENT_LINK, link);
        editor.apply();
    }


    public String getCurrentLink() {
        shared_preferences = PreferenceManager
                .getDefaultSharedPreferences(getCurrentActivity());
        String current_link = shared_preferences.getString(CURRENT_LINK, null);
        return  current_link;
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void setRedirect(String link, Map<String, String> data_post) {
        JApplication app = JFactory.getApplication();
        String screenSize = Integer.toString(VTVConfig.screen_size_width / VTVConfig.screenDensity) + "x" + Integer.toString(VTVConfig.screen_size_height);
        String local_version = VTVConfig.get_version();
        ArrayList<String> listQuery = new ArrayList<String>();
        listQuery.add("os=android");
        listQuery.add("screenSize="+screenSize);
        listQuery.add("version="+local_version);
        String query = TextUtils.join("&", listQuery);

        link = link +"&"+ query;
        System.out.println("link_redirect:" + link);
        setLink_redirect(link);
        setData_post(data_post);
        CacheLinkAndDataPost cache_link_and_data_post=getCacheLinkAndDataPostInstance();
        cache_link_and_data_post.setLink(link);
        cache_link_and_data_post.setData_post(data_post);
        cache_link_and_data_post.save();
        doExecute();

    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void execute(AppCompatActivity mainActivity, String root_url) {
        vtvConfig.setRootUrl(root_url);
        ActionBar actionBar = mainActivity.getSupportActionBar();
        actionBar.hide();
        mainActivity.setContentView(get_layout_activity_main());

        setFragmentManager(mainActivity.getFragmentManager());
        setSupportFragmentManager(mainActivity.getSupportFragmentManager());
        setCurrentActivity(mainActivity);
        setContext(mainActivity.getBaseContext());
        setResources(getResources());
        Configuration configuration=new Configuration();
        configuration.orientation=Configuration.ORIENTATION_LANDSCAPE;
        configuration.keyboardHidden=Configuration.KEYBOARDHIDDEN_YES;
        mainActivity.onConfigurationChanged(configuration);
        setMain_relative_layout((RelativeLayout) mainActivity.findViewById(R.id.main_relative_layout));

        setProgressDialog(JUtilities.generateProgressDialog(mainActivity, false));
        setAlertDialog(JUtilities.generateProgressAlertDialog(mainActivity, false));
        //GifImageView gif_image_view=(GifImageView)getCurrentActivity().findViewById(R.id.bg);

        this.setup_facebook_login();
        check_connection();

    }

    private void setup_login_socket() {
        JUser user=JFactory.getUser();
        if(user!=null){
            JSONObject obj = new JSONObject();
            try {

                obj.put("userName",user.getName().trim());
                // mSocket.send(obj);
                mSocket.emit("setNickname", obj);
                Log.d("SEND setNickname",obj.toString());


            } catch (JSONException e) {
                Log.d("SEND setNickname","ERROR");
                e.printStackTrace();
            }
        }
    }

    private Emitter.Listener onNewMessage = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            getCurrentActivity().runOnUiThread(new Runnable() {
                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    String username;
                    String message;
                    String msg_key;
                    try {
                        username = data.getString("userName");
                        message = data.getString("msg");
                        msg_key = data.getString("msg_key");
                        Timber.d(message);
                    } catch (JSONException e) {
                        return;
                    }

                    // add the message to view
                    addMessage(msg_key,username, message);
                }
            });

        }


    };
    public void setSocketId(String socketId) {
        this.socketId = socketId;
    }

    private Emitter.Listener socketConnected = new Emitter.Listener() {
        @Override
        public void call(final Object... args) {
            getCurrentActivity().runOnUiThread(new Runnable() {



                @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void run() {
                    JSONObject data = (JSONObject) args[0];
                    String socketId;
                    try {
                        socketId = data.getString("socketId");
                        setSocketId(socketId);
                    } catch (JSONException e) {
                        return;
                    }


                }
            });

        }


    };
    @TargetApi(Build.VERSION_CODES.KITKAT_WATCH)
    @RequiresApi(api = Build.VERSION_CODES.JELLY_BEAN)
    private void addMessage(String msg_key,String username, String message) {

        Uri path = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);


        Intent intentQickReply = new Intent(getContext(), getCurrentActivity().getClass());
        intentQickReply.setAction(AppConstant.QUICK_REPLY);
        intentQickReply.putExtra("msg_key",msg_key);
        PendingIntent pendingIntent = PendingIntent.getActivity(getContext(), Integer.parseInt(msg_key), intentQickReply, PendingIntent.FLAG_ONE_SHOT);
        String replyLabel = getCurrentActivity().getResources().getString(R.string.reply_label);
        RemoteInput remoteInput = new RemoteInput.Builder(msg_key)
                .setLabel(replyLabel)
                .build();
        //Button
        // Create the reply action and add the remote input.
        NotificationCompat.Action actionQuickReply =
                new NotificationCompat.Action.Builder(R.drawable.ic_launcher,
                        getCurrentActivity().getResources().getString(R.string.str_answer), pendingIntent)
                        .addRemoteInput(remoteInput)
                        .build();
        //Maybe intent
        Intent IntentRoomChat = new Intent(getContext(), getCurrentActivity().getClass());
        IntentRoomChat.setAction(AppConstant.ROOM_CHAT);
        PendingIntent pendingIntentRoomChat = PendingIntent.getActivity(getContext(), Integer.parseInt(msg_key), IntentRoomChat, PendingIntent.FLAG_UPDATE_CURRENT);
        IntentRoomChat.putExtra("msg_key",msg_key);
        //No intent
        Intent IntentDeleteMessenger = new Intent(getContext(), getCurrentActivity().getClass());
        IntentDeleteMessenger.setAction(AppConstant.DELETE_MESSENGER);
        PendingIntent pendingIntentDeleteMessenger = PendingIntent.getActivity(getContext(), Integer.parseInt(msg_key), IntentDeleteMessenger, PendingIntent.FLAG_UPDATE_CURRENT);
        IntentDeleteMessenger.putExtra("msg_key",msg_key);
        //Notification
        Notification notification = new NotificationCompat.Builder(getContext())
                .setSmallIcon(R.mipmap.ic_launcher)
                .setContentText(message)
                .addAction(actionQuickReply)
                .addAction(R.drawable.facebook_icon, JText._("Room"), pendingIntentRoomChat)
                .addAction(R.drawable.facebook_icon, JText._("Delete"), pendingIntentDeleteMessenger)
                .setContentTitle(username)
                .setAutoCancel(true)
                .setSound(path)
                .build();
        NotificationManager notificationManager = (NotificationManager)
                getCurrentActivity().getSystemService(Context.NOTIFICATION_SERVICE);
        notificationManager.notify(Integer.parseInt(msg_key), notification);

      /*  //What happen when you will click on button
        Intent intent = new Intent(getContext(), getCurrentActivity().getClass());
        intent.putExtra("msg_key",msg_key);
        TaskStackBuilder stackBuilder = TaskStackBuilder.create(getContext());
        // Adds the back stack for the Intent (but not the Intent itself)
        stackBuilder.addParentStack(getCurrentActivity().getClass());
        // Adds the Intent that starts the Activity to the top of the stack
        stackBuilder.addNextIntent(intent);
        PendingIntent pendingIntent = PendingIntent.getActivity(getContext(), 1, intent, PendingIntent.FLAG_ONE_SHOT);
        // Key for the string that's delivered in the action's intent.
        final String KEY_TEXT_REPLY = msg_key;
        String replyLabel = getCurrentActivity().getResources().getString(R.string.reply_label);
        RemoteInput remoteInput = new RemoteInput.Builder(KEY_TEXT_REPLY)
                .setLabel(replyLabel)
                .build();


        //Button
        // Create the reply action and add the remote input.
        NotificationCompat.Action action =
                new NotificationCompat.Action.Builder(R.drawable.ic_launcher,
                        getCurrentActivity().getResources().getString(R.string.str_answer), pendingIntent)
                        .addRemoteInput(remoteInput)
                        .build();


        //Button
        // Create the reply action and add the remote input.
        NotificationCompat.Action actionGoToRoom =
                new NotificationCompat.Action.Builder(R.drawable.ic_launcher,
                        getCurrentActivity().getResources().getString(R.string.strGoToRoom), pendingIntent)
                        .addRemoteInput()

                        .build();


        //Notification
        Notification notification = new NotificationCompat.Builder(getContext())
                .setSmallIcon(R.mipmap.ic_launcher)
                .setContentText(message)
                .setContentTitle(userName)
                .addAction(action) //add buton
                .addAction(actionGoToRoom) //add buton
                .setAutoCancel(true)
                .build();*/

        //Send notification







    }


    private void setup_facebook_login() {

        String package_name = this.getCurrentActivity().getApplicationContext().getPackageName();
        try {
            PackageInfo info= this.getCurrentActivity().getPackageManager().getPackageInfo(
                    package_name,
                    PackageManager.GET_SIGNATURES);
            for (Signature signature : info.signatures) {
                MessageDigest md = MessageDigest.getInstance("SHA");
                md.update(signature.toByteArray());
                Log.d("KeyHash:", Base64.encodeToString(md.digest(), Base64.DEFAULT));
            }
        } catch (PackageManager.NameNotFoundException e) {

        } catch (NoSuchAlgorithmException e) {

        }
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    private void check_connection() {
        if(!isOnline()){
            AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                    getCurrentActivity());
            alertDialogBuilder
                    .setTitle(MessageType.ERROR)
                    .setMessage(R.string.Internal_error)
                    .setCancelable(false)
                    .setPositiveButton(R.string.str_trying,new DialogInterface.OnClickListener() {
                        @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                        public void onClick(DialogInterface dialog, int id) {
                            check_connection();
                        }
                    });
            AlertDialog alertDialog = alertDialogBuilder.create();

            // show it
            alertDialog.show();
            return;
        }

        doExecute();
    }


    public void setAplication(Page page) {
        this.template = page.getTemplate();
        this.modules = page.getModules();
        this.session = JSession.getInstance();
        setSession(page.getSession());

        this.list_input = page.getList_input();
        this.component_response = page.getComponent_response();
        this.input.setList_input(page.getList_input());
        this.input = JInput.getInstance();
        this.getMenu().setMenuactive(page.getMenuActive());
        JUser user=JUser.getInstance();
        user.setActiveUser(page.getActiveUser());
        initSocket();
    }

    private void initSocket() {
        String root_url= vtvConfig.getRootUrl();
        try {
            JUser user=JUser.getInstance();
            JUser activeUser=user.getActiveUser();
            IO.Options options = new IO.Options();
            ArrayList<String> listQuery = new ArrayList<String>();
            String token = session.getToken();
            String title = JDocument.getTitle();
            String name="";
            if(activeUser.getId()!=0){
                name=activeUser.getName();
                listQuery.add("name=" + activeUser.getName());
                listQuery.add("userName=" + activeUser.getUserName());
                listQuery.add("system_user_id="+activeUser.getId());
            }else {

                name = CookieManager.getInstance().getCookie("current_name_user");
                if (name == null || name=="") {
                    name = JUtilities.renderName();
                    CookieManager.getInstance().setCookie("current_name_user", name);
                }
                activeUser.setName(name);
                listQuery.add("name=" + name);
                listQuery.add("userName=" + token);
                listQuery.add("system_user_id=0");
            }
            listQuery.add("title=" + title);
            listQuery.add("token=" + token);
            listQuery.add("device=android");
            String query = TextUtils.join("&", listQuery);
            Timber.d(query);
            options.query = query;
            String current_root_url=root_url+":8888";
            Timber.d(current_root_url);

            String cookie = CookieManager.getInstance().getCookie(current_root_url);
            mSocket = IO.socket(current_root_url,options);
            mSocket.on("newMessage", onNewMessage);
            mSocket.on("connected", socketConnected);
            mSocket.connect();




        } catch (Exception e) {
            e.printStackTrace();
        }

    }

    public String getTitle() {
        return title;
    }

    public Map<String, String> getList_input() {
        return list_input;
    }

    public String getComponent_response() {
        return component_response;
    }

    public int get_Component_width() {
        return component_width;
    }

    public void setMain_scroll_view(ScrollView main_scroll_view) {
        this.main_scroll_view = main_scroll_view;
    }

    public ScrollView getMain_scroll_view() {
        return main_scroll_view;
    }


    public void setResources(Resources resources) {
        this.resources = resources;
    }
    public Resources getResources() {
        return resources;
    }


    public int get_layout_activity_main() {
        return R.layout.vtv_activity_main;
    }


    public void setRoot_linear_layout(LinearLayout root_linear_layout) {
        this.root_linear_layout = root_linear_layout;
    }

    public LinearLayout getRoot_linear_layout() {
        return root_linear_layout;
    }
    public boolean isOnline() {
        ConnectivityManager cm =
                (ConnectivityManager) getCurrentActivity().getSystemService(getContext().CONNECTIVITY_SERVICE);
        NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return netInfo != null && netInfo.isConnectedOrConnecting();
    }
    public void setFragmentManager(FragmentManager fragmentManager) {
        this.fragmentManager = fragmentManager;
    }
    public FragmentManager getFragmentManager() {
        return fragmentManager;
    }

    public void setSupportFragmentManager(android.support.v4.app.FragmentManager supportFragmentManager) {
        this.supportFragmentManager = supportFragmentManager;
    }

    public android.support.v4.app.FragmentManager getSupportFragmentManager() {
        return supportFragmentManager;
    }

    public RelativeLayout getMain_relative_layout() {
        return main_relative_layout;
    }

    public void setMain_relative_layout(RelativeLayout main_relative_layout) {
        this.main_relative_layout = main_relative_layout;
    }

    public SharedPreferences getWebcache_sharedpreferences() {
        return webcache_sharedpreferences;
    }

    public void setWebcache_sharedpreferences(SharedPreferences webcache_sharedpreferences) {
        this.webcache_sharedpreferences = webcache_sharedpreferences;
    }

    public void setData_post(Map<String,String> data_post) {
        this.data_post = data_post;
    }

    public Map<String,String> getData_post() {
        return data_post;
    }

    public void setAlertBuilderDialog(android.app.AlertDialog.Builder alertBuilderDialog) {
        this.alertBuilderDialog = alertBuilderDialog;
    }

    public android.app.AlertDialog.Builder getAlertBuilderDialog() {
        return alertBuilderDialog;
    }

    public void rebuildAlertDialog() {
        setAlertDialog(getAlertBuilderDialog().create());
    }

    public void setSession(JSession session) {
        this.session = session;
    }

    public JSession getSession() {
        return session;
    }

    /* Static 'instance' method */
    public static CacheLinkAndDataPost getCacheLinkAndDataPostInstance() {
        SharedPreferences cache_link_and_data = getCurrentActivity().getSharedPreferences("cache_link_and_data", getCurrentActivity().MODE_PRIVATE);
        String str_cache_link_and_data = (String) cache_link_and_data.getString("cache_link_and_data", "");
        cachelinkanddatapost = JUtilities.getGsonParser().fromJson(str_cache_link_and_data, CacheLinkAndDataPost.class);
        if(cachelinkanddatapost==null){
            cachelinkanddatapost=new CacheLinkAndDataPost();
        }

        return cachelinkanddatapost;
    }

    public void setReloadPage(boolean reloadPage) {
        this.reloadPage = reloadPage;
    }

    public boolean getReloadPage() {
        return this.reloadPage;
    }

    public String getSocketId() {
        return socketId;
    }
}
