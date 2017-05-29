package vantinviet.core.libraries.utilities;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.os.Build;
import android.support.annotation.RequiresApi;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;

import vantinviet.core.R;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

import static vantinviet.core.libraries.legacy.application.JApplication.getCurrentActivity;

/**
 * Created by cuong on 5/29/2017.
 */

public class JAlert {
    public static void show(int MessageType, final int message, final Object object, final String method_name) {
        final JApplication app= JFactory.getApplication();
        final Class noparams[] = {};
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                getCurrentActivity());
        alertDialogBuilder
                .setTitle(MessageType)
                .setMessage(message)
                .setCancelable(false)
                .setPositiveButton(R.string.str_close,new DialogInterface.OnClickListener() {
                    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                    public void onClick(DialogInterface dialog, int id) {
                        try {
                            Method method;
                            method = object.getClass().getDeclaredMethod(method_name,noparams);
                            method.invoke(object);
                        } catch (IllegalAccessException e) {
                            e.printStackTrace();
                        } catch (InvocationTargetException e) {
                            e.printStackTrace();
                        } catch (NoSuchMethodException e) {
                            e.printStackTrace();
                        }
                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();

        // show it
        alertDialog.show();
    }
    public static void show(int MessageType, final int message) {
        final JApplication app= JFactory.getApplication();
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                getCurrentActivity());
        alertDialogBuilder
                .setTitle(MessageType)
                .setMessage(message)
                .setCancelable(false)
                .setPositiveButton(R.string.str_close,new DialogInterface.OnClickListener() {
                    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                    public void onClick(DialogInterface dialog, int id) {

                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();

        // show it
        alertDialog.show();
    }
    public static void show(int MessageType, String message) {
        final JApplication app= JFactory.getApplication();
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                getCurrentActivity());
        alertDialogBuilder
                .setTitle(MessageType)
                .setMessage(message)
                .setCancelable(false)
                .setPositiveButton(R.string.str_close,new DialogInterface.OnClickListener() {
                    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                    public void onClick(DialogInterface dialog, int id) {

                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();

        // show it
        alertDialog.show();
    }
}
