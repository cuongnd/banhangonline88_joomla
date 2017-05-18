package vantinviet.core.libraries.legacy.database;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import vantinviet.core.libraries.cms.application.JApplicationSite;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

/**
 * Created by cuong on 5/18/2017.
 */

public class mysqli extends SQLiteOpenHelper {
    // All Static variables
    // Database Version
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "core";
    private static mysqli instance;

    public mysqli(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    public static mysqli getInstance() {
        JApplication app= JFactory.getApplication();
        if (instance == null) {
            instance = new mysqli(app.getContext());
        }
        return instance;
    }

    @Override
    public void onCreate(SQLiteDatabase sqLiteDatabase) {

    }

    @Override
    public void onUpgrade(SQLiteDatabase sqLiteDatabase, int i, int i1) {

    }
}
