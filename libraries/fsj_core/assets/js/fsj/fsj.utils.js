String.prototype.replaceAll = function (search, replace) {
    //if replace is null, return original string otherwise it will
    //replace search string with 'undefined'.
    if (!replace)
        return this;

    return this.replace(new RegExp('[' + search + ']', 'g'), replace);
};

var fsj_utils = {
    removeParameter: function (url, parameter) {
        var urlparts = url.split('?');

        if (urlparts.length >= 2) {
            var urlBase = urlparts.shift(); //get first part, and remove from array
            var queryString = urlparts.join("?"); //join it back up

            var prefix = encodeURIComponent(parameter) + '=';
            var pars = queryString.split(/[&;]/g);
            for (var i = pars.length; i-- > 0; )               //reverse iteration as may be destructive
                if (pars[i].lastIndexOf(prefix, 0) !== -1)   //idiom for string.startsWith
                    pars.splice(i, 1);
            url = urlBase + '?' + pars.join('&');
        }
        return url;
    },

    xorEncode: function (txt, pass) {

        var ord = [];
        var buf = "";

        var z, j;

        for (z = 1; z <= 255; z++) { ord[String.fromCharCode(z)] = z }

        for (j = z = 0; z < txt.length; z++) {
            buf += String.fromCharCode(ord[txt.substr(z, 1)] ^ ord[pass.substr(j, 1)]);
            j = (j < pass.length - 1) ? j + 1 : 0;
        }

        return buf;
    },

    parseQuerystring: function () {
        var nvpair = {};
        var qs = window.location.search.replace('?', '');
        var pairs = qs.split('&');
        jQuery.each(pairs, function (i, v) {
            var pair = v.split('=');
            nvpair[pair[0]] = pair[1];
        });
        return nvpair;
    }
};