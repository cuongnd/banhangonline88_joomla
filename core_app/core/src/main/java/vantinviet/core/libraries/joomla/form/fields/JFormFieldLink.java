package vantinviet.core.libraries.joomla.form.fields;

import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.beardedhen.androidbootstrap.BootstrapEditText;
import com.beardedhen.androidbootstrap.BootstrapLabel;

import java.util.HashMap;
import java.util.Map;

import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.joomla.form.JFormField;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

/**
 * Created by cuongnd on 6/11/2016.
 */
public class JFormFieldLink extends JFormField{
    static Map<String, JFormFieldText> map_form_field_text = new HashMap<String, JFormFieldText>();
    public JFormFieldLink(JFormField field, String type, String name, String group, String value){
        this.type=type;
        this.name=name;
        this.group=group;
        this.option=field;
        this.value=value;
    }
    public JFormFieldLink(){
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
        TextView text_view = new TextView(context);
        text_view.setText(this.value);
        text_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        this.key_id= JUtilities.getRandomInt(0,999999);
        text_view.setId(this.key_id);
        text_view.setTag(this.key);
        text_view.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                String tag= (String) v.getTag();
                JFormFieldLink form_field_link=(JFormFieldLink)JFormField.map_form_field.get(tag);
                JFormField option=form_field_link.getOption();
                String link="";
                JApplication app=JFactory.getApplication();



                // TODO Auto-generated method stub
                //DO you work here

            }
        });
        ((LinearLayout) linear_layout).addView(text_view);
        linear_layout.setGravity(LinearLayout.TEXT_ALIGNMENT_GRAVITY);
        return (View)linear_layout;
    }
    public String getValue(){
        JApplication app= JFactory.getApplication();
        BootstrapEditText output_box = (BootstrapEditText) app.context.findViewById(this.key_id);
        return output_box.getText().toString();
    }


    public void getOnClickListener() {

    }

}
