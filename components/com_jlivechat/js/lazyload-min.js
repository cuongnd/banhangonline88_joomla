/*
Copyright (c) 2008 Ryan Grove <ryan@wonko.com>. All rights reserved.
Licensed under the BSD License:
http://www.opensource.org/licenses/bsd-license.html
Version: 1.0.4
*/
var LazyLoad=function(){var E=document,D=null,A=[],C;function B(){if(C){return }var G=navigator.userAgent,F;C={gecko:0,ie:0,webkit:0};F=G.match(/AppleWebKit\/(\S*)/);if(F&&F[1]){C.webkit=parseFloat(F[1])}else{F=G.match(/MSIE\s([^;]*)/);if(F&&F[1]){C.ie=parseFloat(F[1])}else{if((/Gecko\/(\S*)/).test(G)){C.gecko=1;F=G.match(/rv:([^\s\)]*)/);if(F&&F[1]){C.gecko=parseFloat(F[1])}}}}}return{load:function(K,L,J,I){var H=E.getElementsByTagName("head")[0],G,F;if(K){K=K.constructor===Array?K:[K];for(G=0;G<K.length;++G){A.push({url:K[G],callback:G===K.length-1?L:null,obj:J,scope:I})}}if(D||!(D=A.shift())){return }B();F=E.createElement("script");F.src=D.url;if(C.ie){F.onreadystatechange=function(){if(this.readyState==="loaded"||this.readyState==="complete"){LazyLoad.requestComplete()}}}else{if(C.gecko||C.webkit>=420){F.onload=LazyLoad.requestComplete;F.onerror=LazyLoad.requestComplete}}H.appendChild(F);if(!C.ie&&!C.gecko&&!(C.webkit>=420)){F=E.createElement("script");F.appendChild(E.createTextNode("LazyLoad.requestComplete();"));H.appendChild(F)}},loadOnce:function(N,O,L,P,G){var H=[],I=E.getElementsByTagName("script"),M,J,K,F;N=N.constructor===Array?N:[N];for(M=0;M<N.length;++M){K=false;F=N[M];for(J=0;J<I.length;++J){if(F===I[J].src){K=true;break}}if(!K){H.push(F)}}if(H.length>0){LazyLoad.load(H,O,L,P)}else{if(G){if(L){if(P){O.call(L)}else{O.call(window,L)}}else{O.call()}}}},requestComplete:function(){if(D.callback){if(D.obj){if(D.scope){D.callback.call(D.obj)}else{D.callback.call(window,D.obj)}}else{D.callback.call()}}D=null;if(A.length){LazyLoad.load()}}}}();
