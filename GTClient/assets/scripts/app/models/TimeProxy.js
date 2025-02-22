import { EFuncOpenType } from 'GameDefine';
import { alertUtil } from '../utils/Utils';
var i = require("Utils");
var n = require("Initializer");
var l = require("Config");
var r = require("BagProxy");
var a = require("ApiUtils");

var TimeProxy = function() {

    this.UPDATE_FLOAT_REWARD = "UPDATE_FLOAT_REWARD";
    this.UPDATE_CARD_FREE_RED = "UPDATE_CARD_FREE_RED";
    this.itemReward = null;
    this.noticeMsg = null;
    this.activityNoticeMsg = null;
    this.allReward = null;
    this.storySelect = null;
    this._flushTime = 0;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.system.sys, this.onServerTime, this);
        JsonHttp.subscribe(
            proto_sc.system.errror,
            this.onServerError,
            this
        );
        JsonHttp.subscribe(proto_sc.msgwin.items, this.onMsgItems, this);
        JsonHttp.subscribe(proto_sc.notice.listNew, this.onNotice, this);
        JsonHttp.subscribe(proto_sc.notice.list, this.onNotice, this);
        JsonHttp.subscribe(
            proto_sc.notice.activity,
            this.onActivityNotice,
            this
        );
        // JsonHttp.subscribe(
        //     proto_sc.loginMod.loginAccount,
        //     this.onAccountData,
        //     this
        // );
        facade.subscribe("ITEM_LIMIT_GO", this.showItemLimit, this);
    };
    this.clearData = function() {
        this.itemReward = null;
        this.allReward = null;
        this.noticeMsg = null;
        this.storySelect = null;
    };
    this.sendFlushZero = function() {
        var t = i.timeUtil.getTodaySecond(),
            e = i.timeUtil.second;
        if (!(this._flushTime > t || e - t > 60)) {
            this._flushTime = e;
            JsonHttp.send(new proto_cs.guide.flushZero());
        }
    };
    this.onServerTime = function(t) {
        i.timeUtil.setServerTime(t.time);
        facade.send(this.UPDATE_CARD_FREE_RED);
    };
    this.onServerError = function(t) {
        if (!i.stringUtil.isBlank(t.msg))
            if (i18n.has(t.msg))
                "LOGIN_YIDIDENGLU" == t.msg
                    ? i.utils.showConfirm(i18n.t(t.msg), function() {
                          n.loginProxy.loginOut();
                      })
                    : i.alertUtil.alert18n(t.msg);
            else if (
                -1 != t.msg.indexOf("RES_SHORT") &&
                t.msg.split("|").length > 1
            )
                i.alertUtil.alertItemLimit(parseInt(t.msg.split("|")[1]));
            else if (-1 != t.msg.indexOf("|")) {
                for (
                    var e = t.msg.split("|"), o = {}, l = 1;
                    l < e.length;
                    l++
                )
                    o["v" + l] = e[l];
                i.alertUtil.alert(e[0], o);
            } else i.alertUtil.alert(t.msg);
    };
    this.onMsgItems = function(t) {
        let tmpDic = {}
        if (t != null){
            for (let ii = 0; ii < t.length;ii++){
                let cg = t[ii];
                if (tmpDic[cg.kind] == null){
                    tmpDic[cg.kind] = {}
                }
                if (cg.id != null){
                    if (tmpDic[cg.kind][cg.id] == null){
                        tmpDic[cg.kind][cg.id] = 0;
                    }
                    tmpDic[cg.kind][cg.id] += cg.count;
                }
                if (cg.itemid != null){
                    if (tmpDic[cg.kind][cg.itemid] == null){
                        tmpDic[cg.kind][cg.itemid] = 0;
                    }
                    tmpDic[cg.kind][cg.itemid] += cg.count;
                }                   
                
            }
        }       
        let listdata = []
        for (let kind in tmpDic){
            for (let id in tmpDic[kind]){
                listdata.push({kind:Number(kind),id:Number(id),count:tmpDic[kind][id]})
            }
        }
        this.itemReward = listdata;
        console.log("this.itemReward is "+this.itemReward)
        facade.send(this.UPDATE_FLOAT_REWARD);
    };
    this.onNotice = function(t) {
        this.noticeMsg = t;
    };
    this.requestKvShow = function(){
        var huodong7001 = new proto_cs.huodong.hd7001List();
        JsonHttp.send(huodong7001, (data)=>{
            if(data.a && data.a.kvShow)
            this.activityNoticeMsg = data.a.kvShow.cfg.address;
        });
    };
    this.onActivityNotice = function(t) {
        // this.activityNoticeMsg = t;
    };
    this.getActivityNotice = function() {
        var t = [],
            e = n.loginProxy.pickServer.id;
        if(this.activityNoticeMsg && this.activityNoticeMsg.length > 0)
        {
            for(var i = 0;i < this.activityNoticeMsg.length;i++){
                let data = this.activityNoticeMsg[i];
                let pdata = {}
                pdata.pictureAddress = data.site;
                pdata.title1 = "";
                pdata.title2 = data.title;
                t.push(pdata);
            }
        }
        // if (this.activityNoticeMsg && this.activityNoticeMsg.length > 0)
        //     for (var o = 0; o < this.activityNoticeMsg.length; o++) {
        //         var i = this.activityNoticeMsg[o],
        //             l = i.sevid + "";
        //         if (-1 != l.indexOf("all")) t.push(i);
        //         else if (-1 != l.indexOf(","))
        //             for (
        //                 var r = i.sevid.split(","), a = 0;
        //                 a < r.length;
        //                 a++
        //             )
        //                 e == parseInt(r[a]) && t.push(i);
        //         else if (-1 != l.indexOf("-")) {
        //             var s = i.sevid.split("-");
        //             parseInt(s[0]) <= e && e <= parseInt(s[o]) && t.push(i);
        //         } else e == parseInt(i.sevid) && t.push(i);
        //     }
        return t;
    };
    this.floatReward = function(t, e) {
        void 0 === t && (t = !0);
        void 0 === e && (e = !1);
        if (null != this.itemReward && 0 != this.itemReward.length) {
            for (var o = 0; o < this.itemReward.length; o++) {
                var l = this.itemReward[o],
                    a =
                        l.count < 0
                            ? i18n.t("COMMON_ADD_2", {
                                  n: "",
                                  c: l.count
                              })
                            : i18n.t("COMMON_ADD", {
                                  n: "",
                                  c: l.count
                              });
                switch (l.kind) {
                    case r.DataType.HERO:
                        facade.send("UNLOCK_AUTO_LOOK", {
                            isFloat: 1
                        });
                        i.utils.openPrefabView(
                            "ServantShow",
                            !0,
                            n.servantProxy.getHeroData(l.id)
                        );
                        break;

                    case r.DataType.WIFE:
                        facade.send("UNLOCK_AUTO_LOOK", {
                            isFloat: 1
                        });
                        i.utils.openPrefabView(
                            "WifeShow",
                            !0,
                            n.wifeProxy.getWifeData(l.id)
                        );
                        break;

                    case r.DataType.NONE:
                    case r.DataType.ITEM:
                    case r.DataType.CHENGHAO:
                    case r.DataType.HUODONG:
                    case r.DataType.HEAD_BLANK:
                    case r.DataType.CLOTHE:
                    case r.DataType.USER_JOB:
                    case r.DataType.JB_ITEM:
                    case r.DataType.BAOWU_ITEM:
                    case r.DataType.BAOWU_SUIPIAN:
                    case r.DataType.CLUB_MONEY:
                    case r.DataType.CLUB_DONATE:
                    case r.DataType.CLUB_EXP:
                        if (l.id < 0) {
                            a =
                                n.playerProxy.getKindIdName(l.kind, l.id) +
                                a;
                            i.alertUtil.alert(a);
                        } else
                            e
                                ? this.addAllReward(l)
                                : i.utils.popView("AlertItemShow", l);
                        break;
                    case r.DataType.HOMEPART_W_206:
                        {
                            break;
                        }
                    case r.DataType.HOMEPART_F_205:
                    case r.DataType.HOMEPART_D_207:
                    case r.DataType.HOMEPART_J_208:
                        i.utils.popView("AlertItemShow", l)
                        break;
                    case r.DataType.ENUM_ITEM:
                        if (18 == l.id || 100 == l.id) continue;
                        if (l.id < 6 && t)
                            e
                                ? this.addAllReward(l)
                                : i.utils.popView("AlertItemShow", l);
                        else {
                            if(l.kind == 2 && l.id >= 20 && l.id <= 23) {
                                a = localcache.getItem(localdb.table_hero, n.servantProxy.lastHero).name
                                    + n.playerProxy.getKindIdName(l.kind, l.id) +
                                    a;
                            } else {
                                a = n.playerProxy.getKindIdName(l.kind, l.id) +
                                    a;
                            }
                            i.alertUtil.alert(a);
                        }
                        break;
                    case r.DataType.BUSINESS_ITEM:
                        e ? this.addAllReward(l) : i.utils.popView("AlertItemShow", l);
                        break;
                    case r.DataType.HERO_DRESS:{
                        e ? this.addAllReward(l): i.utils.popView("AlertItemShow", l);
                    }break;
                    default:
                        var s = n.playerProxy.getKindIdName(l.kind, l.id);
                        a = n.playerProxy.getKindIdName(l.kind, l.id) + a;
                        i.stringUtil.isBlank(s) || i.alertUtil.alert(a);
                        this.addAllReward(l);
                }
            }
            i.utils.popNext(!0);
            this.itemReward = null;
        }
    };
    this.addAllReward = function(t) {
        null == this.allReward && (this.allReward = []);
        for (var e = !1, o = 0; o < this.allReward.length; o++)
            if (this.allReward[o].id == t.id) {
                this.allReward[o].count += t.count;
                e = !0;
            }
        0 == e && this.allReward.push(t);
    };
    this.floatAllReward = function() {
        if (this.allReward)
            for (var t = 0; t < this.allReward.length; t++)
                i.utils.popView("AlertItemShow", this.allReward[t]);
        i.utils.popNext(!0);
        this.allReward = null;
    };
    this.getLoacalValue = function(t) {
        return cc.sys.localStorage.getItem(
            t + "_" + l.Config.serId + "_" + n.playerProxy.userData.uid
        );
    };
    this.saveLocalValue = function(t, e) {
        cc.sys.localStorage.setItem(
            t + "_" + l.Config.serId + "_" + n.playerProxy.userData.uid,
            e
        );
    };
    this.saveLocalAccount = function(t, e) {
        cc.sys.localStorage.setItem(t + "_account", e);
    };
    this.getLocalAccount = function(t) {
        return cc.sys.localStorage.getItem(t + "_account");
    };
    this.showItemLimit = function(t) {
        var e = t,
            l = 0;
        if (null != t.id) {
            e = t.id;
            l = t.count ? t.count : 0;
        }
        switch (e) {
            case 1:
                // unlock recharge and vip --2020.07.21
                this.openFunConfirm(e, exports.funUtils.recharge.id, l);
                // if (t.id == null)
                //     i.alertUtil.alert(i18n.t("COMMON_GOLD_NOT_ENOUGH"));
                break;

            case 2:
                this.openJingying(
                    n.jingyingProxy.coin.num,
                    e,
                    exports.funUtils.qifu,
                    l
                );
                break;

            case 3:
                this.openJingying(
                    n.jingyingProxy.food.num,
                    e,
                    exports.funUtils.qifu,
                    l
                );
                break;

            case 4:
                this.openJingying(
                    n.jingyingProxy.army.num,
                    e,
                    exports.funUtils.qifu,
                    l
                );
                break;

            case 5:
                this.openFunConfirm(e, exports.funUtils.zhengwuView, l);
                break;

            default:
                var r = n.shopProxy.isHaveItem(e, l);
                if (r) {
                    var a = r;
                    0 != l &&
                        (a = {
                            buy: r,
                            needCount: l
                        });
                    i.utils.openPrefabView("shopping/ShopBuy", !1, a);
                } else {
                    var s = localcache.getItem(localdb.table_item, e);
                    s && null != s.iconopen && s.iconopen.length > 0 && exports.funUtils.openView(s.iconopen[0].score);
                }
        }
    };
    this.openJingying = function(t, e, i, n) {
        // t > 0 || !exports.funUtils.isOpenFun(exports.funUtils.qifu)
        //     ? this.openFunConfirm(e, i.id, n) : 
        this.openFunConfirm(e, exports.funUtils.qifu.id, n);
    };
    this.openFunConfirm = function(t, e, l, r) {
        void 0 === l && (l = 0);
        void 0 === r && (r = null);
        var a = i18n.t("COMMON_LIMIT_GO", {
            n: n.playerProxy.getKindIdName(1, t),
            f: exports.funUtils.getFunName(e),
            c: 0 == l || null == l ? "" : l
        });
        i.utils.showConfirm(a, function() {
            exports.funUtils.openView(e, r);
        });
    };
    this.sendCDK = function(t) {
        var e = new proto_cs.recode.exchange();
        e.key = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.isSelectedStory = function(t) {
        if (null == this.storySelect) {
            var e = this.getLoacalValue("STORY_SELECTED");
            this.storySelect = JSON.parse(e);
        }
        null == this.storySelect && (this.storySelect = {});
        return 1 == this.storySelect[t];
    };
    this.saveSelectStory = function(t) {
        if (!this.storySelect || 1 != this.storySelect[t]) {
            null == this.storySelect && (this.storySelect = {});
            this.storySelect[t] = 1;
            this.saveLocalValue(
                "STORY_SELECTED",
                JSON.stringify(this.storySelect)
            );
        }
    };
    this.reqGetNotice = function(cb) {
        let req = new proto_cs.login.getNotice();
        JsonHttp.send(req, () => {
            cb && cb();
        });
    };
}
exports.TimeProxy = TimeProxy;

var FunUtils = function() {
    
    this.alls = null;
    this.setViewData = function() {
        this.alls = new Map();
        let viewList = localcache.getList(localdb.table_iconOpen);
        if (viewList == null || viewList.length <= 0){
            return false;
        }
        for(let i = 0, len = viewList.length; i < len; i++) {
            let data = viewList[i];
            if(null == data.name || "" == data.name) {
                continue;
            }
            if(typeof(data.param) == 'string') {
                data.param = (data.param == "" || data.param == null) ? null : JSON.parse(data.param);
            }
            
            this[data.name] = data;
            this.alls[data.id] = this[data.name];
        }
        return true;
    };
        
    this.openView = function(t, e) {
        void 0 === e && (e = null);
        if (!i.utils._isExit) {
            null == this.alls && this.init();
            var o = localcache.getItem(localdb.table_iconOpen, t);
            if (o) {
                if (
                    t == this.purchase.id &&
                    l.Config.version_code < l.Config.target_version_code
                ) {
                    a.apiUtils.open_download_url();
                    return;
                }
                if (!this.isOpen(o)) {
                    i.alertUtil.alert(o.errmsg);
                    if (!l.Config.DEBUG) return;
                }
                if (t == this.shopping.id) {
                    n.shopProxy.sendList();
                    return;
                }
                if (t == this.unionView.id) {
                    n.unionProxy.enterUnion();
                    // change new guide --2020.08.11
                    // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                    //     type: 4,
                    //     value: t + "|" + n.taskProxy.mainTask.id
                    // });
                    return;
                }
                if (
                    (r = this.alls[t]) &&
                    null != r.activityid &&
                    0 != r.activityid &&
                    !n.limitActivityProxy.isHaveTypeActive(r.activityid)
                ) {
                    i.alertUtil.alert(o.title + i18n.t("GAME_LEVER_UNOPENED"));
                    return;
                }
                if (r) {
                    i.utils.openPrefabView(r.url, !1, e || r.param);
                    // change new guide --2020.08.11
                    // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                    //     type: 4,
                    //     value: r.id + "|" + n.taskProxy.mainTask.id
                    // });
                    // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                    //     type: 10,
                    //     value: r.id
                    // });
                }
            } else {
                i.alertUtil.alert(i18n.t("COMMON_FUN_UNDEFINE") + t);
                if (l.Config.DEBUG) {
                    var r;
                    if ((r = this.alls[t])) {
                        i.utils.openPrefabView(r.url, !1, e || r.param);
                        // change new guide --2020.08.11
                        // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                        //     type: 4,
                        //     value: r.id + "|" + n.taskProxy.mainTask.id
                        // });
                        // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                        //     type: 10,
                        //     value: r.id
                        // });
                    }
                }
            }
        }
    };
    this.openViewUrl = function(t, e) {
        void 0 === e && (e = !1);
        if (!i.utils._isExit)
            if (i.stringUtil.isBlank(t))
                i.alertUtil.alert(i18n.t("MAIN_FUN_UNOPEN"));
            else {
                null == this.alls && this.init();
                for (var r in this.alls) {
                    var a = this.alls[r];
                    if (a && a.url == t) {
                        var s = localcache.getItem(
                            localdb.table_iconOpen,
                            a.id
                        );
                        if (
                            s &&
                            !this.isOpen(s) &&
                            s.id != exports.funUtils.chatView.id
                        ) {
                            if (s.errmsg)
                                i.alertUtil.alert(s.errmsg);
                            if (!l.Config.DEBUG) return;
                        }
                        if (a.id == this.unionView.id) {
                            n.unionProxy.enterUnion();
                            // change new guide --2020.08.11
                            // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                            //     type: 4,
                            //     value: a.id + "|" + n.taskProxy.mainTask.id
                            // });
                            return;
                        }
                        i.utils.openPrefabView(t, !1, null, e,null,false);
                        // change new guide --2020.08.11
                        // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
                        //     type: 4,
                        //     value: a.id + "|" + n.taskProxy.mainTask.id
                        // });
                        return;
                    }
                }
                i.utils.openPrefabView(t);
            }
    };
    this.isCanOpenViewUrl = function(t) {
        if (i.utils._isExit) return !0;
        if (i.stringUtil.isBlank(t)) {
            i.alertUtil.alert(i18n.t("MAIN_FUN_UNOPEN"));
            return !1;
        }
        null == this.alls && this.init();
        var e = this.getOpenFun(t);
        if (e && !this.isOpen(e)) {
            if (e.errmsg)
                i.alertUtil.alert(e.errmsg);
            return !1;
        }
        return !0;
    };
    this.getOpenFun = function(t) {
        for (var e in this.alls) {
            var o = this.alls[e];
            if (o && o.url == t) {
                return localcache.getItem(localdb.table_iconOpen, o.id);
            }
        }
        return null;
    };
    this.getNewOpenFunc = function(type, param) {
        let array = [];
        if(null == type || null == param) return null;
        let group = localcache.getGroup(localdb.table_iconOpen, "type", type);
        if(null == group) {
            return array;
        } 
        for(let i = 0, len = group.length; i < len; i++) {
            let data = group[i];
            if(group[i].pram == param) {
                array.push(data);
            }
        }
        return array;
    };
    this.isOpen = function(t) {
        if (null == t) return !0;
        switch (t.type) {
            case EFuncOpenType.LittleChapter:
                if (n.playerProxy.userData.mmap < t.pram) return !1;
                break;

            case EFuncOpenType.Level:
                if (n.playerProxy.userData.level < t.pram) return !1;
                break;

            case EFuncOpenType.ServantNum:
                if (
                    n.servantProxy.servantList &&
                    n.servantProxy.servantList.length < t.pram
                )
                    return !1;
                break;

            case EFuncOpenType.SonAdultNum:
                if (
                    n.sonProxy.unMarryList &&
                    n.sonProxy.unMarryList.length < t.pram &&
                    0 == n.sonProxy.sonMarryList.length
                )
                    return !1;
                break;

            case EFuncOpenType.Confidant:
                if (
                    n.wifeProxy.wifeList &&
                    n.wifeProxy.wifeList.length < t.pram
                )
                    return !1;
                break;

            case EFuncOpenType.ServantLevel:
                if (
                    n.servantProxy.servantList &&
                    n.servantProxy.servantList.length <
                        i.utils.getParamInt("gongdou_unlock_servant")
                )
                    return !1;
                for (
                    var e = i.utils.getParamInt("gongdou_unlock_level"), o = 0;
                    o < n.servantProxy.servantList.length;
                    o++
                )
                    if (n.servantProxy.servantList[o].level >= e) return !0;
                return !1;

            case EFuncOpenType.Chapter:
                if (n.playerProxy.userData.bmap < t.pram) return !1;
                break;

            case EFuncOpenType.MainTask:
                if (n.taskProxy.mainTask && n.taskProxy.mainTask.id <= t.pram)
                    return !1;
                else if(null == n.taskProxy.mainTask)
                    return !1;
        }
        return !0;
    };
    this.isOpenFun = function(t) {
        var e = localcache.getItem(localdb.table_iconOpen, t.id);
        return null == e || this.isOpen(e);
    };
    this.isOpenActivity = function(t) {
        if (null == t || 0 == t) return !1;
        var e = localcache.getItem(localdb.table_iconOpen, t);
        if (null == e) return !1;
        if (!this.isOpen(e)) return !1;
        null == this.alls && this.init();
        var o = this.alls[t];
        return !(
            o &&
            0 != o.activityid &&
            !n.limitActivityProxy.isHaveTypeActive(o.activityid)
        );
    };
    this.getWillOpen = function() {
        null == this.alls && this.init();
        for (var t in this.alls) {
            var e = this.alls[t],
                o = localcache.getItem(localdb.table_iconOpen, e.id);
            if (o && !this.isOpen(o)) return e;
        }
        return null;
    };
    this.getFunName = function(t) {
        var e = localcache.getItem(localdb.table_iconOpen, t);
        return e ? e.title : "";
    };
    this.init = function() {
        
    };
}
exports.FunUtils = FunUtils;

var FunItem = function() {
    this.id = 0;
    this.url = "";
    this.param = null;
    this.activityid = 0;
};
exports.FunItem = FunItem;
exports.funUtils = new FunUtils();
