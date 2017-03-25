package vantinviet.banhangonline88.entities.module;

import static android.R.attr.name;

/**
 * Created by cuongnd on 25/03/2017.
 */

public class Module {
    int id;
    String title;
    String module;
    String position;
    boolean showtitle;
    String response;
    Params params;
    @Override
    public String toString() {
        return "Module{" +
                "id=" + id +
                ", title='" + title + '\'' +
                ", module='" + module + '\'' +
                ", position='" + position + '\'' +
                ", showtitle='" + showtitle + '\'' +
                ", response='" + response + '\'' +
                ", params='" + params + '\'' +
                '}';
    }

    public String getPosition() {
        return position;
    }

    public String getTitle() {
        return title;
    }
}
