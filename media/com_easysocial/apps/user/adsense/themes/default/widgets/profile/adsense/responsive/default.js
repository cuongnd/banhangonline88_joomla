EasySocial.require()
.script('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js')
.done(function(){
	try {
		(adsbygoogle = window.adsbygoogle || []).push({});
	} catch (err) {
		console.log('invalid adsense code');
	}
});