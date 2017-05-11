package vantinviet.core.libraries.joomla.form.fields;

import android.text.InputType;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AbsoluteLayout;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

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
public class JFormFieldRangeOfIntegers extends JFormField{
    static Map<String, JFormFieldRangeOfIntegers> map_form_field_text = new HashMap<String, JFormFieldRangeOfIntegers>();
    private AbsoluteLayout control_linear_layout;

    public JFormFieldRangeOfIntegers(JFormField field, String type, String name, String group, String value){
        this.type=type;
        this.name=name;
        this.group=group;
        this.option=field;
        this.value=value;
    }
    public JFormFieldRangeOfIntegers(){
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
        BootstrapEditText input_number = new BootstrapEditText(context);
        input_number.setBootstrapBrand(DefaultBootstrapBrand.PRIMARY);
        input_number.setBootstrapSize(DefaultBootstrapSize.LG);
        input_number.setInputType(InputType.TYPE_CLASS_NUMBER);
        input_number.setText(this.value);
        input_number.setTag(this.key);
        input_number.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT));

        this.key_id= JUtilities.getRandomInt(0,999999);
        input_number.setId(this.key_id);



        this.control_linear_layout = new AbsoluteLayout (context);

        TextView stateTitletv = new TextView(context);

        stateTitletv.setText("tv1");






        RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, ViewGroup.LayoutParams.FILL_PARENT);
        params.setMargins(0, 100, 0, 10);




        stateTitletv.setLayoutParams(params);
        LinearLayout content_control_linear_layout=new LinearLayout(context);
        ((LinearLayout ) content_control_linear_layout).addView(stateTitletv);




        ((LinearLayout) linear_layout).addView(input_number);





        linear_layout.setGravity(LinearLayout.TEXT_ALIGNMENT_GRAVITY);
        return (View)linear_layout;
    }


    public String getValue(){
        JApplication app= JFactory.getApplication();
        BootstrapEditText output_box = (BootstrapEditText) app.getCurrentActivity().findViewById(this.key_id);
        return output_box.getText().toString();
    }



}
