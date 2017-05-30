package vantinviet.core.libraries.html.bootstrap;

/**
 * Created by cuongnd on 22/03/2017.
 */

public class Template {
    int id;
    int home;
    String template;
    int parent_template_style_id;
    public Params params;
    @Override
    public String toString() {
        return "Template{" +
                "id=" + id +
                ", home='" + home + '\'' +
                ", template='" + template + '\'' +
                ", parent_template_style_id='" + parent_template_style_id + '\'' +
                ", params='" + params + '\'' +
                '}';
    }

    public String getTemplateName() {
        return template;
    }

    public Params getParams() {
        return params;
    }
}
