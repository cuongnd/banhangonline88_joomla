package vantinviet.core.libraries.utilities;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Color;
import android.util.Base64;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.nio.charset.Charset;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import vantinviet.core.R;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 6/7/2016.
 */
public class JUtilities {
    private static Gson gson;

    public static String callURL(String myURL) {
        System.out.println("Requeted URL:" + myURL);
        StringBuilder sb = new StringBuilder();
        URLConnection urlConn = null;
        InputStreamReader in = null;
        try {
            URL url = new URL(myURL);
            urlConn = url.openConnection();
            if (urlConn != null)
                urlConn.setReadTimeout(60 * 1000);
            if (urlConn != null && urlConn.getInputStream() != null) {
                in = new InputStreamReader(urlConn.getInputStream(),
                        Charset.defaultCharset());
                BufferedReader bufferedReader = new BufferedReader(in);
                if (bufferedReader != null) {
                    int cp;
                    while ((cp = bufferedReader.read()) != -1) {
                        sb.append((char) cp);
                    }
                    bufferedReader.close();
                }
            }
            in.close();
        } catch (Exception e) {
            throw new RuntimeException("Exception while calling URL:"+ myURL, e);
        }

        return sb.toString();
    }
    public static Gson getGsonParser() {
        if (gson == null) {
            GsonBuilder gsonBuilder = new GsonBuilder();
            gson = gsonBuilder.create();
        }
        return gson;
    }

    private static Set<Class> getClassesInPackage(String packageName) {
        Set<Class> classes = new HashSet<Class>();
        String packageNameSlashed = "/" + packageName.replace(".", "/");
        // Get a File object for the package
        URL directoryURL = Thread.currentThread().getContextClassLoader().getResource(packageNameSlashed);
        if (directoryURL == null) {
            //LOG.warn("Could not retrieve URL resource: " + packageNameSlashed);
            return classes;
        }

        String directoryString = directoryURL.getFile();
        if (directoryString == null) {
            //LOG.warn("Could not find directory for URL resource: " + packageNameSlashed);
            return classes;
        }

        File directory = new File(directoryString);
        if (directory.exists()) {
            // Get the list of the files contained in the package
            String[] files = directory.list();
            for (String fileName : files) {
                // We are only interested in .class files
                if (fileName.endsWith(".class")) {
                    // Remove the .class extension
                    fileName = fileName.substring(0, fileName.length() - 6);
                    try {
                        classes.add(Class.forName(packageName + "." + fileName));
                    } catch (ClassNotFoundException e) {
                        //LOG.warn(packageName + "." + fileName + " does not appear to be a valid class.", e);
                    }
                }
            }
        } else {
            //LOG.warn(packageName + " does not appear to exist as a valid package on the file system.");
        }
        return classes;
    }
    private static String internalEncoding = "UTF-8";
    public static String http_build_query(Map<String, String> params) throws UnsupportedEncodingException {

        List<String> list_query = new ArrayList<String>();
        String result = "";
        for(Map.Entry<String, String> e : params.entrySet()){
            if(e.getKey().isEmpty()) continue;
            list_query.add(URLEncoder.encode(e.getKey(), internalEncoding) + "=" +URLEncoder.encode(e.getValue(), internalEncoding));
        }
        String[] array_query = new String[ list_query.size() ];
        list_query.toArray( array_query );
        result=  implodeArray(array_query,"&");
        return result;
    }
    public static String http_build_query_form(Map<String, String> params) throws UnsupportedEncodingException {

        List<String> list_query = new ArrayList<String>();
        String result = "";
        for(Map.Entry<String, String> e : params.entrySet()){
            if(e.getKey().isEmpty()) continue;
            list_query.add("jform["+URLEncoder.encode(e.getKey(), internalEncoding) + "]=" +URLEncoder.encode(e.getValue(), internalEncoding));
        }
        String[] array_query = new String[ list_query.size() ];
        list_query.toArray( array_query );
        result=  implodeArray(array_query,"&");
        return result;
    }
    /**
     * Method to join array elements of type string
     * @author Hendrik Will, imwill.com
     * @param inputArray Array which contains strings
     * @param glueString String between each array element
     * @return String containing all array elements seperated by glue string
     */
    public static String implodeArray(String[] inputArray, String glueString) {

    /** Output variable */
        String output = "";

        if (inputArray.length > 0) {
            StringBuilder sb = new StringBuilder();
            sb.append(inputArray[0]);

            for (int i=1; i<inputArray.length; i++) {
                sb.append(glueString);
                sb.append(inputArray[i]);
            }

            output = sb.toString();
        }

        return output;
    }

    public static Map getMapString(JSONArray item_json_array, String key, String value) {
        if(item_json_array==null)
        {
            return new HashMap<String, String>();
        }
        Map<String, String> a_map = new HashMap<String, String>();
        for (int i=0;i<item_json_array.length();i++){
            try {
                JSONObject item=item_json_array.getJSONObject(i);
                String a_key = item.getString(key);
                String a_value = item.getString(value);
                a_map.put(a_key,a_value);
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }
        return a_map;
    }

    public static int getRandomInt(int min, int max) {
        int random = (int )(Math.random() * max + min);
        return random;
    }

    public static void show_alert_dialog(final String url) {
        final JApplication app= JFactory.getApplication();
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                app.getCurrentActivity());

        alertDialogBuilder.setTitle("Error load page");

        alertDialogBuilder
                .setMessage("Are you sure want to trying load page?")
                .setCancelable(false)
                .setPositiveButton("Yes",new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog,int id) {
                        app.setRedirect(url);
                        // if this button is clicked, close
                        // current activity
                        //menu.this.finish();
                    }
                })
                .setNegativeButton("No",new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog,int id) {
                        // if this button is clicked, just close
                        // the dialog box and do nothing
                        dialog.cancel();
                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();

        // show it
        alertDialog.show();
    }

    public static boolean in_array(ArrayList<String> var_array, String need_var) {
        for (String item: var_array) {
            if(item.equals(need_var)){
                return  true;
            }
        }
        return  false;
    }
    public static boolean is_hex_color_code(final String hexColorCode) {
        String HEX_PATTERN = "^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$";
        Pattern pattern = Pattern.compile(HEX_PATTERN);
        Matcher matcher = pattern.matcher(hexColorCode);
        return matcher.matches();

    }

    public static int getColor(String hexColorCode) {
        if(is_hex_color_code(hexColorCode)){
            return Color.parseColor(hexColorCode);
        }else{
            return 0;
        }


    }
    public static <T> T[] concatAll(T[] first, T[]... rest) {
        int totalLength = first.length;
        for (T[] array : rest) {
            totalLength += array.length;
        }
        T[] result = Arrays.copyOf(first, totalLength);
        int offset = first.length;
        for (T[] array : rest) {
            System.arraycopy(array, 0, result, offset, array.length);
            offset += array.length;
        }
        return result;
    }
    public static <T> Map<String, String> concatAllMap(Map<String, String> ... list_map) {
        Map<String, String> result_map = new HashMap<String, String>();;
        // create list
        List<String> map_key = new ArrayList<String>();

        for (Map<String, String> current_map : list_map) {
            for (Map.Entry<String, String> entry : current_map.entrySet())
            {
                String key=entry.getKey();
                String value=entry.getValue();
                if(!map_key.contains(key))
                {
                    result_map.put(key,value);
                }
            }
        }

        return result_map;
    }
    public static String get_string_by_string_base64(String a_string) {

        byte[] data= Base64.decode(a_string, Base64.DEFAULT);
        // create alert dialog
        try {

            a_string=new String(data, "UTF-8");
        } catch (UnsupportedEncodingException e) {
            e.printStackTrace();

            return "";
        }
        return a_string;
    }

    /**
     * Generate top layer progress indicator.
     *
     * @param context    activity context
     * @param cancelable can be progress layer canceled
     * @return dialog
     */
    public static ProgressDialog generateProgressDialog(Context context, boolean cancelable) {
        ProgressDialog progressDialog = new ProgressDialog(context);
        progressDialog.setMessage(context.getString(R.string.Loading));
        progressDialog.setCancelable(cancelable);
        return progressDialog;
    }}

