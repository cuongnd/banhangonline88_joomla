package vantinviet.banhangonline88.libraries.utilities;

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
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

/**
 * Created by cuongnd on 6/7/2016.
 */
public class JUtilities {
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

}

