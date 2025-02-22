var LuckyCarpProxy = function() {

    this.UPDATE_LUCKY_CARP = "UPDATE_LUCKY_CARP";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.luckyCharm.share, this.onShare, this);
    };
    this.clearData = function() {
        this.share = null;
    };
    this.onShare = function(t) {
        this.share = t;
        facade.send(this.UPDATE_LUCKY_CARP);
    };
}
exports.LuckyCarpProxy = LuckyCarpProxy;
