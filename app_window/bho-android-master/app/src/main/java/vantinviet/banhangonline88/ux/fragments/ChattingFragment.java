package vantinviet.banhangonline88.ux.fragments;

import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.design.widget.AppBarLayout;
import android.support.v4.app.Fragment;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.transition.TransitionInflater;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AccelerateInterpolator;
import android.view.animation.AlphaAnimation;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.view.animation.DecelerateInterpolator;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ImageSwitcher;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.ViewSwitcher;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;

import timber.log.Timber;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.SettingsMy;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.entities.Metadata;
import vantinviet.banhangonline88.entities.SortItem;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemChatting;
import vantinviet.banhangonline88.entities.filtr.Filters;
import vantinviet.banhangonline88.entities.messenger.Messenger;
import vantinviet.banhangonline88.entities.product.Product;
import vantinviet.banhangonline88.entities.messenger.MessengerListResponse;
import vantinviet.banhangonline88.interfaces.ChattingRecyclerInterface;
import vantinviet.banhangonline88.listeners.OnSingleClickListener;
import vantinviet.banhangonline88.utils.Analytics;
import vantinviet.banhangonline88.utils.EndlessRecyclerScrollListener;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.RecyclerMarginDecorator;
import vantinviet.banhangonline88.ux.MainActivity;
import vantinviet.banhangonline88.ux.adapters.ChattingRecyclerAdapter;
import vantinviet.banhangonline88.ux.adapters.SortSpinnerAdapter;

/**
 * Fragment handles various types of product lists.
 * Also allows displaying the search results.
 */
public class ChattingFragment extends Fragment {

    private static final String USER_ID = "user_id";
    private static final String SEARCH_QUERY = "fdfdf";

    /**
     * Prevent the sort selection callback during initialization.
     */
    private boolean firstTimeSort = true;

    private View loadMoreProgress;

    private long productId;
    private String categoryType;

    /**
     * Search string. The value is set only if the fragment is launched in order to searching.
     */
    private String searchQuery = null;

    /**
     * Request metadata containing URLs for endlessScroll.
     */
    private Metadata chattingsMetadata;

    private ImageSwitcher switchLayoutManager;
    private Spinner sortSpinner;

    // Content specific
    private TextView emptyContentView;
    private EditText message_text;
    private RecyclerView chattingRecycler;
    private GridLayoutManager chattingsRecyclerLayoutManager;
    private ChattingRecyclerAdapter chattingRecyclerAdapter;
    private EndlessRecyclerScrollListener endlessRecyclerScrollListener;

    // Filters parameters
    private Filters filters;
    private String filterParameters = null;
    private ImageView filterButton;

    // Properties used to restore previous state
    private int toolbarOffset = -1;
    private boolean isList = false;


    public static ChattingFragment newInstance(long userId) {
        System.out.println("ChattingFragment");
        Bundle args = new Bundle();
        args.putLong(USER_ID, userId);
        ChattingFragment fragment = new ChattingFragment();
        fragment.setArguments(args);
        return fragment;
    }



    /**
     * Show product list populated from drawer menu.
     *
     * @param drawerItemChatting corresponding drawer menu item.
     * @return new fragment instance.
     */
    public static ChattingFragment newInstance(DrawerItemChatting drawerItemChatting) {
        if (drawerItemChatting != null)
            return newInstance(drawerItemChatting.getOriginalId());
        else {
            Timber.e(new RuntimeException(), "Creating category with null arguments");
            return null;
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        Timber.d("%s - onCreateView", this.getClass().getSimpleName());
        View view = inflater.inflate(R.layout.fragment_chatting, container, false);

        this.emptyContentView = (TextView) view.findViewById(R.id.category_products_empty);

        this.loadMoreProgress = view.findViewById(R.id.category_load_more_progress);
        this.sortSpinner = (Spinner) view.findViewById(R.id.category_sort_spinner);
        this.switchLayoutManager = (ImageSwitcher) view.findViewById(R.id.category_switch_layout_manager);
        init(view);
        Bundle startBundle = getArguments();
        if (startBundle != null) {
            productId = startBundle.getLong(USER_ID, 0);
            searchQuery = startBundle.getString(SEARCH_QUERY, null);
            boolean isSearch = false;
            if (searchQuery != null && !searchQuery.isEmpty()) {
                isSearch = true;
                productId = -10;
            }

            Timber.d("productId: %d.", productId);


            MainActivity.setActionBarTitle("Chatting");

            // Opened first time (not form backstack)
            if (chattingRecyclerAdapter == null || chattingRecyclerAdapter.getItemCount() == 0) {
                prepareRecyclerAdapter();
                prepareChattingRecycler(view);
                getChatting(null);

                Analytics.logProductView(productId,"logProductView");
            } else {
                prepareChattingRecycler(view);
                Timber.d("Restore previous category state. (Products already loaded) ");
            }
        } else {
            MsgUtils.showToast(getActivity(), MsgUtils.TOAST_TYPE_INTERNAL_ERROR, getString(R.string.Internal_error), MsgUtils.ToastLength.LONG);
            Timber.e(new RuntimeException(), "Run category fragment without arguments.");
        }
        return view;
    }

    private void init(View view) {
        message_text = (EditText) view.findViewById(R.id.message_text);
        message_text.setOnFocusChangeListener(new View.OnFocusChangeListener() {
            @Override
            public void onFocusChange(View v, boolean hasFocus) {
                message_text.post(new Runnable() {
                    @Override
                    public void run() {
                        InputMethodManager imm = (InputMethodManager) getActivity().getSystemService(Context.INPUT_METHOD_SERVICE);
                        imm.showSoftInput(message_text, InputMethodManager.SHOW_IMPLICIT);
                    }
                });
            }
        });
        message_text.requestFocus();
        message_text.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                InputMethodManager inputMethodManager = (InputMethodManager) getContext().getSystemService(Context.INPUT_METHOD_SERVICE);
                if (inputMethodManager != null) {
                    inputMethodManager.toggleSoftInput(InputMethodManager.SHOW_FORCED, 0);

                }
            }

        });
    }


    /**
     * Prepare content recycler. Create custom adapter and endless scroll.
     *
     * @param view root fragment view.
     */
    private void prepareChattingRecycler(View view) {
        this.chattingRecycler = (RecyclerView) view.findViewById(R.id.chattings_recycler);
        chattingRecycler.addItemDecoration(new RecyclerMarginDecorator(getActivity(), RecyclerMarginDecorator.ORIENTATION.BOTH));
        chattingRecycler.setItemAnimator(new DefaultItemAnimator());
        chattingRecycler.setHasFixedSize(true);

        chattingRecycler.setLayoutManager(chattingsRecyclerLayoutManager);
        endlessRecyclerScrollListener = new EndlessRecyclerScrollListener(chattingsRecyclerLayoutManager) {
            @Override
            public void onLoadMore(int currentPage) {
                Timber.e("Load more");
                if (chattingsMetadata != null && chattingsMetadata.getLinks() != null && chattingsMetadata.getLinks().getNext() != null) {
                    getChatting(chattingsMetadata.getLinks().getNext());
                } else {
                    Timber.d("CustomLoadMoreDataFromApi NO MORE DATA");
                }
            }
        };
        chattingRecycler.addOnScrollListener(endlessRecyclerScrollListener);
        chattingRecycler.setAdapter(chattingRecyclerAdapter);

    }

    private void prepareRecyclerAdapter() {
        chattingRecyclerAdapter = new ChattingRecyclerAdapter(getActivity(), new ChattingRecyclerInterface() {


            @Override
            public void onChattingSelected(View caller, Messenger chatting) {
                if (android.os.Build.VERSION.SDK_INT > Build.VERSION_CODES.LOLLIPOP) {
                    setReenterTransition(TransitionInflater.from(getActivity()).inflateTransition(android.R.transition.fade));
                }
                ((MainActivity) getActivity()).onChattingSelected(chatting.getId());
            }
        });
    }


    @Override
    public void onViewCreated(View view, Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

    }



    /**
     * Endless content loader. Should be used after views inflated.
     *
     * @param url null for fresh load. Otherwise use URLs from response metadata.
     */
    private void getChatting(String url) {
        if(url==null)
        {
            url=EndPoints.MESSENGERS;
        }
        loadMoreProgress.setVisibility(View.VISIBLE);

        GsonRequest<MessengerListResponse> getmessengerRequest = new GsonRequest<>(Request.Method.GET, url, null, MessengerListResponse.class,
                new Response.Listener<MessengerListResponse>() {
                    @Override
                    public void onResponse(@NonNull MessengerListResponse response) {
                        firstTimeSort = false;
//                        Timber.d("response:" + response.toString());
                        chattingRecyclerAdapter.addMessengers(response.getMessengers());
                        chattingsMetadata = response.getMetadata();
                        if (filters == null) filters = chattingsMetadata.getFilters();
                        checkEmptyContent();
                        loadMoreProgress.setVisibility(View.GONE);
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                if (loadMoreProgress != null) loadMoreProgress.setVisibility(View.GONE);
                checkEmptyContent();
                MsgUtils.logAndShowErrorMessage(getActivity(), error);
            }
        });
        getmessengerRequest.setRetryPolicy(MyApplication.getDefaultRetryPolice());
        getmessengerRequest.setShouldCache(false);
        MyApplication.getInstance().addToRequestQueue(getmessengerRequest, CONST.CATEGORY_REQUESTS_TAG);
    }

    private void checkEmptyContent() {
        if (chattingRecyclerAdapter != null && chattingRecyclerAdapter.getItemCount() > 0) {
            emptyContentView.setVisibility(View.INVISIBLE);
            chattingRecycler.setVisibility(View.VISIBLE);
        } else {
            emptyContentView.setVisibility(View.VISIBLE);
            chattingRecycler.setVisibility(View.INVISIBLE);
        }
    }

    @Override
    public void onStop() {
        if (loadMoreProgress != null) {
            // Hide progress dialog if exist.
            if (loadMoreProgress.getVisibility() == View.VISIBLE && endlessRecyclerScrollListener != null) {
                // Fragment stopped during loading data. Allow new loading on return.
                endlessRecyclerScrollListener.resetLoading();
            }
            loadMoreProgress.setVisibility(View.GONE);
        }
        MyApplication.getInstance().cancelPendingRequests(CONST.CATEGORY_REQUESTS_TAG);
        super.onStop();
    }

    @Override
    public void onDestroyView() {
        if (chattingRecycler != null) chattingRecycler.clearOnScrollListeners();
        super.onDestroyView();
    }
}
