package vantinviet.core.libraries.legacy.controller;

import android.support.v4.app.Fragment;
import android.widget.LinearLayout;

import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.Arrays;
import java.util.List;


import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.input.JInput;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuongnd on 03/04/2017.
 */

public class JControllerLegacy {
    private static JControllerLegacy instance;
    private static JInput input;
    public static JApplication app= JFactory.getApplication();
    private String task;

    public JControllerLegacy(){

    }
    /* Static 'instance' method */
    public static JControllerLegacy getInstance(String option) {
        String controller="";
        String task="display";
        input=app.input;
        String command=input.getString("task","display");
        if(command.indexOf(".")!=-1){
            String[] items = command.split(".");
            controller=items[0];
            task=items[1];
        }else{

        }
        input.setString("task",task);
        Class<?> control_class = null;
        try {
            control_class = Class.forName(String.format("vantinviet.core.components.com_%s.%sController",option,controller));
            Constructor<?> cons = control_class.getConstructor();
            Object object = cons.newInstance();
            instance=(JControllerLegacy)object;
        } catch (ClassNotFoundException e) {
            instance=new JControllerLegacy();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        }
        return instance;
    }

    public void execute(String task) {
        this.task=task;
        //no paramater
        Class noparams[] = {};
        Method method = null;
        try {
            method = this.instance.getClass().getDeclaredMethod(task,noparams);
            method.invoke(this.instance,null);
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        }
    }

    public void redirect() {

    }
    public void display() {
        String option=input.getString("option");
        String view=input.getString("view");
        String layout=input.getString("layout");
        LinearLayout component_linear_layout=input.get_component_linear_layout();
        Class<?> control_class = null;
        try {
            control_class = Class.forName(String.format("vantinviet.core.components.%s.views.%s.tmpl.%s",option,view,layout));
            Constructor<?> cons = control_class.getConstructor(LinearLayout.class);
            Object object = cons.newInstance(component_linear_layout);
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        }


    }
}
