var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
let timeProxy = require("TimeProxy");

var SonProxy = function() {

    this.childList = null;
    this.actList = null;
    this.sonMarryList = null;
    this.unMarryList = null;
    this.zhaoQinData = null;
    this.tiQinData = null;
    this.tiQinObj = {
        tUid: 0,
        type: 0,
        tSid: 0,
        mySid: 0,
        marryType: 0
    };
    this.base = null;
    this.lilianData = null;
    this.renameId = null;
    this.lilianSeat = null;
    this.lilianList = null;
    this.lilianLvli = [];
    this.lilianSonData = null;
    this.keJuId = 0;
    this.UPDATE_SON_INFO = "UPDATE_SON_INFO";
    this.UPDATE_SON_SEAT = "UPDATE_SON_SEAT";
    this.UPDATE_SON_ZHAO_QIN = "UPDATE_SON_ZHAO_QIN";
    this.UPDATE_SON_TI_QIN = "UPDATE_SON_TI_QIN";
    this.UPDATE_SON_SHOW_ERRECT = "UPDATE_SON_SHOW_ERRECT";
    this.lilianCount = 0;

    this.lilianderction = 1

    this.ctor = function() {
        JsonHttp.subscribe(
            proto_sc.son.tdDrec,
            this.getlilianderction,
            this
        );
        JsonHttp.subscribe(proto_sc.son.base, this.onBase, this);
        JsonHttp.subscribe(proto_sc.son.sonList, this.onSonList, this);
        JsonHttp.subscribe(proto_sc.son.win, this.onSonWindow, this);
        JsonHttp.subscribe(proto_sc.son.firstkeju, this.onKeju, this);
        JsonHttp.subscribe(proto_sc.son.cList, this.onZhaoQinData, this);
        JsonHttp.subscribe(proto_sc.son.qList, this.onTiQinList, this);
        JsonHttp.subscribe(
            proto_sc.son.lilianList,
            this.onLilianList,
            this
        );
        JsonHttp.subscribe(
            proto_sc.son.lilianSeatNum,
            this.onLilianSeat,
            this
        );
    };
    this.getlilianderction = function(t){
        this.lilianderction = t.direction
    },
    this.clearData = function() {
        this.childList = null;
        this.actList = null;
        this.sonMarryList = null;
        this.unMarryList = null;
        this.zhaoQinData = null;
        this.tiQinData = null;
        this.base = null;
        this.renameId = null;
        this.lilianSeat = null;
        this.lilianList = null;
        this.lilianLvli = [];
        this.lilianSonData = null;
        this.lilianData = null;
        this.lilianCount = 0;
    };
    this.onBase = function(t) {
        this.base = t;
        facade.send(this.UPDATE_SON_SEAT);
    };
    this.onSonList = function(t) {
        null == this.childList
            ? (this.childList = t)
            : i.utils.copyList(this.childList, t);
        this.sonMarryList = [];
        this.actList = [];
        this.unMarryList = [];
        for (var e = 0; e < this.childList.length; e++) {
            var o = this.childList[e];
            switch (o.state) {
                case proto_sc.SomState.tName:
                case proto_sc.SomState.baby:
                case proto_sc.SomState.Child:
                case proto_sc.SomState.Student:
                    this.actList.push(o);
                    break;

                case proto_sc.SomState.loser:
                case proto_sc.SomState.request:
                case proto_sc.SomState.pass:
                case proto_sc.SomState.timeout:
                case proto_sc.SomState.ok:
                case proto_sc.SomState.requestAll:
                    this.unMarryList.push(o);
                    break;

                case proto_sc.SomState.huen:
                    this.sonMarryList.push(o);
            }
        }
        if (null != t && t.length > 0 && t[0].id == this.keJuId) {
            var son = this.getSon(this.keJuId);
            let ac = {child:2,cList:[son]}
            i.utils.openPrefabView("ChildShow", !1, ac);
            this.keJuId = 0;
        }
        let bRed = false;
        let time = null;
        for(let j = 0, len = this.actList.length; j < len; j++) {
            let sonData = this.actList[j];
            if(sonData.state == proto_sc.SomState.tName || sonData.power > 0) {
                bRed = true;
            } else if(sonData.cd.next > 0 && (null == time || time > sonData.cd.next)) {
                time = sonData.cd.next;
            }
        }
        i.timeUtil.addCountEvent(!bRed && null != time, time, "sonpow", () => {
            n.playerProxy.sendAdok("sonpow");
        });
        l.change("son", bRed && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.backView));
        facade.send(this.UPDATE_SON_INFO, t);
    };

    this.onSonWindow = function(t) {
        if (t.jiehun) {
            i.utils.openPrefabView(
                "marry/MarryEffectView",
                null,
                t.jiehun[0]
            );
            facade.send(this.UPDATE_SON_SHOW_ERRECT);
        }
        t.backitem && n.timeProxy.floatReward();
    };

    this.onKeju = function(t) {
        let ac = {child:2,cList:[t]}
        i.utils.openPrefabView("ChildShow", !1, ac);
    };

    this.onZhaoQinData = function(t) {
        this.zhaoQinData = t;
        facade.send(this.UPDATE_SON_ZHAO_QIN);
    };

    this.onTiQinList = function(t) {
        this.tiQinData = t;
        facade.send(this.UPDATE_SON_TI_QIN);
    };

    this.onLilianSeat = function(t) {
        this.lilianSeat = t;
        facade.send("SON_LI_LIAN_SEAT");
    };
    this.onLilianList = function(t) {
        this.lilianList = t.info;
        this.lilianCount = t.spCount;
        let time = null;
        for (var e = !1, o = 0; o < t.length; o++) {
            let data = t[o];
            if (0 != data.sid && 0 == data.cd.next) {
                e = !0;
                break;
            } else if(data.cd.next > 0 && (null == time || time > data.cd.next)) {
                time = data.cd.next;
            }
        }
        i.timeUtil.addCountEvent(!e && null != time, time, "sonLilian", () => {
            n.playerProxy.sendAdok("lilian");
        });
        l.change("sonLilian", e && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.backView));
        facade.send("SON_LI_LIAN_LIST");
    };
    this.sendSonName = function(t, e) {
        var o = new proto_cs.son.sonname();
        o.id = t;
        o.name = e;
        JsonHttp.send(o);
    };
    this.sendBuySeat = function() {
        var t = new proto_cs.son.buyseat();
        JsonHttp.send(t);
    };
    this.sendOnFood = function(t) {
        var e = new proto_cs.son.onfood();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendKeJu = function(t) {
        var e = new proto_cs.son.keju();
        e.id = t;
        this.keJuId = t;
        JsonHttp.send(e, function(t) {});
    };
    this.sendRname = function(t, e) {
        var o = new proto_cs.son.rname();
        o.id = t;
        o.name = e;
        JsonHttp.send(o);
    };

    this.sendChildRandomName = function(t) {
        var dd = new proto_cs.son.randSonName();
        dd.sonid = t;
        JsonHttp.send(dd);
    };

    this.sendPlay = function(t) {
        var e = new proto_cs.son.play();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendAllPlay = function() {
        var t = new proto_cs.son.allplay();
        JsonHttp.send(t);
    };
    this.sendAllFood = function() {
        var t = new proto_cs.son.allfood();
        JsonHttp.send(t);
    };
    this.sendRefreshZhaoQin = function(t, e) {
        var o = new proto_cs.son.rstzhaoqin();
        o.id = t;
        o.naqie = e;
        JsonHttp.send(o);
    };
    this.sendShuaXinZQ = function(t, e) {
        var o = new proto_cs.son.zhaoqin();
        o.id = t;
        o.naqie = e;
        JsonHttp.send(o);
    };
    this.sendJuJueTiQin = function(t, e) {
        var o = new proto_cs.son.pass();
        o.uid = t;
        o.sid = e;
        JsonHttp.send(o);
    };
    this.sendOneKeyJuJueTiQin = function() {
        var t = new proto_cs.son.allpass();
        JsonHttp.send(t);
    };
    this.sendRefreshTiQin = function() {
        var t = new proto_cs.son.getTiqin();
        JsonHttp.send(t);
    };
    this.sendAgree = function(t, e, o, n) {
        var l = new proto_cs.son.agree();
        l.uid = t;
        l.type = e;
        l.sid = o;
        l.mysid = n;
        JsonHttp.send(l, function() {
            i.utils.isOpenView("marry/MySonListView") &&
                i.utils.closeNameView("marry/MySonListView");
            i.utils.isOpenView("marry/BringUpRequestView") &&
                i.utils.closeNameView("marry/BringUpRequestView");
        });
    };
    this.sendJieHun = function(t, e, o, n) {
        var l = new proto_cs.son.jiehun();
        l.uid = t;
        l.type = e;
        l.sid = o;
        l.mysid = n;
        JsonHttp.send(l, function() {
            i.utils.isOpenView("marry/MySonListView") &&
                i.utils.closeNameView("marry/MySonListView");
            i.utils.isOpenView("marry/MarryGetView") &&
                i.utils.closeNameView("marry/MarryGetView");
        });
    };
    this.sendTiQin = function(t, e, o, i) {
        var n = new proto_cs.son.tiqin();
        n.uid = t;
        (n.type = e), (n.sid = o);
        n.ishonor = i;
        JsonHttp.send(n);
    };
    this.sendMeiPo = function() {
        JsonHttp.send(new proto_cs.son.meipo());
    };
    this.sendCancel = function(t) {
        var e = new proto_cs.son.cancel();
        e.id = t;
        JsonHttp.send(e);
    };
    this.sendInfoLilian = function() {
        JsonHttp.send(new proto_cs.son.intoLilian());
    };
    this.sendBuyLilianSeat = function() {
        JsonHttp.send(new proto_cs.son.buyLilianSeat());
    };
    this.sendLilianSon = function(t, e, o, i, n) {
        var l = new proto_cs.son.liLianSon();
        l.sid = t;
        l.did = e;
        l.luggage = o;
        l.travel = i;
        l.localep2 = n;
        JsonHttp.send(l);
    };
    this.sendLilianReward = function(t, e) {
        var o = new proto_cs.son.liLianReward();
        o.did = t;
        o.sid = e;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendDeleteMail = function() {
        JsonHttp.send(new proto_cs.son.delReadMail());
    };
    this.sendOneKeyLilian = function(t) {
        var e = new proto_cs.son.yjLiLianSon();
        e.arr = t;
        JsonHttp.send(e);
    };
    this.sendOneKeyLilianFinish = function() {
        JsonHttp.send(new proto_cs.son.yjLiLianReward(), function() {
            n.timeProxy.floatReward();
        });
    };
    this.getSon = function(t) {
        for (var e = 0; e < this.childList.length; e++)
            if (this.childList[e].id == t) return this.childList[e];
        return null;
    };
    this.getMarryList = function(t) {
        for (var e = new Array(), o = 0; o < this.childList.length; o++)
            t
                ? this.childList[o].sptime &&
                  this.childList[o].sptime >= 0 &&
                  e.push(this.childList[o])
                : (null == this.childList[o].sptime ||
                      null == this.childList[o].sptime ||
                      (0 == this.childList[o].sptime &&
                          4 == this.childList[o].state)) &&
                  e.push(this.childList[o]);
        return e;
    };
    this.getHonourStr = function(t) {
        return localcache.getItem(localdb.table_adult, t).name;
    };
    this.getUnMarryBySex = function(t) {
        for (var e = [], o = 0; o < this.unMarryList.length; o++)
            this.unMarryList[o].sex == t &&
                4 == this.unMarryList[o].state &&
                e.push(this.unMarryList[o]);
        return e;
    };
    this.getWifeSonNum = function(t) {
        for (var e = 0, o = 0; o < this.childList.length; o++)
            this.childList[o].mom == t && e++;
        return e;
    };
    this.getLilianData = function(t) {
        var e = null;
        if (this.lilianList)
            for (var o = 0; o < this.lilianList.length; o++)
                if (this.lilianList[o].id == t) {
                    e = this.lilianList[o];
                    break;
                }
        return e;
    };
    this.isTraveling = function(t) {
        var e = !1;
        if (this.lilianList)
            for (var o = 0; o < this.lilianList.length; o++)
                if (this.lilianList[o].sid == t) {
                    e = !0;
                    break;
                }
        return e;
    };
    this.getChengList = function() {
        var t = [];
        if (this.childList)
            for (var e = 0; e < this.childList.length; e++)
                this.childList[e].state > 3 && t.push(this.childList[e]);
        return t;
    };
    this.sendChildLilianAdok = function() {
        if (null != this.lilianList)
            for (var t = 0; t < this.lilianList.length; t++)
                if (
                    0 != this.lilianList[t].sid &&
                    this.lilianList[t].cd.next > 0
                ) {
                    this.sendInfoLilian();
                    break;
                }
    };

    /**
    *徒弟加速完成历练
    *@param sid 徒弟的唯一id
    *@param idx 座位序号
    */
    this.sendSpeedFinish = function(sid,idx){
        var e = new proto_cs.son.speedFinish();
        e.sid = sid;
        e.did = idx;
        JsonHttp.send(e);
    };
}
exports.SonProxy = SonProxy;
