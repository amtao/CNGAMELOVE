var e = module,
    o = exports;
var i = new (require("polyglot"))({
    phrases: require("zh")
});
e.exports = {
    init: function(e) {
        "zh-ch" == e && (e = "zh");
        var o = require(e);
        i.replace(o);
    },
    t: function(t, e) {
        return i.t(t, e);
    },
    has: function(t) {
        return i.has(t);
    }
};
