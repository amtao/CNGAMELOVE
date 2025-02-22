var e = module,
    o = exports;
var i,
    n,
    l =
        "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
            ? function(t) {
                  return typeof t;
              }
            : function(t) {
                  return t &&
                      "function" == typeof Symbol &&
                      t.constructor === Symbol &&
                      t !== Symbol.prototype
                      ? "symbol"
                      : typeof t;
              };
(i = "undefined" != typeof t ? t : void 0),
    (n = function(t) {
        var e = String.prototype.replace;
        function o(t) {
            t = t || {};
            this.phrases = {};
            this.extend(t.phrases || {});
            this.currentLocale = t.locale || "en";
            this.allowMissing = !!t.allowMissing;
            this.warn = t.warn || _;
        }
        o.VERSION = "1.0.0";
        o.prototype.locale = function(t) {
            t && (this.currentLocale = t);
            return this.currentLocale;
        };
        o.prototype.extend = function(t, e) {
            var o;
            for (var i in t)
                if (t.hasOwnProperty(i)) {
                    o = t[i];
                    e && (i = e + "." + i);
                    "object" === ("undefined" == typeof o ? "undefined" : l(o))
                        ? this.extend(o, i)
                        : (this.phrases[i] = o);
                }
        };
        o.prototype.unset = function(t, e) {
            var o;
            if ("string" == typeof t) delete this.phrases[t];
            else
                for (var i in t)
                    if (t.hasOwnProperty(i)) {
                        o = t[i];
                        e && (i = e + "." + i);
                        "object" ===
                        ("undefined" == typeof o ? "undefined" : l(o))
                            ? this.unset(o, i)
                            : delete this.phrases[i];
                    }
        };
        o.prototype.clear = function() {
            this.phrases = {};
        };
        o.prototype.replace = function(t) {
            this.clear();
            this.extend(t);
        };
        o.prototype.t = function(t, o) {
            var l, _;
            "number" == typeof (o = null == o ? {} : o) &&
                (o = {
                    smart_count: o
                });
            if ("string" == typeof this.phrases[t]) l = this.phrases[t];
            else if ("string" == typeof o._) l = o._;
            else if (this.allowMissing) l = t;
            else {
                this.warn('Missing translation for key: "' + t + '"');
                _ = t;
            }
            if ("string" == typeof l) {
                o = (function(t) {
                    var e = {};
                    for (var o in t) e[o] = t[o];
                    return e;
                })(o);
                _ = (function(t, o) {
                    for (var i in o)
                        if ("_" !== i && o.hasOwnProperty(i)) {
                            var n = o[i];
                            "string" == typeof n && (n = e.call(o[i], s, c));
                            t = e.call(
                                t,
                                new RegExp("%\\{" + i + "\\}", "g"),
                                n
                            );
                        }
                    return t;
                })(
                    (_ = (function(t, o, l) {
                        var s, c, _;
                        if (null != l && t) {
                            c = t.split(i);
                            _ =
                                c[
                                    ((u = o),
                                    (p = l),
                                    n[
                                        ((h = u),
                                        (y = (function(t) {
                                            var e,
                                                o,
                                                i,
                                                n = {};
                                            for (e in t)
                                                if (t.hasOwnProperty(e)) {
                                                    o = t[e];
                                                    for (i in o) n[o[i]] = e;
                                                }
                                            return n;
                                        })(r)),
                                        y[h] || y.en)
                                    ](p))
                                ] || c[0];
                            s = ((d = _), e.call(d, a, ""));
                        } else s = t;
                        var d;
                        var u, p, h, y;
                        return s;
                    })(l, this.currentLocale, o.smart_count)),
                    o
                );
            }
            return _;
        };
        o.prototype.has = function(t) {
            return t in this.phrases;
        };
        var i = "||||",
            n = {
                chinese: function(t) {
                    return 0;
                },
                german: function(t) {
                    return 1 !== t ? 1 : 0;
                },
                french: function(t) {
                    return t > 1 ? 1 : 0;
                },
                russian: function(t) {
                    return t % 10 == 1 && t % 100 != 11
                        ? 0
                        : t % 10 >= 2 &&
                          t % 10 <= 4 &&
                          (t % 100 < 10 || t % 100 >= 20)
                        ? 1
                        : 2;
                },
                czech: function(t) {
                    return 1 === t ? 0 : t >= 2 && t <= 4 ? 1 : 2;
                },
                polish: function(t) {
                    return 1 === t
                        ? 0
                        : t % 10 >= 2 &&
                          t % 10 <= 4 &&
                          (t % 100 < 10 || t % 100 >= 20)
                        ? 1
                        : 2;
                },
                icelandic: function(t) {
                    return t % 10 != 1 || t % 100 == 11 ? 1 : 0;
                }
            },
            r = {
                chinese: ["fa", "id", "ja", "ko", "lo", "ms", "th", "tr", "zh"],
                german: [
                    "da",
                    "de",
                    "en",
                    "es",
                    "fi",
                    "el",
                    "he",
                    "hu",
                    "it",
                    "nl",
                    "no",
                    "pt",
                    "sv"
                ],
                french: ["fr", "tl", "pt-br"],
                russian: ["hr", "ru"],
                czech: ["cs", "sk"],
                polish: ["pl"],
                icelandic: ["is"]
            };
        var a = /^\s+|\s+$/g;
        var s = /\$/g,
            c = "$$$$";
        function _(e) {
            cc.log(e);
            t && t.console && t.console.warn && t.console.warn("WARNING: " + e);
        }
        return o;
    }),
    "function" == typeof define && define.amd
        ? define([], function() {
              return n(i);
          })
        : "object" === ("undefined" == typeof o ? "undefined" : l(o))
        ? (e.exports = n(i))
        : (i.Polyglot = n(i));
