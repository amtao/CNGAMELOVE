var i = require("Initializer");
var n = require("Utils");
var l = require("RedDot");
var r = require("TimeProxy");
var CrossProxy = function() {

    this.CROSS_SHI_LI_CFG = "CROSS_SHI_LI_CFG";
    this.CROSS_USER_LIST = "CROSS_USER_LIST";
    this.CROSS_MY_KUA_SHI_LI = "CROSS_MY_KUA_SHI_LI";
    this.CROSS_QU_FU_LIST = "CROSS_QU_FU_LIST";
    this.CROSS_MY_KUA_QU_FU = "CROSS_MY_KUA_QU_FU";
    this.CROSS_CHAT = "CROSS_CHAT";
    this.CROSS_LOVE_CFG = "CROSS_LOVE_CFG";
    this.CROSS_USER_LOVE_LIST = "CROSS_USER_LOVE_LIST";
    this.CROSS_MY_KUA_LOVE = "CROSS_MY_KUA_LOVE";
    this.CROSS_QU_FU_LOVE_LIST = "CROSS_QU_FU_LOVE_LIST";
    this.CROSS_MY_KUA_QU_FU_LOVE = "CROSS_MY_KUA_QU_FU_LOVE";
    this.CROSS_LOVE_CHAT = "CROSS_LOVE_CHAT";
    this.FENGXIANDIAN_RANK_INFO = "FENGXIANDIAN_RANK_INFO";
    this.FENGXIANDIAN_INFO = "FENGXIANDIAN_INFO";
    this.FENGXIANDIAN_QINAN = "FENGXIANDIAN_QINAN";
    this.KUA_USER_TYPE = 131;
    this.KUA_QU_TYPE = 132;
    this.KUA_LOVE_USER_TYPE = 137;
    this.KUA_LOVE_QU_TYPE = 138;
    this.KUA_GONGDOU_USER_TYPE = 305;
    this.KUA_GONGDOU_QU_TYPE = 306;
    this.kuashili = null;
    this.userlist = null;
    this.mykuashiliRid = null;
    this.qufulist = null;
    this.mykuaquRid = null;
    this.chat = null;
    this.kualove = null;
    this.userlovelist = null;
    this.mykualoveRid = null;
    this.qufulovelist = null;
    this.mykuaquloveRid = null;
    this.lovechat = null;
    this.dhShop = null;
    this.fengxiandian = null;
    this.fengxiandianRankInfo = null;
    this.isShow = !0;
    this.qingAn = null;

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.kuashili,
            this.onKuaShili,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.userlist,
            this.onUserList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.mykuashiliRid,
            this.onMyKuaShiliRid,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.qufulist,
            this.onQuFuList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.mykuaquRid,
            this.onMyKuaquRid,
            this
        );
        JsonHttp.subscribe(proto_sc.kuacbhuodong.chat, this.onChat, this);
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.kualove,
            this.onKuaLove,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.userlovelist,
            this.onUserLoveList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.mykualoveRid,
            this.onMyKuaLoveRid,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.qufulovelist,
            this.onQuFuLoveList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.mykuaquloveRid,
            this.onMyKuaquLoveRid,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.lovechat,
            this.onLoveChat,
            this
        );
        JsonHttp.subscribe(
            proto_sc.fengxiandian.info,
            this.onFengXianDianRankInfo,
            this
        );
        JsonHttp.subscribe(
            proto_sc.kuacbhuodong.fengxiandian,
            this.onFengXianDianInfo,
            this
        );
        JsonHttp.subscribe(
            proto_sc.fengxiandian.qingAn,
            this.onQingAn,
            this
        );
    };
    this.clearData = function() {
        this.kuashili = null;
        this.userlist = null;
        this.mykuashiliRid = null;
        this.qufulist = null;
        this.mykuaquRid = null;
        this.chat = null;
        this.kualove = null;
        this.userlovelist = null;
        this.mykualoveRid = null;
        this.qufulovelist = null;
        this.mykuaquloveRid = null;
        this.lovechat = null;
        this.dhShop = null;
        this.fengxiandian = null;
        this.fengxiandianRankInfo = null;
        this.qingAn = null;
    };
    this.onKuaShili = function(t) {
        if (null == this.kuashili) this.kuashili = t;
        else {
            t.comein && (this.kuashili.comein = t.comein);
            t.get && (this.kuashili.get = t.get);
            t.rnum && (this.kuashili.rnum = t.rnum);
            t.cd && (this.kuashili.cd = t.cd);
            t.type && (this.kuashili.type = t.type);
        }
        l.change("cross_qufu", 1 == t.get);
        facade.send(this.CROSS_SHI_LI_CFG);
    };
    this.onUserList = function(t) {
        this.userlist = t;
        if (t)
            for (var e = 0; e < this.userlist.length; e++)
                this.userlist[e].type = 1;
        facade.send(this.CROSS_USER_LIST);
    };
    this.onMyKuaShiliRid = function(t) {
        this.mykuashiliRid = t;
        this.mykuashiliRid.type = 1;
        facade.send(this.CROSS_MY_KUA_SHI_LI);
    };
    this.onQuFuList = function(t) {
        this.qufulist = t;
        if (t)
            for (var e = 0; e < this.qufulist.length; e++) {
                this.qufulist[e].type = 2;
                this.qufulist[e].uid = null;
            }
        facade.send(this.CROSS_QU_FU_LIST);
    };
    this.onMyKuaquRid = function(t) {
        this.mykuaquRid = t;
        this.mykuaquRid.type = 2;
        this.mykuaquRid.uid = null;
        facade.send(this.CROSS_MY_KUA_QU_FU);
    };
    this.onChat = function(t) {
        this.chat = t;
        facade.send(this.CROSS_CHAT);
    };
    this.onKuaLove = function(t) {
        if (null == this.kualove) this.kualove = t;
        else {
            t.comein && (this.kualove.comein = t.comein);
            t.get && (this.kualove.get = t.get);
            t.rnum && (this.kualove.rnum = t.rnum);
            t.cd && (this.kualove.cd = t.cd);
            t.type && (this.kualove.type = t.type);
        }
        l.change("cross_qufu", 1 == t.get);
        facade.send(this.CROSS_LOVE_CFG);
    };
    this.onUserLoveList = function(t) {
        this.userlovelist = t;
        if (t)
            for (var e = 0; e < this.userlovelist.length; e++)
                this.userlovelist[e].type = 1;
        facade.send(this.CROSS_USER_LOVE_LIST);
    };
    this.onMyKuaLoveRid = function(t) {
        this.mykualoveRid = t;
        this.mykualoveRid.type = 1;
        facade.send(this.CROSS_MY_KUA_LOVE);
    };
    this.onQuFuLoveList = function(t) {
        this.qufulovelist = t;
        if (t)
            for (var e = 0; e < this.qufulovelist.length; e++) {
                this.qufulovelist[e].type = 2;
                this.qufulovelist[e].uid = null;
            }
        facade.send(this.CROSS_QU_FU_LOVE_LIST);
    };
    this.onMyKuaquLoveRid = function(t) {
        this.mykuaquloveRid = t;
        this.mykuaquloveRid.type = 2;
        this.mykuaquloveRid.uid = null;
        facade.send(this.CROSS_MY_KUA_QU_FU_LOVE);
    };
    this.onLoveChat = function(t) {
        this.lovechat = t;
        facade.send(this.CROSS_LOVE_CHAT);
    };
    this.onFengXianDianRankInfo = function(t) {
        this.fengxiandianRankInfo = t;
        facade.send(this.FENGXIANDIAN_RANK_INFO);
    };
    this.onFengXianDianInfo = function(t) {
        this.fengxiandian = t;
        if (
            !(
                null == t ||
                null == t.exchange ||
                (t.exchange && 0 == t.exchange.length)
            )
        ) {
            null == this.dhShop && (this.dhShop = {});
            this.dhShop.hid = this.fengxiandian.info
                ? this.fengxiandian.info.id
                : 1;
            this.dhShop.rwd = t.exchange;
            this.dhShop.stime = this.fengxiandian.info
                ? this.fengxiandian.info.showTime
                : 0;
            facade.send(
                i.limitActivityProxy.ACTIVITY_SHOP_UPDATE,
                this.dhShop
            );
            facade.send(this.FENGXIANDIAN_INFO);
        }
    };
    Object.defineProperty(CrossProxy.prototype, "isDiamond", {
        get: function() {
            var t = this.getQingAn(5);
            if (null == t) return !0;
            if (1 == t.type) return !1;
            if (2 == t.type) {
                return 100 * Math.random() > 50;
            }
            return !0;
        },
        enumerable: !0,
        configurable: !0
    });
    this.onQingAn = function(t) {
        this.qingAn = t;
        this.updateRed();
        facade.send(this.FENGXIANDIAN_QINAN);
    };
    this.updateRed = function() {
        var t = !1;
        if (this.qingAn)
            for (var e = 0; e < this.qingAn.length; e++) {
                var o = this.qingAn[e];
                if (null != o && (5 != o.id && 0 == o.type)) {
                    t = !0;
                    break;
                }
            }
        r.funUtils.isOpenFun(r.funUtils.fengxiandian) || (t = !1);
        l.change("huanggong", t);
    };
    this.sendHd313Info = function() {
        JsonHttp.send(new proto_cs.huodong.hd313Info());
    };
    this.sendHd313Get = function() {
        var t = new proto_cs.huodong.hd313Get();
        JsonHttp.send(t, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendHd313YXRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd313YXRank());
    };
    this.sendHd313UserRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd313UserRank());
    };
    this.sendHd313QuRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd313QuRank());
    };
    this.sendHd313Chat = function(t, e) {
        var o = new proto_cs.huodong.hd313Chat();
        o.msg = t;
        o.type = e;
        JsonHttp.send(o);
    };
    this.sendHd314Info = function() {
        JsonHttp.send(new proto_cs.huodong.hd314Info());
    };
    this.sendHd314Get = function() {
        var t = new proto_cs.huodong.hd314Get();
        JsonHttp.send(t, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendHd314YXRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd314YXRank());
    };
    this.sendHd314UserRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd314UserRank());
    };
    this.sendHd314QuRank = function() {
        JsonHttp.send(new proto_cs.huodong.hd314QuRank());
    };
    this.sendHd314Chat = function(t, e) {
        var o = new proto_cs.huodong.hd314Chat();
        o.msg = t;
        o.type = e;
        JsonHttp.send(o);
    };
    this.sendInfo = function(t) {
        t == i.limitActivityProxy.KUA_SHILI_ID
            ? this.sendHd313Info()
            : t == i.limitActivityProxy.KUA_LOV_ID && this.sendHd314Info();
    };
    this.sendGet = function(t) {
        t == i.limitActivityProxy.KUA_SHILI_ID
            ? this.sendHd313Get()
            : t == i.limitActivityProxy.KUA_LOV_ID && this.sendHd314Get();
    };
    this.sendYXRank = function(t) {
        t == i.limitActivityProxy.KUA_SHILI_ID
            ? this.sendHd313YXRank()
            : t == i.limitActivityProxy.KUA_LOV_ID &&
              this.sendHd314YXRank();
    };
    this.sendUserRank = function(t) {
        t == i.limitActivityProxy.KUA_SHILI_ID
            ? this.sendHd313UserRank()
            : t == i.limitActivityProxy.KUA_LOV_ID &&
              this.sendHd314UserRank();
    };
    this.sendQuRank = function(t) {
        t == i.limitActivityProxy.KUA_SHILI_ID
            ? this.sendHd313QuRank()
            : t == i.limitActivityProxy.KUA_LOV_ID &&
              this.sendHd314QuRank();
    };
    Object.defineProperty(CrossProxy.prototype, "isShowFengXianDian", {
        get: function() {
            var t = i.limitActivityProxy.getHuodongList(
                i.limitActivityProxy.KUA_CHONG_BANG_TYPE
            );
            if (null == t) return !1;
            for (var e = 0; e < t.length; e++)
                if (n.timeUtil.second < t[e].showTime) return !0;
            return !1;
        },
        enumerable: !0,
        configurable: !0
    });
    this.sendOpenActivity = function() {
        JsonHttp.send(new proto_cs.huodong.hd6240Info());
    };
    this.sendGetInfo = function() {
        var t = new proto_cs.fengxiandian.getInfo();
        JsonHttp.send(t);
    };
    this.sendMoBai = function(t) {
        var e = new proto_cs.fengxiandian.qingAn();
        e.type = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.getCurCfg = function(t) {
        return t == i.limitActivityProxy.KUA_SHILI_ID
            ? this.kuashili
            : t == i.limitActivityProxy.KUA_LOV_ID
            ? this.kualove
            : null;
    };
    this.isGet = function(t) {
        var e = this.getCurCfg(t);
        return null == e ? 0 : e.get;
    };
    this.getCurHuoDong = function() {
        var t = i.limitActivityProxy.getHuodongList(
            i.limitActivityProxy.KUA_CHONG_BANG_TYPE
        );
        if (null == t) return null;
        for (var e = 0; e < t.length; e++)
            if (
                !(
                    n.timeUtil.second > t[e].showTime ||
                    n.timeUtil.second < t[e].sTime
                )
            )
                return t[e];
        return null;
    };
    this.getYuXuanCd = function(t) {
        var e = t - 7200;
        return Math.max(0, e - n.timeUtil.second);
    };
    this.getQingAn = function(t) {
        if (null == this.qingAn) return null;
        for (var e = 0; e < this.qingAn.length; e++) {
            var o = this.qingAn[e];
            if (null != o && t == o.id) return o;
        }
        return null;
    };
    this.getCurTop = function(t) {
        if (null == this.fengxiandianRankInfo) return null;
        for (var e = 0; e < this.fengxiandianRankInfo.length; e++) {
            var o = this.fengxiandianRankInfo[e];
            if (null != o && t == o.topKey) return o;
        }
        return null;
    };
    this.isShowMobai = function() {
        return (
            null != this.fengxiandianRankInfo &&
            this.fengxiandianRankInfo.length > 0
        );
    };
}
exports.CrossProxy = CrossProxy;
