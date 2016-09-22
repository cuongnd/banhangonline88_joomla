<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
/*
(function($window){

var _space = " ",
    _width = "width",
    _height = "height",
    _replace = "replace",
    _classList = "classList",
    _className = "className",
    _parentNode = "parentNode",
    _fitWidth = "fit-width",
    _fitHeight = "fit-height",
    _fitBoth = "fit-both",
    _fitSmall = "fit-small",
    _fitClasses = _fitWidth + _space + _fitHeight + _space + _fitBoth + _space + _fitSmall,

    getData = function(node, key){
        return node.getAttribute("data-" + key);
    },

    getNaturalSize = function(node, key) {
        return node["natural" + key[0].toUpperCase() + key.slice(1)];
    },

    getSize = function(node, key) {
        return parseInt(getData(node, key) || getNaturalSize(node, key) || node[key]);
    },

    addClass = function(node, className) {
        node[_classList] ? node[_classList].add(className) : node[_className] += _space + className;
    },

    removeClass = function(node, className) {
        node[_className] = node[_className][_replace](new RegExp("\\b(" + className[_replace](/\s+/g, "|") + ")\\b", "g"), _space)[_replace](/\s+/g, _space)[_replace](/^\s+|\s+$/g, "");
    },

    setStyle = function(node, key, val) {
        node.style[key] = val + "px";
    },

    retry = {},

    setLayout = function(image, viewport, gutter, container, mode, threshold, width, height, viewportWidth, viewportHeight) {

        // If we're not taking dimensions from data attributes,
        // and we're not ready to take dimensions from natural attributes,
        // try again after 200ms  or give up after 5 seconds.
        if (!getData(image, _width) && getNaturalSize(image, _width)===0 && (image._retry || (image._retry = 0)) <= 25) {
            return setTimeout(function(){
                image._retry++; setLayout(image);
            }, 200);
        }

        // Get image viewport (b), gutter (u) and container (a).
        viewport  = image[_parentNode];
        gutter    = viewport[_parentNode];
        container = gutter[_parentNode];
        mode      = getData(viewport, "mode");
        threshold = getData(viewport, "threshold");
        width     = getSize(image, _width);
        height    = getSize(image, _height);
        viewportWidth  = viewport.offsetWidth;
        viewportHeight = viewport.offsetHeight;

        // Remove class
        removeClass(container, _fitClasses);

        // Add the correct classname to the image container
        addClass(container,

            // If image is smaller than threshold, show the
            // image exactly as it is inside the container.
            (width < threshold && height < threshold) ?
            (function(){
                // Enforce width & height in case the actual
                // image loaded is larger than dimension specified
                // from the data-width & data-height tag
                setStyle(image, _width, width);
                setStyle(image, _height, height);
                return _fitSmall;
            })()

            // Else determine if we should fit or fill the image within the viewport
            : mode=="cover" ?

            // If we should fill the image within the viewport,
            // determine strategy to fill the image within the viewport
            // by assessing the orientation of the image
            // against the orientation of the viewport.
            // https://gist.github.com/jstonne/2ea7077236a1245c397a
            (function(ratio, wRatio, hRatio){

                // When viewport's width & height is 0,
                // put it on the watchlist first. When resize
                // event is triggered, the actual layout will be set again.
                if (viewportWidth < 1 || viewportHeight < 1) {
                    watchList.push(image);
                    return _fitBoth;
                }

                ratio  = viewportWidth / viewportHeight;
                wRatio = viewportWidth / width;
                hRatio = viewportHeight / height;

                // Tall viewport
                if (ratio < 1) return (height * wRatio < viewportHeight) ? _fitHeight : _fitWidth;

                // Wide viewport
                if (ratio > 1) return (width * hRatio < viewportWidth) ? _fitWidth : _fitHeight;

                // Square viewport
                if (ratio==1) return (width/height <= 1) ? _fitWidth : _fitHeight;
            })()

            // If we should fit the image within the container
            // add the image to the watchlist because tall
            // images needs a fixed maxHeight. (Chrome/Webkit issue)
            :(function(){
                watchList.push(image);
                image.style.maxHeight = "none";
                setStyle(image, "maxHeight", viewport.offsetHeight);
                return _fitBoth;
            })()
        );

        // Remove onload attribute
        image.removeAttribute("onload");
    },

    updateLayout = function(image, imageList) {
        imageList = watchList;
        watchList = [];
        while (image = imageList.shift()) {
            image[_parentNode] && setLayout(image);
        }
    },

    watchList = [],

    watchTimer,

    watchLayout = function(){
        clearTimeout(watchTimer);
        watchTimer = setTimeout(updateLayout, 500);
    },

    imageList = $window.ESImageList || [];

    $window.ESImage = setLayout;
    $window.ESImageRefresh = updateLayout;

    $window.addEventListener("resize", watchLayout, false);

    while (imageList.length) {
        setLayout(imageList.shift());
    }

})(window);
*/
?>
<?php echo SOCIAL_FOUNDRY_BOOTCODE ?>.component("EasySocial", <?php echo $this->toJSON(); ?>);
!function(t){var e,n=" ",i="width",r="height",o="replace",f="classList",s="className",u="parentNode",a="fit-width",c="fit-height",h="fit-both",g="fit-small",d=a+n+c+n+h+n+g,l=function(t,e){return t.getAttribute("data-"+e)},m=function(t,e){return t["natural"+e[0].toUpperCase()+e.slice(1)]},p=function(t,e){return parseInt(l(t,e)||m(t,e)||t[e])},v=function(t,e){t[f]?t[f].add(e):t[s]+=n+e},b=function(t,e){t[s]=t[s][o](RegExp("\\b("+e[o](/\s+/g,"|")+")\\b","g"),n)[o](/\s+/g,n)[o](/^\s+|\s+$/g,"")},y=function(t,e,n){t.style[e]=n+"px"},E=function(t,e,n,o,f,s,w,H,I,L){return l(t,i)||0!==m(t,i)||(t._retry||(t._retry=0))>25?(e=t[u],n=e[u],o=n[u],f=l(e,"mode"),s=l(e,"threshold"),w=p(t,i),H=p(t,r),I=e.offsetWidth,L=e.offsetHeight,b(o,d),v(o,s>w&&s>H?function(){return y(t,i,w),y(t,r,H),g}():"cover"==f?function(e,n,i){return 1>I||1>L?(x.push(t),h):(e=I/L,n=I/w,i=L/H,1>e?L>H*n?c:a:e>1?I>w*i?a:c:1==e?w/H>1?c:a:void 0)}():function(){return x.push(t),t.style.maxHeight="none",y(t,"maxHeight",e.offsetHeight),h}()),void t.removeAttribute("onload")):setTimeout(function(){t._retry++,E(t)},200)},w=function(t,e){for(e=x,x=[];t=e.shift();)t[u]&&E(t)},x=[],H=function(){clearTimeout(e),e=setTimeout(w,500)},I=t.ESImageList||[];for(t.ESImage=E,t.ESImageRefresh=w,t.addEventListener("resize",H,!1);I.length;)E(I.shift())}(window);
