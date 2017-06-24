!function (a, b) {
    function I(a) {
        var b = a.length, c = t.type(a);
        return !t.isWindow(a) && (!(1 !== a.nodeType || !b) || ("array" === c || "function" !== c && (0 === b || "number" == typeof b && b > 0 && b - 1 in a)))
    }

    function K(a) {
        var b = J[a] = {};
        return t.each(a.match(v) || [], function (a, c) {
            b[c] = !0
        }), b
    }

    function N(a, c, d, e) {
        if (t.acceptData(a)) {
            var f, g, h = t.expando, i = "string" == typeof c, j = a.nodeType, l = j ? t.cache : a, m = j ? a[h] : a[h] && h;
            if (m && l[m] && (e || l[m].data) || !i || d !== b)return m || (j ? a[h] = m = k.pop() || t.guid++ : m = h), l[m] || (l[m] = {}, j || (l[m].toJSON = t.noop)), "object" != typeof c && "function" != typeof c || (e ? l[m] = t.extend(l[m], c) : l[m].data = t.extend(l[m].data, c)), f = l[m], e || (f.data || (f.data = {}), f = f.data), d !== b && (f[t.camelCase(c)] = d), i ? (g = f[c], null == g && (g = f[t.camelCase(c)])) : g = f, g
        }
    }

    function O(a, b, c) {
        if (t.acceptData(a)) {
            var d, e, f, g = a.nodeType, h = g ? t.cache : a, i = g ? a[t.expando] : t.expando;
            if (h[i]) {
                if (b && (f = c ? h[i] : h[i].data)) {
                    t.isArray(b) ? b = b.concat(t.map(b, t.camelCase)) : b in f ? b = [b] : (b = t.camelCase(b), b = b in f ? [b] : b.split(" "));
                    for (d = 0, e = b.length; d < e; d++)delete f[b[d]];
                    if (!(c ? Q : t.isEmptyObject)(f))return
                }
                (c || (delete h[i].data, Q(h[i]))) && (g ? t.cleanData([a], !0) : t.support.deleteExpando || h != h.window ? delete h[i] : h[i] = null)
            }
        }
    }

    function P(a, c, d) {
        if (d === b && 1 === a.nodeType) {
            var e = "data-" + c.replace(M, "-$1").toLowerCase();
            if (d = a.getAttribute(e), "string" == typeof d) {
                try {
                    d = "true" === d || "false" !== d && ("null" === d ? null : +d + "" === d ? +d : L.test(d) ? t.parseJSON(d) : d)
                } catch (a) {
                }
                t.data(a, c, d)
            } else d = b
        }
        return d
    }

    function Q(a) {
        var b;
        for (b in a)if (("data" !== b || !t.isEmptyObject(a[b])) && "toJSON" !== b)return !1;
        return !0
    }

    function ea() {
        return !0
    }

    function fa() {
        return !1
    }

    function la(a, b) {
        do a = a[b]; while (a && 1 !== a.nodeType);
        return a
    }

    function ma(a, b, c) {
        if (b = b || 0, t.isFunction(b))return t.grep(a, function (a, d) {
            var e = !!b.call(a, d, a);
            return e === c
        });
        if (b.nodeType)return t.grep(a, function (a) {
            return a === b === c
        });
        if ("string" == typeof b) {
            var d = t.grep(a, function (a) {
                return 1 === a.nodeType
            });
            if (ia.test(b))return t.filter(b, d, !c);
            b = t.filter(b, d)
        }
        return t.grep(a, function (a) {
            return t.inArray(a, b) >= 0 === c
        })
    }

    function na(a) {
        var b = oa.split("|"), c = a.createDocumentFragment();
        if (c.createElement)for (; b.length;)c.createElement(b.pop());
        return c
    }

    function Fa(a, b) {
        return a.getElementsByTagName(b)[0] || a.appendChild(a.ownerDocument.createElement(b))
    }

    function Ga(a) {
        var b = a.getAttributeNode("type");
        return a.type = (b && b.specified) + "/" + a.type, a
    }

    function Ha(a) {
        var b = Aa.exec(a.type);
        return b ? a.type = b[1] : a.removeAttribute("type"), a
    }

    function Ia(a, b) {
        for (var c, d = 0; null != (c = a[d]); d++)t._data(c, "globalEval", !b || t._data(b[d], "globalEval"))
    }

    function Ja(a, b) {
        if (1 === b.nodeType && t.hasData(a)) {
            var c, d, e, f = t._data(a), g = t._data(b, f), h = f.events;
            if (h) {
                delete g.handle, g.events = {};
                for (c in h)for (d = 0, e = h[c].length; d < e; d++)t.event.add(b, c, h[c][d])
            }
            g.data && (g.data = t.extend({}, g.data))
        }
    }

    function Ka(a, b) {
        var c, d, e;
        if (1 === b.nodeType) {
            if (c = b.nodeName.toLowerCase(), !t.support.noCloneEvent && b[t.expando]) {
                e = t._data(b);
                for (d in e.events)t.removeEvent(b, d, e.handle);
                b.removeAttribute(t.expando)
            }
            "script" === c && b.text !== a.text ? (Ga(b).text = a.text, Ha(b)) : "object" === c ? (b.parentNode && (b.outerHTML = a.outerHTML), t.support.html5Clone && a.innerHTML && !t.trim(b.innerHTML) && (b.innerHTML = a.innerHTML)) : "input" === c && xa.test(a.type) ? (b.defaultChecked = b.checked = a.checked, b.value !== a.value && (b.value = a.value)) : "option" === c ? b.defaultSelected = b.selected = a.defaultSelected : "input" !== c && "textarea" !== c || (b.defaultValue = a.defaultValue)
        }
    }

    function La(a, c) {
        var d, f, g = 0, h = typeof a.getElementsByTagName !== e ? a.getElementsByTagName(c || "*") : typeof a.querySelectorAll !== e ? a.querySelectorAll(c || "*") : b;
        if (!h)for (h = [], d = a.childNodes || a; null != (f = d[g]); g++)!c || t.nodeName(f, c) ? h.push(f) : t.merge(h, La(f, c));
        return c === b || c && t.nodeName(a, c) ? t.merge([a], h) : h
    }

    function Ma(a) {
        xa.test(a.type) && (a.defaultChecked = a.checked)
    }

    function bb(a, b) {
        if (b in a)return b;
        for (var c = b.charAt(0).toUpperCase() + b.slice(1), d = b, e = ab.length; e--;)if (b = ab[e] + c, b in a)return b;
        return d
    }

    function cb(a, b) {
        return a = b || a, "none" === t.css(a, "display") || !t.contains(a.ownerDocument, a)
    }

    function db(a, b) {
        for (var c, d, e, f = [], g = 0, h = a.length; g < h; g++)d = a[g], d.style && (f[g] = t._data(d, "olddisplay"), c = d.style.display, b ? (f[g] || "none" !== c || (d.style.display = ""), "" === d.style.display && cb(d) && (f[g] = t._data(d, "olddisplay", hb(d.nodeName)))) : f[g] || (e = cb(d), (c && "none" !== c || !e) && t._data(d, "olddisplay", e ? c : t.css(d, "display"))));
        for (g = 0; g < h; g++)d = a[g], d.style && (b && "none" !== d.style.display && "" !== d.style.display || (d.style.display = b ? f[g] || "" : "none"));
        return a
    }

    function eb(a, b, c) {
        var d = Va.exec(b);
        return d ? Math.max(0, d[1] - (c || 0)) + (d[2] || "px") : b
    }

    function fb(a, b, c, d, e) {
        for (var f = c === (d ? "border" : "content") ? 4 : "width" === b ? 1 : 0, g = 0; f < 4; f += 2)"margin" === c && (g += t.css(a, c + _a[f], !0, e)), d ? ("content" === c && (g -= t.css(a, "padding" + _a[f], !0, e)), "margin" !== c && (g -= t.css(a, "border" + _a[f] + "Width", !0, e))) : (g += t.css(a, "padding" + _a[f], !0, e), "padding" !== c && (g += t.css(a, "border" + _a[f] + "Width", !0, e)));
        return g
    }

    function gb(a, b, c) {
        var d = !0, e = "width" === b ? a.offsetWidth : a.offsetHeight, f = Oa(a), g = t.support.boxSizing && "border-box" === t.css(a, "boxSizing", !1, f);
        if (e <= 0 || null == e) {
            if (e = Pa(a, b, f), (e < 0 || null == e) && (e = a.style[b]), Wa.test(e))return e;
            d = g && (t.support.boxSizingReliable || e === a.style[b]), e = parseFloat(e) || 0
        }
        return e + fb(a, b, c || (g ? "border" : "content"), d, f) + "px"
    }

    function hb(a) {
        var b = f, c = Ya[a];
        return c || (c = ib(a, b), "none" !== c && c || (Na = (Na || t("<iframe frameborder='0' width='0' height='0'/>").css("cssText", "display:block !important")).appendTo(b.documentElement), b = (Na[0].contentWindow || Na[0].contentDocument).document, b.write("<!doctype html><html><body>"), b.close(), c = ib(a, b), Na.detach()), Ya[a] = c), c
    }

    function ib(a, b) {
        var c = t(b.createElement(a)).appendTo(b.body), d = t.css(c[0], "display");
        return c.remove(), d
    }

    function ob(a, b, c, d) {
        var e;
        if (t.isArray(b))t.each(b, function (b, e) {
            c || kb.test(a) ? d(a, e) : ob(a + "[" + ("object" == typeof e ? b : "") + "]", e, c, d)
        }); else if (c || "object" !== t.type(b))d(a, b); else for (e in b)ob(a + "[" + e + "]", b[e], c, d)
    }

    function Eb(a) {
        return function (b, c) {
            "string" != typeof b && (c = b, b = "*");
            var d, e = 0, f = b.toLowerCase().match(v) || [];
            if (t.isFunction(c))for (; d = f[e++];)"+" === d[0] ? (d = d.slice(1) || "*", (a[d] = a[d] || []).unshift(c)) : (a[d] = a[d] || []).push(c)
        }
    }

    function Fb(a, b, c, d) {
        function g(h) {
            var i;
            return e[h] = !0, t.each(a[h] || [], function (a, h) {
                var j = h(b, c, d);
                return "string" != typeof j || f || e[j] ? f ? !(i = j) : void 0 : (b.dataTypes.unshift(j), g(j), !1)
            }), i
        }

        var e = {}, f = a === Cb;
        return g(b.dataTypes[0]) || !e["*"] && g("*")
    }

    function Gb(a, c) {
        var d, e, f = t.ajaxSettings.flatOptions || {};
        for (e in c)c[e] !== b && ((f[e] ? a : d || (d = {}))[e] = c[e]);
        return d && t.extend(!0, a, d), a
    }

    function Hb(a, c, d) {
        var e, f, g, h, i = a.contents, j = a.dataTypes, k = a.responseFields;
        for (h in k)h in d && (c[k[h]] = d[h]);
        for (; "*" === j[0];)j.shift(), f === b && (f = a.mimeType || c.getResponseHeader("Content-Type"));
        if (f)for (h in i)if (i[h] && i[h].test(f)) {
            j.unshift(h);
            break
        }
        if (j[0] in d)g = j[0]; else {
            for (h in d) {
                if (!j[0] || a.converters[h + " " + j[0]]) {
                    g = h;
                    break
                }
                e || (e = h)
            }
            g = g || e
        }
        if (g)return g !== j[0] && j.unshift(g), d[g]
    }

    function Ib(a, b) {
        var c, d, e, f, g = {}, h = 0, i = a.dataTypes.slice(), j = i[0];
        if (a.dataFilter && (b = a.dataFilter(b, a.dataType)), i[1])for (e in a.converters)g[e.toLowerCase()] = a.converters[e];
        for (; d = i[++h];)if ("*" !== d) {
            if ("*" !== j && j !== d) {
                if (e = g[j + " " + d] || g["* " + d], !e)for (c in g)if (f = c.split(" "), f[1] === d && (e = g[j + " " + f[0]] || g["* " + f[0]])) {
                    e === !0 ? e = g[c] : g[c] !== !0 && (d = f[0], i.splice(h--, 0, d));
                    break
                }
                if (e !== !0)if (e && a.throws)b = e(b); else try {
                    b = e(b)
                } catch (a) {
                    return {state: "parsererror", error: e ? a : "No conversion from " + j + " to " + d}
                }
            }
            j = d
        }
        return {state: "success", data: b}
    }

    function Pb() {
        try {
            return new a.XMLHttpRequest
        } catch (a) {
        }
    }

    function Qb() {
        try {
            return new a.ActiveXObject("Microsoft.XMLHTTP")
        } catch (a) {
        }
    }

    function Yb() {
        return setTimeout(function () {
            Rb = b
        }), Rb = t.now()
    }

    function Zb(a, b) {
        t.each(b, function (b, c) {
            for (var d = (Xb[b] || []).concat(Xb["*"]), e = 0, f = d.length; e < f; e++)if (d[e].call(a, b, c))return
        })
    }

    function $b(a, b, c) {
        var d, e, f = 0, g = Wb.length, h = t.Deferred().always(function () {
            delete i.elem
        }), i = function () {
            if (e)return !1;
            for (var b = Rb || Yb(), c = Math.max(0, j.startTime + j.duration - b), d = c / j.duration || 0, f = 1 - d, g = 0, i = j.tweens.length; g < i; g++)j.tweens[g].run(f);
            return h.notifyWith(a, [j, f, c]), f < 1 && i ? c : (h.resolveWith(a, [j]), !1)
        }, j = h.promise({
            elem: a,
            props: t.extend({}, b),
            opts: t.extend(!0, {specialEasing: {}}, c),
            originalProperties: b,
            originalOptions: c,
            startTime: Rb || Yb(),
            duration: c.duration,
            tweens: [],
            createTween: function (b, c) {
                var d = t.Tween(a, j.opts, b, c, j.opts.specialEasing[b] || j.opts.easing);
                return j.tweens.push(d), d
            },
            stop: function (b) {
                var c = 0, d = b ? j.tweens.length : 0;
                if (e)return this;
                for (e = !0; c < d; c++)j.tweens[c].run(1);
                return b ? h.resolveWith(a, [j, b]) : h.rejectWith(a, [j, b]), this
            }
        }), k = j.props;
        for (_b(k, j.opts.specialEasing); f < g; f++)if (d = Wb[f].call(j, a, k, j.opts))return d;
        return Zb(j, k), t.isFunction(j.opts.start) && j.opts.start.call(a, j), t.fx.timer(t.extend(i, {
            elem: a,
            anim: j,
            queue: j.opts.queue
        })), j.progress(j.opts.progress).done(j.opts.done, j.opts.complete).fail(j.opts.fail).always(j.opts.always)
    }

    function _b(a, b) {
        var c, d, e, f, g;
        for (e in a)if (d = t.camelCase(e), f = b[d], c = a[e], t.isArray(c) && (f = c[1], c = a[e] = c[0]), e !== d && (a[d] = c, delete a[e]), g = t.cssHooks[d], g && "expand" in g) {
            c = g.expand(c), delete a[d];
            for (e in c)e in a || (a[e] = c[e], b[e] = f)
        } else b[d] = f
    }

    function ac(a, b, c) {
        var d, e, f, g, h, i, j, k, l, m = this, n = a.style, o = {}, p = [], q = a.nodeType && cb(a);
        c.queue || (k = t._queueHooks(a, "fx"), null == k.unqueued && (k.unqueued = 0, l = k.empty.fire, k.empty.fire = function () {
            k.unqueued || l()
        }), k.unqueued++, m.always(function () {
            m.always(function () {
                k.unqueued--, t.queue(a, "fx").length || k.empty.fire()
            })
        })), 1 === a.nodeType && ("height" in b || "width" in b) && (c.overflow = [n.overflow, n.overflowX, n.overflowY], "inline" === t.css(a, "display") && "none" === t.css(a, "float") && (t.support.inlineBlockNeedsLayout && "inline" !== hb(a.nodeName) ? n.zoom = 1 : n.display = "inline-block")), c.overflow && (n.overflow = "hidden", t.support.shrinkWrapBlocks || m.always(function () {
            n.overflow = c.overflow[0], n.overflowX = c.overflow[1], n.overflowY = c.overflow[2]
        }));
        for (e in b)if (g = b[e], Tb.exec(g)) {
            if (delete b[e], i = i || "toggle" === g, g === (q ? "hide" : "show"))continue;
            p.push(e)
        }
        if (f = p.length) {
            h = t._data(a, "fxshow") || t._data(a, "fxshow", {}), "hidden" in h && (q = h.hidden), i && (h.hidden = !q), q ? t(a).show() : m.done(function () {
                t(a).hide()
            }), m.done(function () {
                var b;
                t._removeData(a, "fxshow");
                for (b in o)t.style(a, b, o[b])
            });
            for (e = 0; e < f; e++)d = p[e], j = m.createTween(d, q ? h[d] : 0), o[d] = h[d] || t.style(a, d), d in h || (h[d] = j.start, q && (j.end = j.start, j.start = "width" === d || "height" === d ? 1 : 0))
        }
    }

    function bc(a, b, c, d, e) {
        return new bc.prototype.init(a, b, c, d, e)
    }

    function cc(a, b) {
        var c, d = {height: a}, e = 0;
        for (b = b ? 1 : 0; e < 4; e += 2 - b)c = _a[e], d["margin" + c] = d["padding" + c] = a;
        return b && (d.opacity = d.width = a), d
    }

    function dc(a) {
        return t.isWindow(a) ? a : 9 === a.nodeType && (a.defaultView || a.parentWindow)
    }

    var c, d, e = typeof b, f = a.document, g = a.location, h = a.jQuery, i = a.$, j = {}, k = [], l = "1.9.1", m = k.concat, n = k.push, o = k.slice, p = k.indexOf, q = j.toString, r = j.hasOwnProperty, s = l.trim, t = function (a, b) {
        return new t.fn.init(a, b, d)
    }, u = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source, v = /\S+/g, w = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, x = /^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/, y = /^<(\w+)\s*\/?>(?:<\/\1>|)$/, z = /^[\],:{}\s]*$/, A = /(?:^|:|,)(?:\s*\[)+/g, B = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g, C = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g, D = /^-ms-/, E = /-([\da-z])/gi, F = function (a, b) {
        return b.toUpperCase()
    }, G = function (a) {
        (f.addEventListener || "load" === a.type || "complete" === f.readyState) && (H(), t.ready())
    }, H = function () {
        f.addEventListener ? (f.removeEventListener("DOMContentLoaded", G, !1), a.removeEventListener("load", G, !1)) : (f.detachEvent("onreadystatechange", G), a.detachEvent("onload", G))
    };
    t.fn = t.prototype = {
        jquery: l, constructor: t, init: function (a, c, d) {
            var e, g;
            if (!a)return this;
            if ("string" == typeof a) {
                if (e = "<" === a.charAt(0) && ">" === a.charAt(a.length - 1) && a.length >= 3 ? [null, a, null] : x.exec(a), !e || !e[1] && c)return !c || c.jquery ? (c || d).find(a) : this.constructor(c).find(a);
                if (e[1]) {
                    if (c = c instanceof t ? c[0] : c, t.merge(this, t.parseHTML(e[1], c && c.nodeType ? c.ownerDocument || c : f, !0)), y.test(e[1]) && t.isPlainObject(c))for (e in c)t.isFunction(this[e]) ? this[e](c[e]) : this.attr(e, c[e]);
                    return this
                }
                if (g = f.getElementById(e[2]), g && g.parentNode) {
                    if (g.id !== e[2])return d.find(a);
                    this.length = 1, this[0] = g
                }
                return this.context = f, this.selector = a, this
            }
            return a.nodeType ? (this.context = this[0] = a, this.length = 1, this) : t.isFunction(a) ? d.ready(a) : (a.selector !== b && (this.selector = a.selector, this.context = a.context), t.makeArray(a, this))
        }, selector: "", length: 0, size: function () {
            return this.length
        }, toArray: function () {
            return o.call(this)
        }, get: function (a) {
            return null == a ? this.toArray() : a < 0 ? this[this.length + a] : this[a]
        }, pushStack: function (a) {
            var b = t.merge(this.constructor(), a);
            return b.prevObject = this, b.context = this.context, b
        }, each: function (a, b) {
            return t.each(this, a, b)
        }, ready: function (a) {
            return t.ready.promise().done(a), this
        }, slice: function () {
            return this.pushStack(o.apply(this, arguments))
        }, first: function () {
            return this.eq(0)
        }, last: function () {
            return this.eq(-1)
        }, eq: function (a) {
            var b = this.length, c = +a + (a < 0 ? b : 0);
            return this.pushStack(c >= 0 && c < b ? [this[c]] : [])
        }, map: function (a) {
            return this.pushStack(t.map(this, function (b, c) {
                return a.call(b, c, b)
            }))
        }, end: function () {
            return this.prevObject || this.constructor(null)
        }, push: n, sort: [].sort, splice: [].splice
    }, t.fn.init.prototype = t.fn, t.extend = t.fn.extend = function () {
        var a, c, d, e, f, g, h = arguments[0] || {}, i = 1, j = arguments.length, k = !1;
        for ("boolean" == typeof h && (k = h, h = arguments[1] || {}, i = 2), "object" == typeof h || t.isFunction(h) || (h = {}), j === i && (h = this, --i); i < j; i++)if (null != (f = arguments[i]))for (e in f)a = h[e], d = f[e], h !== d && (k && d && (t.isPlainObject(d) || (c = t.isArray(d))) ? (c ? (c = !1, g = a && t.isArray(a) ? a : []) : g = a && t.isPlainObject(a) ? a : {}, h[e] = t.extend(k, g, d)) : d !== b && (h[e] = d));
        return h
    }, t.extend({
        noConflict: function (b) {
            return a.$ === t && (a.$ = i), b && a.jQuery === t && (a.jQuery = h), t
        }, isReady: !1, readyWait: 1, holdReady: function (a) {
            a ? t.readyWait++ : t.ready(!0)
        }, ready: function (a) {
            if (a === !0 ? !--t.readyWait : !t.isReady) {
                if (!f.body)return setTimeout(t.ready);
                t.isReady = !0, a !== !0 && --t.readyWait > 0 || (c.resolveWith(f, [t]), t.fn.trigger && t(f).trigger("ready").off("ready"))
            }
        }, isFunction: function (a) {
            return "function" === t.type(a)
        }, isArray: Array.isArray || function (a) {
            return "array" === t.type(a)
        }, isWindow: function (a) {
            return null != a && a == a.window
        }, isNumeric: function (a) {
            return !isNaN(parseFloat(a)) && isFinite(a)
        }, type: function (a) {
            return null == a ? String(a) : "object" == typeof a || "function" == typeof a ? j[q.call(a)] || "object" : typeof a
        }, isPlainObject: function (a) {
            if (!a || "object" !== t.type(a) || a.nodeType || t.isWindow(a))return !1;
            try {
                if (a.constructor && !r.call(a, "constructor") && !r.call(a.constructor.prototype, "isPrototypeOf"))return !1
            } catch (a) {
                return !1
            }
            var c;
            for (c in a);
            return c === b || r.call(a, c)
        }, isEmptyObject: function (a) {
            var b;
            for (b in a)return !1;
            return !0
        }, error: function (a) {
            throw new Error(a)
        }, parseHTML: function (a, b, c) {
            if (!a || "string" != typeof a)return null;
            "boolean" == typeof b && (c = b, b = !1), b = b || f;
            var d = y.exec(a), e = !c && [];
            return d ? [b.createElement(d[1])] : (d = t.buildFragment([a], b, e), e && t(e).remove(), t.merge([], d.childNodes))
        }, parseJSON: function (b) {
            return a.JSON && a.JSON.parse ? a.JSON.parse(b) : null === b ? b : "string" == typeof b && (b = t.trim(b), b && z.test(b.replace(B, "@").replace(C, "]").replace(A, ""))) ? new Function("return " + b)() : void t.error("Invalid JSON: " + b)
        }, parseXML: function (c) {
            var d, e;
            if (!c || "string" != typeof c)return null;
            try {
                a.DOMParser ? (e = new DOMParser, d = e.parseFromString(c, "text/xml")) : (d = new ActiveXObject("Microsoft.XMLDOM"), d.async = "false", d.loadXML(c))
            } catch (a) {
                d = b
            }
            return d && d.documentElement && !d.getElementsByTagName("parsererror").length || t.error("Invalid XML: " + c), d
        }, noop: function () {
        }, globalEval: function (b) {
            b && t.trim(b) && (a.execScript || function (b) {
                a.eval.call(a, b)
            })(b)
        }, camelCase: function (a) {
            return a.replace(D, "ms-").replace(E, F)
        }, nodeName: function (a, b) {
            return a.nodeName && a.nodeName.toLowerCase() === b.toLowerCase()
        }, each: function (a, b, c) {
            var d, e = 0, f = a.length, g = I(a);
            if (c) {
                if (g)for (; e < f && (d = b.apply(a[e], c), d !== !1); e++); else for (e in a)if (d = b.apply(a[e], c), d === !1)break
            } else if (g)for (; e < f && (d = b.call(a[e], e, a[e]), d !== !1); e++); else for (e in a)if (d = b.call(a[e], e, a[e]), d === !1)break;
            return a
        }, trim: s && !s.call("\ufeffÂ ") ? function (a) {
            return null == a ? "" : s.call(a)
        } : function (a) {
            return null == a ? "" : (a + "").replace(w, "")
        }, makeArray: function (a, b) {
            var c = b || [];
            return null != a && (I(Object(a)) ? t.merge(c, "string" == typeof a ? [a] : a) : n.call(c, a)), c
        }, inArray: function (a, b, c) {
            var d;
            if (b) {
                if (p)return p.call(b, a, c);
                for (d = b.length, c = c ? c < 0 ? Math.max(0, d + c) : c : 0; c < d; c++)if (c in b && b[c] === a)return c
            }
            return -1
        }, merge: function (a, c) {
            var d = c.length, e = a.length, f = 0;
            if ("number" == typeof d)for (; f < d; f++)a[e++] = c[f]; else for (; c[f] !== b;)a[e++] = c[f++];
            return a.length = e, a
        }, grep: function (a, b, c) {
            var d, e = [], f = 0, g = a.length;
            for (c = !!c; f < g; f++)d = !!b(a[f], f), c !== d && e.push(a[f]);
            return e
        }, map: function (a, b, c) {
            var d, e = 0, f = a.length, g = I(a), h = [];
            if (g)for (; e < f; e++)d = b(a[e], e, c), null != d && (h[h.length] = d); else for (e in a)d = b(a[e], e, c), null != d && (h[h.length] = d);
            return m.apply([], h)
        }, guid: 1, proxy: function (a, c) {
            var d, e, f;
            return "string" == typeof c && (f = a[c], c = a, a = f), t.isFunction(a) ? (d = o.call(arguments, 2), e = function () {
                return a.apply(c || this, d.concat(o.call(arguments)))
            }, e.guid = a.guid = a.guid || t.guid++, e) : b
        }, access: function (a, c, d, e, f, g, h) {
            var i = 0, j = a.length, k = null == d;
            if ("object" === t.type(d)) {
                f = !0;
                for (i in d)t.access(a, c, i, d[i], !0, g, h)
            } else if (e !== b && (f = !0, t.isFunction(e) || (h = !0), k && (h ? (c.call(a, e), c = null) : (k = c, c = function (a, b, c) {
                    return k.call(t(a), c)
                })), c))for (; i < j; i++)c(a[i], d, h ? e : e.call(a[i], i, c(a[i], d)));
            return f ? a : k ? c.call(a) : j ? c(a[0], d) : g
        }, now: function () {
            return (new Date).getTime()
        }
    }), t.ready.promise = function (b) {
        if (!c)if (c = t.Deferred(), "complete" === f.readyState)setTimeout(t.ready); else if (f.addEventListener)f.addEventListener("DOMContentLoaded", G, !1), a.addEventListener("load", G, !1); else {
            f.attachEvent("onreadystatechange", G), a.attachEvent("onload", G);
            var d = !1;
            try {
                d = null == a.frameElement && f.documentElement
            } catch (a) {
            }
            d && d.doScroll && !function a() {
                if (!t.isReady) {
                    try {
                        d.doScroll("left")
                    } catch (b) {
                        return setTimeout(a, 50)
                    }
                    H(), t.ready()
                }
            }()
        }
        return c.promise(b)
    }, t.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function (a, b) {
        j["[object " + b + "]"] = b.toLowerCase()
    }), d = t(f);
    var J = {};
    t.Callbacks = function (a) {
        a = "string" == typeof a ? J[a] || K(a) : t.extend({}, a);
        var c, d, e, f, g, h, i = [], j = !a.once && [], k = function (b) {
            for (d = a.memory && b, e = !0, g = h || 0, h = 0, f = i.length, c = !0; i && g < f; g++)if (i[g].apply(b[0], b[1]) === !1 && a.stopOnFalse) {
                d = !1;
                break
            }
            c = !1, i && (j ? j.length && k(j.shift()) : d ? i = [] : l.disable())
        }, l = {
            add: function () {
                if (i) {
                    var b = i.length;
                    !function b(c) {
                        t.each(c, function (c, d) {
                            var e = t.type(d);
                            "function" === e ? a.unique && l.has(d) || i.push(d) : d && d.length && "string" !== e && b(d)
                        })
                    }(arguments), c ? f = i.length : d && (h = b, k(d))
                }
                return this
            }, remove: function () {
                return i && t.each(arguments, function (a, b) {
                    for (var d; (d = t.inArray(b, i, d)) > -1;)i.splice(d, 1), c && (d <= f && f--, d <= g && g--)
                }), this
            }, has: function (a) {
                return a ? t.inArray(a, i) > -1 : !(!i || !i.length)
            }, empty: function () {
                return i = [], this
            }, disable: function () {
                return i = j = d = b, this
            }, disabled: function () {
                return !i
            }, lock: function () {
                return j = b, d || l.disable(), this
            }, locked: function () {
                return !j
            }, fireWith: function (a, b) {
                return b = b || [], b = [a, b.slice ? b.slice() : b], !i || e && !j || (c ? j.push(b) : k(b)), this
            }, fire: function () {
                return l.fireWith(this, arguments), this
            }, fired: function () {
                return !!e
            }
        };
        return l
    }, t.extend({
        Deferred: function (a) {
            var b = [["resolve", "done", t.Callbacks("once memory"), "resolved"], ["reject", "fail", t.Callbacks("once memory"), "rejected"], ["notify", "progress", t.Callbacks("memory")]], c = "pending", d = {
                state: function () {
                    return c
                }, always: function () {
                    return e.done(arguments).fail(arguments), this
                }, then: function () {
                    var a = arguments;
                    return t.Deferred(function (c) {
                        t.each(b, function (b, f) {
                            var g = f[0], h = t.isFunction(a[b]) && a[b];
                            e[f[1]](function () {
                                var a = h && h.apply(this, arguments);
                                a && t.isFunction(a.promise) ? a.promise().done(c.resolve).fail(c.reject).progress(c.notify) : c[g + "With"](this === d ? c.promise() : this, h ? [a] : arguments)
                            })
                        }), a = null
                    }).promise()
                }, promise: function (a) {
                    return null != a ? t.extend(a, d) : d
                }
            }, e = {};
            return d.pipe = d.then, t.each(b, function (a, f) {
                var g = f[2], h = f[3];
                d[f[1]] = g.add, h && g.add(function () {
                    c = h
                }, b[1 ^ a][2].disable, b[2][2].lock), e[f[0]] = function () {
                    return e[f[0] + "With"](this === e ? d : this, arguments), this
                }, e[f[0] + "With"] = g.fireWith
            }), d.promise(e), a && a.call(e, e), e
        }, when: function (a) {
            var h, i, j, b = 0, c = o.call(arguments), d = c.length, e = 1 !== d || a && t.isFunction(a.promise) ? d : 0, f = 1 === e ? a : t.Deferred(), g = function (a, b, c) {
                return function (d) {
                    b[a] = this, c[a] = arguments.length > 1 ? o.call(arguments) : d, c === h ? f.notifyWith(b, c) : --e || f.resolveWith(b, c)
                }
            };
            if (d > 1)for (h = new Array(d), i = new Array(d), j = new Array(d); b < d; b++)c[b] && t.isFunction(c[b].promise) ? c[b].promise().done(g(b, j, c)).fail(f.reject).progress(g(b, i, h)) : --e;
            return e || f.resolveWith(j, c), f.promise()
        }
    }), t.support = function () {
        var b, c, d, g, h, i, j, k, l, m, n = f.createElement("div");
        if (n.setAttribute("className", "t"), n.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", c = n.getElementsByTagName("*"), d = n.getElementsByTagName("a")[0], !c || !d || !c.length)return {};
        h = f.createElement("select"), j = h.appendChild(f.createElement("option")), g = n.getElementsByTagName("input")[0], d.style.cssText = "top:1px;float:left;opacity:.5", b = {
            getSetAttribute: "t" !== n.className,
            leadingWhitespace: 3 === n.firstChild.nodeType,
            tbody: !n.getElementsByTagName("tbody").length,
            htmlSerialize: !!n.getElementsByTagName("link").length,
            style: /top/.test(d.getAttribute("style")),
            hrefNormalized: "/a" === d.getAttribute("href"),
            opacity: /^0.5/.test(d.style.opacity),
            cssFloat: !!d.style.cssFloat,
            checkOn: !!g.value,
            optSelected: j.selected,
            enctype: !!f.createElement("form").enctype,
            html5Clone: "<:nav></:nav>" !== f.createElement("nav").cloneNode(!0).outerHTML,
            boxModel: "CSS1Compat" === f.compatMode,
            deleteExpando: !0,
            noCloneEvent: !0,
            inlineBlockNeedsLayout: !1,
            shrinkWrapBlocks: !1,
            reliableMarginRight: !0,
            boxSizingReliable: !0,
            pixelPosition: !1
        }, g.checked = !0, b.noCloneChecked = g.cloneNode(!0).checked, h.disabled = !0, b.optDisabled = !j.disabled;
        try {
            delete n.test
        } catch (a) {
            b.deleteExpando = !1
        }
        g = f.createElement("input"), g.setAttribute("value", ""), b.input = "" === g.getAttribute("value"), g.value = "t", g.setAttribute("type", "radio"), b.radioValue = "t" === g.value, g.setAttribute("checked", "t"), g.setAttribute("name", "t"), i = f.createDocumentFragment(), i.appendChild(g), b.appendChecked = g.checked, b.checkClone = i.cloneNode(!0).cloneNode(!0).lastChild.checked, n.attachEvent && (n.attachEvent("onclick", function () {
            b.noCloneEvent = !1
        }), n.cloneNode(!0).click());
        for (m in{
            submit: !0,
            change: !0,
            focusin: !0
        })n.setAttribute(k = "on" + m, "t"), b[m + "Bubbles"] = k in a || n.attributes[k].expando === !1;
        return n.style.backgroundClip = "content-box", n.cloneNode(!0).style.backgroundClip = "", b.clearCloneStyle = "content-box" === n.style.backgroundClip, t(function () {
            var c, d, g, h = "padding:0;margin:0;border:0;display:block;box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;", i = f.getElementsByTagName("body")[0];
            i && (c = f.createElement("div"), c.style.cssText = "border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px", i.appendChild(c).appendChild(n), n.innerHTML = "<table><tr><td></td><td>t</td></tr></table>", g = n.getElementsByTagName("td"), g[0].style.cssText = "padding:0;margin:0;border:0;display:none", l = 0 === g[0].offsetHeight, g[0].style.display = "", g[1].style.display = "none", b.reliableHiddenOffsets = l && 0 === g[0].offsetHeight, n.innerHTML = "", n.style.cssText = "box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;", b.boxSizing = 4 === n.offsetWidth, b.doesNotIncludeMarginInBodyOffset = 1 !== i.offsetTop, a.getComputedStyle && (b.pixelPosition = "1%" !== (a.getComputedStyle(n, null) || {}).top, b.boxSizingReliable = "4px" === (a.getComputedStyle(n, null) || {width: "4px"}).width, d = n.appendChild(f.createElement("div")), d.style.cssText = n.style.cssText = h, d.style.marginRight = d.style.width = "0", n.style.width = "1px", b.reliableMarginRight = !parseFloat((a.getComputedStyle(d, null) || {}).marginRight)), typeof n.style.zoom !== e && (n.innerHTML = "", n.style.cssText = h + "width:1px;padding:1px;display:inline;zoom:1", b.inlineBlockNeedsLayout = 3 === n.offsetWidth, n.style.display = "block", n.innerHTML = "<div></div>", n.firstChild.style.width = "5px", b.shrinkWrapBlocks = 3 !== n.offsetWidth, b.inlineBlockNeedsLayout && (i.style.zoom = 1)), i.removeChild(c), c = n = g = d = null)
        }), c = h = i = j = d = g = null, b
    }();
    var L = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/, M = /([A-Z])/g;
    t.extend({
        cache: {},
        expando: "jQuery" + (l + Math.random()).replace(/\D/g, ""),
        noData: {embed: !0, object: "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000", applet: !0},
        hasData: function (a) {
            return a = a.nodeType ? t.cache[a[t.expando]] : a[t.expando], !!a && !Q(a)
        },
        data: function (a, b, c) {
            return N(a, b, c)
        },
        removeData: function (a, b) {
            return O(a, b)
        },
        _data: function (a, b, c) {
            return N(a, b, c, !0)
        },
        _removeData: function (a, b) {
            return O(a, b, !0)
        },
        acceptData: function (a) {
            if (a.nodeType && 1 !== a.nodeType && 9 !== a.nodeType)return !1;
            var b = a.nodeName && t.noData[a.nodeName.toLowerCase()];
            return !b || b !== !0 && a.getAttribute("classid") === b
        }
    }), t.fn.extend({
        data: function (a, c) {
            var d, e, f = this[0], g = 0, h = null;
            if (a === b) {
                if (this.length && (h = t.data(f), 1 === f.nodeType && !t._data(f, "parsedAttrs"))) {
                    for (d = f.attributes; g < d.length; g++)e = d[g].name, e.indexOf("data-") || (e = t.camelCase(e.slice(5)), P(f, e, h[e]));
                    t._data(f, "parsedAttrs", !0)
                }
                return h
            }
            return "object" == typeof a ? this.each(function () {
                t.data(this, a)
            }) : t.access(this, function (c) {
                return c === b ? f ? P(f, a, t.data(f, a)) : null : void this.each(function () {
                    t.data(this, a, c)
                })
            }, null, c, arguments.length > 1, null, !0)
        }, removeData: function (a) {
            return this.each(function () {
                t.removeData(this, a)
            })
        }
    }), t.extend({
        queue: function (a, b, c) {
            var d;
            if (a)return b = (b || "fx") + "queue", d = t._data(a, b), c && (!d || t.isArray(c) ? d = t._data(a, b, t.makeArray(c)) : d.push(c)), d || []
        }, dequeue: function (a, b) {
            b = b || "fx";
            var c = t.queue(a, b), d = c.length, e = c.shift(), f = t._queueHooks(a, b), g = function () {
                t.dequeue(a, b)
            };
            "inprogress" === e && (e = c.shift(), d--), f.cur = e, e && ("fx" === b && c.unshift("inprogress"), delete f.stop, e.call(a, g, f)), !d && f && f.empty.fire()
        }, _queueHooks: function (a, b) {
            var c = b + "queueHooks";
            return t._data(a, c) || t._data(a, c, {
                    empty: t.Callbacks("once memory").add(function () {
                        t._removeData(a, b + "queue"), t._removeData(a, c)
                    })
                })
        }
    }), t.fn.extend({
        queue: function (a, c) {
            var d = 2;
            return "string" != typeof a && (c = a, a = "fx", d--), arguments.length < d ? t.queue(this[0], a) : c === b ? this : this.each(function () {
                var b = t.queue(this, a, c);
                t._queueHooks(this, a), "fx" === a && "inprogress" !== b[0] && t.dequeue(this, a)
            })
        }, dequeue: function (a) {
            return this.each(function () {
                t.dequeue(this, a)
            })
        }, delay: function (a, b) {
            return a = t.fx ? t.fx.speeds[a] || a : a, b = b || "fx", this.queue(b, function (b, c) {
                var d = setTimeout(b, a);
                c.stop = function () {
                    clearTimeout(d)
                }
            })
        }, clearQueue: function (a) {
            return this.queue(a || "fx", [])
        }, promise: function (a, c) {
            var d, e = 1, f = t.Deferred(), g = this, h = this.length, i = function () {
                --e || f.resolveWith(g, [g])
            };
            for ("string" != typeof a && (c = a, a = b), a = a || "fx"; h--;)d = t._data(g[h], a + "queueHooks"), d && d.empty && (e++, d.empty.add(i));
            return i(), f.promise(c)
        }
    });
    var R, S, T = /[\t\r\n]/g, U = /\r/g, V = /^(?:input|select|textarea|button|object)$/i, W = /^(?:a|area)$/i, X = /^(?:checked|selected|autofocus|autoplay|async|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped)$/i, Y = /^(?:checked|selected)$/i, Z = t.support.getSetAttribute, $ = t.support.input;
    t.fn.extend({
        attr: function (a, b) {
            return t.access(this, t.attr, a, b, arguments.length > 1)
        }, removeAttr: function (a) {
            return this.each(function () {
                t.removeAttr(this, a)
            })
        }, prop: function (a, b) {
            return t.access(this, t.prop, a, b, arguments.length > 1)
        }, removeProp: function (a) {
            return a = t.propFix[a] || a, this.each(function () {
                try {
                    this[a] = b, delete this[a]
                } catch (a) {
                }
            })
        }, addClass: function (a) {
            var b, c, d, e, f, g = 0, h = this.length, i = "string" == typeof a && a;
            if (t.isFunction(a))return this.each(function (b) {
                t(this).addClass(a.call(this, b, this.className))
            });
            if (i)for (b = (a || "").match(v) || []; g < h; g++)if (c = this[g], d = 1 === c.nodeType && (c.className ? (" " + c.className + " ").replace(T, " ") : " ")) {
                for (f = 0; e = b[f++];)d.indexOf(" " + e + " ") < 0 && (d += e + " ");
                c.className = t.trim(d)
            }
            return this
        }, removeClass: function (a) {
            var b, c, d, e, f, g = 0, h = this.length, i = 0 === arguments.length || "string" == typeof a && a;
            if (t.isFunction(a))return this.each(function (b) {
                t(this).removeClass(a.call(this, b, this.className))
            });
            if (i)for (b = (a || "").match(v) || []; g < h; g++)if (c = this[g], d = 1 === c.nodeType && (c.className ? (" " + c.className + " ").replace(T, " ") : "")) {
                for (f = 0; e = b[f++];)for (; d.indexOf(" " + e + " ") >= 0;)d = d.replace(" " + e + " ", " ");
                c.className = a ? t.trim(d) : ""
            }
            return this
        }, toggleClass: function (a, b) {
            var c = typeof a, d = "boolean" == typeof b;
            return t.isFunction(a) ? this.each(function (c) {
                t(this).toggleClass(a.call(this, c, this.className, b), b)
            }) : this.each(function () {
                if ("string" === c)for (var f, g = 0, h = t(this), i = b, j = a.match(v) || []; f = j[g++];)i = d ? i : !h.hasClass(f), h[i ? "addClass" : "removeClass"](f); else c !== e && "boolean" !== c || (this.className && t._data(this, "__className__", this.className), this.className = this.className || a === !1 ? "" : t._data(this, "__className__") || "")
            })
        }, hasClass: function (a) {
            for (var b = " " + a + " ", c = 0, d = this.length; c < d; c++)if (1 === this[c].nodeType && (" " + this[c].className + " ").replace(T, " ").indexOf(b) >= 0)return !0;
            return !1
        }, val: function (a) {
            var c, d, e, f = this[0];
            {
                if (arguments.length)return e = t.isFunction(a), this.each(function (c) {
                    var f, g = t(this);
                    1 === this.nodeType && (f = e ? a.call(this, c, g.val()) : a, null == f ? f = "" : "number" == typeof f ? f += "" : t.isArray(f) && (f = t.map(f, function (a) {
                        return null == a ? "" : a + ""
                    })), d = t.valHooks[this.type] || t.valHooks[this.nodeName.toLowerCase()], d && "set" in d && d.set(this, f, "value") !== b || (this.value = f))
                });
                if (f)return d = t.valHooks[f.type] || t.valHooks[f.nodeName.toLowerCase()], d && "get" in d && (c = d.get(f, "value")) !== b ? c : (c = f.value, "string" == typeof c ? c.replace(U, "") : null == c ? "" : c)
            }
        }
    }), t.extend({
        valHooks: {
            option: {
                get: function (a) {
                    var b = a.attributes.value;
                    return !b || b.specified ? a.value : a.text
                }
            }, select: {
                get: function (a) {
                    for (var b, c, d = a.options, e = a.selectedIndex, f = "select-one" === a.type || e < 0, g = f ? null : [], h = f ? e + 1 : d.length, i = e < 0 ? h : f ? e : 0; i < h; i++)if (c = d[i], (c.selected || i === e) && (t.support.optDisabled ? !c.disabled : null === c.getAttribute("disabled")) && (!c.parentNode.disabled || !t.nodeName(c.parentNode, "optgroup"))) {
                        if (b = t(c).val(), f)return b;
                        g.push(b)
                    }
                    return g
                }, set: function (a, b) {
                    var c = t.makeArray(b);
                    return t(a).find("option").each(function () {
                        this.selected = t.inArray(t(this).val(), c) >= 0
                    }), c.length || (a.selectedIndex = -1), c
                }
            }
        },
        attr: function (a, c, d) {
            var f, g, h, i = a.nodeType;
            if (a && 3 !== i && 8 !== i && 2 !== i)return typeof a.getAttribute === e ? t.prop(a, c, d) : (g = 1 !== i || !t.isXMLDoc(a), g && (c = c.toLowerCase(), f = t.attrHooks[c] || (X.test(c) ? S : R)), d === b ? f && g && "get" in f && null !== (h = f.get(a, c)) ? h : (typeof a.getAttribute !== e && (h = a.getAttribute(c)), null == h ? b : h) : null !== d ? f && g && "set" in f && (h = f.set(a, d, c)) !== b ? h : (a.setAttribute(c, d + ""), d) : void t.removeAttr(a, c))
        },
        removeAttr: function (a, b) {
            var c, d, e = 0, f = b && b.match(v);
            if (f && 1 === a.nodeType)for (; c = f[e++];)d = t.propFix[c] || c, X.test(c) ? !Z && Y.test(c) ? a[t.camelCase("default-" + c)] = a[d] = !1 : a[d] = !1 : t.attr(a, c, ""), a.removeAttribute(Z ? c : d)
        },
        attrHooks: {
            type: {
                set: function (a, b) {
                    if (!t.support.radioValue && "radio" === b && t.nodeName(a, "input")) {
                        var c = a.value;
                        return a.setAttribute("type", b), c && (a.value = c), b
                    }
                }
            }
        },
        propFix: {
            tabindex: "tabIndex",
            readonly: "readOnly",
            for: "htmlFor",
            class: "className",
            maxlength: "maxLength",
            cellspacing: "cellSpacing",
            cellpadding: "cellPadding",
            rowspan: "rowSpan",
            colspan: "colSpan",
            usemap: "useMap",
            frameborder: "frameBorder",
            contenteditable: "contentEditable"
        },
        prop: function (a, c, d) {
            var e, f, g, h = a.nodeType;
            if (a && 3 !== h && 8 !== h && 2 !== h)return g = 1 !== h || !t.isXMLDoc(a), g && (c = t.propFix[c] || c, f = t.propHooks[c]), d !== b ? f && "set" in f && (e = f.set(a, d, c)) !== b ? e : a[c] = d : f && "get" in f && null !== (e = f.get(a, c)) ? e : a[c]
        },
        propHooks: {
            tabIndex: {
                get: function (a) {
                    var c = a.getAttributeNode("tabindex");
                    return c && c.specified ? parseInt(c.value, 10) : V.test(a.nodeName) || W.test(a.nodeName) && a.href ? 0 : b
                }
            }
        }
    }), S = {
        get: function (a, c) {
            var d = t.prop(a, c), e = "boolean" == typeof d && a.getAttribute(c), f = "boolean" == typeof d ? $ && Z ? null != e : Y.test(c) ? a[t.camelCase("default-" + c)] : !!e : a.getAttributeNode(c);
            return f && f.value !== !1 ? c.toLowerCase() : b
        }, set: function (a, b, c) {
            return b === !1 ? t.removeAttr(a, c) : $ && Z || !Y.test(c) ? a.setAttribute(!Z && t.propFix[c] || c, c) : a[t.camelCase("default-" + c)] = a[c] = !0, c
        }
    }, $ && Z || (t.attrHooks.value = {
        get: function (a, c) {
            var d = a.getAttributeNode(c);
            return t.nodeName(a, "input") ? a.defaultValue : d && d.specified ? d.value : b
        }, set: function (a, b, c) {

            return t.nodeName(a, "input") ? void(a.defaultValue = b) : R && R.set(a, b, c)
        }
    }), Z || (R = t.valHooks.button = {
        get: function (a, c) {
            var d = a.getAttributeNode(c);
            return d && ("id" === c || "name" === c || "coords" === c ? "" !== d.value : d.specified) ? d.value : b
        }, set: function (a, c, d) {
            var e = a.getAttributeNode(d);
            return e || a.setAttributeNode(e = a.ownerDocument.createAttribute(d)), e.value = c += "", "value" === d || c === a.getAttribute(d) ? c : b
        }
    }, t.attrHooks.contenteditable = {
        get: R.get, set: function (a, b, c) {
            R.set(a, "" !== b && b, c)
        }
    }, t.each(["width", "height"], function (a, b) {
        t.attrHooks[b] = t.extend(t.attrHooks[b], {
            set: function (a, c) {
                if ("" === c)return a.setAttribute(b, "auto"), c
            }
        })
    })), t.support.hrefNormalized || (t.each(["href", "src", "width", "height"], function (a, c) {
        t.attrHooks[c] = t.extend(t.attrHooks[c], {
            get: function (a) {
                var d = a.getAttribute(c, 2);
                return null == d ? b : d
            }
        })
    }), t.each(["href", "src"], function (a, b) {
        t.propHooks[b] = {
            get: function (a) {
                return a.getAttribute(b, 4)
            }
        }
    })), t.support.style || (t.attrHooks.style = {
        get: function (a) {
            return a.style.cssText || b
        }, set: function (a, b) {
            return a.style.cssText = b + ""
        }
    }), t.support.optSelected || (t.propHooks.selected = t.extend(t.propHooks.selected, {
        get: function (a) {
            var b = a.parentNode;
            return b && (b.selectedIndex, b.parentNode && b.parentNode.selectedIndex), null
        }
    })), t.support.enctype || (t.propFix.enctype = "encoding"), t.support.checkOn || t.each(["radio", "checkbox"], function () {
        t.valHooks[this] = {
            get: function (a) {
                return null === a.getAttribute("value") ? "on" : a.value
            }
        }
    }), t.each(["radio", "checkbox"], function () {
        t.valHooks[this] = t.extend(t.valHooks[this], {
            set: function (a, b) {
                if (t.isArray(b))return a.checked = t.inArray(t(a).val(), b) >= 0
            }
        })
    });
    var _ = /^(?:input|select|textarea)$/i, aa = /^key/, ba = /^(?:mouse|contextmenu)|click/, ca = /^(?:focusinfocus|focusoutblur)$/, da = /^([^.]*)(?:\.(.+)|)$/;
    t.event = {
        global: {},
        add: function (a, c, d, f, g) {
            var h, i, j, k, l, m, n, o, p, q, r, s = t._data(a);
            if (s) {
                for (d.handler && (k = d, d = k.handler, g = k.selector), d.guid || (d.guid = t.guid++), (i = s.events) || (i = s.events = {}), (m = s.handle) || (m = s.handle = function (a) {
                    return typeof t === e || a && t.event.triggered === a.type ? b : t.event.dispatch.apply(m.elem, arguments)
                }, m.elem = a), c = (c || "").match(v) || [""], j = c.length; j--;)h = da.exec(c[j]) || [], p = r = h[1], q = (h[2] || "").split(".").sort(), l = t.event.special[p] || {}, p = (g ? l.delegateType : l.bindType) || p, l = t.event.special[p] || {}, n = t.extend({
                    type: p,
                    origType: r,
                    data: f,
                    handler: d,
                    guid: d.guid,
                    selector: g,
                    needsContext: g && t.expr.match.needsContext.test(g),
                    namespace: q.join(".")
                }, k), (o = i[p]) || (o = i[p] = [], o.delegateCount = 0, l.setup && l.setup.call(a, f, q, m) !== !1 || (a.addEventListener ? a.addEventListener(p, m, !1) : a.attachEvent && a.attachEvent("on" + p, m))), l.add && (l.add.call(a, n), n.handler.guid || (n.handler.guid = d.guid)), g ? o.splice(o.delegateCount++, 0, n) : o.push(n), t.event.global[p] = !0;
                a = null
            }
        },
        remove: function (a, b, c, d, e) {
            var f, g, h, i, j, k, l, m, n, o, p, q = t.hasData(a) && t._data(a);
            if (q && (k = q.events)) {
                for (b = (b || "").match(v) || [""], j = b.length; j--;)if (h = da.exec(b[j]) || [], n = p = h[1], o = (h[2] || "").split(".").sort(), n) {
                    for (l = t.event.special[n] || {}, n = (d ? l.delegateType : l.bindType) || n, m = k[n] || [], h = h[2] && new RegExp("(^|\\.)" + o.join("\\.(?:.*\\.|)") + "(\\.|$)"), i = f = m.length; f--;)g = m[f], !e && p !== g.origType || c && c.guid !== g.guid || h && !h.test(g.namespace) || d && d !== g.selector && ("**" !== d || !g.selector) || (m.splice(f, 1), g.selector && m.delegateCount--, l.remove && l.remove.call(a, g));
                    i && !m.length && (l.teardown && l.teardown.call(a, o, q.handle) !== !1 || t.removeEvent(a, n, q.handle), delete k[n])
                } else for (n in k)t.event.remove(a, n + b[j], c, d, !0);
                t.isEmptyObject(k) && (delete q.handle, t._removeData(a, "events"))
            }
        },
        trigger: function (c, d, e, g) {
            var h, i, j, k, l, m, n, o = [e || f], p = r.call(c, "type") ? c.type : c, q = r.call(c, "namespace") ? c.namespace.split(".") : [];
            if (j = m = e = e || f, 3 !== e.nodeType && 8 !== e.nodeType && !ca.test(p + t.event.triggered) && (p.indexOf(".") >= 0 && (q = p.split("."), p = q.shift(), q.sort()), i = p.indexOf(":") < 0 && "on" + p, c = c[t.expando] ? c : new t.Event(p, "object" == typeof c && c), c.isTrigger = !0, c.namespace = q.join("."), c.namespace_re = c.namespace ? new RegExp("(^|\\.)" + q.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, c.result = b, c.target || (c.target = e), d = null == d ? [c] : t.makeArray(d, [c]), l = t.event.special[p] || {}, g || !l.trigger || l.trigger.apply(e, d) !== !1)) {
                if (!g && !l.noBubble && !t.isWindow(e)) {
                    for (k = l.delegateType || p, ca.test(k + p) || (j = j.parentNode); j; j = j.parentNode)o.push(j), m = j;
                    m === (e.ownerDocument || f) && o.push(m.defaultView || m.parentWindow || a)
                }
                for (n = 0; (j = o[n++]) && !c.isPropagationStopped();)c.type = n > 1 ? k : l.bindType || p, h = (t._data(j, "events") || {})[c.type] && t._data(j, "handle"), h && h.apply(j, d), h = i && j[i], h && t.acceptData(j) && h.apply && h.apply(j, d) === !1 && c.preventDefault();
                if (c.type = p, !g && !c.isDefaultPrevented() && (!l._default || l._default.apply(e.ownerDocument, d) === !1) && ("click" !== p || !t.nodeName(e, "a")) && t.acceptData(e) && i && e[p] && !t.isWindow(e)) {
                    m = e[i], m && (e[i] = null), t.event.triggered = p;
                    try {
                        e[p]()
                    } catch (a) {
                    }
                    t.event.triggered = b, m && (e[i] = m)
                }
                return c.result
            }
        },
        dispatch: function (a) {
            a = t.event.fix(a);
            var c, d, e, f, g, h = [], i = o.call(arguments), j = (t._data(this, "events") || {})[a.type] || [], k = t.event.special[a.type] || {};
            if (i[0] = a, a.delegateTarget = this, !k.preDispatch || k.preDispatch.call(this, a) !== !1) {
                for (h = t.event.handlers.call(this, a, j), c = 0; (f = h[c++]) && !a.isPropagationStopped();)for (a.currentTarget = f.elem, g = 0; (e = f.handlers[g++]) && !a.isImmediatePropagationStopped();)a.namespace_re && !a.namespace_re.test(e.namespace) || (a.handleObj = e, a.data = e.data, d = ((t.event.special[e.origType] || {}).handle || e.handler).apply(f.elem, i), d !== b && (a.result = d) === !1 && (a.preventDefault(), a.stopPropagation()));
                return k.postDispatch && k.postDispatch.call(this, a), a.result
            }
        },
        handlers: function (a, c) {
            var d, e, f, g, h = [], i = c.delegateCount, j = a.target;
            if (i && j.nodeType && (!a.button || "click" !== a.type))for (; j != this; j = j.parentNode || this)if (1 === j.nodeType && (j.disabled !== !0 || "click" !== a.type)) {
                for (f = [], g = 0; g < i; g++)e = c[g], d = e.selector + " ", f[d] === b && (f[d] = e.needsContext ? t(d, this).index(j) >= 0 : t.find(d, this, null, [j]).length), f[d] && f.push(e);
                f.length && h.push({elem: j, handlers: f})
            }
            return i < c.length && h.push({elem: this, handlers: c.slice(i)}), h
        },
        fix: function (a) {
            if (a[t.expando])return a;
            var b, c, d, e = a.type, g = a, h = this.fixHooks[e];
            for (h || (this.fixHooks[e] = h = ba.test(e) ? this.mouseHooks : aa.test(e) ? this.keyHooks : {}), d = h.props ? this.props.concat(h.props) : this.props, a = new t.Event(g), b = d.length; b--;)c = d[b], a[c] = g[c];
            return a.target || (a.target = g.srcElement || f), 3 === a.target.nodeType && (a.target = a.target.parentNode), a.metaKey = !!a.metaKey, h.filter ? h.filter(a, g) : a
        },
        props: "altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
        fixHooks: {},
        keyHooks: {
            props: "char charCode key keyCode".split(" "), filter: function (a, b) {
                return null == a.which && (a.which = null != b.charCode ? b.charCode : b.keyCode), a
            }
        },
        mouseHooks: {
            props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
            filter: function (a, c) {
                var d, e, g, h = c.button, i = c.fromElement;
                return null == a.pageX && null != c.clientX && (e = a.target.ownerDocument || f, g = e.documentElement, d = e.body, a.pageX = c.clientX + (g && g.scrollLeft || d && d.scrollLeft || 0) - (g && g.clientLeft || d && d.clientLeft || 0), a.pageY = c.clientY + (g && g.scrollTop || d && d.scrollTop || 0) - (g && g.clientTop || d && d.clientTop || 0)), !a.relatedTarget && i && (a.relatedTarget = i === a.target ? c.toElement : i), a.which || h === b || (a.which = 1 & h ? 1 : 2 & h ? 3 : 4 & h ? 2 : 0), a
            }
        },
        special: {
            load: {noBubble: !0}, click: {
                trigger: function () {
                    if (t.nodeName(this, "input") && "checkbox" === this.type && this.click)return this.click(), !1
                }
            }, focus: {
                trigger: function () {
                    if (this !== f.activeElement && this.focus)try {
                        return this.focus(), !1
                    } catch (a) {
                    }
                }, delegateType: "focusin"
            }, blur: {
                trigger: function () {
                    if (this === f.activeElement && this.blur)return this.blur(), !1
                }, delegateType: "focusout"
            }, beforeunload: {
                postDispatch: function (a) {
                    a.result !== b && (a.originalEvent.returnValue = a.result)
                }
            }
        },
        simulate: function (a, b, c, d) {
            var e = t.extend(new t.Event, c, {type: a, isSimulated: !0, originalEvent: {}});
            d ? t.event.trigger(e, null, b) : t.event.dispatch.call(b, e), e.isDefaultPrevented() && c.preventDefault()
        }
    }, t.removeEvent = f.removeEventListener ? function (a, b, c) {
        a.removeEventListener && a.removeEventListener(b, c, !1)
    } : function (a, b, c) {
        var d = "on" + b;
        a.detachEvent && (typeof a[d] === e && (a[d] = null), a.detachEvent(d, c))
    }, t.Event = function (a, b) {
        return this instanceof t.Event ? (a && a.type ? (this.originalEvent = a, this.type = a.type, this.isDefaultPrevented = a.defaultPrevented || a.returnValue === !1 || a.getPreventDefault && a.getPreventDefault() ? ea : fa) : this.type = a, b && t.extend(this, b), this.timeStamp = a && a.timeStamp || t.now(), void(this[t.expando] = !0)) : new t.Event(a, b)
    }, t.Event.prototype = {
        isDefaultPrevented: fa,
        isPropagationStopped: fa,
        isImmediatePropagationStopped: fa,
        preventDefault: function () {
            var a = this.originalEvent;
            this.isDefaultPrevented = ea, a && (a.preventDefault ? a.preventDefault() : a.returnValue = !1)
        },
        stopPropagation: function () {
            var a = this.originalEvent;
            this.isPropagationStopped = ea, a && (a.stopPropagation && a.stopPropagation(), a.cancelBubble = !0)
        },
        stopImmediatePropagation: function () {
            this.isImmediatePropagationStopped = ea, this.stopPropagation()
        }
    }, t.each({mouseenter: "mouseover", mouseleave: "mouseout"}, function (a, b) {
        t.event.special[a] = {
            delegateType: b, bindType: b, handle: function (a) {
                var c, d = this, e = a.relatedTarget, f = a.handleObj;
                return e && (e === d || t.contains(d, e)) || (a.type = f.origType, c = f.handler.apply(this, arguments), a.type = b), c
            }
        }
    }), t.support.submitBubbles || (t.event.special.submit = {
        setup: function () {
            return !t.nodeName(this, "form") && void t.event.add(this, "click._submit keypress._submit", function (a) {
                    var c = a.target, d = t.nodeName(c, "input") || t.nodeName(c, "button") ? c.form : b;
                    d && !t._data(d, "submitBubbles") && (t.event.add(d, "submit._submit", function (a) {
                        a._submit_bubble = !0
                    }), t._data(d, "submitBubbles", !0))
                })
        }, postDispatch: function (a) {
            a._submit_bubble && (delete a._submit_bubble, this.parentNode && !a.isTrigger && t.event.simulate("submit", this.parentNode, a, !0))
        }, teardown: function () {
            return !t.nodeName(this, "form") && void t.event.remove(this, "._submit")
        }
    }), t.support.changeBubbles || (t.event.special.change = {
        setup: function () {
            return _.test(this.nodeName) ? ("checkbox" !== this.type && "radio" !== this.type || (t.event.add(this, "propertychange._change", function (a) {
                "checked" === a.originalEvent.propertyName && (this._just_changed = !0)
            }), t.event.add(this, "click._change", function (a) {
                this._just_changed && !a.isTrigger && (this._just_changed = !1), t.event.simulate("change", this, a, !0)
            })), !1) : void t.event.add(this, "beforeactivate._change", function (a) {
                var b = a.target;
                _.test(b.nodeName) && !t._data(b, "changeBubbles") && (t.event.add(b, "change._change", function (a) {
                    !this.parentNode || a.isSimulated || a.isTrigger || t.event.simulate("change", this.parentNode, a, !0)
                }), t._data(b, "changeBubbles", !0))
            })
        }, handle: function (a) {
            var b = a.target;
            if (this !== b || a.isSimulated || a.isTrigger || "radio" !== b.type && "checkbox" !== b.type)return a.handleObj.handler.apply(this, arguments)
        }, teardown: function () {
            return t.event.remove(this, "._change"), !_.test(this.nodeName)
        }
    }), t.support.focusinBubbles || t.each({focus: "focusin", blur: "focusout"}, function (a, b) {
        var c = 0, d = function (a) {
            t.event.simulate(b, a.target, t.event.fix(a), !0)
        };
        t.event.special[b] = {
            setup: function () {
                0 === c++ && f.addEventListener(a, d, !0)
            }, teardown: function () {
                0 === --c && f.removeEventListener(a, d, !0)
            }
        }
    }), t.fn.extend({
        on: function (a, c, d, e, f) {
            var g, h;
            if ("object" == typeof a) {
                "string" != typeof c && (d = d || c, c = b);
                for (g in a)this.on(g, c, d, a[g], f);
                return this
            }
            if (null == d && null == e ? (e = c, d = c = b) : null == e && ("string" == typeof c ? (e = d, d = b) : (e = d, d = c, c = b)), e === !1)e = fa; else if (!e)return this;
            return 1 === f && (h = e, e = function (a) {
                return t().off(a), h.apply(this, arguments)
            }, e.guid = h.guid || (h.guid = t.guid++)), this.each(function () {
                t.event.add(this, a, e, d, c)
            })
        }, one: function (a, b, c, d) {
            return this.on(a, b, c, d, 1)
        }, off: function (a, c, d) {
            var e, f;
            if (a && a.preventDefault && a.handleObj)return e = a.handleObj, t(a.delegateTarget).off(e.namespace ? e.origType + "." + e.namespace : e.origType, e.selector, e.handler), this;
            if ("object" == typeof a) {
                for (f in a)this.off(f, c, a[f]);
                return this
            }
            return c !== !1 && "function" != typeof c || (d = c, c = b), d === !1 && (d = fa), this.each(function () {
                t.event.remove(this, a, d, c)
            })
        }, bind: function (a, b, c) {
            return this.on(a, null, b, c)
        }, unbind: function (a, b) {
            return this.off(a, null, b)
        }, delegate: function (a, b, c, d) {
            return this.on(b, a, c, d)
        }, undelegate: function (a, b, c) {
            return 1 === arguments.length ? this.off(a, "**") : this.off(b, a || "**", c)
        }, trigger: function (a, b) {
            return this.each(function () {
                t.event.trigger(a, b, this)
            })
        }, triggerHandler: function (a, b) {
            var c = this[0];
            if (c)return t.event.trigger(a, b, c, !0)
        }
    }), function (a, b) {
        function ca(a) {
            return W.test(a + "")
        }

        function da() {
            var a, b = [];
            return a = function (c, d) {
                return b.push(c += " ") > e.cacheLength && delete a[b.shift()], a[c] = d
            }
        }

        function ea(a) {
            return a[u] = !0, a
        }

        function fa(a) {
            var b = l.createElement("div");
            try {
                return a(b)
            } catch (a) {
                return !1
            } finally {
                b = null
            }
        }

        function ga(a, b, c, d) {
            var e, f, g, h, i, j, m, p, q, s;
            if ((b ? b.ownerDocument || b : v) !== l && k(b), b = b || l, c = c || [], !a || "string" != typeof a)return c;
            if (1 !== (h = b.nodeType) && 9 !== h)return [];
            if (!n && !d) {
                if (e = X.exec(a))if (g = e[1]) {
                    if (9 === h) {
                        if (f = b.getElementById(g), !f || !f.parentNode)return c;
                        if (f.id === g)return c.push(f), c
                    } else if (b.ownerDocument && (f = b.ownerDocument.getElementById(g)) && r(b, f) && f.id === g)return c.push(f), c
                } else {
                    if (e[2])return G.apply(c, H.call(b.getElementsByTagName(a), 0)), c;
                    if ((g = e[3]) && w.getByClassName && b.getElementsByClassName)return G.apply(c, H.call(b.getElementsByClassName(g), 0)), c
                }
                if (w.qsa && !o.test(a)) {
                    if (m = !0, p = u, q = b, s = 9 === h && a, 1 === h && "object" !== b.nodeName.toLowerCase()) {
                        for (j = la(a), (m = b.getAttribute("id")) ? p = m.replace($, "\\$&") : b.setAttribute("id", p), p = "[id='" + p + "'] ", i = j.length; i--;)j[i] = p + ma(j[i]);
                        q = V.test(a) && b.parentNode || b, s = j.join(",")
                    }
                    if (s)try {
                        return G.apply(c, H.call(q.querySelectorAll(s), 0)), c
                    } catch (a) {
                    } finally {
                        m || b.removeAttribute("id")
                    }
                }
            }
            return ua(a.replace(P, "$1"), b, c, d)
        }

        function ha(a, b) {
            var c = b && a, d = c && (~b.sourceIndex || D) - (~a.sourceIndex || D);
            if (d)return d;
            if (c)for (; c = c.nextSibling;)if (c === b)return -1;
            return a ? 1 : -1
        }

        function ia(a) {
            return function (b) {
                var c = b.nodeName.toLowerCase();
                return "input" === c && b.type === a
            }
        }

        function ja(a) {
            return function (b) {
                var c = b.nodeName.toLowerCase();
                return ("input" === c || "button" === c) && b.type === a
            }
        }

        function ka(a) {
            return ea(function (b) {
                return b = +b, ea(function (c, d) {
                    for (var e, f = a([], c.length, b), g = f.length; g--;)c[e = f[g]] && (c[e] = !(d[e] = c[e]))
                })
            })
        }

        function la(a, b) {
            var c, d, f, g, h, i, j, k = A[a + " "];
            if (k)return b ? 0 : k.slice(0);
            for (h = a, i = [], j = e.preFilter; h;) {
                c && !(d = Q.exec(h)) || (d && (h = h.slice(d[0].length) || h), i.push(f = [])), c = !1, (d = R.exec(h)) && (c = d.shift(), f.push({
                    value: c,
                    type: d[0].replace(P, " ")
                }), h = h.slice(c.length));
                for (g in e.filter)!(d = U[g].exec(h)) || j[g] && !(d = j[g](d)) || (c = d.shift(), f.push({
                    value: c,
                    type: g,
                    matches: d
                }), h = h.slice(c.length));
                if (!c)break
            }
            return b ? h.length : h ? ga.error(a) : A(a, i).slice(0)
        }

        function ma(a) {
            for (var b = 0, c = a.length, d = ""; b < c; b++)d += a[b].value;
            return d
        }

        function na(a, b, c) {
            var e = b.dir, f = c && "parentNode" === e, g = y++;
            return b.first ? function (b, c, d) {
                for (; b = b[e];)if (1 === b.nodeType || f)return a(b, c, d)
            } : function (b, c, h) {
                var i, j, k, l = x + " " + g;
                if (h) {
                    for (; b = b[e];)if ((1 === b.nodeType || f) && a(b, c, h))return !0
                } else for (; b = b[e];)if (1 === b.nodeType || f)if (k = b[u] || (b[u] = {}), (j = k[e]) && j[0] === l) {
                    if ((i = j[1]) === !0 || i === d)return i === !0
                } else if (j = k[e] = [l], j[1] = a(b, c, h) || d, j[1] === !0)return !0
            }
        }

        function oa(a) {
            return a.length > 1 ? function (b, c, d) {
                for (var e = a.length; e--;)if (!a[e](b, c, d))return !1;
                return !0
            } : a[0]
        }

        function pa(a, b, c, d, e) {
            for (var f, g = [], h = 0, i = a.length, j = null != b; h < i; h++)(f = a[h]) && (c && !c(f, d, e) || (g.push(f), j && b.push(h)));
            return g
        }

        function qa(a, b, c, d, e, f) {
            return d && !d[u] && (d = qa(d)), e && !e[u] && (e = qa(e, f)), ea(function (f, g, h, i) {
                var j, k, l, m = [], n = [], o = g.length, p = f || ta(b || "*", h.nodeType ? [h] : h, []), q = !a || !f && b ? p : pa(p, m, a, h, i), r = c ? e || (f ? a : o || d) ? [] : g : q;
                if (c && c(q, r, h, i), d)for (j = pa(r, n), d(j, [], h, i), k = j.length; k--;)(l = j[k]) && (r[n[k]] = !(q[n[k]] = l));
                if (f) {
                    if (e || a) {
                        if (e) {
                            for (j = [], k = r.length; k--;)(l = r[k]) && j.push(q[k] = l);
                            e(null, r = [], j, i)
                        }
                        for (k = r.length; k--;)(l = r[k]) && (j = e ? I.call(f, l) : m[k]) > -1 && (f[j] = !(g[j] = l))
                    }
                } else r = pa(r === g ? r.splice(o, r.length) : r), e ? e(null, g, r, i) : G.apply(g, r)
            })
        }

        function ra(a) {
            for (var b, c, d, f = a.length, g = e.relative[a[0].type], h = g || e.relative[" "], i = g ? 1 : 0, k = na(function (a) {
                return a === b
            }, h, !0), l = na(function (a) {
                return I.call(b, a) > -1
            }, h, !0), m = [function (a, c, d) {
                return !g && (d || c !== j) || ((b = c).nodeType ? k(a, c, d) : l(a, c, d))
            }]; i < f; i++)if (c = e.relative[a[i].type])m = [na(oa(m), c)]; else {
                if (c = e.filter[a[i].type].apply(null, a[i].matches), c[u]) {
                    for (d = ++i; d < f && !e.relative[a[d].type]; d++);
                    return qa(i > 1 && oa(m), i > 1 && ma(a.slice(0, i - 1)).replace(P, "$1"), c, i < d && ra(a.slice(i, d)), d < f && ra(a = a.slice(d)), d < f && ma(a))
                }
                m.push(c)
            }
            return oa(m)
        }

        function sa(a, b) {
            var c = 0, f = b.length > 0, g = a.length > 0, h = function (h, i, k, m, n) {
                var o, p, q, r = [], s = 0, t = "0", u = h && [], v = null != n, w = j, y = h || g && e.find.TAG("*", n && i.parentNode || i), z = x += null == w ? 1 : Math.random() || .1;
                for (v && (j = i !== l && i, d = c); null != (o = y[t]); t++) {
                    if (g && o) {
                        for (p = 0; q = a[p++];)if (q(o, i, k)) {
                            m.push(o);
                            break
                        }
                        v && (x = z, d = ++c)
                    }
                    f && ((o = !q && o) && s--, h && u.push(o))
                }
                if (s += t, f && t !== s) {
                    for (p = 0; q = b[p++];)q(u, r, i, k);
                    if (h) {
                        if (s > 0)for (; t--;)u[t] || r[t] || (r[t] = F.call(m));
                        r = pa(r)
                    }
                    G.apply(m, r), v && !h && r.length > 0 && s + b.length > 1 && ga.uniqueSort(m)
                }
                return v && (x = z, j = w), u
            };
            return f ? ea(h) : h
        }

        function ta(a, b, c) {
            for (var d = 0, e = b.length; d < e; d++)ga(a, b[d], c);
            return c
        }

        function ua(a, b, c, d) {
            var f, g, i, j, k, l = la(a);
            if (!d && 1 === l.length) {
                if (g = l[0] = l[0].slice(0), g.length > 2 && "ID" === (i = g[0]).type && 9 === b.nodeType && !n && e.relative[g[1].type]) {
                    if (b = e.find.ID(i.matches[0].replace(aa, ba), b)[0], !b)return c;
                    a = a.slice(g.shift().value.length)
                }
                for (f = U.needsContext.test(a) ? 0 : g.length; f-- && (i = g[f], !e.relative[j = i.type]);)if ((k = e.find[j]) && (d = k(i.matches[0].replace(aa, ba), V.test(g[0].type) && b.parentNode || b))) {
                    if (g.splice(f, 1), a = d.length && ma(g), !a)return G.apply(c, H.call(d, 0)), c;
                    break
                }
            }
            return h(a, l)(d, b, n, c, V.test(a)), c
        }

        function va() {
        }

        var c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, u = "sizzle" + -new Date, v = a.document, w = {}, x = 0, y = 0, z = da(), A = da(), B = da(), C = typeof b, D = 1 << 31, E = [], F = E.pop, G = E.push, H = E.slice, I = E.indexOf || function (a) {
                for (var b = 0, c = this.length; b < c; b++)if (this[b] === a)return b;
                return -1
            }, J = "[\\x20\\t\\r\\n\\f]", K = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+", L = K.replace("w", "w#"), M = "([*^$|!~]?=)", N = "\\[" + J + "*(" + K + ")" + J + "*(?:" + M + J + "*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|(" + L + ")|)|)" + J + "*\\]", O = ":(" + K + ")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|" + N.replace(3, 8) + ")*)|.*)\\)|)", P = new RegExp("^" + J + "+|((?:^|[^\\\\])(?:\\\\.)*)" + J + "+$", "g"), Q = new RegExp("^" + J + "*," + J + "*"), R = new RegExp("^" + J + "*([\\x20\\t\\r\\n\\f>+~])" + J + "*"), S = new RegExp(O), T = new RegExp("^" + L + "$"), U = {
            ID: new RegExp("^#(" + K + ")"),
            CLASS: new RegExp("^\\.(" + K + ")"),
            NAME: new RegExp("^\\[name=['\"]?(" + K + ")['\"]?\\]"),
            TAG: new RegExp("^(" + K.replace("w", "w*") + ")"),
            ATTR: new RegExp("^" + N),
            PSEUDO: new RegExp("^" + O),
            CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + J + "*(even|odd|(([+-]|)(\\d*)n|)" + J + "*(?:([+-]|)" + J + "*(\\d+)|))" + J + "*\\)|)", "i"),
            needsContext: new RegExp("^" + J + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + J + "*((?:-\\d)?\\d*)" + J + "*\\)|)(?=[^-]|$)", "i")
        }, V = /[\x20\t\r\n\f]*[+~]/, W = /^[^{]+\{\s*\[native code/, X = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/, Y = /^(?:input|select|textarea|button)$/i, Z = /^h\d$/i, $ = /'|\\/g, _ = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g, aa = /\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g, ba = function (a, b) {
            var c = "0x" + b - 65536;
            return c !== c ? b : c < 0 ? String.fromCharCode(c + 65536) : String.fromCharCode(c >> 10 | 55296, 1023 & c | 56320)
        };
        try {
            H.call(v.documentElement.childNodes, 0)[0].nodeType
        } catch (a) {
            H = function (a) {
                for (var b, c = []; b = this[a++];)c.push(b);
                return c
            }
        }
        g = ga.isXML = function (a) {
            var b = a && (a.ownerDocument || a).documentElement;
            return !!b && "HTML" !== b.nodeName
        }, k = ga.setDocument = function (a) {
            var c = a ? a.ownerDocument || a : v;
            return c !== l && 9 === c.nodeType && c.documentElement ? (l = c, m = c.documentElement, n = g(c), w.tagNameNoComments = fa(function (a) {
                return a.appendChild(c.createComment("")), !a.getElementsByTagName("*").length
            }), w.attributes = fa(function (a) {
                a.innerHTML = "<select></select>";
                var b = typeof a.lastChild.getAttribute("multiple");
                return "boolean" !== b && "string" !== b
            }), w.getByClassName = fa(function (a) {
                return a.innerHTML = "<div class='hidden e'></div><div class='hidden'></div>", !(!a.getElementsByClassName || !a.getElementsByClassName("e").length) && (a.lastChild.className = "e", 2 === a.getElementsByClassName("e").length)
            }), w.getByName = fa(function (a) {
                a.id = u + 0, a.innerHTML = "<a name='" + u + "'></a><div name='" + u + "'></div>", m.insertBefore(a, m.firstChild);
                var b = c.getElementsByName && c.getElementsByName(u).length === 2 + c.getElementsByName(u + 0).length;
                return w.getIdNotName = !c.getElementById(u), m.removeChild(a), b
            }), e.attrHandle = fa(function (a) {
                return a.innerHTML = "<a href='#'></a>", a.firstChild && typeof a.firstChild.getAttribute !== C && "#" === a.firstChild.getAttribute("href")
            }) ? {} : {
                href: function (a) {
                    return a.getAttribute("href", 2)
                }, type: function (a) {
                    return a.getAttribute("type")
                }
            }, w.getIdNotName ? (e.find.ID = function (a, b) {
                if (typeof b.getElementById !== C && !n) {
                    var c = b.getElementById(a);
                    return c && c.parentNode ? [c] : []
                }
            }, e.filter.ID = function (a) {
                var b = a.replace(aa, ba);
                return function (a) {
                    return a.getAttribute("id") === b
                }
            }) : (e.find.ID = function (a, c) {
                if (typeof c.getElementById !== C && !n) {
                    var d = c.getElementById(a);
                    return d ? d.id === a || typeof d.getAttributeNode !== C && d.getAttributeNode("id").value === a ? [d] : b : []
                }
            }, e.filter.ID = function (a) {
                var b = a.replace(aa, ba);
                return function (a) {
                    var c = typeof a.getAttributeNode !== C && a.getAttributeNode("id");
                    return c && c.value === b
                }
            }), e.find.TAG = w.tagNameNoComments ? function (a, b) {
                if (typeof b.getElementsByTagName !== C)return b.getElementsByTagName(a)
            } : function (a, b) {
                var c, d = [], e = 0, f = b.getElementsByTagName(a);
                if ("*" === a) {
                    for (; c = f[e++];)1 === c.nodeType && d.push(c);
                    return d
                }
                return f
            }, e.find.NAME = w.getByName && function (a, b) {
                    if (typeof b.getElementsByName !== C)return b.getElementsByName(name)
                }, e.find.CLASS = w.getByClassName && function (a, b) {
                    if (typeof b.getElementsByClassName !== C && !n)return b.getElementsByClassName(a)
                }, p = [], o = [":focus"], (w.qsa = ca(c.querySelectorAll)) && (fa(function (a) {
                a.innerHTML = "<select><option selected=''></option></select>", a.querySelectorAll("[selected]").length || o.push("\\[" + J + "*(?:checked|disabled|ismap|multiple|readonly|selected|value)"), a.querySelectorAll(":checked").length || o.push(":checked")
            }), fa(function (a) {
                a.innerHTML = "<input type='hidden' i=''/>", a.querySelectorAll("[i^='']").length && o.push("[*^$]=" + J + "*(?:\"\"|'')"), a.querySelectorAll(":enabled").length || o.push(":enabled", ":disabled"), a.querySelectorAll("*,:x"), o.push(",.*:")
            })), (w.matchesSelector = ca(q = m.matchesSelector || m.mozMatchesSelector || m.webkitMatchesSelector || m.oMatchesSelector || m.msMatchesSelector)) && fa(function (a) {
                w.disconnectedMatch = q.call(a, "div"), q.call(a, "[s!='']:x"), p.push("!=", O)
            }), o = new RegExp(o.join("|")), p = new RegExp(p.join("|")), r = ca(m.contains) || m.compareDocumentPosition ? function (a, b) {
                var c = 9 === a.nodeType ? a.documentElement : a, d = b && b.parentNode;
                return a === d || !(!d || 1 !== d.nodeType || !(c.contains ? c.contains(d) : a.compareDocumentPosition && 16 & a.compareDocumentPosition(d)))
            } : function (a, b) {
                if (b)for (; b = b.parentNode;)if (b === a)return !0;
                return !1
            }, s = m.compareDocumentPosition ? function (a, b) {
                var d;
                return a === b ? (i = !0, 0) : (d = b.compareDocumentPosition && a.compareDocumentPosition && a.compareDocumentPosition(b)) ? 1 & d || a.parentNode && 11 === a.parentNode.nodeType ? a === c || r(v, a) ? -1 : b === c || r(v, b) ? 1 : 0 : 4 & d ? -1 : 1 : a.compareDocumentPosition ? -1 : 1
            } : function (a, b) {
                var d, e = 0, f = a.parentNode, g = b.parentNode, h = [a], j = [b];
                if (a === b)return i = !0, 0;
                if (!f || !g)return a === c ? -1 : b === c ? 1 : f ? -1 : g ? 1 : 0;
                if (f === g)return ha(a, b);
                for (d = a; d = d.parentNode;)h.unshift(d);
                for (d = b; d = d.parentNode;)j.unshift(d);
                for (; h[e] === j[e];)e++;
                return e ? ha(h[e], j[e]) : h[e] === v ? -1 : j[e] === v ? 1 : 0
            }, i = !1, [0, 0].sort(s), w.detectDuplicates = i, l) : l
        }, ga.matches = function (a, b) {
            return ga(a, null, null, b)
        }, ga.matchesSelector = function (a, b) {
            if ((a.ownerDocument || a) !== l && k(a), b = b.replace(_, "='$1']"), w.matchesSelector && !n && (!p || !p.test(b)) && !o.test(b))try {
                var c = q.call(a, b);
                if (c || w.disconnectedMatch || a.document && 11 !== a.document.nodeType)return c
            } catch (a) {
            }
            return ga(b, l, null, [a]).length > 0
        }, ga.contains = function (a, b) {
            return (a.ownerDocument || a) !== l && k(a), r(a, b)
        }, ga.attr = function (a, b) {
            var c;
            return (a.ownerDocument || a) !== l && k(a), n || (b = b.toLowerCase()), (c = e.attrHandle[b]) ? c(a) : n || w.attributes ? a.getAttribute(b) : ((c = a.getAttributeNode(b)) || a.getAttribute(b)) && a[b] === !0 ? b : c && c.specified ? c.value : null
        }, ga.error = function (a) {
            throw new Error("Syntax error, unrecognized expression: " + a)
        }, ga.uniqueSort = function (a) {
            var b, c = [], d = 1, e = 0;
            if (i = !w.detectDuplicates, a.sort(s), i) {
                for (; b = a[d]; d++)b === a[d - 1] && (e = c.push(d));
                for (; e--;)a.splice(c[e], 1)
            }
            return a
        }, f = ga.getText = function (a) {
            var b, c = "", d = 0, e = a.nodeType;
            if (e) {
                if (1 === e || 9 === e || 11 === e) {
                    if ("string" == typeof a.textContent)return a.textContent;
                    for (a = a.firstChild; a; a = a.nextSibling)c += f(a)
                } else if (3 === e || 4 === e)return a.nodeValue
            } else for (; b = a[d]; d++)c += f(b);
            return c
        }, e = ga.selectors = {
            cacheLength: 50,
            createPseudo: ea,
            match: U,
            find: {},
            relative: {
                ">": {dir: "parentNode", first: !0},
                " ": {dir: "parentNode"},
                "+": {dir: "previousSibling", first: !0},
                "~": {dir: "previousSibling"}
            },
            preFilter: {
                ATTR: function (a) {
                    return a[1] = a[1].replace(aa, ba), a[3] = (a[4] || a[5] || "").replace(aa, ba), "~=" === a[2] && (a[3] = " " + a[3] + " "), a.slice(0, 4)
                }, CHILD: function (a) {
                    return a[1] = a[1].toLowerCase(), "nth" === a[1].slice(0, 3) ? (a[3] || ga.error(a[0]), a[4] = +(a[4] ? a[5] + (a[6] || 1) : 2 * ("even" === a[3] || "odd" === a[3])), a[5] = +(a[7] + a[8] || "odd" === a[3])) : a[3] && ga.error(a[0]), a
                }, PSEUDO: function (a) {
                    var b, c = !a[5] && a[2];
                    return U.CHILD.test(a[0]) ? null : (a[4] ? a[2] = a[4] : c && S.test(c) && (b = la(c, !0)) && (b = c.indexOf(")", c.length - b) - c.length) && (a[0] = a[0].slice(0, b), a[2] = c.slice(0, b)), a.slice(0, 3))
                }
            },
            filter: {
                TAG: function (a) {
                    return "*" === a ? function () {
                        return !0
                    } : (a = a.replace(aa, ba).toLowerCase(), function (b) {
                        return b.nodeName && b.nodeName.toLowerCase() === a
                    })
                }, CLASS: function (a) {
                    var b = z[a + " "];
                    return b || (b = new RegExp("(^|" + J + ")" + a + "(" + J + "|$)")) && z(a, function (a) {
                            return b.test(a.className || typeof a.getAttribute !== C && a.getAttribute("class") || "")
                        })
                }, ATTR: function (a, b, c) {
                    return function (d) {
                        var e = ga.attr(d, a);
                        return null == e ? "!=" === b : !b || (e += "", "=" === b ? e === c : "!=" === b ? e !== c : "^=" === b ? c && 0 === e.indexOf(c) : "*=" === b ? c && e.indexOf(c) > -1 : "$=" === b ? c && e.slice(-c.length) === c : "~=" === b ? (" " + e + " ").indexOf(c) > -1 : "|=" === b && (e === c || e.slice(0, c.length + 1) === c + "-"))
                    }
                }, CHILD: function (a, b, c, d, e) {
                    var f = "nth" !== a.slice(0, 3), g = "last" !== a.slice(-4), h = "of-type" === b;
                    return 1 === d && 0 === e ? function (a) {
                        return !!a.parentNode
                    } : function (b, c, i) {
                        var j, k, l, m, n, o, p = f !== g ? "nextSibling" : "previousSibling", q = b.parentNode, r = h && b.nodeName.toLowerCase(), s = !i && !h;
                        if (q) {
                            if (f) {
                                for (; p;) {
                                    for (l = b; l = l[p];)if (h ? l.nodeName.toLowerCase() === r : 1 === l.nodeType)return !1;
                                    o = p = "only" === a && !o && "nextSibling"
                                }
                                return !0
                            }
                            if (o = [g ? q.firstChild : q.lastChild], g && s) {
                                for (k = q[u] || (q[u] = {}), j = k[a] || [], n = j[0] === x && j[1], m = j[0] === x && j[2], l = n && q.childNodes[n]; l = ++n && l && l[p] || (m = n = 0) || o.pop();)if (1 === l.nodeType && ++m && l === b) {
                                    k[a] = [x, n, m];
                                    break
                                }
                            } else if (s && (j = (b[u] || (b[u] = {}))[a]) && j[0] === x)m = j[1]; else for (; (l = ++n && l && l[p] || (m = n = 0) || o.pop()) && ((h ? l.nodeName.toLowerCase() !== r : 1 !== l.nodeType) || !++m || (s && ((l[u] || (l[u] = {}))[a] = [x, m]), l !== b)););
                            return m -= e, m === d || m % d === 0 && m / d >= 0
                        }
                    }
                }, PSEUDO: function (a, b) {
                    var c, d = e.pseudos[a] || e.setFilters[a.toLowerCase()] || ga.error("unsupported pseudo: " + a);
                    return d[u] ? d(b) : d.length > 1 ? (c = [a, a, "", b], e.setFilters.hasOwnProperty(a.toLowerCase()) ? ea(function (a, c) {
                        for (var e, f = d(a, b), g = f.length; g--;)e = I.call(a, f[g]), a[e] = !(c[e] = f[g])
                    }) : function (a) {
                        return d(a, 0, c)
                    }) : d
                }
            },
            pseudos: {
                not: ea(function (a) {
                    var b = [], c = [], d = h(a.replace(P, "$1"));
                    return d[u] ? ea(function (a, b, c, e) {
                        for (var f, g = d(a, null, e, []), h = a.length; h--;)(f = g[h]) && (a[h] = !(b[h] = f))
                    }) : function (a, e, f) {
                        return b[0] = a, d(b, null, f, c), !c.pop()
                    }
                }), has: ea(function (a) {
                    return function (b) {
                        return ga(a, b).length > 0
                    }
                }), contains: ea(function (a) {
                    return function (b) {
                        return (b.textContent || b.innerText || f(b)).indexOf(a) > -1
                    }
                }), lang: ea(function (a) {
                    return T.test(a || "") || ga.error("unsupported lang: " + a), a = a.replace(aa, ba).toLowerCase(), function (b) {
                        var c;
                        do if (c = n ? b.getAttribute("xml:lang") || b.getAttribute("lang") : b.lang)return c = c.toLowerCase(), c === a || 0 === c.indexOf(a + "-"); while ((b = b.parentNode) && 1 === b.nodeType);
                        return !1
                    }
                }), target: function (b) {
                    var c = a.location && a.location.hash;
                    return c && c.slice(1) === b.id
                }, root: function (a) {
                    return a === m
                }, focus: function (a) {
                    return a === l.activeElement && (!l.hasFocus || l.hasFocus()) && !!(a.type || a.href || ~a.tabIndex)
                }, enabled: function (a) {
                    return a.disabled === !1
                }, disabled: function (a) {
                    return a.disabled === !0
                }, checked: function (a) {
                    var b = a.nodeName.toLowerCase();
                    return "input" === b && !!a.checked || "option" === b && !!a.selected
                }, selected: function (a) {
                    return a.parentNode && a.parentNode.selectedIndex, a.selected === !0
                }, empty: function (a) {
                    for (a = a.firstChild; a; a = a.nextSibling)if (a.nodeName > "@" || 3 === a.nodeType || 4 === a.nodeType)return !1;
                    return !0
                }, parent: function (a) {
                    return !e.pseudos.empty(a)
                }, header: function (a) {
                    return Z.test(a.nodeName)
                }, input: function (a) {
                    return Y.test(a.nodeName)
                }, button: function (a) {
                    var b = a.nodeName.toLowerCase();
                    return "input" === b && "button" === a.type || "button" === b
                }, text: function (a) {
                    var b;
                    return "input" === a.nodeName.toLowerCase() && "text" === a.type && (null == (b = a.getAttribute("type")) || b.toLowerCase() === a.type)
                }, first: ka(function () {
                    return [0]
                }), last: ka(function (a, b) {
                    return [b - 1]
                }), eq: ka(function (a, b, c) {
                    return [c < 0 ? c + b : c]
                }), even: ka(function (a, b) {
                    for (var c = 0; c < b; c += 2)a.push(c);
                    return a
                }), odd: ka(function (a, b) {
                    for (var c = 1; c < b; c += 2)a.push(c);
                    return a
                }), lt: ka(function (a, b, c) {
                    for (var d = c < 0 ? c + b : c; --d >= 0;)a.push(d);
                    return a
                }), gt: ka(function (a, b, c) {
                    for (var d = c < 0 ? c + b : c; ++d < b;)a.push(d);
                    return a
                })
            }
        };
        for (c in{radio: !0, checkbox: !0, file: !0, password: !0, image: !0})e.pseudos[c] = ia(c);
        for (c in{submit: !0, reset: !0})e.pseudos[c] = ja(c);
        h = ga.compile = function (a, b) {
            var c, d = [], e = [], f = B[a + " "];
            if (!f) {
                for (b || (b = la(a)), c = b.length; c--;)f = ra(b[c]), f[u] ? d.push(f) : e.push(f);
                f = B(a, sa(e, d))
            }
            return f
        }, e.pseudos.nth = e.pseudos.eq, e.filters = va.prototype = e.pseudos, e.setFilters = new va, k(), ga.attr = t.attr, t.find = ga, t.expr = ga.selectors, t.expr[":"] = t.expr.pseudos, t.unique = ga.uniqueSort, t.text = ga.getText, t.isXMLDoc = ga.isXML, t.contains = ga.contains
    }(a);
    var ga = /Until$/, ha = /^(?:parents|prev(?:Until|All))/, ia = /^.[^:#\[\.,]*$/, ja = t.expr.match.needsContext, ka = {
        children: !0,
        contents: !0,
        next: !0,
        prev: !0
    };
    t.fn.extend({
        find: function (a) {
            var b, c, d, e = this.length;
            if ("string" != typeof a)return d = this, this.pushStack(t(a).filter(function () {
                for (b = 0; b < e; b++)if (t.contains(d[b], this))return !0
            }));
            for (c = [], b = 0; b < e; b++)t.find(a, this[b], c);
            return c = this.pushStack(e > 1 ? t.unique(c) : c), c.selector = (this.selector ? this.selector + " " : "") + a, c
        }, has: function (a) {
            var b, c = t(a, this), d = c.length;
            return this.filter(function () {
                for (b = 0; b < d; b++)if (t.contains(this, c[b]))return !0
            })
        }, not: function (a) {
            return this.pushStack(ma(this, a, !1))
        }, filter: function (a) {
            return this.pushStack(ma(this, a, !0))
        }, is: function (a) {
            return !!a && ("string" == typeof a ? ja.test(a) ? t(a, this.context).index(this[0]) >= 0 : t.filter(a, this).length > 0 : this.filter(a).length > 0)
        }, closest: function (a, b) {
            for (var c, d = 0, e = this.length, f = [], g = ja.test(a) || "string" != typeof a ? t(a, b || this.context) : 0; d < e; d++)for (c = this[d]; c && c.ownerDocument && c !== b && 11 !== c.nodeType;) {
                if (g ? g.index(c) > -1 : t.find.matchesSelector(c, a)) {
                    f.push(c);
                    break
                }
                c = c.parentNode
            }
            return this.pushStack(f.length > 1 ? t.unique(f) : f)
        }, index: function (a) {
            return a ? "string" == typeof a ? t.inArray(this[0], t(a)) : t.inArray(a.jquery ? a[0] : a, this) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1
        }, add: function (a, b) {
            var c = "string" == typeof a ? t(a, b) : t.makeArray(a && a.nodeType ? [a] : a), d = t.merge(this.get(), c);
            return this.pushStack(t.unique(d))
        }, addBack: function (a) {
            return this.add(null == a ? this.prevObject : this.prevObject.filter(a))
        }
    }), t.fn.andSelf = t.fn.addBack, t.each({
        parent: function (a) {
            var b = a.parentNode;
            return b && 11 !== b.nodeType ? b : null
        }, parents: function (a) {
            return t.dir(a, "parentNode")
        }, parentsUntil: function (a, b, c) {
            return t.dir(a, "parentNode", c)
        }, next: function (a) {
            return la(a, "nextSibling")
        }, prev: function (a) {
            return la(a, "previousSibling")
        }, nextAll: function (a) {
            return t.dir(a, "nextSibling")
        }, prevAll: function (a) {
            return t.dir(a, "previousSibling")
        }, nextUntil: function (a, b, c) {
            return t.dir(a, "nextSibling", c)
        }, prevUntil: function (a, b, c) {
            return t.dir(a, "previousSibling", c)
        }, siblings: function (a) {
            return t.sibling((a.parentNode || {}).firstChild, a)
        }, children: function (a) {
            return t.sibling(a.firstChild)
        }, contents: function (a) {
            return t.nodeName(a, "iframe") ? a.contentDocument || a.contentWindow.document : t.merge([], a.childNodes);

        }
    }, function (a, b) {
        t.fn[a] = function (c, d) {
            var e = t.map(this, b, c);
            return ga.test(a) || (d = c), d && "string" == typeof d && (e = t.filter(d, e)), e = this.length > 1 && !ka[a] ? t.unique(e) : e, this.length > 1 && ha.test(a) && (e = e.reverse()), this.pushStack(e)
        }
    }), t.extend({
        filter: function (a, b, c) {
            return c && (a = ":not(" + a + ")"), 1 === b.length ? t.find.matchesSelector(b[0], a) ? [b[0]] : [] : t.find.matches(a, b)
        }, dir: function (a, c, d) {
            for (var e = [], f = a[c]; f && 9 !== f.nodeType && (d === b || 1 !== f.nodeType || !t(f).is(d));)1 === f.nodeType && e.push(f), f = f[c];
            return e
        }, sibling: function (a, b) {
            for (var c = []; a; a = a.nextSibling)1 === a.nodeType && a !== b && c.push(a);
            return c
        }
    });
    var oa = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video", pa = / jQuery\d+="(?:null|\d+)"/g, qa = new RegExp("<(?:" + oa + ")[\\s/>]", "i"), ra = /^\s+/, sa = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi, ta = /<([\w:]+)/, ua = /<tbody/i, va = /<|&#?\w+;/, wa = /<(?:script|style|link)/i, xa = /^(?:checkbox|radio)$/i, ya = /checked\s*(?:[^=]|=\s*.checked.)/i, za = /^$|\/(?:java|ecma)script/i, Aa = /^true\/(.*)/, Ba = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g, Ca = {
        option: [1, "<select multiple='multiple'>", "</select>"],
        legend: [1, "<fieldset>", "</fieldset>"],
        area: [1, "<map>", "</map>"],
        param: [1, "<object>", "</object>"],
        thead: [1, "<table>", "</table>"],
        tr: [2, "<table><tbody>", "</tbody></table>"],
        col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
        td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
        _default: t.support.htmlSerialize ? [0, "", ""] : [1, "X<div>", "</div>"]
    }, Da = na(f), Ea = Da.appendChild(f.createElement("div"));
    Ca.optgroup = Ca.option, Ca.tbody = Ca.tfoot = Ca.colgroup = Ca.caption = Ca.thead, Ca.th = Ca.td, t.fn.extend({
        text: function (a) {
            return t.access(this, function (a) {
                return a === b ? t.text(this) : this.empty().append((this[0] && this[0].ownerDocument || f).createTextNode(a))
            }, null, a, arguments.length)
        }, wrapAll: function (a) {
            if (t.isFunction(a))return this.each(function (b) {
                t(this).wrapAll(a.call(this, b))
            });
            if (this[0]) {
                var b = t(a, this[0].ownerDocument).eq(0).clone(!0);
                this[0].parentNode && b.insertBefore(this[0]), b.map(function () {
                    for (var a = this; a.firstChild && 1 === a.firstChild.nodeType;)a = a.firstChild;
                    return a
                }).append(this)
            }
            return this
        }, wrapInner: function (a) {
            return t.isFunction(a) ? this.each(function (b) {
                t(this).wrapInner(a.call(this, b))
            }) : this.each(function () {
                var b = t(this), c = b.contents();
                c.length ? c.wrapAll(a) : b.append(a)
            })
        }, wrap: function (a) {
            var b = t.isFunction(a);
            return this.each(function (c) {
                t(this).wrapAll(b ? a.call(this, c) : a)
            })
        }, unwrap: function () {
            return this.parent().each(function () {
                t.nodeName(this, "body") || t(this).replaceWith(this.childNodes)
            }).end()
        }, append: function () {
            return this.domManip(arguments, !0, function (a) {
                1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType || this.appendChild(a)
            })
        }, prepend: function () {
            return this.domManip(arguments, !0, function (a) {
                1 !== this.nodeType && 11 !== this.nodeType && 9 !== this.nodeType || this.insertBefore(a, this.firstChild)
            })
        }, before: function () {
            return this.domManip(arguments, !1, function (a) {
                this.parentNode && this.parentNode.insertBefore(a, this)
            })
        }, after: function () {
            return this.domManip(arguments, !1, function (a) {
                this.parentNode && this.parentNode.insertBefore(a, this.nextSibling)
            })
        }, remove: function (a, b) {
            for (var c, d = 0; null != (c = this[d]); d++)(!a || t.filter(a, [c]).length > 0) && (b || 1 !== c.nodeType || t.cleanData(La(c)), c.parentNode && (b && t.contains(c.ownerDocument, c) && Ia(La(c, "script")), c.parentNode.removeChild(c)));
            return this
        }, empty: function () {
            for (var a, b = 0; null != (a = this[b]); b++) {
                for (1 === a.nodeType && t.cleanData(La(a, !1)); a.firstChild;)a.removeChild(a.firstChild);
                a.options && t.nodeName(a, "select") && (a.options.length = 0)
            }
            return this
        }, clone: function (a, b) {
            return a = null != a && a, b = null == b ? a : b, this.map(function () {
                return t.clone(this, a, b)
            })
        }, html: function (a) {
            return t.access(this, function (a) {
                var c = this[0] || {}, d = 0, e = this.length;
                if (a === b)return 1 === c.nodeType ? c.innerHTML.replace(pa, "") : b;
                if ("string" == typeof a && !wa.test(a) && (t.support.htmlSerialize || !qa.test(a)) && (t.support.leadingWhitespace || !ra.test(a)) && !Ca[(ta.exec(a) || ["", ""])[1].toLowerCase()]) {
                    a = a.replace(sa, "<$1></$2>");
                    try {
                        for (; d < e; d++)c = this[d] || {}, 1 === c.nodeType && (t.cleanData(La(c, !1)), c.innerHTML = a);
                        c = 0
                    } catch (a) {
                    }
                }
                c && this.empty().append(a)
            }, null, a, arguments.length)
        }, replaceWith: function (a) {
            var b = t.isFunction(a);
            return b || "string" == typeof a || (a = t(a).not(this).detach()), this.domManip([a], !0, function (a) {
                var b = this.nextSibling, c = this.parentNode;
                c && (t(this).remove(), c.insertBefore(a, b))
            })
        }, detach: function (a) {
            return this.remove(a, !0)
        }, domManip: function (a, c, d) {
            a = m.apply([], a);
            var e, f, g, h, i, j, k = 0, l = this.length, n = this, o = l - 1, p = a[0], q = t.isFunction(p);
            if (q || !(l <= 1 || "string" != typeof p || t.support.checkClone) && ya.test(p))return this.each(function (e) {
                var f = n.eq(e);
                q && (a[0] = p.call(this, e, c ? f.html() : b)), f.domManip(a, c, d)
            });
            if (l && (j = t.buildFragment(a, this[0].ownerDocument, !1, this), e = j.firstChild, 1 === j.childNodes.length && (j = e), e)) {
                for (c = c && t.nodeName(e, "tr"), h = t.map(La(j, "script"), Ga), g = h.length; k < l; k++)f = j, k !== o && (f = t.clone(f, !0, !0), g && t.merge(h, La(f, "script"))), d.call(c && t.nodeName(this[k], "table") ? Fa(this[k], "tbody") : this[k], f, k);
                if (g)for (i = h[h.length - 1].ownerDocument, t.map(h, Ha), k = 0; k < g; k++)f = h[k], za.test(f.type || "") && !t._data(f, "globalEval") && t.contains(i, f) && (f.src ? t.ajax({
                    url: f.src,
                    type: "GET",
                    dataType: "script",
                    async: !1,
                    global: !1,
                    throws: !0
                }) : t.globalEval((f.text || f.textContent || f.innerHTML || "").replace(Ba, "")));
                j = e = null
            }
            return this
        }
    }), t.each({
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith"
    }, function (a, b) {
        t.fn[a] = function (a) {
            for (var c, d = 0, e = [], f = t(a), g = f.length - 1; d <= g; d++)c = d === g ? this : this.clone(!0), t(f[d])[b](c), n.apply(e, c.get());
            return this.pushStack(e)
        }
    }), t.extend({
        clone: function (a, b, c) {
            var d, e, f, g, h, i = t.contains(a.ownerDocument, a);
            if (t.support.html5Clone || t.isXMLDoc(a) || !qa.test("<" + a.nodeName + ">") ? f = a.cloneNode(!0) : (Ea.innerHTML = a.outerHTML, Ea.removeChild(f = Ea.firstChild)), !(t.support.noCloneEvent && t.support.noCloneChecked || 1 !== a.nodeType && 11 !== a.nodeType || t.isXMLDoc(a)))for (d = La(f), h = La(a), g = 0; null != (e = h[g]); ++g)d[g] && Ka(e, d[g]);
            if (b)if (c)for (h = h || La(a), d = d || La(f), g = 0; null != (e = h[g]); g++)Ja(e, d[g]); else Ja(a, f);
            return d = La(f, "script"), d.length > 0 && Ia(d, !i && La(a, "script")), d = h = e = null, f
        }, buildFragment: function (a, b, c, d) {
            for (var e, f, g, h, i, j, k, l = a.length, m = na(b), n = [], o = 0; o < l; o++)if (f = a[o], f || 0 === f)if ("object" === t.type(f))t.merge(n, f.nodeType ? [f] : f); else if (va.test(f)) {
                for (h = h || m.appendChild(b.createElement("div")), i = (ta.exec(f) || ["", ""])[1].toLowerCase(), k = Ca[i] || Ca._default, h.innerHTML = k[1] + f.replace(sa, "<$1></$2>") + k[2], e = k[0]; e--;)h = h.lastChild;
                if (!t.support.leadingWhitespace && ra.test(f) && n.push(b.createTextNode(ra.exec(f)[0])), !t.support.tbody)for (f = "table" !== i || ua.test(f) ? "<table>" !== k[1] || ua.test(f) ? 0 : h : h.firstChild, e = f && f.childNodes.length; e--;)t.nodeName(j = f.childNodes[e], "tbody") && !j.childNodes.length && f.removeChild(j);
                for (t.merge(n, h.childNodes), h.textContent = ""; h.firstChild;)h.removeChild(h.firstChild);
                h = m.lastChild
            } else n.push(b.createTextNode(f));
            for (h && m.removeChild(h), t.support.appendChecked || t.grep(La(n, "input"), Ma), o = 0; f = n[o++];)if ((!d || t.inArray(f, d) === -1) && (g = t.contains(f.ownerDocument, f), h = La(m.appendChild(f), "script"), g && Ia(h), c))for (e = 0; f = h[e++];)za.test(f.type || "") && c.push(f);
            return h = null, m
        }, cleanData: function (a, b) {
            for (var c, d, f, g, h = 0, i = t.expando, j = t.cache, l = t.support.deleteExpando, m = t.event.special; null != (c = a[h]); h++)if ((b || t.acceptData(c)) && (f = c[i], g = f && j[f])) {
                if (g.events)for (d in g.events)m[d] ? t.event.remove(c, d) : t.removeEvent(c, d, g.handle);
                j[f] && (delete j[f], l ? delete c[i] : typeof c.removeAttribute !== e ? c.removeAttribute(i) : c[i] = null, k.push(f))
            }
        }
    });
    var Na, Oa, Pa, Qa = /alpha\([^)]*\)/i, Ra = /opacity\s*=\s*([^)]*)/, Sa = /^(top|right|bottom|left)$/, Ta = /^(none|table(?!-c[ea]).+)/, Ua = /^margin/, Va = new RegExp("^(" + u + ")(.*)$", "i"), Wa = new RegExp("^(" + u + ")(?!px)[a-z%]+$", "i"), Xa = new RegExp("^([+-])=(" + u + ")", "i"), Ya = {BODY: "block"}, Za = {
        position: "absolute",
        visibility: "hidden",
        display: "block"
    }, $a = {
        letterSpacing: 0,
        fontWeight: 400
    }, _a = ["Top", "Right", "Bottom", "Left"], ab = ["Webkit", "O", "Moz", "ms"];
    t.fn.extend({
        css: function (a, c) {
            return t.access(this, function (a, c, d) {
                var e, f, g = {}, h = 0;
                if (t.isArray(c)) {
                    for (f = Oa(a), e = c.length; h < e; h++)g[c[h]] = t.css(a, c[h], !1, f);
                    return g
                }
                return d !== b ? t.style(a, c, d) : t.css(a, c)
            }, a, c, arguments.length > 1)
        }, show: function () {
            return db(this, !0)
        }, hide: function () {
            return db(this)
        }, toggle: function (a) {
            var b = "boolean" == typeof a;
            return this.each(function () {
                (b ? a : cb(this)) ? t(this).show() : t(this).hide()
            })
        }
    }), t.extend({
        cssHooks: {
            opacity: {
                get: function (a, b) {
                    if (b) {
                        var c = Pa(a, "opacity");
                        return "" === c ? "1" : c
                    }
                }
            }
        },
        cssNumber: {
            columnCount: !0,
            fillOpacity: !0,
            fontWeight: !0,
            lineHeight: !0,
            opacity: !0,
            orphans: !0,
            widows: !0,
            zIndex: !0,
            zoom: !0
        },
        cssProps: {float: t.support.cssFloat ? "cssFloat" : "styleFloat"},
        style: function (a, c, d, e) {
            if (a && 3 !== a.nodeType && 8 !== a.nodeType && a.style) {
                var f, g, h, i = t.camelCase(c), j = a.style;
                if (c = t.cssProps[i] || (t.cssProps[i] = bb(j, i)), h = t.cssHooks[c] || t.cssHooks[i], d === b)return h && "get" in h && (f = h.get(a, !1, e)) !== b ? f : j[c];
                if (g = typeof d, "string" === g && (f = Xa.exec(d)) && (d = (f[1] + 1) * f[2] + parseFloat(t.css(a, c)), g = "number"), !(null == d || "number" === g && isNaN(d) || ("number" !== g || t.cssNumber[i] || (d += "px"), t.support.clearCloneStyle || "" !== d || 0 !== c.indexOf("background") || (j[c] = "inherit"), h && "set" in h && (d = h.set(a, d, e)) === b)))try {
                    j[c] = d
                } catch (a) {
                }
            }
        },
        css: function (a, c, d, e) {
            var f, g, h, i = t.camelCase(c);
            return c = t.cssProps[i] || (t.cssProps[i] = bb(a.style, i)), h = t.cssHooks[c] || t.cssHooks[i], h && "get" in h && (g = h.get(a, !0, d)), g === b && (g = Pa(a, c, e)), "normal" === g && c in $a && (g = $a[c]), "" === d || d ? (f = parseFloat(g), d === !0 || t.isNumeric(f) ? f || 0 : g) : g
        },
        swap: function (a, b, c, d) {
            var e, f, g = {};
            for (f in b)g[f] = a.style[f], a.style[f] = b[f];
            e = c.apply(a, d || []);
            for (f in b)a.style[f] = g[f];
            return e
        }
    }), a.getComputedStyle ? (Oa = function (b) {
        return a.getComputedStyle(b, null)
    }, Pa = function (a, c, d) {
        var e, f, g, h = d || Oa(a), i = h ? h.getPropertyValue(c) || h[c] : b, j = a.style;
        return h && ("" !== i || t.contains(a.ownerDocument, a) || (i = t.style(a, c)), Wa.test(i) && Ua.test(c) && (e = j.width, f = j.minWidth, g = j.maxWidth, j.minWidth = j.maxWidth = j.width = i, i = h.width, j.width = e, j.minWidth = f, j.maxWidth = g)), i
    }) : f.documentElement.currentStyle && (Oa = function (a) {
        return a.currentStyle
    }, Pa = function (a, c, d) {
        var e, f, g, h = d || Oa(a), i = h ? h[c] : b, j = a.style;
        return null == i && j && j[c] && (i = j[c]), Wa.test(i) && !Sa.test(c) && (e = j.left, f = a.runtimeStyle, g = f && f.left, g && (f.left = a.currentStyle.left), j.left = "fontSize" === c ? "1em" : i, i = j.pixelLeft + "px", j.left = e, g && (f.left = g)), "" === i ? "auto" : i
    }), t.each(["height", "width"], function (a, b) {
        t.cssHooks[b] = {
            get: function (a, c, d) {
                if (c)return 0 === a.offsetWidth && Ta.test(t.css(a, "display")) ? t.swap(a, Za, function () {
                    return gb(a, b, d)
                }) : gb(a, b, d)
            }, set: function (a, c, d) {
                var e = d && Oa(a);
                return eb(a, c, d ? fb(a, b, d, t.support.boxSizing && "border-box" === t.css(a, "boxSizing", !1, e), e) : 0)
            }
        }
    }), t.support.opacity || (t.cssHooks.opacity = {
        get: function (a, b) {
            return Ra.test((b && a.currentStyle ? a.currentStyle.filter : a.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "" : b ? "1" : ""
        }, set: function (a, b) {
            var c = a.style, d = a.currentStyle, e = t.isNumeric(b) ? "alpha(opacity=" + 100 * b + ")" : "", f = d && d.filter || c.filter || "";
            c.zoom = 1, (b >= 1 || "" === b) && "" === t.trim(f.replace(Qa, "")) && c.removeAttribute && (c.removeAttribute("filter"), "" === b || d && !d.filter) || (c.filter = Qa.test(f) ? f.replace(Qa, e) : f + " " + e)
        }
    }), t(function () {
        t.support.reliableMarginRight || (t.cssHooks.marginRight = {
            get: function (a, b) {
                if (b)return t.swap(a, {display: "inline-block"}, Pa, [a, "marginRight"])
            }
        }), !t.support.pixelPosition && t.fn.position && t.each(["top", "left"], function (a, b) {
            t.cssHooks[b] = {
                get: function (a, c) {
                    if (c)return c = Pa(a, b), Wa.test(c) ? t(a).position()[b] + "px" : c
                }
            }
        })
    }), t.expr && t.expr.filters && (t.expr.filters.hidden = function (a) {
        return a.offsetWidth <= 0 && a.offsetHeight <= 0 || !t.support.reliableHiddenOffsets && "none" === (a.style && a.style.display || t.css(a, "display"))
    }, t.expr.filters.visible = function (a) {
        return !t.expr.filters.hidden(a)
    }), t.each({margin: "", padding: "", border: "Width"}, function (a, b) {
        t.cssHooks[a + b] = {
            expand: function (c) {
                for (var d = 0, e = {}, f = "string" == typeof c ? c.split(" ") : [c]; d < 4; d++)e[a + _a[d] + b] = f[d] || f[d - 2] || f[0];
                return e
            }
        }, Ua.test(a) || (t.cssHooks[a + b].set = eb)
    });
    var jb = /%20/g, kb = /\[\]$/, lb = /\r?\n/g, mb = /^(?:submit|button|image|reset|file)$/i, nb = /^(?:input|select|textarea|keygen)/i;
    t.fn.extend({
        serialize: function () {
            return t.param(this.serializeArray())
        }, serializeArray: function () {
            return this.map(function () {
                var a = t.prop(this, "elements");
                return a ? t.makeArray(a) : this
            }).filter(function () {
                var a = this.type;
                return this.name && !t(this).is(":disabled") && nb.test(this.nodeName) && !mb.test(a) && (this.checked || !xa.test(a))
            }).map(function (a, b) {
                var c = t(this).val();
                return null == c ? null : t.isArray(c) ? t.map(c, function (a) {
                    return {name: b.name, value: a.replace(lb, "\r\n")}
                }) : {name: b.name, value: c.replace(lb, "\r\n")}
            }).get()
        }
    }), t.param = function (a, c) {
        var d, e = [], f = function (a, b) {
            b = t.isFunction(b) ? b() : null == b ? "" : b, e[e.length] = encodeURIComponent(a) + "=" + encodeURIComponent(b)
        };
        if (c === b && (c = t.ajaxSettings && t.ajaxSettings.traditional), t.isArray(a) || a.jquery && !t.isPlainObject(a))t.each(a, function () {
            f(this.name, this.value)
        }); else for (d in a)ob(d, a[d], c, f);
        return e.join("&").replace(jb, "+")
    }, t.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "), function (a, b) {
        t.fn[b] = function (a, c) {
            return arguments.length > 0 ? this.on(b, null, a, c) : this.trigger(b)
        }
    }), t.fn.hover = function (a, b) {
        return this.mouseenter(a).mouseleave(b || a)
    };
    var pb, qb, rb = t.now(), sb = /\?/, tb = /#.*$/, ub = /([?&])_=[^&]*/, vb = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm, wb = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/, xb = /^(?:GET|HEAD)$/, yb = /^\/\//, zb = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/, Ab = t.fn.load, Bb = {}, Cb = {}, Db = "*/".concat("*");
    try {
        qb = g.href
    } catch (a) {
        qb = f.createElement("a"), qb.href = "", qb = qb.href
    }
    pb = zb.exec(qb.toLowerCase()) || [], t.fn.load = function (a, c, d) {
        if ("string" != typeof a && Ab)return Ab.apply(this, arguments);
        var e, f, g, h = this, i = a.indexOf(" ");
        return i >= 0 && (e = a.slice(i, a.length), a = a.slice(0, i)), t.isFunction(c) ? (d = c, c = b) : c && "object" == typeof c && (g = "POST"), h.length > 0 && t.ajax({
            url: a,
            type: g,
            dataType: "html",
            data: c
        }).done(function (a) {
            f = arguments, h.html(e ? t("<div>").append(t.parseHTML(a)).find(e) : a)
        }).complete(d && function (a, b) {
                h.each(d, f || [a.responseText, b, a])
            }), this
    }, t.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function (a, b) {
        t.fn[b] = function (a) {
            return this.on(b, a)
        }
    }), t.each(["get", "post"], function (a, c) {
        t[c] = function (a, d, e, f) {
            return t.isFunction(d) && (f = f || e, e = d, d = b), t.ajax({
                url: a,
                type: c,
                dataType: f,
                data: d,
                success: e
            })
        }
    }), t.extend({
        active: 0,
        lastModified: {},
        etag: {},
        ajaxSettings: {
            url: qb,
            type: "GET",
            isLocal: wb.test(pb[1]),
            global: !0,
            processData: !0,
            async: !0,
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            accepts: {
                "*": Db,
                text: "text/plain",
                html: "text/html",
                xml: "application/xml, text/xml",
                json: "application/json, text/javascript"
            },
            contents: {xml: /xml/, html: /html/, json: /json/},
            responseFields: {xml: "responseXML", text: "responseText"},
            converters: {"* text": a.String, "text html": !0, "text json": t.parseJSON, "text xml": t.parseXML},
            flatOptions: {url: !0, context: !0}
        },
        ajaxSetup: function (a, b) {
            return b ? Gb(Gb(a, t.ajaxSettings), b) : Gb(t.ajaxSettings, a)
        },
        ajaxPrefilter: Eb(Bb),
        ajaxTransport: Eb(Cb),
        ajax: function (a, c) {
            function y(a, c, d, e) {
                var k, r, s, v, w, y = c;
                2 !== u && (u = 2, h && clearTimeout(h), j = b, g = e || "", x.readyState = a > 0 ? 4 : 0, d && (v = Hb(l, x, d)), a >= 200 && a < 300 || 304 === a ? (l.ifModified && (w = x.getResponseHeader("Last-Modified"), w && (t.lastModified[f] = w), w = x.getResponseHeader("etag"), w && (t.etag[f] = w)), 204 === a ? (k = !0, y = "nocontent") : 304 === a ? (k = !0, y = "notmodified") : (k = Ib(l, v), y = k.state, r = k.data, s = k.error, k = !s)) : (s = y, !a && y || (y = "error", a < 0 && (a = 0))), x.status = a, x.statusText = (c || y) + "", k ? o.resolveWith(m, [r, y, x]) : o.rejectWith(m, [x, y, s]), x.statusCode(q), q = b, i && n.trigger(k ? "ajaxSuccess" : "ajaxError", [x, l, k ? r : s]), p.fireWith(m, [x, y]), i && (n.trigger("ajaxComplete", [x, l]), --t.active || t.event.trigger("ajaxStop")))
            }

            "object" == typeof a && (c = a, a = b), c = c || {};
            var d, e, f, g, h, i, j, k, l = t.ajaxSetup({}, c), m = l.context || l, n = l.context && (m.nodeType || m.jquery) ? t(m) : t.event, o = t.Deferred(), p = t.Callbacks("once memory"), q = l.statusCode || {}, r = {}, s = {}, u = 0, w = "canceled", x = {
                readyState: 0,
                getResponseHeader: function (a) {
                    var b;
                    if (2 === u) {
                        if (!k)for (k = {}; b = vb.exec(g);)k[b[1].toLowerCase()] = b[2];
                        b = k[a.toLowerCase()]
                    }
                    return null == b ? null : b
                },
                getAllResponseHeaders: function () {
                    return 2 === u ? g : null
                },
                setRequestHeader: function (a, b) {
                    var c = a.toLowerCase();
                    return u || (a = s[c] = s[c] || a, r[a] = b), this
                },
                overrideMimeType: function (a) {
                    return u || (l.mimeType = a), this
                },
                statusCode: function (a) {
                    var b;
                    if (a)if (u < 2)for (b in a)q[b] = [q[b], a[b]]; else x.always(a[x.status]);
                    return this
                },
                abort: function (a) {
                    var b = a || w;
                    return j && j.abort(b), y(0, b), this
                }
            };
            if (o.promise(x).complete = p.add, x.success = x.done, x.error = x.fail, l.url = ((a || l.url || qb) + "").replace(tb, "").replace(yb, pb[1] + "//"), l.type = c.method || c.type || l.method || l.type, l.dataTypes = t.trim(l.dataType || "*").toLowerCase().match(v) || [""], null == l.crossDomain && (d = zb.exec(l.url.toLowerCase()), l.crossDomain = !(!d || d[1] === pb[1] && d[2] === pb[2] && (d[3] || ("http:" === d[1] ? 80 : 443)) == (pb[3] || ("http:" === pb[1] ? 80 : 443)))), l.data && l.processData && "string" != typeof l.data && (l.data = t.param(l.data, l.traditional)), Fb(Bb, l, c, x), 2 === u)return x;
            i = l.global, i && 0 === t.active++ && t.event.trigger("ajaxStart"), l.type = l.type.toUpperCase(), l.hasContent = !xb.test(l.type), f = l.url, l.hasContent || (l.data && (f = l.url += (sb.test(f) ? "&" : "?") + l.data, delete l.data), l.cache === !1 && (l.url = ub.test(f) ? f.replace(ub, "$1_=" + rb++) : f + (sb.test(f) ? "&" : "?") + "_=" + rb++)), l.ifModified && (t.lastModified[f] && x.setRequestHeader("If-Modified-Since", t.lastModified[f]), t.etag[f] && x.setRequestHeader("If-None-Match", t.etag[f])), (l.data && l.hasContent && l.contentType !== !1 || c.contentType) && x.setRequestHeader("Content-Type", l.contentType), x.setRequestHeader("Accept", l.dataTypes[0] && l.accepts[l.dataTypes[0]] ? l.accepts[l.dataTypes[0]] + ("*" !== l.dataTypes[0] ? ", " + Db + "; q=0.01" : "") : l.accepts["*"]);
            for (e in l.headers)x.setRequestHeader(e, l.headers[e]);
            if (l.beforeSend && (l.beforeSend.call(m, x, l) === !1 || 2 === u))return x.abort();
            w = "abort";
            for (e in{success: 1, error: 1, complete: 1})x[e](l[e]);
            if (j = Fb(Cb, l, c, x)) {
                x.readyState = 1, i && n.trigger("ajaxSend", [x, l]), l.async && l.timeout > 0 && (h = setTimeout(function () {
                    x.abort("timeout")
                }, l.timeout));
                try {
                    u = 1, j.send(r, y)
                } catch (a) {
                    if (!(u < 2))throw a;
                    y(-1, a)
                }
            } else y(-1, "No Transport");
            return x
        },
        getScript: function (a, c) {
            return t.get(a, b, c, "script")
        },
        getJSON: function (a, b, c) {
            return t.get(a, b, c, "json")
        }
    }), t.ajaxSetup({
        accepts: {script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},
        contents: {script: /(?:java|ecma)script/},
        converters: {
            "text script": function (a) {
                return t.globalEval(a), a
            }
        }
    }), t.ajaxPrefilter("script", function (a) {
        a.cache === b && (a.cache = !1), a.crossDomain && (a.type = "GET", a.global = !1)
    }), t.ajaxTransport("script", function (a) {
        if (a.crossDomain) {
            var c, d = f.head || t("head")[0] || f.documentElement;
            return {
                send: function (b, e) {
                    c = f.createElement("script"), c.async = !0, a.scriptCharset && (c.charset = a.scriptCharset), c.src = a.url, c.onload = c.onreadystatechange = function (a, b) {
                        (b || !c.readyState || /loaded|complete/.test(c.readyState)) && (c.onload = c.onreadystatechange = null, c.parentNode && c.parentNode.removeChild(c), c = null, b || e(200, "success"))
                    }, d.insertBefore(c, d.firstChild)
                }, abort: function () {
                    c && c.onload(b, !0)
                }
            }
        }
    });
    var Jb = [], Kb = /(=)\?(?=&|$)|\?\?/;
    t.ajaxSetup({
        jsonp: "callback", jsonpCallback: function () {
            var a = Jb.pop() || t.expando + "_" + rb++;
            return this[a] = !0, a
        }
    }), t.ajaxPrefilter("json jsonp", function (c, d, e) {
        var f, g, h, i = c.jsonp !== !1 && (Kb.test(c.url) ? "url" : "string" == typeof c.data && !(c.contentType || "").indexOf("application/x-www-form-urlencoded") && Kb.test(c.data) && "data");
        if (i || "jsonp" === c.dataTypes[0])return f = c.jsonpCallback = t.isFunction(c.jsonpCallback) ? c.jsonpCallback() : c.jsonpCallback, i ? c[i] = c[i].replace(Kb, "$1" + f) : c.jsonp !== !1 && (c.url += (sb.test(c.url) ? "&" : "?") + c.jsonp + "=" + f), c.converters["script json"] = function () {
            return h || t.error(f + " was not called"), h[0]
        }, c.dataTypes[0] = "json", g = a[f], a[f] = function () {
            h = arguments
        }, e.always(function () {
            a[f] = g, c[f] && (c.jsonpCallback = d.jsonpCallback, Jb.push(f)), h && t.isFunction(g) && g(h[0]), h = g = b
        }), "script"
    });
    var Lb, Mb, Nb = 0, Ob = a.ActiveXObject && function () {
            var a;
            for (a in Lb)Lb[a](b, !0)
        };
    t.ajaxSettings.xhr = a.ActiveXObject ? function () {
        return !this.isLocal && Pb() || Qb()
    } : Pb, Mb = t.ajaxSettings.xhr(), t.support.cors = !!Mb && "withCredentials" in Mb, Mb = t.support.ajax = !!Mb, Mb && t.ajaxTransport(function (c) {
        if (!c.crossDomain || t.support.cors) {
            var d;
            return {
                send: function (e, f) {
                    var g, h, i = c.xhr();
                    if (c.username ? i.open(c.type, c.url, c.async, c.username, c.password) : i.open(c.type, c.url, c.async), c.xhrFields)for (h in c.xhrFields)i[h] = c.xhrFields[h];
                    c.mimeType && i.overrideMimeType && i.overrideMimeType(c.mimeType), c.crossDomain || e["X-Requested-With"] || (e["X-Requested-With"] = "XMLHttpRequest");
                    try {
                        for (h in e)i.setRequestHeader(h, e[h])
                    } catch (a) {
                    }
                    i.send(c.hasContent && c.data || null), d = function (a, e) {
                        var h, j, k, l;
                        try {
                            if (d && (e || 4 === i.readyState))if (d = b, g && (i.onreadystatechange = t.noop, Ob && delete Lb[g]), e)4 !== i.readyState && i.abort(); else {
                                l = {}, h = i.status, j = i.getAllResponseHeaders(), "string" == typeof i.responseText && (l.text = i.responseText);
                                try {
                                    k = i.statusText
                                } catch (a) {
                                    k = ""
                                }
                                h || !c.isLocal || c.crossDomain ? 1223 === h && (h = 204) : h = l.text ? 200 : 404
                            }
                        } catch (a) {
                            e || f(-1, a)
                        }
                        l && f(h, k, l, j)
                    }, c.async ? 4 === i.readyState ? setTimeout(d) : (g = ++Nb, Ob && (Lb || (Lb = {}, t(a).unload(Ob)), Lb[g] = d), i.onreadystatechange = d) : d()
                }, abort: function () {
                    d && d(b, !0)
                }
            }
        }
    });
    var Rb, Sb, Tb = /^(?:toggle|show|hide)$/, Ub = new RegExp("^(?:([+-])=|)(" + u + ")([a-z%]*)$", "i"), Vb = /queueHooks$/, Wb = [ac], Xb = {
        "*": [function (a, b) {
            var c, d, e = this.createTween(a, b), f = Ub.exec(b), g = e.cur(), h = +g || 0, i = 1, j = 20;
            if (f) {
                if (c = +f[2], d = f[3] || (t.cssNumber[a] ? "" : "px"), "px" !== d && h) {
                    h = t.css(e.elem, a, !0) || c || 1;
                    do i = i || ".5", h /= i, t.style(e.elem, a, h + d); while (i !== (i = e.cur() / g) && 1 !== i && --j)
                }
                e.unit = d, e.start = h, e.end = f[1] ? h + (f[1] + 1) * c : c
            }
            return e
        }]
    };
    t.Animation = t.extend($b, {
        tweener: function (a, b) {
            t.isFunction(a) ? (b = a, a = ["*"]) : a = a.split(" ");
            for (var c, d = 0, e = a.length; d < e; d++)c = a[d], Xb[c] = Xb[c] || [], Xb[c].unshift(b)
        }, prefilter: function (a, b) {
            b ? Wb.unshift(a) : Wb.push(a)
        }
    }), t.Tween = bc, bc.prototype = {
        constructor: bc, init: function (a, b, c, d, e, f) {
            this.elem = a, this.prop = c, this.easing = e || "swing", this.options = b, this.start = this.now = this.cur(), this.end = d, this.unit = f || (t.cssNumber[c] ? "" : "px")
        }, cur: function () {
            var a = bc.propHooks[this.prop];
            return a && a.get ? a.get(this) : bc.propHooks._default.get(this)
        }, run: function (a) {
            var b, c = bc.propHooks[this.prop];
            return this.options.duration ? this.pos = b = t.easing[this.easing](a, this.options.duration * a, 0, 1, this.options.duration) : this.pos = b = a, this.now = (this.end - this.start) * b + this.start, this.options.step && this.options.step.call(this.elem, this.now, this), c && c.set ? c.set(this) : bc.propHooks._default.set(this), this
        }
    }, bc.prototype.init.prototype = bc.prototype, bc.propHooks = {
        _default: {
            get: function (a) {
                var b;
                return null == a.elem[a.prop] || a.elem.style && null != a.elem.style[a.prop] ? (b = t.css(a.elem, a.prop, ""), b && "auto" !== b ? b : 0) : a.elem[a.prop]
            }, set: function (a) {
                t.fx.step[a.prop] ? t.fx.step[a.prop](a) : a.elem.style && (null != a.elem.style[t.cssProps[a.prop]] || t.cssHooks[a.prop]) ? t.style(a.elem, a.prop, a.now + a.unit) : a.elem[a.prop] = a.now
            }
        }
    }, bc.propHooks.scrollTop = bc.propHooks.scrollLeft = {
        set: function (a) {
            a.elem.nodeType && a.elem.parentNode && (a.elem[a.prop] = a.now)
        }
    }, t.each(["toggle", "show", "hide"], function (a, b) {
        var c = t.fn[b];
        t.fn[b] = function (a, d, e) {
            return null == a || "boolean" == typeof a ? c.apply(this, arguments) : this.animate(cc(b, !0), a, d, e)
        }
    }), t.fn.extend({
        fadeTo: function (a, b, c, d) {
            return this.filter(cb).css("opacity", 0).show().end().animate({opacity: b}, a, c, d)
        }, animate: function (a, b, c, d) {
            var e = t.isEmptyObject(a), f = t.speed(b, c, d), g = function () {
                var b = $b(this, t.extend({}, a), f);
                g.finish = function () {
                    b.stop(!0)
                }, (e || t._data(this, "finish")) && b.stop(!0)
            };
            return g.finish = g, e || f.queue === !1 ? this.each(g) : this.queue(f.queue, g)
        }, stop: function (a, c, d) {
            var e = function (a) {
                var b = a.stop;
                delete a.stop, b(d)
            };
            return "string" != typeof a && (d = c, c = a, a = b), c && a !== !1 && this.queue(a || "fx", []), this.each(function () {
                var b = !0, c = null != a && a + "queueHooks", f = t.timers, g = t._data(this);
                if (c)g[c] && g[c].stop && e(g[c]); else for (c in g)g[c] && g[c].stop && Vb.test(c) && e(g[c]);
                for (c = f.length; c--;)f[c].elem !== this || null != a && f[c].queue !== a || (f[c].anim.stop(d), b = !1, f.splice(c, 1));
                !b && d || t.dequeue(this, a)
            })
        }, finish: function (a) {
            return a !== !1 && (a = a || "fx"), this.each(function () {
                var b, c = t._data(this), d = c[a + "queue"], e = c[a + "queueHooks"], f = t.timers, g = d ? d.length : 0;
                for (c.finish = !0, t.queue(this, a, []), e && e.cur && e.cur.finish && e.cur.finish.call(this), b = f.length; b--;)f[b].elem === this && f[b].queue === a && (f[b].anim.stop(!0), f.splice(b, 1));
                for (b = 0; b < g; b++)d[b] && d[b].finish && d[b].finish.call(this);
                delete c.finish
            })
        }
    }), t.each({
        slideDown: cc("show"),
        slideUp: cc("hide"),
        slideToggle: cc("toggle"),
        fadeIn: {opacity: "show"},
        fadeOut: {opacity: "hide"},
        fadeToggle: {opacity: "toggle"}
    }, function (a, b) {
        t.fn[a] = function (a, c, d) {
            return this.animate(b, a, c, d)
        }
    }), t.speed = function (a, b, c) {
        var d = a && "object" == typeof a ? t.extend({}, a) : {
            complete: c || !c && b || t.isFunction(a) && a,
            duration: a,
            easing: c && b || b && !t.isFunction(b) && b
        };
        return d.duration = t.fx.off ? 0 : "number" == typeof d.duration ? d.duration : d.duration in t.fx.speeds ? t.fx.speeds[d.duration] : t.fx.speeds._default, null != d.queue && d.queue !== !0 || (d.queue = "fx"), d.old = d.complete, d.complete = function () {
            t.isFunction(d.old) && d.old.call(this), d.queue && t.dequeue(this, d.queue)
        }, d
    }, t.easing = {
        linear: function (a) {
            return a
        }, swing: function (a) {
            return .5 - Math.cos(a * Math.PI) / 2
        }
    }, t.timers = [], t.fx = bc.prototype.init, t.fx.tick = function () {
        var a, c = t.timers, d = 0;
        for (Rb = t.now(); d < c.length; d++)a = c[d], a() || c[d] !== a || c.splice(d--, 1);
        c.length || t.fx.stop(), Rb = b
    }, t.fx.timer = function (a) {
        a() && t.timers.push(a) && t.fx.start()
    }, t.fx.interval = 13, t.fx.start = function () {
        Sb || (Sb = setInterval(t.fx.tick, t.fx.interval))
    }, t.fx.stop = function () {
        clearInterval(Sb), Sb = null
    }, t.fx.speeds = {
        slow: 600,
        fast: 200,
        _default: 400
    }, t.fx.step = {}, t.expr && t.expr.filters && (t.expr.filters.animated = function (a) {
        return t.grep(t.timers, function (b) {
            return a === b.elem
        }).length
    }), t.fn.offset = function (a) {
        if (arguments.length)return a === b ? this : this.each(function (b) {
            t.offset.setOffset(this, a, b)
        });
        var c, d, f = {top: 0, left: 0}, g = this[0], h = g && g.ownerDocument;
        if (h)return c = h.documentElement, t.contains(c, g) ? (typeof g.getBoundingClientRect !== e && (f = g.getBoundingClientRect()), d = dc(h), {
            top: f.top + (d.pageYOffset || c.scrollTop) - (c.clientTop || 0),
            left: f.left + (d.pageXOffset || c.scrollLeft) - (c.clientLeft || 0)
        }) : f
    }, t.offset = {
        setOffset: function (a, b, c) {
            var d = t.css(a, "position");
            "static" === d && (a.style.position = "relative");
            var l, m, e = t(a), f = e.offset(), g = t.css(a, "top"), h = t.css(a, "left"), i = ("absolute" === d || "fixed" === d) && t.inArray("auto", [g, h]) > -1, j = {}, k = {};
            i ? (k = e.position(), l = k.top, m = k.left) : (l = parseFloat(g) || 0, m = parseFloat(h) || 0), t.isFunction(b) && (b = b.call(a, c, f)), null != b.top && (j.top = b.top - f.top + l), null != b.left && (j.left = b.left - f.left + m), "using" in b ? b.using.call(a, j) : e.css(j)
        }
    }, t.fn.extend({
        position: function () {
            if (this[0]) {
                var a, b, c = {top: 0, left: 0}, d = this[0];
                return "fixed" === t.css(d, "position") ? b = d.getBoundingClientRect() : (a = this.offsetParent(), b = this.offset(), t.nodeName(a[0], "html") || (c = a.offset()), c.top += t.css(a[0], "borderTopWidth", !0), c.left += t.css(a[0], "borderLeftWidth", !0)), {
                    top: b.top - c.top - t.css(d, "marginTop", !0),
                    left: b.left - c.left - t.css(d, "marginLeft", !0)
                }
            }
        }, offsetParent: function () {
            return this.map(function () {
                for (var a = this.offsetParent || f.documentElement; a && !t.nodeName(a, "html") && "static" === t.css(a, "position");)a = a.offsetParent;
                return a || f.documentElement
            })
        }
    }), t.each({scrollLeft: "pageXOffset", scrollTop: "pageYOffset"}, function (a, c) {
        var d = /Y/.test(c);
        t.fn[a] = function (e) {
            return t.access(this, function (a, e, f) {
                var g = dc(a);
                return f === b ? g ? c in g ? g[c] : g.document.documentElement[e] : a[e] : void(g ? g.scrollTo(d ? t(g).scrollLeft() : f, d ? f : t(g).scrollTop()) : a[e] = f)
            }, a, e, arguments.length, null)
        }
    }), t.each({Height: "height", Width: "width"}, function (a, c) {
        t.each({padding: "inner" + a, content: c, "": "outer" + a}, function (d, e) {
            t.fn[e] = function (e, f) {
                var g = arguments.length && (d || "boolean" != typeof e), h = d || (e === !0 || f === !0 ? "margin" : "border");
                return t.access(this, function (c, d, e) {
                    var f;
                    return t.isWindow(c) ? c.document.documentElement["client" + a] : 9 === c.nodeType ? (f = c.documentElement, Math.max(c.body["scroll" + a], f["scroll" + a], c.body["offset" + a], f["offset" + a], f["client" + a])) : e === b ? t.css(c, d, h) : t.style(c, d, e, h)
                }, c, g ? e : b, g, null)
            }
        })
    }), a.jQuery = a.$ = t, "function" == typeof define && define.amd && define.amd.jQuery && define("jquery", [], function () {
        return t
    })
}(window);