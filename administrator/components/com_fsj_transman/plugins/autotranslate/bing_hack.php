<?php

/*
Todo:

Base URL http://www.bing.com/translator/
Find latest version of LandingPage.js file
Get Url http://www.bing.com/translator/dynamic/214860/js/LandingPage.js?loc=en&phenabled=&rttenabled=&v=214860
From landing page find:

appId:"TW-4-hluHk7lgfcmd13Pv1Yvmr4T-qPFpA2-kMA0BlqM*"

The appid seems to be good for a while, so cache it for a bit (hour?, 15 mins?)

Then URL for translate is:

http://api.microsofttranslator.com/v2/ajax.svc/TranslateArray2?appId=%22TW-4-hluHk7lgfcmd13Pv1Yvmr4T-qPFpA2-kMA0BlqM*%22&texts=[%221%22%2C%22Your+comment+has+been+submitted+for+approval.%22%2C%222%22%2C%22Some+more+text+here%22]&from=%22%22&to=%22ru%22&options={}&oncomplete=onComplete_6&onerror=onError_6&_=1428754734374

http://api.microsofttranslator.com/v2/ajax.svc/

TranslateArray2?
appId=%22TW-4-hluHk7lgfcmd13Pv1Yvmr4T-qPFpA2-kMA0BlqM*%22&
texts=[%221%22%2C%22Your+comment+has+been+submitted+for+approval.%22%2C%222%22%2C%22Some+more+text+here%22]&
from=%22%22&
to=%22ru%22&
options={}&
oncomplete=onComplete_6&
onerror=onError_6&_=1428754734374


Result:

onComplete_6([{"Alignment":"0:0-0:0","From":"en","OriginalTextSentenceLengths":[1],"TranslatedText":"1","TranslatedTextSentenceLengths":[1]},{"Alignment":"0:3-0:2 5:11-4:14 17:20-16:18 22:30-20:30 32:34-32:33 36:43-35:45 44:44-46:46","From":"en","OriginalTextSentenceLengths":[45],"TranslatedText":"??? ??????????? ??? ??????????? ?? ???????????.","TranslatedTextSentenceLengths":[47]},{"Alignment":"0:0-0:0","From":"en","OriginalTextSentenceLengths":[1],"TranslatedText":"2","TranslatedTextSentenceLengths":[1]},{"Alignment":"0:3-0:8 5:8-10:15 10:13-17:21 15:18-23:27","From":"en","OriginalTextSentenceLengths":[19],"TranslatedText":"????????? ?????? ????? ?????","TranslatedTextSentenceLengths":[28]}]);


Language Codes:

<tbody><tr>
      
          <td><a id="Lang_DstLangList_ar" href="javascript:void(0)" value="ar" class="">Arabic</a></td>
      
          <td><a id="Lang_DstLangList_he" href="javascript:void(0)" value="he">Hebrew</a></td>
      
          <td><a id="Lang_DstLangList_pt" href="javascript:void(0)" value="pt">Portuguese</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_bs-Latn" href="javascript:void(0)" value="bs-Latn">Bosnian (Latin)</a></td>
      
          <td><a id="Lang_DstLangList_hi" href="javascript:void(0)" value="hi">Hindi</a></td>
      
          <td><a id="Lang_DstLangList_otq" href="javascript:void(0)" value="otq">Querétaro Otomi</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_bg" href="javascript:void(0)" value="bg">Bulgarian</a></td>
      
          <td><a id="Lang_DstLangList_mww" href="javascript:void(0)" value="mww" class="">Hmong Daw</a></td>
      
          <td><a id="Lang_DstLangList_ro" href="javascript:void(0)" value="ro" class="">Romanian</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_ca" href="javascript:void(0)" value="ca" class="">Catalan</a></td>
      
          <td><a id="Lang_DstLangList_hu" href="javascript:void(0)" value="hu">Hungarian</a></td>
      
          <td><a id="Lang_DstLangList_ru" href="javascript:void(0)" value="ru" class="">Russian</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_zh-CHS" href="javascript:void(0)" value="zh-CHS">Chinese Simplified</a></td>
      
          <td><a id="Lang_DstLangList_id" href="javascript:void(0)" value="id" class="">Indonesian</a></td>
      
          <td><a id="Lang_DstLangList_sr-Cyrl" href="javascript:void(0)" value="sr-Cyrl">Serbian (Cyrillic)</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_zh-CHT" href="javascript:void(0)" value="zh-CHT">Chinese Traditional</a></td>
      
          <td><a id="Lang_DstLangList_it" href="javascript:void(0)" value="it">Italian</a></td>
      
          <td><a id="Lang_DstLangList_sr-Latn" href="javascript:void(0)" value="sr-Latn">Serbian (Latin)</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_hr" href="javascript:void(0)" value="hr">Croatian</a></td>
      
          <td><a id="Lang_DstLangList_ja" href="javascript:void(0)" value="ja">Japanese</a></td>
      
          <td><a id="Lang_DstLangList_sk" href="javascript:void(0)" value="sk">Slovak</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_cs" href="javascript:void(0)" value="cs">Czech</a></td>
      
          <td><a id="Lang_DstLangList_tlh" href="javascript:void(0)" value="tlh">Klingon</a></td>
      
          <td><a id="Lang_DstLangList_sl" href="javascript:void(0)" value="sl" class="">Slovenian</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_da" href="javascript:void(0)" value="da">Danish</a></td>
      
          <td><a id="Lang_DstLangList_tlh-Qaak" href="javascript:void(0)" value="tlh-Qaak">Klingon (pIqaD)</a></td>
      
          <td><a id="Lang_DstLangList_es" href="javascript:void(0)" value="es">Spanish</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_nl" href="javascript:void(0)" value="nl">Dutch</a></td>
      
          <td><a id="Lang_DstLangList_ko" href="javascript:void(0)" value="ko">Korean</a></td>
      
          <td><a id="Lang_DstLangList_sv" href="javascript:void(0)" value="sv">Swedish</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_en" href="javascript:void(0)" value="en">English</a></td>
      
          <td><a id="Lang_DstLangList_lv" href="javascript:void(0)" value="lv">Latvian</a></td>
      
          <td><a id="Lang_DstLangList_th" href="javascript:void(0)" value="th" class="Selected">Thai</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_et" href="javascript:void(0)" value="et" class="">Estonian</a></td>
      
          <td><a id="Lang_DstLangList_lt" href="javascript:void(0)" value="lt">Lithuanian</a></td>
      
          <td><a id="Lang_DstLangList_tr" href="javascript:void(0)" value="tr">Turkish</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_fi" href="javascript:void(0)" value="fi">Finnish</a></td>
      
          <td><a id="Lang_DstLangList_ms" href="javascript:void(0)" value="ms">Malay</a></td>
      
          <td><a id="Lang_DstLangList_uk" href="javascript:void(0)" value="uk">Ukrainian</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_fr" href="javascript:void(0)" value="fr">French</a></td>
      
          <td><a id="Lang_DstLangList_mt" href="javascript:void(0)" value="mt">Maltese</a></td>
      
          <td><a id="Lang_DstLangList_ur" href="javascript:void(0)" value="ur">Urdu</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_de" href="javascript:void(0)" value="de">German</a></td>
      
          <td><a id="Lang_DstLangList_no" href="javascript:void(0)" value="no">Norwegian</a></td>
      
          <td><a id="Lang_DstLangList_vi" href="javascript:void(0)" value="vi">Vietnamese</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_el" href="javascript:void(0)" value="el">Greek</a></td>
      
          <td><a id="Lang_DstLangList_fa" href="javascript:void(0)" value="fa">Persian</a></td>
      
          <td><a id="Lang_DstLangList_cy" href="javascript:void(0)" value="cy">Welsh</a></td>
      
    </tr>
  
    <tr>
      
          <td><a id="Lang_DstLangList_ht" href="javascript:void(0)" value="ht">Haitian Creole</a></td>
      
          <td><a id="Lang_DstLangList_pl" href="javascript:void(0)" value="pl">Polish</a></td>
      
          <td><a id="Lang_DstLangList_yua" href="javascript:void(0)" value="yua">Yucatec Maya</a></td>
      
    </tr>
  
  
</tbody>
*/