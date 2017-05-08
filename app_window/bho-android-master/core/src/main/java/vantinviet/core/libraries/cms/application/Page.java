package vantinviet.core.libraries.cms.application;

import java.util.ArrayList;
import java.util.Map;

import vantinviet.core.libraries.html.bootstrap.Template;
import vantinviet.core.libraries.html.module.Module;

/**
 * Created by cuong on 5/8/2017.
 */

public class Page {
    private long id;
    public String title;
    public String text;
    public String component_response;
    public Template template;
    ArrayList<Module> modules=new ArrayList<Module>();
    private Map<String, String> list_input;

    public Page() {
    }

    public long getId() {
        return id;
    }

    public void setId(long id) {
        this.id = id;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getText() {
        return text;
    }

    public void setText(String text) {
        this.text = text;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        Page page = (Page) o;

        if (id != page.id) return false;
        if (title != null ? !title.equals(page.title) : page.title != null) return false;
        return !(text != null ? !text.equals(page.text) : page.text != null);
    }

    @Override
    public int hashCode() {
        int result = (int) (id ^ (id >>> 32));
        result = 31 * result + (title != null ? title.hashCode() : 0);
        result = 31 * result + (text != null ? text.hashCode() : 0);
        return result;
    }

    @Override
    public String toString() {
        return "Page{" +
                "id=" + id +
                ", title='" + title + '\'' +
                ", component_response='" + component_response + '\'' +
                ", template='" + template + '\'' +
                ", modules='" + modules + '\'' +
                ", text='" + text + '\'' +
                ", list_input='" + list_input + '\'' +
                '}';
    }

    public Template getTemplate() {
        return template;
    }

    public ArrayList<Module> getModules() {
        return modules;
    }

    public Map<String, String> getList_input() {
        return list_input;
    }

    public String getComponent_response() {
        return component_response;
    }
}
