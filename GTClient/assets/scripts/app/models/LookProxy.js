var i = require("Initializer");
var n = require("RedDot");
var l = require("Utils");
var LookProxy = function() {

    this.UPDATE_XUNFANG_RECOVER = "UPDATE_XUNFANG_RECOVER";
    this.UPDATE_XUNFANG_WIN = "UPDATE_XUNFANG_WIN";
    this.UPDATE_XUNFANG_XFINFO = "UPDATE_XUNFANG_XFINFO";
    this.UPDATE_XUNFANG_ZHENZAI = "UPDATE_XUNFANG_ZHENZAI";
    this.recover = null;
    this.win = null;
    this.xfinfo = null;
    this.zhenzai = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.xunfang.recover, this.onRecover, this);
        JsonHttp.subscribe(proto_sc.xunfang.win, this.onWin, this);
        JsonHttp.subscribe(proto_sc.xunfang.xfInfo, this.onXfInfo, this);
        JsonHttp.subscribe(proto_sc.xunfang.zhenZai, this.onZhenzai, this);
    };
    this.clearData = function() {
        this.recover = null;
        this.win = null;
        this.xfinfo = null;
        this.zhenzai = null;
    };
    this.onRecover = function(t) {
        this.recover = t;
        facade.send(this.UPDATE_XUNFANG_RECOVER);
    };
    this.onWin = function(t) {
        if (null == this.win) this.win = t;
        else {
            this.win.xfAll = t.xfAll ? t.xfAll : this.win.xfAll;
            this.win.yunsi = t.yunsi ? t.yunsi : this.win.yunsi;
        }
        null != t.xfAll && facade.send(this.UPDATE_XUNFANG_WIN);
    };
    this.onXfInfo = function(t) {
        this.xfinfo = t;
        n.change(
            "xunfang",
            this.xfinfo.num > 0 && i.playerProxy.userData.level > 5
        );
        facade.send(this.UPDATE_XUNFANG_XFINFO);
    };
    this.onZhenzai = function(t) {
        this.zhenzai = t;
        facade.send(this.UPDATE_XUNFANG_ZHENZAI);
    };
    this.sendRecover = function(t) {
        void 0 === t && (t = 1);
        var e = new proto_cs.xunfang.recover();
        e.type = t;
        JsonHttp.send(e);
    };
    this.sendXunfan = function(t) {
        var e = new proto_cs.xunfang.xunfan();
        e.type = t;
        JsonHttp.send(e, function() {
            i.timeProxy.floatReward();
        });
    };
    this.sendYunshi = function(t, e, o) {
        var i = new proto_cs.xunfang.yunshi();
        i.auto2 = t;
        i.auto3 = e;
        i.ysSet = o;
        JsonHttp.send(i, function() {
            l.alertUtil.alert18n("LOOK_SAVE_LOG_TIP");
        });
    };
    this.sendZzHand = function(t) {
        var e = new proto_cs.xunfang.zzHand();
        e.type = t;
        JsonHttp.send(e);
    };
    this.getString = function(t) {
        var e = "";
        switch (t.type) {
            case 1:
            case 2:
                for (var o = 0; o < t.items.length; o++) {
                    e += 0 == o ? "" : "，";
                    e += i18n.t("COMMON_ADD", {
                        n: i.playerProxy.getKindIdName(1, t.items[o].id),
                        c: t.items[o].count
                    });
                }
                break;

            case 3:
            case 5:
            case 4:
                var n = localcache.getItem(localdb.table_look, t.npcid);
                localcache.getItem(localdb.table_wife, n.wfid) &&
                    (e = i18n.t("LOOK_ADD_" + t.type, {
                        n: i.playerProxy.getWifeName(n.wfid)
                    }));
                break;

            case 6:
                n = localcache.getItem(localdb.table_look, t.npcid);
                var l = localcache.getItem(localdb.table_hero, n.wfid);
                l &&
                    (e = i18n.t("LOOK_ADD_" + t.type, {
                        n: l.name
                    }));
                break;
        }
        return e;
    };

    /**获取出城随机城市的文字*/
    this.getBaoWuText = function(buildId){
        let listCfg = localcache.getList(localdb.table_baowuText);
        let allWeight = 0;
        let cList = [];
        let weightList = [];
        let weightNum = 0;
        for (let ii = 0; ii < listCfg.length;ii++){
            let cg = listCfg[ii];           
            if (cg.textType == 0 || cg.textType == buildId){
                cList.push(cg);
                allWeight += cg.textWeight;               
                weightList.push({last:weightNum,cur:weightNum+cg.textWeight})
                weightNum += cg.textWeight;
            }
        }
        let randomIdx = Math.ceil(Math.random() * allWeight);
        for (let ii = 0; ii < weightList.length;ii++){
            let cg = weightList[ii];
            if (randomIdx >= cg.last && randomIdx < cg.cur){
                return cList[ii].baowuText;
            }
        }
        return "";
    };

    this.isLock = function(t) {
        return (
            0 == t.unlock ||
            (1 == t.unlock
                ? this.isOpen(t.uk_para)
                : 2 == t.unlock
                ? i.taskProxy.mainTask.id > t.uk_para
                : 3 == t.unlock
                ? i.playerProxy.userData.bmap < t.uk_para
                : 4 == t.unlock &&
                  i.playerProxy.userData.level >= t.uk_para)
        );
    };
    this.isOpen = function(t) {
        var e = localcache.getItem(localdb.table_lookBuild, t);
        return !!e && e.lock < i.playerProxy.userData.bmap;
    };
}
exports.LookProxy = LookProxy;
