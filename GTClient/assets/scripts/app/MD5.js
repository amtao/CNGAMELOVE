var e = module,
    o = exports;
var i;
(function(t) {
    function e(t, e) {
        var o = (65535 & t) + (65535 & e);
        return (((t >> 16) + (e >> 16) + (o >> 16)) << 16) | (65535 & o);
    }
    function o(t, o, i, n, l, r) {
        return e(((a = e(e(o, t), e(n, r))) << (s = l)) | (a >>> (32 - s)), i);
        var a, s;
    }
    function n(t, e, i, n, l, r, a) {
        return o((e & i) | (~e & n), t, e, l, r, a);
    }
    function l(t, e, i, n, l, r, a) {
        return o((e & n) | (i & ~n), t, e, l, r, a);
    }
    function r(t, e, i, n, l, r, a) {
        return o(e ^ i ^ n, t, e, l, r, a);
    }
    function a(t, e, i, n, l, r, a) {
        return o(i ^ (e | ~n), t, e, l, r, a);
    }
    function s(t, o) {
        t[o >> 5] |= 128 << o % 32;
        t[14 + (((o + 64) >>> 9) << 4)] = o;
        var i,
            s,
            c,
            _,
            d,
            u = 1732584193,
            p = -271733879,
            h = -1732584194,
            y = 271733878;
        for (i = 0; i < t.length; i += 16) {
            s = u;
            c = p;
            _ = h;
            d = y;
            p = a(
                (p = a(
                    (p = a(
                        (p = a(
                            (p = r(
                                (p = r(
                                    (p = r(
                                        (p = r(
                                            (p = l(
                                                (p = l(
                                                    (p = l(
                                                        (p = l(
                                                            (p = n(
                                                                (p = n(
                                                                    (p = n(
                                                                        (p = n(
                                                                            p,
                                                                            (h = n(
                                                                                h,
                                                                                (y = n(
                                                                                    y,
                                                                                    (u = n(
                                                                                        u,
                                                                                        p,
                                                                                        h,
                                                                                        y,
                                                                                        t[
                                                                                            i
                                                                                        ],
                                                                                        7,
                                                                                        -680876936
                                                                                    )),
                                                                                    p,
                                                                                    h,
                                                                                    t[
                                                                                        i +
                                                                                            1
                                                                                    ],
                                                                                    12,
                                                                                    -389564586
                                                                                )),
                                                                                u,
                                                                                p,
                                                                                t[
                                                                                    i +
                                                                                        2
                                                                                ],
                                                                                17,
                                                                                606105819
                                                                            )),
                                                                            y,
                                                                            u,
                                                                            t[
                                                                                i +
                                                                                    3
                                                                            ],
                                                                            22,
                                                                            -1044525330
                                                                        )),
                                                                        (h = n(
                                                                            h,
                                                                            (y = n(
                                                                                y,
                                                                                (u = n(
                                                                                    u,
                                                                                    p,
                                                                                    h,
                                                                                    y,
                                                                                    t[
                                                                                        i +
                                                                                            4
                                                                                    ],
                                                                                    7,
                                                                                    -176418897
                                                                                )),
                                                                                p,
                                                                                h,
                                                                                t[
                                                                                    i +
                                                                                        5
                                                                                ],
                                                                                12,
                                                                                1200080426
                                                                            )),
                                                                            u,
                                                                            p,
                                                                            t[
                                                                                i +
                                                                                    6
                                                                            ],
                                                                            17,
                                                                            -1473231341
                                                                        )),
                                                                        y,
                                                                        u,
                                                                        t[
                                                                            i +
                                                                                7
                                                                        ],
                                                                        22,
                                                                        -45705983
                                                                    )),
                                                                    (h = n(
                                                                        h,
                                                                        (y = n(
                                                                            y,
                                                                            (u = n(
                                                                                u,
                                                                                p,
                                                                                h,
                                                                                y,
                                                                                t[
                                                                                    i +
                                                                                        8
                                                                                ],
                                                                                7,
                                                                                1770035416
                                                                            )),
                                                                            p,
                                                                            h,
                                                                            t[
                                                                                i +
                                                                                    9
                                                                            ],
                                                                            12,
                                                                            -1958414417
                                                                        )),
                                                                        u,
                                                                        p,
                                                                        t[
                                                                            i +
                                                                                10
                                                                        ],
                                                                        17,
                                                                        -42063
                                                                    )),
                                                                    y,
                                                                    u,
                                                                    t[i + 11],
                                                                    22,
                                                                    -1990404162
                                                                )),
                                                                (h = n(
                                                                    h,
                                                                    (y = n(
                                                                        y,
                                                                        (u = n(
                                                                            u,
                                                                            p,
                                                                            h,
                                                                            y,
                                                                            t[
                                                                                i +
                                                                                    12
                                                                            ],
                                                                            7,
                                                                            1804603682
                                                                        )),
                                                                        p,
                                                                        h,
                                                                        t[
                                                                            i +
                                                                                13
                                                                        ],
                                                                        12,
                                                                        -40341101
                                                                    )),
                                                                    u,
                                                                    p,
                                                                    t[i + 14],
                                                                    17,
                                                                    -1502002290
                                                                )),
                                                                y,
                                                                u,
                                                                t[i + 15],
                                                                22,
                                                                1236535329
                                                            )),
                                                            (h = l(
                                                                h,
                                                                (y = l(
                                                                    y,
                                                                    (u = l(
                                                                        u,
                                                                        p,
                                                                        h,
                                                                        y,
                                                                        t[
                                                                            i +
                                                                                1
                                                                        ],
                                                                        5,
                                                                        -165796510
                                                                    )),
                                                                    p,
                                                                    h,
                                                                    t[i + 6],
                                                                    9,
                                                                    -1069501632
                                                                )),
                                                                u,
                                                                p,
                                                                t[i + 11],
                                                                14,
                                                                643717713
                                                            )),
                                                            y,
                                                            u,
                                                            t[i],
                                                            20,
                                                            -373897302
                                                        )),
                                                        (h = l(
                                                            h,
                                                            (y = l(
                                                                y,
                                                                (u = l(
                                                                    u,
                                                                    p,
                                                                    h,
                                                                    y,
                                                                    t[i + 5],
                                                                    5,
                                                                    -701558691
                                                                )),
                                                                p,
                                                                h,
                                                                t[i + 10],
                                                                9,
                                                                38016083
                                                            )),
                                                            u,
                                                            p,
                                                            t[i + 15],
                                                            14,
                                                            -660478335
                                                        )),
                                                        y,
                                                        u,
                                                        t[i + 4],
                                                        20,
                                                        -405537848
                                                    )),
                                                    (h = l(
                                                        h,
                                                        (y = l(
                                                            y,
                                                            (u = l(
                                                                u,
                                                                p,
                                                                h,
                                                                y,
                                                                t[i + 9],
                                                                5,
                                                                568446438
                                                            )),
                                                            p,
                                                            h,
                                                            t[i + 14],
                                                            9,
                                                            -1019803690
                                                        )),
                                                        u,
                                                        p,
                                                        t[i + 3],
                                                        14,
                                                        -187363961
                                                    )),
                                                    y,
                                                    u,
                                                    t[i + 8],
                                                    20,
                                                    1163531501
                                                )),
                                                (h = l(
                                                    h,
                                                    (y = l(
                                                        y,
                                                        (u = l(
                                                            u,
                                                            p,
                                                            h,
                                                            y,
                                                            t[i + 13],
                                                            5,
                                                            -1444681467
                                                        )),
                                                        p,
                                                        h,
                                                        t[i + 2],
                                                        9,
                                                        -51403784
                                                    )),
                                                    u,
                                                    p,
                                                    t[i + 7],
                                                    14,
                                                    1735328473
                                                )),
                                                y,
                                                u,
                                                t[i + 12],
                                                20,
                                                -1926607734
                                            )),
                                            (h = r(
                                                h,
                                                (y = r(
                                                    y,
                                                    (u = r(
                                                        u,
                                                        p,
                                                        h,
                                                        y,
                                                        t[i + 5],
                                                        4,
                                                        -378558
                                                    )),
                                                    p,
                                                    h,
                                                    t[i + 8],
                                                    11,
                                                    -2022574463
                                                )),
                                                u,
                                                p,
                                                t[i + 11],
                                                16,
                                                1839030562
                                            )),
                                            y,
                                            u,
                                            t[i + 14],
                                            23,
                                            -35309556
                                        )),
                                        (h = r(
                                            h,
                                            (y = r(
                                                y,
                                                (u = r(
                                                    u,
                                                    p,
                                                    h,
                                                    y,
                                                    t[i + 1],
                                                    4,
                                                    -1530992060
                                                )),
                                                p,
                                                h,
                                                t[i + 4],
                                                11,
                                                1272893353
                                            )),
                                            u,
                                            p,
                                            t[i + 7],
                                            16,
                                            -155497632
                                        )),
                                        y,
                                        u,
                                        t[i + 10],
                                        23,
                                        -1094730640
                                    )),
                                    (h = r(
                                        h,
                                        (y = r(
                                            y,
                                            (u = r(
                                                u,
                                                p,
                                                h,
                                                y,
                                                t[i + 13],
                                                4,
                                                681279174
                                            )),
                                            p,
                                            h,
                                            t[i],
                                            11,
                                            -358537222
                                        )),
                                        u,
                                        p,
                                        t[i + 3],
                                        16,
                                        -722521979
                                    )),
                                    y,
                                    u,
                                    t[i + 6],
                                    23,
                                    76029189
                                )),
                                (h = r(
                                    h,
                                    (y = r(
                                        y,
                                        (u = r(
                                            u,
                                            p,
                                            h,
                                            y,
                                            t[i + 9],
                                            4,
                                            -640364487
                                        )),
                                        p,
                                        h,
                                        t[i + 12],
                                        11,
                                        -421815835
                                    )),
                                    u,
                                    p,
                                    t[i + 15],
                                    16,
                                    530742520
                                )),
                                y,
                                u,
                                t[i + 2],
                                23,
                                -995338651
                            )),
                            (h = a(
                                h,
                                (y = a(
                                    y,
                                    (u = a(u, p, h, y, t[i], 6, -198630844)),
                                    p,
                                    h,
                                    t[i + 7],
                                    10,
                                    1126891415
                                )),
                                u,
                                p,
                                t[i + 14],
                                15,
                                -1416354905
                            )),
                            y,
                            u,
                            t[i + 5],
                            21,
                            -57434055
                        )),
                        (h = a(
                            h,
                            (y = a(
                                y,
                                (u = a(u, p, h, y, t[i + 12], 6, 1700485571)),
                                p,
                                h,
                                t[i + 3],
                                10,
                                -1894986606
                            )),
                            u,
                            p,
                            t[i + 10],
                            15,
                            -1051523
                        )),
                        y,
                        u,
                        t[i + 1],
                        21,
                        -2054922799
                    )),
                    (h = a(
                        h,
                        (y = a(
                            y,
                            (u = a(u, p, h, y, t[i + 8], 6, 1873313359)),
                            p,
                            h,
                            t[i + 15],
                            10,
                            -30611744
                        )),
                        u,
                        p,
                        t[i + 6],
                        15,
                        -1560198380
                    )),
                    y,
                    u,
                    t[i + 13],
                    21,
                    1309151649
                )),
                (h = a(
                    h,
                    (y = a(
                        y,
                        (u = a(u, p, h, y, t[i + 4], 6, -145523070)),
                        p,
                        h,
                        t[i + 11],
                        10,
                        -1120210379
                    )),
                    u,
                    p,
                    t[i + 2],
                    15,
                    718787259
                )),
                y,
                u,
                t[i + 9],
                21,
                -343485551
            );
            u = e(u, s);
            p = e(p, c);
            h = e(h, _);
            y = e(y, d);
        }
        return [u, p, h, y];
    }
    function c(t) {
        var e,
            o = "",
            i = 32 * t.length;
        for (e = 0; e < i; e += 8)
            o += String.fromCharCode((t[e >> 5] >>> e % 32) & 255);
        return o;
    }
    function _(t) {
        var e,
            o = [];
        o[(t.length >> 2) - 1] = void 0;
        for (e = 0; e < o.length; e += 1) o[e] = 0;
        var i = 8 * t.length;
        for (e = 0; e < i; e += 8)
            o[e >> 5] |= (255 & t.charCodeAt(e / 8)) << e % 32;
        return o;
    }
    function d(t) {
        var e,
            o,
            i = "0123456789abcdef",
            n = "";
        for (o = 0; o < t.length; o += 1) {
            e = t.charCodeAt(o);
            n += i.charAt((e >>> 4) & 15) + i.charAt(15 & e);
        }
        return n;
    }
    function u(t) {
        return unescape(encodeURIComponent(t));
    }
    function p(t) {
        return c(s(_((e = u(t))), 8 * e.length));
        var e;
    }
    function h(t, e) {
        return (function(t, e) {
            var o,
                i,
                n = _(t),
                l = [],
                r = [];
            l[15] = r[15] = void 0;
            n.length > 16 && (n = s(n, 8 * t.length));
            for (o = 0; o < 16; o += 1) {
                l[o] = 909522486 ^ n[o];
                r[o] = 1549556828 ^ n[o];
            }
            i = s(l.concat(_(e)), 512 + 8 * e.length);
            return c(s(r.concat(i), 640));
        })(u(t), u(e));
    }
    (i || (i = {})).md5 = function(t, e, o) {
        return e ? (o ? h(e, t) : d(h(e, t))) : o ? p(t) : d(p(t));
    };
})();
window.MD5 = i;
