package vantinviet.core.libraries.joomla.form;

/**
 * Created by cuongnd on 6/11/2016.
 */
public class JForm {
    private final String name;
    private final String[] option;
    private String[] errors;

    private JForm(String name,String[] option){
        this.name=name;
        this.option=option;
    }
    public void bind(){

    }
    public String[] getErrors(){
        return this.errors;
    }


    private static boolean findField( String name, String group) {
        boolean element=false;
        JFormField[] fields;
        return element;
    }


}
