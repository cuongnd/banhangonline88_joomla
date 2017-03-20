package vantinviet.banhangonline88.ux.adapters;

import android.content.Context;
import android.support.v4.content.ContextCompat;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.List;

import vantinviet.banhangonline88.entities.User;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerMenuItem;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.SettingsMy;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemPage;
import vantinviet.banhangonline88.interfaces.DrawerRecyclerInterface;
import vantinviet.banhangonline88.listeners.OnSingleClickListener;

/**
 * Adapter handling list of drawer items.
 */
public class DrawerRecyclerAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {
    private static final int TYPE_HEADER = 0;
    private static final int TYPE_ITEM_CATEGORY = 1;
    private static final int TYPE_ITEM_PAGE = 2;

    private final DrawerRecyclerInterface drawerRecyclerInterface;
    private LayoutInflater layoutInflater;
    private Context context;
    private List<DrawerMenuItem> drawerMenuItemList = new ArrayList<>();
    private List<DrawerItemPage> drawerItemPageList = new ArrayList<>();

    /**
     * Creates an adapter that handles a list of drawer items.
     *
     * @param context                 activity context.
     * @param drawerRecyclerInterface listener indicating events that occurred.
     */
    public DrawerRecyclerAdapter(Context context, DrawerRecyclerInterface drawerRecyclerInterface) {
        this.context = context;
        this.drawerRecyclerInterface = drawerRecyclerInterface;
    }


    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        if (layoutInflater == null)
            layoutInflater = LayoutInflater.from(parent.getContext());
        if (viewType == TYPE_ITEM_CATEGORY) {
            View view = layoutInflater.inflate(R.layout.list_item_drawer_category, parent, false);
            return new ViewHolderMenuItem(view, drawerRecyclerInterface);
        } else if (viewType == TYPE_ITEM_PAGE) {
            View view = layoutInflater.inflate(R.layout.list_item_drawer_page, parent, false);
            return new ViewHolderItemPage(view, drawerRecyclerInterface);
        } else {
            View view = layoutInflater.inflate(R.layout.list_item_drawer_header, parent, false);
            return new ViewHolderHeader(view, drawerRecyclerInterface);
        }
    }

    @Override
    public void onBindViewHolder(RecyclerView.ViewHolder holder, int position) {
        if (holder instanceof ViewHolderMenuItem) {
            ViewHolderMenuItem viewHolderItemCategory = (ViewHolderMenuItem) holder;

            DrawerMenuItem drawerMenuItem = getDrawerItem(position);
            viewHolderItemCategory.bindContent(drawerMenuItem);
            viewHolderItemCategory.itemText.setText(drawerMenuItem.getName());
            if (position == 1) {
                viewHolderItemCategory.itemText.setTextColor(ContextCompat.getColor(context, R.color.colorAccent));
                viewHolderItemCategory.itemText.setCompoundDrawablesWithIntrinsicBounds(ContextCompat.getDrawable(context, R.drawable.star), null, null, null);
                viewHolderItemCategory.divider.setVisibility(View.VISIBLE);
            } else {
                viewHolderItemCategory.itemText.setTextColor(ContextCompat.getColor(context, R.color.textPrimary));
                viewHolderItemCategory.itemText.setCompoundDrawablesWithIntrinsicBounds(null, null, null, null);
                viewHolderItemCategory.divider.setVisibility(View.GONE);
            }
            if (drawerMenuItem.getChildren() == null || drawerMenuItem.getChildren().isEmpty()) {
                viewHolderItemCategory.subMenuIndicator.setVisibility(View.INVISIBLE);
            } else {
                viewHolderItemCategory.subMenuIndicator.setVisibility(View.VISIBLE);
            }
        } else if (holder instanceof ViewHolderItemPage) {
            ViewHolderItemPage viewHolderItemPage = (ViewHolderItemPage) holder;

            DrawerItemPage drawerItemPage = getPageItem(position);
            viewHolderItemPage.bindContent(drawerItemPage);
            viewHolderItemPage.itemText.setText(drawerItemPage.getName());
        } else if (holder instanceof ViewHolderHeader) {
            ViewHolderHeader viewHolderHeader = (ViewHolderHeader) holder;

            User user = SettingsMy.getActiveUser();
            if (user != null) {
                viewHolderHeader.userName.setText(user.getEmail());
            } else {
                viewHolderHeader.userName.setText(context.getString(R.string.Unknown_user));
            }
        }
    }

    @Override
    public void onViewDetachedFromWindow(RecyclerView.ViewHolder holder) {
        super.onViewDetachedFromWindow(holder);
        // Clear the animation when the view is detached. Prevent bugs during fast scroll.
        if (holder instanceof ViewHolderMenuItem) {
            ((ViewHolderMenuItem) holder).layout.clearAnimation();
        } else if (holder instanceof ViewHolderItemPage) {
            ((ViewHolderItemPage) holder).layout.clearAnimation();
        }
    }

    @Override
    public void onViewAttachedToWindow(RecyclerView.ViewHolder holder) {
        super.onViewAttachedToWindow(holder);
        // Apply the animation when the view is attached
        if (holder instanceof ViewHolderMenuItem) {
            setAnimation(((ViewHolderMenuItem) holder).layout);
        } else if (holder instanceof ViewHolderItemPage) {
            setAnimation(((ViewHolderItemPage) holder).layout);
        }
    }

    /**
     * Here is the key method to apply the animation
     */
    private void setAnimation(View viewToAnimate) {
        Animation animation = AnimationUtils.loadAnimation(context, android.R.anim.slide_in_left);
        viewToAnimate.startAnimation(animation);
    }

    // This method returns the number of items present in the list
    @Override
    public int getItemCount() {
        return drawerMenuItemList.size() + drawerItemPageList.size() + 1; // the number of items in the list will be +1 the titles including the header view.
    }

    @Override
    public int getItemViewType(int position) {
        if (position == 0)
            return TYPE_HEADER;
        else if (position <= drawerMenuItemList.size())
            return TYPE_ITEM_CATEGORY;
        else
            return TYPE_ITEM_PAGE;
    }

    private DrawerMenuItem getDrawerItem(int position) {
        return drawerMenuItemList.get(position - 1);
    }

    private DrawerItemPage getPageItem(int position) {
        return drawerItemPageList.get(position - drawerMenuItemList.size() - 1);
    }

    public void addDrawerItemList(List<DrawerMenuItem> drawerItemCategories) {
        if (drawerItemCategories != null)
            drawerMenuItemList.addAll(drawerItemCategories);
    }

    public void addPageItemList(List<DrawerItemPage> drawerItemPages) {
        if (drawerItemPages != null)
            drawerItemPageList.addAll(drawerItemPages);
    }

    public void addDrawerItem(DrawerMenuItem drawerMenuItem) {
        drawerMenuItemList.add(drawerMenuItem);

    }

    // Provide a reference to the views for each data item
    public static class ViewHolderItemPage extends RecyclerView.ViewHolder {
        public TextView itemText;
        public View layout;
        private DrawerItemPage drawerItemPage;

        public ViewHolderItemPage(View itemView, final DrawerRecyclerInterface drawerRecyclerInterface) {
            super(itemView);
            itemText = (TextView) itemView.findViewById(R.id.drawer_list_item_text);
            layout = itemView.findViewById(R.id.drawer_list_item_layout);
            itemView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                }
            });
        }

        public void bindContent(DrawerItemPage drawerItemPage) {
            this.drawerItemPage = drawerItemPage;
        }
    }

    // Provide a reference to the views for each data item
    public static class ViewHolderMenuItem extends RecyclerView.ViewHolder {
        public TextView itemText;
        public ImageView subMenuIndicator;
        public LinearLayout layout;
        private DrawerMenuItem drawerMenuItem;
        private View divider;

        public ViewHolderMenuItem(View itemView, final DrawerRecyclerInterface drawerRecyclerInterface) {
            super(itemView);
            itemText = (TextView) itemView.findViewById(R.id.drawer_list_item_text);
            subMenuIndicator = (ImageView) itemView.findViewById(R.id.drawer_list_item_indicator);
            layout = (LinearLayout) itemView.findViewById(R.id.drawer_list_item_layout);
            divider = itemView.findViewById(R.id.drawer_list_item_divider);
            itemView.setOnClickListener(new OnSingleClickListener() {
                @Override
                public void onSingleClick(View v) {
                    drawerRecyclerInterface.onMenuItemSelected(v, drawerMenuItem);
                }
            });
        }

        public void bindContent(DrawerMenuItem drawerMenuItem) {
            this.drawerMenuItem = drawerMenuItem;
        }
    }

    public static class ViewHolderHeader extends RecyclerView.ViewHolder {
        public TextView userName;

        public ViewHolderHeader(View headerView, final DrawerRecyclerInterface drawerRecyclerInterface) {
            super(headerView);
            userName = (TextView) headerView.findViewById(R.id.navigation_drawer_list_header_text);
            headerView.setOnClickListener(new OnSingleClickListener() {
                @Override
                public void onSingleClick(View v) {
                    drawerRecyclerInterface.onHeaderSelected();
                }
            });
        }
    }
}
