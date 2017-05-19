package vantinviet.core.modules.mod_menu;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Handler;
import android.os.Looper;
import android.support.annotation.RequiresApi;
import android.util.Log;
import android.view.View;
import android.webkit.JavascriptInterface;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.google.gson.reflect.TypeToken;
import com.squareup.picasso.Picasso;
import com.unnamed.b.atv.model.TreeNode;

import java.io.InputStream;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import timber.log.Timber;
import vantinviet.core.R;
import vantinviet.core.VTVConfig;
import vantinviet.core.libraries.cms.application.Page;
import vantinviet.core.libraries.cms.application.vtv_WebView;
import vantinviet.core.libraries.cms.menu.JMenu;
import vantinviet.core.libraries.cms.menu.JMenuparams;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;
import vantinviet.core.libraries.utilities.JUtilities;

import static android.view.ViewGroup.LayoutParams.MATCH_PARENT;


/**
 * Created by Bogdan Melnychuk on 2/12/15.
 */
    public class IconTreeItemHolderPopup extends TreeNode.BaseNodeViewHolder<JMenu> {

    TreeNode node;
    private JMenu redirectMenu;

    public IconTreeItemHolderPopup(Context context) {
        super(context);
    }

    @Override
    public View createNodeView(TreeNode node, JMenu menu) {
        final JApplication app = JFactory.getApplication();
        this.node = node;
        MenuLinearLayoutPopup menu_linear_layout = new MenuLinearLayoutPopup(app.getContext());
        menu_linear_layout.setNode(node);
        menu_linear_layout.setMenu(menu);
        menu_linear_layout.init();
        TextView menu_title = (TextView) menu_linear_layout.findViewById(R.id.popup_txt_menu_item);
        final ImageView menu_icon = (ImageView) menu_linear_layout.findViewById(R.id.popup_menu_icon);
        JMenuparams params = menu.getParams();
        String menu_image_path="";
        try{
            menu_image_path = "/" + params.getMenu_image();
        }catch (Exception ex){

        }

        //show The Image in a ImageView
        if(menu_image_path!=null&&!menu_image_path.isEmpty())
        {
            try{
                //new DownloadImageTask(menu_icon).execute(VTVConfig.rootUrl.concat(menu_image_path));
            }catch (Exception ex){

            }

        }




        menu_title.setText(menu.getTitle());
        return menu_linear_layout;
    }



    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    public void setRedirectMenu(JMenu redirectMenu) {
        JApplication app=JFactory.getApplication();
        String link = redirectMenu.getLink();
        Timber.d("menu item %s", redirectMenu.toString());
        Map<String, String> post = new HashMap<String, String>();
        post.put("Itemid", String.valueOf(redirectMenu.getId()));
        app.getAlertDialog().dismiss();
        app.setRedirect(VTVConfig.getRootUrl().concat("/" + link), post);
    }

    private class MenuLinearLayoutPopup  extends LinearLayout{

        public TreeNode node;
        public JMenu menu;
        public JApplication app=JFactory.getApplication();
        public MenuLinearLayoutPopup(Context context) {
            super(context);
            inflate(getContext(), R.layout.modules_mod_menu_tmpl_homeverticalmenutag_menu_item_popup, this);

        }
        public void init(){
            LayoutParams layoutparams=new LayoutParams(MATCH_PARENT,MATCH_PARENT);
            int level=menu.getLevel();
            int padding_top_button=10;
            if(level>1){
                padding_top_button=10;
            }
            this.setPadding(level*30,padding_top_button,0,padding_top_button);
            layoutparams.setMargins(level*30,0,0,20);
            this.setLayoutParams(layoutparams);
            this.setBackgroundColor(Color.parseColor("#039BE5"));
            final Button btn_go_to_page = (Button) this.findViewById(R.id.popup_btn_go_to_page);
            final TextView txt_menu_item = (TextView) this.findViewById(R.id.popup_txt_menu_item);
            if(menu.getTotalChildren()==0) {
                btn_go_to_page.setVisibility(INVISIBLE);
                this.setOnClickListener(new OnClickListener() {
                    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
                    @Override
                    public void onClick(View view) {
                        setRedirectMenu(menu);


                    }
                });

            }
        }


        public void setNode(TreeNode node) {
            this.node = node;
        }

        public void setMenu(JMenu menu) {
            this.menu = menu;
        }

    }
    private class DownloadImageTask extends AsyncTask<String, Void, Bitmap> {
        ImageView bmImage;

        public DownloadImageTask(ImageView bmImage) {
            this.bmImage = bmImage;
        }

        protected Bitmap doInBackground(String... urls) {
            String urldisplay = urls[0];
            Bitmap mIcon11 = null;
            try {
                InputStream in = new java.net.URL(urldisplay).openStream();
                mIcon11 = BitmapFactory.decodeStream(in);
            } catch (Exception e) {
                Log.e("Error", e.getMessage());
                e.printStackTrace();
            }
            return mIcon11;
        }

        protected void onPostExecute(Bitmap result) {
            try {
                bmImage.setImageBitmap(result);
            } catch (Exception e) {
                Log.e("Error", e.getMessage());
                e.printStackTrace();
            }

        }
    }
}
