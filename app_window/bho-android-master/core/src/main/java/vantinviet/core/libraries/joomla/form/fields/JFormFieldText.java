package vantinviet.core.libraries.joomla.form.fields;

import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.beardedhen.androidbootstrap.BootstrapEditText;
import com.beardedhen.androidbootstrap.BootstrapLabel;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapBrand;
import com.beardedhen.androidbootstrap.api.defaults.DefaultBootstrapSize;

import java.util.HashMap;
import java.util.Map;

import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.form.JFormField;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuongnd on 6/11/2016.
 */
public class JFormFieldText extends JFormField{
    static Map<String, JFormFieldText> map_form_field_text = new HashMap<String, JFormFieldText>();
    public JFormFieldText(JFormField field,String type, String name, String group,String value){
        this.type=type;
        this.name=name;
        this.group=group;
        this.option=field;
        this.value=value;
    }
    public JFormFieldText(){
    }


    @Override
    public View getInput() {
        LinearLayout linear_layout = new LinearLayout(context);
        JFormField option=this.option;
        boolean show_label=true;
        show_label = option.getShowLabel();

        if(show_label){
            BootstrapLabel label_text = new BootstrapLabel(context);
            label_text.setText(this.label);
            label_text.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));
            ((LinearLayout) linear_layout).addView(label_text);
        }
        BootstrapEditText edit_text = new BootstrapEditText(context);
        edit_text.setBootstrapBrand(DefaultBootstrapBrand.PRIMARY);
        edit_text.setBootstrapSize(DefaultBootstrapSize.LG);
        edit_text.setText(this.value);
        edit_text.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        this.key_id= JUtilities.getRandomInt(0,999999);
        edit_text.setId(this.key_id);
        edit_text.setOnFocusChangeListener(getOnFocusChangeListener(edit_text,this));


        ((LinearLayout) linear_layout).addView(edit_text);
        linear_layout.setGravity(LinearLayout.TEXT_ALIGNMENT_GRAVITY);
        return (View)linear_layout;
    }

    private View.OnFocusChangeListener getOnFocusChangeListener(BootstrapEditText edit_text, JFormFieldText jFormFieldText) {
        return null;
    }

    public String getValue(){
        JApplication app= JFactory.getApplication();
        BootstrapEditText output_box = (BootstrapEditText) app.context.findViewById(this.key_id);
        return output_box.getText().toString();
    }



}
