package vantinviet.banhangonline88;

import android.app.Application;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.res.Resources;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.provider.Settings;
import android.support.annotation.NonNull;
import android.support.annotation.VisibleForTesting;
import android.support.test.espresso.IdlingResource;
import android.support.v4.app.NotificationCompat;
import android.text.TextUtils;
import android.util.DisplayMetrics;

import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.Volley;
import com.facebook.FacebookSdk;

import java.util.Locale;

import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.api.OkHttpStack;
import vantinviet.banhangonline88.entities.Notification;
import vantinviet.banhangonline88.testing.EspressoIdlingResource;
import timber.log.Timber;
import vantinviet.banhangonline88.ux.MainActivity;

import static vantinviet.banhangonline88.ux.MainActivity.MyPREFERENCES;
import static vantinviet.banhangonline88.ux.MainActivity.SESSION;

/**
 * Created by petr.melicherik.
 */
public class MyApplication extends Application {
    public static final String PACKAGE_NAME = MyApplication.class.getPackage().getName();

    private static final String TAG = MyApplication.class.getSimpleName();


    public static String APP_VERSION = "0.0.0";
    public static String ANDROID_ID = "0000000000000000";

    private static MyApplication mInstance;

    private RequestQueue mRequestQueue;


    public static synchronized MyApplication getInstance() {
        return mInstance;
    }


    /**
     * Method sets app specific language localization by selected shop.
     * Have to be called from every activity.
     *
     * @param lang language code.
     */
    public static void setAppLocale(String lang) {
        Resources res = mInstance.getResources();
        DisplayMetrics dm = res.getDisplayMetrics();
        android.content.res.Configuration conf = res.getConfiguration();
        conf.locale = new Locale(lang);
        Timber.d("Setting language: %s", lang);
        res.updateConfiguration(conf, dm);
    }

    /**
     * Method provides defaultRetryPolice.
     * First Attempt = 14+(14*1)= 28s.
     * Second attempt = 28+(28*1)= 56s.
     * then invoke Response.ErrorListener callback.
     *
     * @return DefaultRetryPolicy object
     */
    public static DefaultRetryPolicy getDefaultRetryPolice() {
        return new DefaultRetryPolicy(14000, 2, 1);
    }

    @Override
    public void onCreate() {
        super.onCreate();
        mInstance = this;
        FacebookSdk.sdkInitialize(this);

        if (BuildConfig.DEBUG) {
            Timber.plant(new Timber.DebugTree());
        } else {
            // TODO example of implementation custom crash reporting solution -  Crashlytics.
//            Fabric.with(this, new Crashlytics());
//            Timber.plant(new CrashReportingTree());
        }


        try {
            ANDROID_ID = Settings.Secure.getString(getContentResolver(), Settings.Secure.ANDROID_ID);
            if (ANDROID_ID == null || ANDROID_ID.isEmpty()) {
                ANDROID_ID = "0000000000000000";
            }
        } catch (Exception e) {
            ANDROID_ID = "0000000000000000";
        }
        try {
            PackageInfo packageInfo = getPackageManager().getPackageInfo(getPackageName(), 0);
            APP_VERSION = packageInfo.versionName;
        } catch (PackageManager.NameNotFoundException e) {
            // should never happen
            Timber.e(e, "App versionName not found. WTF?. This should never happen.");
        }
    }

    /**
     * Method check, if internet is available.
     *
     * @return true if internet is available. Else otherwise.
     */
    public boolean isDataConnected() {
        ConnectivityManager connectMan = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = connectMan.getActiveNetworkInfo();
        return activeNetworkInfo != null && activeNetworkInfo.isConnectedOrConnecting();
    }

    public boolean isWiFiConnection() {
        ConnectivityManager connectMan = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        NetworkInfo activeNetworkInfo = connectMan.getActiveNetworkInfo();
        return activeNetworkInfo != null && activeNetworkInfo.getType() == ConnectivityManager.TYPE_WIFI;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////// Volley request ///////////////////////////////////////////////////////////////////////////////////////
    public RequestQueue getRequestQueue() {
        if (mRequestQueue == null) {
            mRequestQueue = Volley.newRequestQueue(this, new OkHttpStack());
        }
        return mRequestQueue;
    }

    @VisibleForTesting
    public void setRequestQueue(RequestQueue requestQueue) {
        mRequestQueue = requestQueue;
    }

    @VisibleForTesting
    public IdlingResource getCountingIdlingResource() {
        return EspressoIdlingResource.getIdlingResource();
    }

    public <T> void addToRequestQueue(Request<T> req, String tag) {
        // set the default tag if tag is empty
        req.setTag(TextUtils.isEmpty(tag) ? TAG : tag);
        getRequestQueue().add(req);
    }

    public void cancelPendingRequests(Object tag) {
        if (mRequestQueue != null) {
            mRequestQueue.cancelAll(tag);
        }
    }
    private void addNotification() {
        NotificationCompat.Builder builder =
                new NotificationCompat.Builder(this)
                        .setSmallIcon(R.drawable.ic_launcher)
                        .setContentTitle("Notifications Example")
                        .setContentText("This is a test notification")

                ;

        Intent notificationIntent = new Intent(this, MainActivity.class);
        PendingIntent contentIntent = PendingIntent.getActivity(this, 0, notificationIntent,
                PendingIntent.FLAG_UPDATE_CURRENT);
        builder.setContentIntent(contentIntent);

        // Add as notification
        NotificationManager manager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        manager.notify(12, builder.build());




    }

    public void getNotification() {


        String url=EndPoints.LINK_NOTIFICATION;
        url= get_token_android_link(url);
        String session=get_session();
        GsonRequest<Notification> getNotification = new GsonRequest<>(Request.Method.GET, url, null, Notification.class,
                new Response.Listener<Notification>() {
                    @Override
                    public void onResponse(@NonNull Notification response) {
                        Timber.d("Available shops response: %s", response.toString());
                        addNotification();

                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {


            }
        });
        addToRequestQueue(getNotification, CONST.CATEGORY_REQUESTS_TAG);
    }

    public String get_session() {
        final SharedPreferences sharedpreferences = getSharedPreferences(MyPREFERENCES, Context.MODE_PRIVATE);
        String session=sharedpreferences.getString(SESSION,"");
        return session;
    }

    public String get_token_android_link(String url) {
        String session=get_session();
        url=url+"&tmpl=component&ignoreMessages=true&format=json&os=android&token="+session+"&"+session+"=1";
        return url;

    }
    //////////////////////// end of Volley request. ///////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // TODO example of implementation custom crash reporting solution -  Crashlytics.
//    /**
//     * A tree which logs important information for crash reporting.
//     */
//    private static class CrashReportingTree extends Timber.Tree {
//        @Override
//        protected void log(int priority, String tag, String message, Throwable t) {
//            // Define message log priority
//            if (priority <= android.util.Log.VERBOSE) {
//                return;
//            }
//
//            if (t != null) {
//                if (message != null) logMessage(tag, message);
//                Crashlytics.logException(t);
//            } else {
//                logMessage(tag, message);
//            }
//        }
//
//        private void logMessage(String tag, String message) {
//            if (tag != null)
//                Crashlytics.log("TAG: " + tag + ". MSG: " + message);
//            else
//                Crashlytics.log(message);
//        }
//    }

}
