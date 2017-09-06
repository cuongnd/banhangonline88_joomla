package vantinviet.core.libraries.legacy.controller;

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
    private static Class<?> control_class = null;
    public static JControllerLegacy getInstance(String option){
        String controller="";
        String task="display";
        input=app.input;
        String command=input.getString("task","display");
        if(command.indexOf(".")!=-1){
            String[] items = command.split(".");
            controller=items[0];
            task=items[1];
        }else{
            controller=input.getString("controller","");
            if(controller.equals("")){
                controller=input.getString("ctrl","");
            }
            task=input.getString("task","");
        }
        if(controller.equals("")){
            controller=input.getString("view","");
        }
        input.setString("task",task);

        try {
            control_class = Class.forName(String.format("vantinviet.core.components.com_%s.controllers.%sController%s",option,option.substring(0, 1).toUpperCase()+option.substring(1),controller.substring(0, 1).toUpperCase()+controller.substring(1)));
            Constructor<?> cons = control_class.getConstructor();
            Object object = cons.newInstance();
            instance=(JControllerLegacy)object;
            return instance;
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        }

        instance=new JControllerLegacy();
        return instance;
    }

    public void execute(String task) {
        if(task.equals("")){
            task="display";
        }
        this.task=task;
        //no paramater
        Class noparams[] = {};
        String params="";
        Method method = null;
        try {
            Constructor<?> cons = control_class.getConstructor();
            Object object = cons.newInstance();
            method = object.getClass().getDeclaredMethod(task,noparams);
            method.invoke(object);
        } catch (NoSuchMethodException e) {
            //e.printStackTrace();
            this.display();
        } catch (InvocationTargetException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        }
    }

    public void redirect() {

    }
    public void display() {
        String option=input.getString("option");
        String view=input.getString("view");
        String layout=input.getString("layout","c_default");
        LinearLayout component_linear_layout=input.get_component_linear_layout();
        Class<?> layout_class = null;
        try {
            layout_class = Class.forName(String.format("vantinviet.core.components.%s.views.%s.tmpl.%s",option,view,layout));
            Constructor<?> cons = layout_class.getConstructor(LinearLayout.class);
            Object object = cons.newInstance(component_linear_layout);
        } catch (ClassNotFoundException e) {
            e.printStackTrace();
        } catch (IllegalAccessException e) {
            e.printStackTrace();
        } catch (NoSuchMethodException e) {
            e.printStackTrace();
        } catch (InvocationTargetException e) {
            e.getCause().printStackTrace();
        } catch (InstantiationException e) {
            e.printStackTrace();
        }


    }
}
