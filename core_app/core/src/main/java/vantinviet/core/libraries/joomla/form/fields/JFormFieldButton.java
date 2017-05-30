package vantinviet.core.libraries.joomla.form.fields;

import android.view.View;

import com.beardedhen.androidbootstrap.BootstrapButton;

import java.util.HashMap;
import java.util.Map;

import vantinviet.core.libraries.joomla.form.JFormField;

/**
 * Created by cuongnd on 6/11/2016.
 */
public class JFormFieldButton extends JFormField{
    static Map<String, JFormFieldButton> map_form_field_button = new HashMap<String, JFormFieldButton>();
    public JFormFieldButton(){

    }

    public JFormFieldButton(JFormField field,String type, String name, String group,String value){

        this.type=type;
        this.name=name;
        this.group=group;
        this.option=field;
        this.value=value;
    }
    @Override
    public View getInput() {
        BootstrapButton button= new BootstrapButton(context);
        button.setText(this.label);
        button.setPadding(20, 10, 20, 10);
        button.setTextSize(23);

        return button;
    }


}
