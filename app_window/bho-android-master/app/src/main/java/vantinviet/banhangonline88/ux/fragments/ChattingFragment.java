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
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemCategory;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemChatting;
import vantinviet.banhangonline88.entities.filtr.Filters;
import vantinviet.banhangonline88.entities.product.Product;
import vantinviet.banhangonline88.entities.product.ProductListResponse;
import vantinviet.banhangonline88.interfaces.CategoryRecyclerInterface;
import vantinviet.banhangonline88.interfaces.ChattingRecyclerInterface;
import vantinviet.banhangonline88.listeners.OnSingleClickListener;
import vantinviet.banhangonline88.utils.Analytics;
import vantinviet.banhangonline88.utils.EndlessRecyclerScrollListener;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.RecyclerMarginDecorator;
import vantinviet.banhangonline88.utils.Utils;
import vantinviet.banhangonline88.ux.MainActivity;
import vantinviet.banhangonline88.ux.adapters.ChattingRecyclerAdapter;
import vantinviet.banhangonline88.ux.adapters.ProductsRecyclerAdapter;
import vantinviet.banhangonline88.ux.adapters.SortSpinnerAdapter;

/**
 * Fragment handles various types of product lists.
 * Also allows displaying the search results.
 */
public class ChattingFragment extends Fragment {

    private static final String PRODUCT_ID = "product_id";
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
    private Metadata productsMetadata;

    private ImageSwitcher switchLayoutManager;
    private Spinner sortSpinner;

    // Content specific
    private TextView emptyContentView;
    private EditText message_text;
    private RecyclerView productsRecycler;
    private GridLayoutManager productsRecyclerLayoutManager;
    private ChattingRecyclerAdapter chattingRecyclerAdapter;
    private EndlessRecyclerScrollListener endlessRecyclerScrollListener;

    // Filters parameters
    private Filters filters;
    private String filterParameters = null;
    private ImageView filterButton;

    // Properties used to restore previous state
    private int toolbarOffset = -1;
    private boolean isList = false;


    public static ChattingFragment newInstance(long productId) {
        System.out.println("ChattingFragment");
        Bundle args = new Bundle();
        args.putLong(PRODUCT_ID, productId);
        ChattingFragment fragment = new ChattingFragment();
        fragment.setArguments(args);
        return fragment;
    }


    /**
     * Show product list based on search results.
     *
     * @param searchQuery word for searching.
     * @return new fragment instance.
     */
    public static ChattingFragment newInstance(String searchQuery) {
        Bundle args = new Bundle();
        args.putString(SEARCH_QUERY, searchQuery);

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
            productId = startBundle.getLong(PRODUCT_ID, 0);
            searchQuery = startBundle.getString(SEARCH_QUERY, null);
            boolean isSearch = false;
            if (searchQuery != null && !searchQuery.isEmpty()) {
                isSearch = true;
                productId = -10;
            }

            Timber.d("productId: %d.", productId);

            AppBarLayout appBarLayout = (AppBarLayout) view.findViewById(R.id.category_appbar_layout);
            if (toolbarOffset != -1) appBarLayout.offsetTopAndBottom(toolbarOffset);
            appBarLayout.addOnOffsetChangedListener(new AppBarLayout.OnOffsetChangedListener() {
                @Override
                public void onOffsetChanged(AppBarLayout appBarLayout, int i) {
                    toolbarOffset = i;
                }
            });
            MainActivity.setActionBarTitle("Chatting");

            // Opened first time (not form backstack)
            if (chattingRecyclerAdapter == null || chattingRecyclerAdapter.getItemCount() == 0) {
                prepareRecyclerAdapter();
                prepareChattingRecycler(view);
                prepareSortSpinner();
                getChatting(null);

                Analytics.logProductView(productId,"logProductView");
            } else {
                prepareChattingRecycler(view);
                prepareSortSpinner();
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
        this.productsRecycler = (RecyclerView) view.findViewById(R.id.category_products_recycler);
        productsRecycler.addItemDecoration(new RecyclerMarginDecorator(getActivity(), RecyclerMarginDecorator.ORIENTATION.BOTH));
        productsRecycler.setItemAnimator(new DefaultItemAnimator());
        productsRecycler.setHasFixedSize(true);
        switchLayoutManager.setFactory(new ViewSwitcher.ViewFactory() {
            @Override
            public View makeView() {
                return new ImageView(getContext());
            }
        });
        if (isList) {
            switchLayoutManager.setImageResource(R.drawable.grid_off);
            productsRecyclerLayoutManager = new GridLayoutManager(getActivity(), 1);
        } else {
            switchLayoutManager.setImageResource(R.drawable.grid_on);
            // TODO A better solution would be to dynamically determine the number of columns.
            productsRecyclerLayoutManager = new GridLayoutManager(getActivity(), 2);
        }
        productsRecycler.setLayoutManager(productsRecyclerLayoutManager);
        endlessRecyclerScrollListener = new EndlessRecyclerScrollListener(productsRecyclerLayoutManager) {
            @Override
            public void onLoadMore(int currentPage) {
                Timber.e("Load more");
                if (productsMetadata != null && productsMetadata.getLinks() != null && productsMetadata.getLinks().getNext() != null) {
                    getChatting(productsMetadata.getLinks().getNext());
                } else {
                    Timber.d("CustomLoadMoreDataFromApi NO MORE DATA");
                }
            }
        };
        productsRecycler.addOnScrollListener(endlessRecyclerScrollListener);
        productsRecycler.setAdapter(chattingRecyclerAdapter);

        switchLayoutManager.setOnClickListener(new OnSingleClickListener() {
            @Override
            public void onSingleClick(View v) {
                if (isList) {
                    isList = false;
                    switchLayoutManager.setImageResource(R.drawable.grid_on);
                    chattingRecyclerAdapter.defineImagesQuality(false);
                    animateRecyclerLayoutChange(2);
                } else {
                    isList = true;
                    switchLayoutManager.setImageResource(R.drawable.grid_off);
                    chattingRecyclerAdapter.defineImagesQuality(true);
                    animateRecyclerLayoutChange(1);
                }
            }
        });
    }

    private void prepareRecyclerAdapter() {
        chattingRecyclerAdapter = new ChattingRecyclerAdapter(getActivity(), new ChattingRecyclerInterface() {


            @Override
            public void onChattingSelected(View caller, Product product) {
                if (android.os.Build.VERSION.SDK_INT > Build.VERSION_CODES.LOLLIPOP) {
                    setReenterTransition(TransitionInflater.from(getActivity()).inflateTransition(android.R.transition.fade));
                }
                ((MainActivity) getActivity()).onChattingSelected(product.getId());
            }
        });
    }

    /**
     * Animate change of rows in products recycler LayoutManager.
     *
     * @param layoutSpanCount number of rows to display.
     */
    private void animateRecyclerLayoutChange(final int layoutSpanCount) {
        Animation fadeOut = new AlphaAnimation(1, 0);
        fadeOut.setInterpolator(new DecelerateInterpolator());
        fadeOut.setDuration(400);
        fadeOut.setAnimationListener(new Animation.AnimationListener() {
            @Override
            public void onAnimationStart(Animation animation) {
            }

            @Override
            public void onAnimationRepeat(Animation animation) {
            }

            @Override
            public void onAnimationEnd(Animation animation) {
                productsRecyclerLayoutManager.setSpanCount(layoutSpanCount);
                productsRecyclerLayoutManager.requestLayout();
                Animation fadeIn = new AlphaAnimation(0, 1);
                fadeIn.setInterpolator(new AccelerateInterpolator());
                fadeIn.setDuration(400);
                productsRecycler.startAnimation(fadeIn);
            }
        });
        productsRecycler.startAnimation(fadeOut);
    }

    @Override
    public void onViewCreated(View view, Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        Animation in = AnimationUtils.loadAnimation(getContext(), R.anim.fade_in_slowed);
        Animation out = AnimationUtils.loadAnimation(getContext(), android.R.anim.fade_out);
        switchLayoutManager.setInAnimation(in);
        switchLayoutManager.setOutAnimation(out);
    }

    private void prepareSortSpinner() {
        SortSpinnerAdapter sortSpinnerAdapter = new SortSpinnerAdapter(getActivity());
        sortSpinner.setAdapter(sortSpinnerAdapter);
        sortSpinner.setOnItemSelectedListener(null);
        sortSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            private int lastSortSpinnerPosition = -1;

            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if (firstTimeSort) {
                    firstTimeSort = false;
                    return;
                }
                Timber.d("Selected pos: %d", position);

                if (position != lastSortSpinnerPosition) {
                    Timber.d("OnItemSelected change");
                    lastSortSpinnerPosition = position;
                    getChatting(null);
                } else {
                    Timber.d("OnItemSelected no change");
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                Timber.d("OnNothingSelected - no change");
            }
        });
    }


    /**
     * Endless content loader. Should be used after views inflated.
     *
     * @param url null for fresh load. Otherwise use URLs from response metadata.
     */
    private void getChatting(String url) {
        loadMoreProgress.setVisibility(View.VISIBLE);
        if (url == null) {
            if (endlessRecyclerScrollListener != null) endlessRecyclerScrollListener.clean();
            chattingRecyclerAdapter.clear();
            url = String.format(EndPoints.PRODUCTS, SettingsMy.getActualNonNullShop(getActivity()).getId());

            // Build request url
            if (searchQuery != null) {
                String newSearchQueryString;
                try {
                    newSearchQueryString = URLEncoder.encode(searchQuery, "UTF-8");
                } catch (UnsupportedEncodingException e) {
                    Timber.e(e, "Unsupported encoding exception");
                    newSearchQueryString = URLEncoder.encode(searchQuery);
                }
                Timber.d("GetFirstProductsInCategory isSearch: %s", searchQuery);
                url += "&search=" + newSearchQueryString;
            } else {
                url +="&"+ categoryType + "=" + productId;
            }
            url += "&Itemid=" + productId;
            // Add filters parameter if exist
            if (filterParameters != null && !filterParameters.isEmpty()) {
                url += filterParameters;
            }

            SortItem sortItem = (SortItem) sortSpinner.getSelectedItem();
            if (sortItem != null) {
                url = url + "&sort=" + sortItem.getValue();
            }
        }

        GsonRequest<ProductListResponse> getProductsRequest = new GsonRequest<>(Request.Method.GET, url, null, ProductListResponse.class,
                new Response.Listener<ProductListResponse>() {
                    @Override
                    public void onResponse(@NonNull ProductListResponse response) {
                        firstTimeSort = false;
//                        Timber.d("response:" + response.toString());
                        chattingRecyclerAdapter.addProducts(response.getProducts());
                        productsMetadata = response.getMetadata();
                        if (filters == null) filters = productsMetadata.getFilters();
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
        getProductsRequest.setRetryPolicy(MyApplication.getDefaultRetryPolice());
        getProductsRequest.setShouldCache(false);
        MyApplication.getInstance().addToRequestQueue(getProductsRequest, CONST.CATEGORY_REQUESTS_TAG);
    }

    private void checkEmptyContent() {
        if (chattingRecyclerAdapter != null && chattingRecyclerAdapter.getItemCount() > 0) {
            emptyContentView.setVisibility(View.INVISIBLE);
            productsRecycler.setVisibility(View.VISIBLE);
        } else {
            emptyContentView.setVisibility(View.VISIBLE);
            productsRecycler.setVisibility(View.INVISIBLE);
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
        if (productsRecycler != null) productsRecycler.clearOnScrollListeners();
        super.onDestroyView();
    }
}
