package vantinviet.banhangonline88.ux.fragments;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Rect;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.google.android.gms.appindexing.Action;
import com.google.android.gms.appindexing.AppIndex;
import com.google.android.gms.common.api.GoogleApiClient;

import java.util.ArrayList;
import java.util.Date;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;

import timber.log.Timber;
import vantinviet.banhangonline88.CONST;
import vantinviet.banhangonline88.MyApplication;
import vantinviet.banhangonline88.R;
import vantinviet.banhangonline88.SettingsMy;
import vantinviet.banhangonline88.api.EndPoints;
import vantinviet.banhangonline88.api.GsonRequest;
import vantinviet.banhangonline88.entities.User;
import vantinviet.banhangonline88.utils.MsgUtils;
import vantinviet.banhangonline88.utils.Utils;
import vantinviet.banhangonline88.ux.MainActivity;


import in.co.madhur.chatbubblesdemo.AndroidUtilities;
import in.co.madhur.chatbubblesdemo.App;
import in.co.madhur.chatbubblesdemo.ChatListAdapter;
import in.co.madhur.chatbubblesdemo.Constants;
import in.co.madhur.chatbubblesdemo.NotificationCenter;
import in.co.madhur.chatbubblesdemo.model.ChatMessage;
import in.co.madhur.chatbubblesdemo.model.Status;
import in.co.madhur.chatbubblesdemo.model.UserType;
import in.co.madhur.chatbubblesdemo.widgets.Emoji;
import in.co.madhur.chatbubblesdemo.widgets.EmojiView;
import in.co.madhur.chatbubblesdemo.widgets.SizeNotifierRelativeLayout;

/**
 * Fragment provides the account screen with options such as logging, editing and more.
 */
public class ChattingFragment extends Fragment  implements SizeNotifierRelativeLayout.SizeNotifierRelativeLayoutDelegate, NotificationCenter.NotificationCenterDelegate{

    private ProgressDialog pDialog;

    private ListView chatListView;
    private EditText chatEditText1;
    private ArrayList<ChatMessage> chatMessages;
    private ImageView enterChatView1, emojiButton;
    private ChatListAdapter listAdapter;
    private EmojiView emojiView;
    private SizeNotifierRelativeLayout sizeNotifierRelativeLayout;
    private boolean showingEmoji;
    private int keyboardHeight;
    private boolean keyboardVisible;
    private WindowManager.LayoutParams windowLayoutParams;
    public View view;
    @Override
    public View onCreateView(LayoutInflater inflater, final ViewGroup container, Bundle savedInstanceState) {
        Timber.d("%s - OnCreateView", this.getClass().getSimpleName());
        MainActivity.setActionBarTitle(getString(R.string.Profile));

        view= inflater.inflate(R.layout.fragment_chatting, container, false);

        pDialog = Utils.generateProgressDialog(getActivity(), false);

        chatMessages = new ArrayList<>();

        chatListView = (ListView) view.findViewById(R.id.chat_list_view);

        chatEditText1 = (EditText) view.findViewById(R.id.chat_edit_text1);
        enterChatView1 = (ImageView) view.findViewById(R.id.enter_chat1);

        // Hide the emoji on click of edit text
        chatEditText1.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (showingEmoji)
                    hideEmojiPopup();
            }
        });


        emojiButton = (ImageView) view.findViewById(R.id.emojiButton);

        emojiButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showEmojiPopup(!showingEmoji);
            }
        });

        listAdapter = new ChatListAdapter(chatMessages, view.getContext());

        chatListView.setAdapter(listAdapter);

        chatEditText1.setOnKeyListener(keyListener);

        enterChatView1.setOnClickListener(clickListener);

        chatEditText1.addTextChangedListener(watcher1);

        sizeNotifierRelativeLayout = (SizeNotifierRelativeLayout) view.findViewById(R.id.chat_layout);
        sizeNotifierRelativeLayout.delegate = this;

        NotificationCenter.getInstance().addObserver(this, NotificationCenter.emojiDidLoaded);
        // ATTENTION: This was auto-generated to implement the App Indexing API.
        // See https://g.co/AppIndexing/AndroidStudio for more information.
        client = new GoogleApiClient.Builder(view.getContext()).addApi(AppIndex.API).build();

        return view;
    }

    private EditText.OnKeyListener keyListener = new View.OnKeyListener() {
        @Override
        public boolean onKey(View v, int keyCode, KeyEvent event) {

            // If the event is a key-down event on the "enter" button
            if ((event.getAction() == KeyEvent.ACTION_DOWN) &&
                    (keyCode == KeyEvent.KEYCODE_ENTER)) {
                // Perform action on key press

                EditText editText = (EditText) v;

                if (v == chatEditText1) {
                    sendMessage(editText.getText().toString(), UserType.OTHER);
                }

                chatEditText1.setText("");

                return true;
            }
            return false;

        }
    };

    private ImageView.OnClickListener clickListener = new View.OnClickListener() {
        @Override
        public void onClick(View v) {

            if (v == enterChatView1) {
                sendMessage(chatEditText1.getText().toString(), UserType.OTHER);
            }

            chatEditText1.setText("");

        }
    };

    private final TextWatcher watcher1 = new TextWatcher() {
        @Override
        public void beforeTextChanged(CharSequence charSequence, int i, int i2, int i3) {
        }

        @Override
        public void onTextChanged(CharSequence charSequence, int i, int i2, int i3) {
            if (chatEditText1.getText().toString().equals("")) {

            } else {
                enterChatView1.setImageResource(R.drawable.ic_chat_send);

            }
        }

        @Override
        public void afterTextChanged(Editable editable) {
            if (editable.length() == 0) {
                enterChatView1.setImageResource(R.drawable.ic_chat_send);
            } else {
                enterChatView1.setImageResource(R.drawable.ic_chat_send_active);
            }
        }
    };
    /**
     * ATTENTION: This was auto-generated to implement the App Indexing API.
     * See https://g.co/AppIndexing/AndroidStudio for more information.
     */
    private GoogleApiClient client;


    private void sendMessage(final String messageText, final UserType userType) {
        if (messageText.trim().length() == 0)
            return;

        final ChatMessage message = new ChatMessage();
        message.setMessageStatus(Status.SENT);
        message.setMessageText(messageText);
        message.setUserType(userType);
        message.setMessageTime(new Date().getTime());
        chatMessages.add(message);

        if (listAdapter != null)
            listAdapter.notifyDataSetChanged();

        // Mark message as delivered after one second

        final ScheduledExecutorService exec = Executors.newScheduledThreadPool(1);

        exec.schedule(new Runnable() {
            @Override
            public void run() {
                message.setMessageStatus(Status.DELIVERED);

                final ChatMessage message = new ChatMessage();
                message.setMessageStatus(Status.SENT);
                message.setMessageText(messageText);
                message.setUserType(UserType.SELF);
                message.setMessageTime(new Date().getTime());

                chatMessages.add(message);




            }
        }, 1, TimeUnit.SECONDS);

    }




    /**
     * Show or hide the emoji popup
     *
     * @param show
     */
    private void showEmojiPopup(boolean show) {
        showingEmoji = show;

        if (show) {
            if (emojiView == null) {
                if (getActivity() == null) {
                    return;
                }
                emojiView = new EmojiView(getActivity());

                emojiView.setListener(new EmojiView.Listener() {
                    public void onBackspace() {
                        chatEditText1.dispatchKeyEvent(new KeyEvent(0, 67));
                    }

                    public void onEmojiSelected(String symbol) {
                        int i = chatEditText1.getSelectionEnd();
                        if (i < 0) {
                            i = 0;
                        }
                        try {
                            CharSequence localCharSequence = Emoji.replaceEmoji(symbol, chatEditText1.getPaint().getFontMetricsInt(), AndroidUtilities.dp(20));
                            chatEditText1.setText(chatEditText1.getText().insert(i, localCharSequence));
                            int j = i + localCharSequence.length();
                            chatEditText1.setSelection(j, j);
                        } catch (Exception e) {
                            Log.e(Constants.TAG, "Error showing emoji");
                        }
                    }
                });


                windowLayoutParams = new WindowManager.LayoutParams();
                windowLayoutParams.gravity = Gravity.BOTTOM | Gravity.LEFT;
                if (Build.VERSION.SDK_INT >= 21) {
                    windowLayoutParams.type = WindowManager.LayoutParams.TYPE_SYSTEM_ERROR;
                } else {
                    windowLayoutParams.type = WindowManager.LayoutParams.TYPE_APPLICATION_PANEL;
                    windowLayoutParams.token = getActivity().getWindow().getDecorView().getWindowToken();
                }
                windowLayoutParams.flags = WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE;
            }

            final int currentHeight;

            if (keyboardHeight <= 0)
                keyboardHeight = App.getInstance().getSharedPreferences("emoji", 0).getInt("kbd_height", AndroidUtilities.dp(200));

            currentHeight = keyboardHeight;

            WindowManager wm = (WindowManager) App.getInstance().getSystemService(Activity.WINDOW_SERVICE);

            windowLayoutParams.height = currentHeight;
            windowLayoutParams.width = AndroidUtilities.displaySize.x;

            try {
                if (emojiView.getParent() != null) {
                    wm.removeViewImmediate(emojiView);
                }
            } catch (Exception e) {
                Log.e(Constants.TAG, e.getMessage());
            }

            try {
                wm.addView(emojiView, windowLayoutParams);
            } catch (Exception e) {
                Log.e(Constants.TAG, e.getMessage());
                return;
            }

            if (!keyboardVisible) {
                if (sizeNotifierRelativeLayout != null) {
                    sizeNotifierRelativeLayout.setPadding(0, 0, 0, currentHeight);
                }

                return;
            }

        } else {
            removeEmojiWindow();
            if (sizeNotifierRelativeLayout != null) {
                sizeNotifierRelativeLayout.post(new Runnable() {
                    public void run() {
                        if (sizeNotifierRelativeLayout != null) {
                            sizeNotifierRelativeLayout.setPadding(0, 0, 0, 0);
                        }
                    }
                });
            }
        }


    }


    /**
     * Remove emoji window
     */
    private void removeEmojiWindow() {
        if (emojiView == null) {
            return;
        }
        try {
            if (emojiView.getParent() != null) {
                WindowManager wm = (WindowManager) App.getInstance().getSystemService(Context.WINDOW_SERVICE);
                wm.removeViewImmediate(emojiView);
            }
        } catch (Exception e) {
            Log.e(Constants.TAG, e.getMessage());
        }
    }


    /**
     * Hides the emoji popup
     */
    public void hideEmojiPopup() {
        if (showingEmoji) {
            showEmojiPopup(false);
        }
    }

    /**
     * Check if the emoji popup is showing
     *
     * @return
     */
    public boolean isEmojiPopupShowing() {
        return showingEmoji;
    }


    /**
     * Updates emoji views when they are complete loading
     *
     * @param id
     * @param args
     */
    @Override
    public void didReceivedNotification(int id, Object... args) {
        if (id == NotificationCenter.emojiDidLoaded) {
            if (emojiView != null) {
                emojiView.invalidateViews();
            }

            if (chatListView != null) {
                chatListView.invalidateViews();
            }
        }
    }

    @Override
    public void onSizeChanged(int height) {

        Rect localRect = new Rect();
        getActivity().getWindow().getDecorView().getWindowVisibleDisplayFrame(localRect);

        WindowManager wm = (WindowManager) App.getInstance().getSystemService(Activity.WINDOW_SERVICE);
        if (wm == null || wm.getDefaultDisplay() == null) {
            return;
        }


        if (height > AndroidUtilities.dp(50) && keyboardVisible) {
            keyboardHeight = height;
            App.getInstance().getSharedPreferences("emoji", 0).edit().putInt("kbd_height", keyboardHeight).commit();
        }


        if (showingEmoji) {
            int newHeight = 0;

            newHeight = keyboardHeight;

            if (windowLayoutParams.width != AndroidUtilities.displaySize.x || windowLayoutParams.height != newHeight) {
                windowLayoutParams.width = AndroidUtilities.displaySize.x;
                windowLayoutParams.height = newHeight;

                wm.updateViewLayout(emojiView, windowLayoutParams);
                if (!keyboardVisible) {
                    sizeNotifierRelativeLayout.post(new Runnable() {
                        @Override
                        public void run() {
                            if (sizeNotifierRelativeLayout != null) {
                                sizeNotifierRelativeLayout.setPadding(0, 0, 0, windowLayoutParams.height);
                                sizeNotifierRelativeLayout.requestLayout();
                            }
                        }
                    });
                }
            }
        }


        boolean oldValue = keyboardVisible;
        keyboardVisible = height > 0;
        if (keyboardVisible && sizeNotifierRelativeLayout.getPaddingBottom() > 0) {
            showEmojiPopup(false);
        } else if (!keyboardVisible && keyboardVisible != oldValue && showingEmoji) {
            showEmojiPopup(false);
        }

    }

    @Override
    public void onDestroy() {
        super.onDestroy();

        NotificationCenter.getInstance().removeObserver(this, NotificationCenter.emojiDidLoaded);
    }

    /**
     * Get the system status bar height
     *
     * @return
     */
    public int getStatusBarHeight() {
        int result = 0;
        int resourceId = getResources().getIdentifier("status_bar_height", "dimen", "android");
        if (resourceId > 0) {
            result = getResources().getDimensionPixelSize(resourceId);
        }
        return result;
    }

    @Override
    public void onStart() {
        super.onStart();

        // ATTENTION: This was auto-generated to implement the App Indexing API.
        // See https://g.co/AppIndexing/AndroidStudio for more information.
        client.connect();
        Action viewAction = Action.newAction(
                Action.TYPE_VIEW, // TODO: choose an action type.
                "chattingfrom Page", // TODO: Define a title for the content shown.
                // TODO: If you have web page content that matches this app activity's content,
                // make sure this auto-generated web page URL is correct.
                // Otherwise, set the URL to null.
                Uri.parse("http://host/path"),
                // TODO: Make sure this auto-generated app URL is correct.
                Uri.parse("android-app://com.vantinviet.vtv/http/host/path")
        );
        AppIndex.AppIndexApi.start(client, viewAction);
    }

    @Override
    public void onStop() {
        super.onStop();

        // ATTENTION: This was auto-generated to implement the App Indexing API.
        // See https://g.co/AppIndexing/AndroidStudio for more information.
        Action viewAction = Action.newAction(
                Action.TYPE_VIEW, // TODO: choose an action type.
                "chattingfrom Page", // TODO: Define a title for the content shown.
                // TODO: If you have web page content that matches this app activity's content,
                // make sure this auto-generated web page URL is correct.
                // Otherwise, set the URL to null.
                Uri.parse("http://host/path"),
                // TODO: Make sure this auto-generated app URL is correct.
                Uri.parse("android-app://com.vantinviet.vtv/http/host/path")
        );
        AppIndex.AppIndexApi.end(client, viewAction);
        client.disconnect();
    }



}
