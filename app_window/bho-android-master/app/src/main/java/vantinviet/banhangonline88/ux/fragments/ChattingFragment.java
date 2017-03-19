package vantinviet.banhangonline88.ux.fragments;

import android.content.Context;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.v4.app.Fragment;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.transition.TransitionInflater;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageSwitcher;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.ViewSwitcher;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import timber.log.Timber;
import vantinviet.banhangonline88.BuildConfig;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.SettingsMy;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.entities.Metadata;
import vantinviet.banhangonline88.entities.User;
import vantinviet.banhangonline88.entities.drawerMenu.DrawerItemChatting;
import vantinviet.banhangonline88.entities.filtr.Filters;
import vantinviet.banhangonline88.entities.messenger.Messenger;
import vantinviet.banhangonline88.entities.messenger.Movie;
import vantinviet.banhangonline88.entities.messenger.Storing;
import vantinviet.banhangonline88.entities.messenger.MessengerListResponse;
import vantinviet.banhangonline88.interfaces.ChattingRecyclerInterface;
import vantinviet.banhangonline88.utils.Analytics;
import vantinviet.banhangonline88.utils.EndlessRecyclerScrollListener;
import vantinviet.banhangonline88.utils.JsonUtils;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.RecyclerMarginDecorator;
import vantinviet.banhangonline88.ux.MainActivity;
import vantinviet.banhangonline88.ux.adapters.ChattingRecyclerAdapter;

import static com.facebook.FacebookSdk.getApplicationContext;

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

    private long user_id;
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
    private ChattingRecyclerAdapter chattingRecyclerAdapter;
    private EndlessRecyclerScrollListener endlessRecyclerScrollListener;

    // Filters parameters
    private Filters filters;
    private String filterParameters = null;
    private ImageView filterButton;

    // Properties used to restore previous state
    private int toolbarOffset = -1;
    private boolean isList = false;
    private MyApplication app;
    private ImageView send_button;
    private LinearLayoutManager mLayoutManager;
    private RecyclerView recyclerView;


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
        message_text = (EditText) view.findViewById(R.id.message_text);
        send_button = (ImageView) view.findViewById(R.id.send_button);

        recyclerView = (RecyclerView) view.findViewById(R.id.chattings_recycler);
        recyclerView.setHasFixedSize(true);
        chattingRecyclerAdapter = new ChattingRecyclerAdapter(movieList);

        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        recyclerView.setLayoutManager(mLayoutManager);
        recyclerView.setItemAnimator(new DefaultItemAnimator());
        recyclerView.setAdapter(chattingRecyclerAdapter);

        endlessRecyclerScrollListener = new EndlessRecyclerScrollListener((LinearLayoutManager) mLayoutManager) {
            @Override
            public void onLoadMore(int currentPage) {
                Timber.e("Load more");

            }
        };
        recyclerView.addOnScrollListener(endlessRecyclerScrollListener);

        prepareMovieData();
        init(view);
        return view;
    }

    private void init(View view) {

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

        send_button.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(message_text.getText()!=null)
                {
                    sendMessenger();
                }
            }
        });
        message_text.setOnKeyListener(new View.OnKeyListener() {
            public boolean onKey(View v, int keyCode, KeyEvent event) {
                // If the event is a key-down event on the "enter" button
                if ((event.getAction() == KeyEvent.ACTION_DOWN) &&
                        (keyCode == KeyEvent.KEYCODE_ENTER)) {
                    // Perform action on key press
                    sendMessenger();
                    return true;
                }
                return false;
            }
        });

    }

    private void sendMessenger() {
        User user = SettingsMy.getActiveUser();
        app=MyApplication.getInstance();
        String url=EndPoints.SAVEENTITY;
        url=app.get_token_link(url);
        //url= String.format(url+"&user_id=%d", user.getId());
        JSONObject jo = new JSONObject();
        try {
            jo.put(JsonUtils.MESSENGER, message_text.getText());
            jo.put("task", "stream.saveEntity");
            jo.put("to", "b1kbogkkmh4h7qhrfo6p869375");
            jo.put("tologged", "");
        } catch (JSONException e) {
            Timber.e(e, "Parsing change  exception.");
            return;
        }
        if (BuildConfig.DEBUG) Timber.d("send messenger: %s", jo.toString());
        GsonRequest<Storing> get_storing = new GsonRequest<>(Request.Method.POST, url,  jo.toString(), Storing.class,
                new Response.Listener<Storing>() {
                    @Override
                    public void onResponse(@NonNull Storing response) {
                        Timber.d("Storing response:" + response.toString());
                        Messenger messenger=new Messenger();
                        messenger.setMessage(message_text.getText().toString());
                        ArrayList<Messenger> Messengers=new ArrayList<Messenger>();
                        Messengers.add(messenger);

                        Movie movie = new Movie(message_text.getText().toString(), "Action & Adventure", "2015");
                        movieList.add(movie);
                        chattingRecyclerAdapter.notifyDataSetChanged();
                        recyclerView.smoothScrollToPosition(movieList.size());
                        message_text.setText("");
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {

            }
        });


        MyApplication.getInstance().addToRequestQueue(get_storing, CONST.ACCOUNT_EDIT_REQUESTS_TAG);
    }
    private List<Movie> movieList = new ArrayList<>();
    private void prepareMovieData() {
        Movie movie = new Movie("Mad Max: Fury Road", "Action & Adventure", "2015");
        movieList.add(movie);

        movie = new Movie("Inside Out", "Animation, Kids & Family", "2015");
        movieList.add(movie);

        movie = new Movie("Star Wars: Episode VII - The Force Awakens", "Action", "2015");
        movieList.add(movie);

        movie = new Movie("Shaun the Sheep", "Animation", "2015");
        movieList.add(movie);

        movie = new Movie("The Martian", "Science Fiction & Fantasy", "2015");
        movieList.add(movie);

        movie = new Movie("Mission: Impossible Rogue Nation", "Action", "2015");
        movieList.add(movie);

        movie = new Movie("Up", "Animation", "2009");
        movieList.add(movie);

        movie = new Movie("Star Trek", "Science Fiction", "2009");
        movieList.add(movie);

        movie = new Movie("The LEGO Movie", "Animation", "2014");
        movieList.add(movie);

        movie = new Movie("Iron Man", "Action & Adventure", "2008");
        movieList.add(movie);

        movie = new Movie("Aliens", "Science Fiction", "1986");
        movieList.add(movie);

        movie = new Movie("Chicken Run", "Animation", "2000");
        movieList.add(movie);

        movie = new Movie("Back to the Future", "Science Fiction", "1985");
        movieList.add(movie);

        movie = new Movie("Raiders of the Lost Ark", "Action & Adventure", "1981");
        movieList.add(movie);

        movie = new Movie("Goldfinger", "Action & Adventure", "1965");
        movieList.add(movie);

        movie = new Movie("Guardians of the Galaxy", "Science Fiction & Fantasy", "2014");
        movieList.add(movie);
        chattingRecyclerAdapter.notifyDataSetChanged();

    }
    /**
     * Prepare content recycler. Create custom adapter and endless scroll.
     *
     * @param view root fragment view.
     */
    private void prepareChattingRecycler(View view) {




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
        app=MyApplication.getInstance();
        if(url==null)
        {
            url=app.get_token_link(EndPoints.MESSENGERS);
        }
        loadMoreProgress.setVisibility(View.VISIBLE);
        JSONObject jo;
        try {
            jo = JsonUtils.createChatJson();
        } catch (JSONException e) {
            Timber.e(e, "Parse logInWithEmail exception");
            MsgUtils.showToast(getActivity(), MsgUtils.TOAST_TYPE_INTERNAL_ERROR, null, MsgUtils.ToastLength.SHORT);
            return;
        }
        if (BuildConfig.DEBUG) Timber.d("Chatting Post: %s", jo.toString());
        GsonRequest<MessengerListResponse> getmessengerRequest = new GsonRequest<>(Request.Method.POST, url,  jo.toString(), MessengerListResponse.class,
                new Response.Listener<MessengerListResponse>() {
                    @Override
                    public void onResponse(@NonNull MessengerListResponse response) {
                        firstTimeSort = false;
                        Timber.d("chatting response:" + response.toString());
                        ArrayList<Messenger> Messengers=response.getMessengers();
                        if(Messengers.size()>0)
                        {
                        }
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
