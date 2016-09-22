$jMaQma(document).ready(function ($) {
    // Google Analytics Tracking
    if (IMQM_ANALYTICS)
    {
        $jMaQma(".trackdownload").click(function () {
            _gaq.push(['_trackEvent', 'Download', MQM_DOWNLOAD_LINK]);
        });
        $jMaQma(".tracktrial").click(function () {
            _gaq.push(['_trackEvent', 'Trial', MQM_DOWNLOAD_TRIAL]);
        });
    }
});