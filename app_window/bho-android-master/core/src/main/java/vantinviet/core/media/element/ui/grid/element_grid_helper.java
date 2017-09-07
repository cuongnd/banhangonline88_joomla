package vantinviet.core.media.element.ui.grid;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Base64;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.android.http.JSONParser;
import vantinviet.core.libraries.android.registry.JRegistry;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import de.codecrafters.tableview.SortableTableView;
import de.codecrafters.tableview.TableDataAdapter;
import de.codecrafters.tableview.listeners.TableDataClickListener;
import de.codecrafters.tableview.toolkit.SimpleTableHeaderAdapter;
import de.codecrafters.tableview.toolkit.SortStateViewProviders;
import de.codecrafters.tableview.toolkit.TableDataRowColorizers;

/**
 * Created by cuongnd on 12/18/2015.
 */
public class element_grid_helper {



    public static Context main_context;
    public static View main_parent_object;
    public static List<String> list_column_name;
    public static SortableTableView table_view;
    private static class TableClickListener implements TableDataClickListener {


        @Override
        public void onDataClicked(int rowIndex, Object clickedData) {
            String item_string= clickedData.toString();
            Toast.makeText(main_context, item_string, Toast.LENGTH_SHORT).show();
        }
    }

    public static void render_element(Context context, View parent_object, JSONObject object, int width) {
        main_context=context;
        main_parent_object=parent_object;
        table_view=new SortableTableView(context);
        try{
            int id = object.getInt("id");
            table_view.setId(id);
            String params=object.getString("params");
            JSONObject json_object_params=new JSONObject(params);
            System.out.println(json_object_params);
            JRegistry a_params = new JRegistry(json_object_params);

            String formart_header=a_params.get("config_view_grid.formart_header", "", "string");
            byte[] data_formart_header = Base64.decode(formart_header, Base64.DEFAULT);
            formart_header = new String(data_formart_header, "UTF-8");

            JRegistry params_format_header = new JRegistry(new JSONObject(formart_header));

            String mode_select_column_template=params_format_header.get("mode_select_column_template","","String");
            JSONArray array_mode_select_column_template=new JSONArray(mode_select_column_template);
            List<String> list_column_title = new ArrayList<String>();
            list_column_name = new ArrayList<String>();

            for (int i = 0; i < array_mode_select_column_template.length(); i++) {
                JSONObject row = array_mode_select_column_template.getJSONObject(i);
                if(row.has("column_title"))
                {
                    String column_title = row.getString("column_title");
                    list_column_title.add(column_title);
                }

                if(row.has("column_name"))
                {
                    String column_name = row.getString("column_name");
                    list_column_name.add(column_name);

                }

            }

            String[] array_column_title = new String[list_column_title.size()];
            list_column_title.toArray(array_column_title);
            SimpleTableHeaderAdapter simpleTableHeaderAdapter = new SimpleTableHeaderAdapter(context,array_column_title );

            simpleTableHeaderAdapter.setTextColor(context.getResources().getColor(R.color.table_header_text));
            table_view.setHeaderAdapter(simpleTableHeaderAdapter);

            int rowColorEven = context.getResources().getColor(R.color.table_data_row_even);
            int rowColorOdd = context.getResources().getColor(R.color.table_data_row_odd);
            table_view.setHeaderSortStateViewProvider(SortStateViewProviders.brightArrows());

            table_view.setColumnWeight(0, 2);
            table_view.setColumnWeight(1, 3);
            table_view.setColumnWeight(2, 3);
            table_view.setColumnWeight(3, 2);




            String binding_source =a_params.get("data.bindingSource", "", "string");
            String url_get_data= VTVConfig.rootUrl+"index.php?option=com_phpmyadmin&task=datasource.readData&block_id="+String.valueOf(id);
            table_view.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, 1000));
            (new AsyncJsonDataLoader()).execute(url_get_data);

        }catch (JSONException e)
        {

        } catch (Exception e) {
            e.printStackTrace();
        }

    }
    private static class AsyncJsonDataLoader extends AsyncTask<String, Void, String> {
        private final ProgressDialog dialog = new ProgressDialog(main_context);

        @Override
        protected void onPostExecute(String json_string) {
            super.onPostExecute(json_string);
            dialog.dismiss();
            try {
                JSONObject json_object = new JSONObject(json_string);

                List<String> list_data = new ArrayList<String>();
                JSONArray array_data=json_object.getJSONArray("data");

                for (int i = 0; i < array_data.length(); i++) {
                    JSONObject row = array_data.getJSONObject(i);
                    list_data.add(row.toString());
                }
                System.out.println(list_data);
                table_view.setDataAdapter(new TableDataAdapter(main_context, list_data) {
                    @Override
                    public View getCellView(int rowIndex, int columnIndex, ViewGroup parentView) {
                        Object item = getRowData(rowIndex);
                        JSONObject item_json_object = new JSONObject();
                        try {
                            item_json_object = new JSONObject(item.toString());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        String[] array_column_name = new String[list_column_name.size()];
                        list_column_name.toArray(array_column_name);
                        TextView textView = new TextView(getContext());
                        String column_name = array_column_name[columnIndex];
                        if (item_json_object.has(column_name)) {
                            try {
                                textView.setText(item_json_object.getString(column_name));
                            } catch (JSONException e) {
                                e.printStackTrace();
                            }
                            textView.setPadding(20, 10, 20, 10);
                            textView.setTextSize(23);
                        }


                        return textView;
                    }
                });
                table_view.addDataClickListener(new TableClickListener());

                ((LinearLayout) main_parent_object).addView(table_view);


            } catch (Throwable t) {
                t.printStackTrace();
            }

        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            dialog.setMessage("Downloading data...");
            dialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String return_json = "";
            try {
                // instantiate our json parser
                JSONParser jParser = new JSONParser();
                // get json string from url
                System.out.println(params[0]);
                JSONObject json = jParser.getJSONFromUrl(params[0]);
                //System.out.println(json.toString());
                return_json = json.toString();

            } catch (Throwable t) {
                t.printStackTrace();
            }
            return return_json;
        }


    }


}
