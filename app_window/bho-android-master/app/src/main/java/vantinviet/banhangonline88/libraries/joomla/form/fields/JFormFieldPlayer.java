package vantinviet.banhangonline88.libraries.joomla.form.fields;

import android.media.MediaCodec;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.Handler;
import android.view.LayoutInflater;
import android.view.SurfaceHolder;
import android.view.SurfaceView;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AbsoluteLayout;
import android.widget.ImageButton;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.SeekBar;
import android.widget.TextView;

import com.beardedhen.androidbootstrap.BootstrapLabel;
import com.google.android.exoplayer.DefaultLoadControl;
import com.google.android.exoplayer.ExoPlayer;
import com.google.android.exoplayer.LoadControl;
import com.google.android.exoplayer.MediaCodecAudioTrackRenderer;
import com.google.android.exoplayer.MediaCodecSelector;
import com.google.android.exoplayer.MediaCodecVideoTrackRenderer;
import com.google.android.exoplayer.extractor.ExtractorSampleSource;
import com.google.android.exoplayer.hls.DefaultHlsTrackSelector;
import com.google.android.exoplayer.hls.HlsChunkSource;
import com.google.android.exoplayer.hls.HlsPlaylist;
import com.google.android.exoplayer.hls.HlsPlaylistParser;
import com.google.android.exoplayer.hls.HlsSampleSource;
import com.google.android.exoplayer.hls.PtsTimestampAdjusterProvider;
import com.google.android.exoplayer.upstream.Allocator;
import com.google.android.exoplayer.upstream.DataSource;
import com.google.android.exoplayer.upstream.DefaultAllocator;
import com.google.android.exoplayer.upstream.DefaultBandwidthMeter;
import com.google.android.exoplayer.upstream.DefaultUriDataSource;
import com.google.android.exoplayer.util.ManifestFetcher;
import com.vantinviet.vtv.R;
import com.vantinviet.vtv.libraries.joomla.JFactory;
import com.vantinviet.vtv.libraries.joomla.form.JFormField;
import com.vantinviet.vtv.libraries.legacy.application.JApplication;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.util.Formatter;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;
/**
 * Created by cuongnd on 6/11/2016.
 */
public class JFormFieldPlayer extends JFormField  {
    static Map<String, JFormFieldPlayer> map_form_field_text = new HashMap<String, JFormFieldPlayer>();
    private AbsoluteLayout control_linear_layout;
    private MediaPlayer mp;
    private SurfaceHolder holder;
    boolean pausing = false;
    public static String filepath;
    private static final String TEST_URL = "http://clips.vorwaerts-gmbh.de/big_buck_bunny.mp4";

    private SurfaceView surfaceView;
    private ExoPlayer exoPlayer;
    private boolean bAutoplay=true;
    private boolean bIsPlaying=false;
    private boolean bControlsActive=true;
    private ImageButton btnPlay;
    private ImageButton btnFwd;
    private ImageButton btnPrev;
    private ImageButton btnRew;
    private ImageButton btnNext;
    private int RENDERER_COUNT = 300000;
    private int minBufferMs =    250000;

    private final int BUFFER_SEGMENT_SIZE = 32 * 500;
    private final int BUFFER_SEGMENT_COUNT = 256;
    private LinearLayout mediaController;
    private SeekBar seekPlayerProgress;
    private Handler handler;
    private TextView txtCurrentTime;
    private TextView txtEndTime;
    private StringBuilder mFormatBuilder;
    private Formatter mFormatter;
    private String HLSurl = "http://walterebert.com/playground/video/hls/sintel-trailer.m3u8";
    private String mp4URL = "http://www.sample-videos.com/video/mp4/480/big_buck_bunny_480p_5mb.mp4";
    private String userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:40.0) Gecko/20100101 Firefox/40.0";







    public JFormFieldPlayer(JSONObject field, String type, String name, String group, String value){
        this.type=type;
        this.name=name;
        this.group=group;
        this.option=field;
        this.value=value;
    }
    public JFormFieldPlayer(){
    }


    @Override
    public View getInput() {
        JApplication app= JFactory.getApplication();
        LinearLayout linear_layout = new LinearLayout(context);
        JSONObject option=this.option;
        boolean show_label=true;
        try {
            show_label = option.has("show_label")?option.getBoolean("show_label"):false;
        } catch (JSONException e) {
            e.printStackTrace();
        }

        if(show_label){
            BootstrapLabel label_text = new BootstrapLabel(context);
            label_text.setText(this.label);
            label_text.setLayoutParams(new ViewGroup.LayoutParams(ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT));
            ((LinearLayout) linear_layout).addView(label_text);
        }
       RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(ViewGroup.LayoutParams.FILL_PARENT, 1500);
        params.setMargins(0, 100, 0, 10);
        linear_layout.setGravity(LinearLayout.TEXT_ALIGNMENT_GRAVITY);
        LayoutInflater factory = LayoutInflater.from(context);
        View myView = factory.inflate(R.layout.player_main_layout, null);
        myView.setLayoutParams(params);

        ((LinearLayout) linear_layout).addView(myView);


        surfaceView = (SurfaceView) app.context.findViewById(R.id.sv_player);
        mediaController = (LinearLayout) app.context.findViewById(R.id.lin_media_controller);
        app.context.getWindow().addFlags(500);

        //initPlayer(0);
        initHLSPlayer(0);


        if(bAutoplay){
            if(exoPlayer!=null){
                exoPlayer.setPlayWhenReady(true);
                bIsPlaying=true;
                setProgress();
            }

        }



 /*
        player = (EasyVideoPlayer) app.context.findViewById(R.id.easy_player);

        assert player != null;
        player.setCallback(this);
        // All further configuration is done from the XML layout.

       player=new EasyVideoPlayer(context);
       player.setSource(Uri.parse(TEST_URL));*/

       //((LinearLayout) linear_layout).addView(player);


        return (View)linear_layout;
    }
    private void initMediaControls() {
        initSurfaceView();
        initPlayButton();
        initSeekBar();
        initTxtTime();
        initFwd();
        initPrev();
        initRew();
        initNext();

    }

    private void initNext() {
        btnNext = (ImageButton) findViewById(R.id.next);
        btnNext.requestFocus();
        btnNext.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                exoPlayer.seekTo(exoPlayer.getDuration());
            }
        });
    }

    private View findViewById(int id_element) {
       JApplication app=JFactory.getApplication();
        return app.context.findViewById(id_element) ;
    }

    private void initRew() {
        btnRew = (ImageButton) findViewById(R.id.rew);
        btnRew.requestFocus();
        btnRew.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                exoPlayer.seekTo(exoPlayer.getCurrentPosition()-10000);
            }
        });
    }

    private void initPrev() {
        btnPrev = (ImageButton) findViewById(R.id.prev);
        btnPrev.requestFocus();
        btnPrev.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                exoPlayer.seekTo(0);
            }
        });


    }


    private void initFwd() {
        btnFwd = (ImageButton) findViewById(R.id.ffwd);
        btnFwd.requestFocus();
        btnFwd.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                exoPlayer.seekTo(exoPlayer.getCurrentPosition()+10000);
            }
        });


    }

    private void initTxtTime() {
        txtCurrentTime = (TextView) findViewById(R.id.time_current);
        txtEndTime = (TextView) findViewById(R.id.player_end_time);
    }

    private void initSurfaceView() {
        surfaceView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toggleMediaControls();
            }
        });
    }

    private String stringForTime(int timeMs) {
        mFormatBuilder = new StringBuilder();
        mFormatter = new Formatter(mFormatBuilder, Locale.getDefault());
        int totalSeconds =  timeMs / 1000;

        int seconds = totalSeconds % 60;
        int minutes = (totalSeconds / 60) % 60;
        int hours   = totalSeconds / 3600;

        mFormatBuilder.setLength(0);
        if (hours > 0) {
            return mFormatter.format("%d:%02d:%02d", hours, minutes, seconds).toString();
        } else {
            return mFormatter.format("%02d:%02d", minutes, seconds).toString();
        }
    }

    private void setProgress() {
        seekPlayerProgress.setProgress(0);
        seekPlayerProgress.setMax(0);
        seekPlayerProgress.setMax((int) exoPlayer.getDuration()/1000);


        handler = new Handler();
        //Make sure you update Seekbar on UI thread
        handler.post(new Runnable() {

            @Override
            public void run() {
                if (exoPlayer != null && bIsPlaying ) {
                    seekPlayerProgress.setMax(0);
                    seekPlayerProgress.setMax((int) exoPlayer.getDuration()/1000);
                    int mCurrentPosition = (int) exoPlayer.getCurrentPosition() / 1000;
                    seekPlayerProgress.setProgress(mCurrentPosition);
                    txtCurrentTime.setText(stringForTime((int)exoPlayer.getCurrentPosition()));
                    txtEndTime.setText(stringForTime((int)exoPlayer.getDuration()));

                    handler.postDelayed(this, 1000);
                }

            }
        });


    }

    private void initSeekBar() {
        seekPlayerProgress = (SeekBar) findViewById(R.id.mediacontroller_progress);
        seekPlayerProgress.requestFocus();

        seekPlayerProgress.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            @Override
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                if (!fromUser) {
                    // We're not interested in programmatically generated changes to
                    // the progress bar's position.
                    return;
                }

                exoPlayer.seekTo(progress*1000);
            }

            @Override
            public void onStartTrackingTouch(SeekBar seekBar) {

            }

            @Override
            public void onStopTrackingTouch(SeekBar seekBar) {

            }
        });

        seekPlayerProgress.setMax(0);
        seekPlayerProgress.setMax((int) exoPlayer.getDuration()/1000);

    }


    private void toggleMediaControls() {

        if(bControlsActive){
            hideMediaController();
            bControlsActive=false;

        }else{
            showController();
            bControlsActive=true;
            setProgress();
        }
    }

    private void showController() {
        mediaController.setVisibility(View.VISIBLE);
        getWindow().clearFlags(500);
    }

    private Window getWindow() {
        JApplication app=JFactory.getApplication();
        return app.context.getWindow();
    }

    private void hideMediaController() {
        mediaController.setVisibility(View.GONE);
        getWindow().addFlags(500);
    }

    private void initPlayButton() {
        btnPlay = (ImageButton) findViewById(R.id.btnPlay);
        btnPlay.requestFocus();
        btnPlay.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(bIsPlaying){
                    exoPlayer.setPlayWhenReady(false);
                    bIsPlaying=false;
                }else{
                    exoPlayer.setPlayWhenReady(true);
                    bIsPlaying=true;
                    setProgress();
                }
            }
        });
    }

    private void initPlayer(int position) {

        JApplication app=JFactory.getApplication();
        Allocator allocator = new DefaultAllocator(minBufferMs);
        DataSource dataSource = new DefaultUriDataSource(context, null, userAgent);

        ExtractorSampleSource sampleSource = new ExtractorSampleSource( Uri.parse(mp4URL), dataSource, allocator,BUFFER_SEGMENT_COUNT * BUFFER_SEGMENT_SIZE);

        MediaCodecVideoTrackRenderer videoRenderer = new
                MediaCodecVideoTrackRenderer(context, sampleSource, MediaCodecSelector.DEFAULT,MediaCodec.VIDEO_SCALING_MODE_SCALE_TO_FIT);

        MediaCodecAudioTrackRenderer audioRenderer = new MediaCodecAudioTrackRenderer(sampleSource, MediaCodecSelector.DEFAULT);

        exoPlayer = ExoPlayer.Factory.newInstance(RENDERER_COUNT);
        exoPlayer.prepare(videoRenderer, audioRenderer);
        exoPlayer.sendMessage(videoRenderer,
                MediaCodecVideoTrackRenderer.MSG_SET_SURFACE,
                surfaceView.getHolder().getSurface());
        exoPlayer.seekTo(position);
        initMediaControls();

    }

    private void initHLSPlayer(int position) {
        JApplication app=JFactory.getApplication();
        Handler mHandler= new Handler();
        final ManifestFetcher<HlsPlaylist> playlistFetcher;
        HlsPlaylistParser parser = new HlsPlaylistParser();
        playlistFetcher = new ManifestFetcher<>(HLSurl,
                new DefaultUriDataSource(context, userAgent), parser);


        playlistFetcher.singleLoad(mHandler.getLooper(), new ManifestFetcher.ManifestCallback<HlsPlaylist>() {


            @Override
            public void onSingleManifest(HlsPlaylist manifest) {
                JApplication app=JFactory.getApplication();
                LoadControl loadControl = new DefaultLoadControl(new DefaultAllocator(BUFFER_SEGMENT_SIZE));
                DefaultBandwidthMeter bandwidthMeter = new DefaultBandwidthMeter();
                PtsTimestampAdjusterProvider timestampAdjusterProvider = new PtsTimestampAdjusterProvider();
                DataSource dataSource = new DefaultUriDataSource(context, bandwidthMeter, userAgent);
                HlsChunkSource chunkSource = new HlsChunkSource(true , dataSource, HLSurl, playlistFetcher.getManifest(),
                        DefaultHlsTrackSelector.newDefaultInstance(context), bandwidthMeter, timestampAdjusterProvider,
                        HlsChunkSource.ADAPTIVE_MODE_SPLICE);
                HlsSampleSource sampleSource = new HlsSampleSource(chunkSource, loadControl,
                        BUFFER_SEGMENT_COUNT * BUFFER_SEGMENT_SIZE);
                MediaCodecVideoTrackRenderer videoRenderer = new MediaCodecVideoTrackRenderer(context, sampleSource,
                        MediaCodecSelector.DEFAULT, MediaCodec.VIDEO_SCALING_MODE_SCALE_TO_FIT);
                MediaCodecAudioTrackRenderer audioRenderer = new MediaCodecAudioTrackRenderer(sampleSource,
                        MediaCodecSelector.DEFAULT);

                exoPlayer = ExoPlayer.Factory.newInstance(RENDERER_COUNT);
                exoPlayer.prepare(videoRenderer, audioRenderer);
                exoPlayer.sendMessage(videoRenderer,
                        MediaCodecVideoTrackRenderer.MSG_SET_SURFACE,
                        surfaceView.getHolder().getSurface());
                exoPlayer.seekTo(0);

                initMediaControls();


            }

            @Override
            public void onSingleManifestError(IOException e) {

            }
        });
    }

}
