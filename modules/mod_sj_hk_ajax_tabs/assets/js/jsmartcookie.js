!function ($) {
    "use strict";
    $.fn.cookie = function (name, value, days) {
        var set, get;

        /**
         * Pass in the name of the cookie, the value to set and how many days it
         * should last. Use a negative number to unset
         */
        set = function (name, value, days) {
            var date, expires = '';
            if (days) {
                date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }

            // set the actual cookie
            document.cookie = name + "=" + value + expires + "; path=/";
            return true;
        }

        /**
         * Return a cookie by name should we find it
         */
        get = function (cookieName) {
            var i, name, equal, numCookies, cookies = document.cookie
                .split(";");

            if (cookies) {
                numCookies = cookies.length;
                for (i = 0; i < numCookies; i++) {
                    equal = cookies[i].indexOf("=");
                    name = cookies[i].substr(0, equal);
                    if (name.replace(/^\s+|\s+$/g, "") === cookieName) {
                        return unescape(cookies[i].substr(equal + 1));
                    }
                }
            }

            // nothing found
            return false;
        };

        // get a cookie value or set one?
        if (name && !value && !days) { // get
            return get(name);
        } else { // set
            return set(name, value, days);
        }
    };
}(window.jQuery);