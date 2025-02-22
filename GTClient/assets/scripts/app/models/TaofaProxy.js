var TaofaProxy = function() {

    this.UPDATE_TAOFA_FAIL = "UPDATE_TAOFA_FAIL";
    this.UPDATE_TAOFA_MYRAND = "UPDATE_TAOFA_MYRAND";
    this.UPDATE_TAOFA_PLAYINFO = "UPDATE_TAOFA_PLAYINFO";
    this.UPDATE_TAOFA_ROOTINFO = "UPDATE_TAOFA_ROOTINFO";
    this.UPDATE_TAOFA_SCORERANK = "UPDATE_TAOFA_SCORERANK";
    this.UPDATE_TAOFA_WIN = "UPDATE_TAOFA_WIN";
    this.fail = null;
    this.myRand = null;
    this.playerInfo = null;
    this.rootInfo = null;
    this.scoreRank = null;
    this.win = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.taofa.fail, this.onFail, this);
        JsonHttp.subscribe(proto_sc.taofa.myRand, this.onMyRand, this);
        JsonHttp.subscribe(proto_sc.taofa.playInfo, this.onPlayerInfo, this);
        JsonHttp.subscribe(proto_sc.taofa.rootInfo, this.onRootInfo, this);
        JsonHttp.subscribe(proto_sc.taofa.scoreRank, this.onScoreRank, this);
        JsonHttp.subscribe(proto_sc.taofa.win, this.onWin, this);
    };
    this.clearData = function() {
        this.fail = null;
        this.myRand = null;
        this.playerInfo = null;
        this.rootInfo = null;
        this.scoreRank = null;
        this.win = null;
    };
    this.onFail = function(t) {
        facade.send(this.UPDATE_TAOFA_FAIL);
    };
    this.onMyRand = function(t) {
        facade.send(this.UPDATE_TAOFA_MYRAND);
    };
    this.onPlayerInfo = function(t) {
        facade.send(this.UPDATE_TAOFA_PLAYINFO);
    };
    this.onRootInfo = function(t) {
        facade.send(this.UPDATE_TAOFA_ROOTINFO);
    };
    this.onScoreRank = function(t) {
        facade.send(this.UPDATE_TAOFA_SCORERANK);
    };
    this.onWin = function(t) {
        facade.send(this.UPDATE_TAOFA_WIN);
    };
}
exports.TaofaProxy = TaofaProxy;
