var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        sonList: n,
        lvliList: n,
        lvliScroll: cc.ScrollView,
        tipNode: cc.Node,
        btnOneKeyLilian: cc.Node,
        yil:cc.Label,
        jil:cc.Label,
    },
    ctor() {
        this._sonList = [];
    },
    onLoad() {
        facade.subscribe("SON_LI_LIAN_LIST", this.onSonList, this);
        facade.subscribe("SON_LI_LIAN_SEAT", this.onSonList, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        this.sonList.selectHandle = function(t) {
            if (! (l.sonProxy.lilianSeat.desk + 1 < t.id)) if (l.sonProxy.lilianSeat.desk < t.id) {
                var e = localcache.getItem(localdb.table_practiceSeat, l.sonProxy.lilianSeat.desk + 1);
                i.utils.showConfirmItem(i18n.t("SON_LI_LIAN_JIE_SUO_XI_WEI", {
                    value: e.cost
                }), 1, l.playerProxy.userData.cash,
                function() {
                    var t = localcache.getItem(localdb.table_practiceSeat, l.sonProxy.lilianSeat.desk + 1);
                    l.playerProxy.userData.cash < t.cost ? i.alertUtil.alertItemLimit(1) : l.sonProxy.sendBuyLilianSeat();
                },
                "SON_LI_LIAN_JIE_SUO_XI_WEI");
            } else {
                var o = t.data;
                if (null == o || 0 == o.sid) {
                    l.sonProxy.lilianData = new proto_cs.son.liLianSon();
                    l.sonProxy.lilianData.sid = 0;
                    l.sonProxy.lilianData.travel = 0;
                    l.sonProxy.lilianData.luggage = 0;
                    l.sonProxy.lilianData.did = parseInt(t.id);
                    i.utils.openPrefabView("child/ChildLilianSelectWin");
                } else 0 == o.cd.next ? l.sonProxy.sendLilianReward(o.id, o.sid) : i.alertUtil.alert18n("SON_LI_LIAN_ZHENG_ZAI");
            }
        };
        l.sonProxy.sendInfoLilian();
        this.showLvli();
        this.schedule(this.onLvli, 60);
    },
    onSonList() {
        this._sonList = [];
        for (var t = 1; t < 7; t++) this._sonList.push({
            id: t,
            data: l.sonProxy.getLilianData(t)
        });
        this.sonList.data = this._sonList;
        let o = !1;
        if(null != l.sonProxy.lilianList) {
            for (var e = l.timeProxy.getLoacalValue("CHILD_ONE_KEY_LI_LIAN"), n = 0; n < l.sonProxy.lilianList.length; n++) if (l.sonProxy.lilianList[n].sid && 0 != l.sonProxy.lilianList[n].sid) {
                o = !0;
                break;
            }
        }
        this.showDirction()
        //i.stringUtil.isBlank(e) || o ? (this.btnOneKeyLilian.active = !1) : (this.btnOneKeyLilian.active = !0);
    },

    showDirction(){
        this.yil.string = i18n.t("LOOK_FOR_FATE_TODAY_CAN", {
            v: i18n.t("TUDI_DI"+l.sonProxy.lilianderction)
        })

        this.jil.string = i18n.t("LOOK_FOR_FATE_TODAY_NCAN", {
            v: i18n.t("TUDI_NDI"+l.sonProxy.lilianderction)
        })
    },
    onLvli() {
        for (var t = localcache.getList(localdb.table_practiceLvli), e = [], o = [], i = 0; i < t.length; i++) 1 == t[i].sex || 0 == t[i].sex ? e.push(t[i]) : (2 != t[i].sex && 0 != t[i].sex) || o.push(t[i]);
        if(null != l.sonProxy.lilianList) {
            for (i = 0; i < l.sonProxy.lilianList.length; i++) {
                var n = l.sonProxy.lilianList[i];
                if (n.sid) {
                    var r = l.sonProxy.getSon(n.sid),
                    a = null;
                    if (1 == r.sex) {
                        a = e[Math.floor(Math.random() * e.length)];
                    } else if (2 == r.sex) {
                        a = o[Math.floor(Math.random() * o.length)];
                    }
                    var s = {
                        name: r.name,
                        sys: a
                    };
                    l.sonProxy.lilianLvli.push(s);
                }
            }
        }
        this.showLvli();
    },
    showLvli() {
        this.tipNode.active = 0 == l.sonProxy.lilianLvli.length;
        var t = [];
        t = l.sonProxy.lilianLvli.length <= 25 ? l.sonProxy.lilianLvli: l.sonProxy.lilianLvli.splice(l.sonProxy.lilianLvli.length - 25, 25);
        this.lvliList.data = t;
        t.length > 8 && this.lvliScroll.scrollToBottom();
    },
    onClickClose() {
        i.utils.closeView(this, !0);
    },
    onClickOnekeyLilian() {
        if (l.playerProxy.userData.vip < 5) {
            // unlock recharge and vip --2020.07.21
            // i.alertUtil.alert18n("COMMON_NOT_OPEN");
            i.alertUtil.alert18n("SON_LI_LIAN_ONE_KEY_VIP_OPEN");
        } else {
            var t = l.timeProxy.getLoacalValue("CHILD_ONE_KEY_LI_LIAN")
            let olde = JSON.parse(t)||[]
            let addar = []
            let canCheck = []
            let empMap = {}
            for (let ee = 0; ee < l.sonProxy.childList.length; ee++) {
                let childm = l.sonProxy.childList[ee];
                childm.state > 0 && !l.sonProxy.isTraveling(childm.id) && 5 != childm.state && 6 != childm.state && 
                7 != childm.state && 10 != childm.state && (childm.spouse instanceof Array) && canCheck.push(childm);
            }
            let isposition = false
            let lilians = l.sonProxy.lilianList
            if(!lilians || l.sonProxy.lilianSeat.desk>lilians.length){
                lilians = lilians||[]
                for (let i = 1; i <= l.sonProxy.lilianSeat.desk ; i++) {
                    let isget = false
                    for (let j = 0; j < lilians.length; j++) {
                        if(lilians[j].id === i){
                            isget = true
                            break;
                        }
                        
                    }
                    if(!isget){
                        lilians.push({
                            id:i,
                            sid:0,
                            travel:1,
                            luggage:1,
                            localep2:0,
                        })
                    }
                    
                }
            }
            let len = lilians.length
            let index = 0
            let n = 0,r = 0,a = {};
            for (let i = 0; i < len; i++) {
                let cpmode = null;
                if(lilians[i].sid === 0 && olde[lilians[i].id]){
                    isposition = true
                    let id = lilians[i].id
                    cpmode = {};
                    cpmode.did = olde[id].did;
                    cpmode.sid = olde[id].sid;
                    cpmode.travel = olde[id].travel;
                    cpmode.luggage = olde[id].luggage;
                    cpmode.localep2 = l.playerProxy.userEp.e2;
                    empMap[olde[id].sid] = true
                    addar.push(cpmode)
                }else if( lilians[i].sid === 0 && canCheck.length>index){
                    if(empMap[canCheck[index].id]){
                        index++
                        continue;
                    }
                    isposition = true
                    cpmode = {};
                    cpmode.did = i+1
                    cpmode.sid = canCheck[index].id;
                    cpmode.travel = 1
                    cpmode.luggage = 1
                    cpmode.localep2 = l.playerProxy.userEp.e2;
                    addar.push(cpmode)
                    index++
                }
                if(cpmode){
                    let _ = localcache.getItem(localdb.table_practiceItem, cpmode.luggage);
                    if (null != _) {
                        if (0 == _.itemid) {
                            var d = l.sonProxy.getSon(cpmode.sid);
                            n += Math.ceil(((30 * _.max) / Math.ceil(l.playerProxy.userEp.e2 / 800)) * 0.5 * l.playerProxy.userEp.e2 * d.talent * 0.3);
                        } else a[_.itemid] += 1;
                        var u = localcache.getItem(localdb.table_practiceTravel, cpmode.travel);
                        null != u ? 1 == u.type ? (r += u.money) : 2 == u.type && (n += u.money) : i.alertUtil.alert(i18n.t("SON_LI_LIAN_CHU_XING_LIMIT", {
                            num: cpmode.travel
                        }));
                    } else i.alertUtil.alert(i18n.t("SON_LI_LIAN_XING_LI_LIMIT", {
                        num: cpmode.luggage
                    }));
                }
            }
            
            if(addar.length === 0){
                i.alertUtil.alert(i18n.t("SON_LI_LIAN_NO_SON"))
                return
            }
            if(!isposition){
                i.alertUtil.alert(i18n.t("SON_LI_LIAN_NO_SONPOSITION"))
                return
            }
            if (l.playerProxy.userData.cash < r){
                i.alertUtil.alertItemLimit(1);
            }else if (l.playerProxy.userData.food < n){
                i.alertUtil.alertItemLimit(3);
            }else {
                    var p = 0;
                    for (var h in a) if (l.bagProxy.getItemCount(h) < a[h]) {
                        p = parseInt(h);
                        break;
                    }
                    0 == p ? l.sonProxy.sendOneKeyLilian(addar) : i.alertUtil.alertItemLimit(p);
                }
            }
            return
















            // var t = l.timeProxy.getLoacalValue("CHILD_ONE_KEY_LI_LIAN"),
            // e = JSON.parse(t);
            // console.log(e)
            // let idmap = {}
            // if (null != e) {
            //     var o = [],
            //     n = 0,
            //     r = 0,
            //     a = {};
            //     for (var s in e) if (null != e[s]) {
            //         var c = {};
            //         c.did = e[s].did;
            //         c.sid = e[s].sid;
            //         c.travel = e[s].travel;
            //         c.luggage = e[s].luggage;
            //         c.localep2 = l.playerProxy.userEp.e2;
            //         o.push(c);
            //         idmap[c.sid] = true
            //         var _ = localcache.getItem(localdb.table_practiceItem, e[s].luggage);
            //         if (null != _) {
            //             if (0 == _.itemid) {
            //                 var d = l.sonProxy.getSon(e[s].sid);
            //                 n += Math.ceil(((30 * _.max) / Math.ceil(l.playerProxy.userEp.e2 / 800)) * 0.5 * l.playerProxy.userEp.e2 * d.talent * 0.3);
            //             } else a[_.itemid] += 1;
            //             var u = localcache.getItem(localdb.table_practiceTravel, e[s].travel);
            //             null != u ? 1 == u.type ? (r += u.money) : 2 == u.type && (n += u.money) : i.alertUtil.alert(i18n.t("SON_LI_LIAN_CHU_XING_LIMIT", {
            //                 num: e[s].travel
            //             }));
            //         } else i.alertUtil.alert(i18n.t("SON_LI_LIAN_XING_LI_LIMIT", {
            //             num: e[s].luggage
            //         }));
            //     }

            //     //luggage: 1, travel: 1, localep2: 7779

            //     if (l.playerProxy.userData.cash < r) i.alertUtil.alertItemLimit(1);
            //     else if (l.playerProxy.userData.food < n) i.alertUtil.alertItemLimit(3);
            //     else {
            //         var p = 0;
            //         for (var h in a) if (l.bagProxy.getItemCount(h) < a[h]) {
            //             p = parseInt(h);
            //             break;
            //         }
            //         return
            //         0 == p ? l.sonProxy.sendOneKeyLilian(o) : i.alertUtil.alertItemLimit(p);
            //     }
            // } else i.alertUtil.alert18n("SON_LI_LIAN_XIAN_AN_PAI");
        
    },
    onClickOneKeyGuilai() {
        if (l.playerProxy.userData.vip < 4) {
            // unlock recharge and vip --2020.07.21
            // i.alertUtil.alert18n("COMMON_NOT_OPEN");
            i.alertUtil.alert18n("SON_LI_LIAN_FINNISH_VIP_OPEN");
        } else {
            for (var t = !1,
            e = 0; e < l.sonProxy.lilianList.length; e++) l.sonProxy.lilianList[e].sid && 0 != l.sonProxy.lilianList[e].sid && (t = !0);
            t ? l.sonProxy.sendOneKeyLilianFinish() : i.alertUtil.alert18n("SON_LI_LIAN_NO_BODY");
        }
    },
});
