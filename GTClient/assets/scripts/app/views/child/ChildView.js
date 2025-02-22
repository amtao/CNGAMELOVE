var i = require("Utils");
var n = require("UIUtils");
var l = require("Initializer");
var r = require("List");
var a = require("UrlLoad");
var s = require("StateImg");
var c = require("ServantStarShow");
var _ = require("ChildSpine");
let stars = require("stars");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblLevel: cc.Label,
        lblExp: cc.Label,
        lblTime: cc.Label,
        lblAllProp: cc.Label,
        lblEp1: cc.Label,
        lblEp2: cc.Label,
        lblEp3: cc.Label,
        lblEp4: cc.Label,
        lblMonther: cc.Label,
        lblLover: cc.Label,
        lblChildNum: cc.Label,
        list: r,
        nodeRename: cc.Node,
        nodeFeed: cc.Node,
        nodeInfo: cc.Node,
        nodeChildName: cc.Node,
        nodeLimit: cc.Node,
        nodeResume: cc.Node,
        nodeKeju: cc.Node,
        prg: cc.ProgressBar,
        lblSex: cc.Label,
        lblHuoLi_Time: cc.Label,
        starShow: c,
        checkPeiYang: cc.Toggle,
        checkHuiFu: cc.Toggle,
        childSpine: _,

        starsCom:stars,
        lbchildn:cc.Label,
        leftNode:cc.Node
    },
    ctor() {},
    onLoad() {
        facade.subscribe(l.sonProxy.UPDATE_SON_INFO, this.updateSonInfo, this);
        facade.subscribe(l.sonProxy.UPDATE_SON_SEAT, this.updateSeat, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        this.prg.progress = 0;
        var t = this;
        this.list.selectHandle = function(e) {
            t.updateSon();
        };
        l.sonProxy.actList.sort(this.sortChild);
        this.updateSeat();
        this.list.selectIndex = 0;


        // this.scheduleOnce(()=>{
        //     i.utils.openPrefabView("union/FlowerPot");
        // },1)


    },
    updateSonInfo() {
        this.updateSeat();
        this.list.selectIndex = this.list.selectIndex;
    },
    updateSeat() {
        var t = [],
        e = l.sonProxy.base.seat;
        if (l.sonProxy.actList) for (var o = 0; o < l.sonProxy.actList.length; o++) t.push(l.sonProxy.actList[o]);
        for (o = t.length; o < e; o++) t.push({});
        localcache.getItem(localdb.table_seat, e + 1) && t.push({
            isLock: !0
        });
        this.list.data = t;
        this.lblChildNum.string = i18n.t("SON_SEAT_NUM", {
            value1: l.sonProxy.actList.length,
            value2: e
        });
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
    updateSon() {
        var t = this.list.selectData;
        if (t && null != t.sex) {
            t = l.sonProxy.getSon(t.id);
            this.starsCom.setValue(t.talent)
            this.nodeInfo.active = !0;
            this.nodeChildName.active = !0;
            this.nodeLimit.active = !1;
            this.leftNode.active = 1
            var e = localcache.getItem(localdb.table_minor, t.talent),
            o = localcache.getItem(localdb.table_lvUp, t.level),
            r = localcache.getItem(localdb.table_vip, l.playerProxy.userData.vip),
            a = l.servantProxy.getHeroData(t.mom),
            s = t.exp / o.exp;
            t.name == ""? this.lbchildn.string = i18n.t("SON_NAME_NEED"):this.lbchildn.string = t.name
            this.lblLevel.string = i18n.t("SON_LEVEL", {
                l: t.level,
                m: e.level_max
            });

            this.lblLevel.string = t.level + '/' + e.level_max
            // i18n.t("SON_LEVEL", {
            //     l: t.level,
            //     m: e.level_max
            // });

            this.nodeFeed.active = this.checkPeiYang.node.active = (t.state == proto_sc.SomState.baby || t.state == proto_sc.SomState.Child) && t.power > 0;
            this.nodeResume.active = this.checkHuiFu.node.active = (t.state == proto_sc.SomState.baby || t.state == proto_sc.SomState.Child) && 0 == t.power;
            this.nodeKeju.active = t.state == proto_sc.SomState.Student;
            this.childSpine.setKid(t.id, t.sex, !1);
            this.lblExp.string = i18n.t("COMMON_NUM", {
                f: t.exp,
                s: o.exp
            });
            if (t.state != proto_sc.SomState.Student) {
                if (this.prg.progress != s) {
                    var c = this;
                    n.uiUtils.showPrgChange(this.prg, this.prg.progress, 0 == s ? 0 : s, 1, 5,
                    function() {
                        c.prg.progress = s;
                    });
                }
            } else {
                this.prg.progress = 1;
                this.lblExp.string = "";
            }
            t.power < r.sonpow ? n.uiUtils.countDown(t.cd.next, this.lblTime,
            function() {
                t.cd.label = "sonpow";
                l.playerProxy.sendAdok(t.cd.label);
            },
            0 == t.power) : this.lblTime.unscheduleAllCallbacks();
            if (t.power > 0) {
                this.lblTime.string = i18n.t("COMMON_NUM", {
                    f: t.power,
                    s: r.sonpow
                });
                this.lblHuoLi_Time.string = i18n.t("SON_CUR_HUO_LI");
            } else {
                this.lblHuoLi_Time.string = i18n.t("SON_HUI_FU_TIME");
            }
            //this.stateImg.total = r.sonpow;
            //this.stateImg.value = t.power;
            this.nodeRename.active = t.state == proto_sc.SomState.tName;
            this.lblSex.string = 1 == t.sex ? i18n.t("CREATE_NAN") : i18n.t("CREATE_NV");
            this.nodeRename.active ? (this.lblName.string = i18n.t("SON_NAME_NEED")) : (this.lblName.string = t.name);
            this.lblAllProp.string = "" +  l.jibanProxy.getHeroJB(t.mom)
            console.log(t)
            this.lblEp1.string = t.ep.e1 + "";
            this.lblEp2.string = t.ep.e2 + "";
            this.lblEp3.string = t.ep.e3 + "";
            this.lblEp4.string = t.ep.e4 + "";
            this.lblLover.string = a.love + "";
            this.lblMonther.string = l.playerProxy.getWifeName(t.mom);
            this.starShow.setValue(t.talent);
            // change new guide --2020.08.11
            //var d = localcache.getItem(localdb.table_hero, t.mom);
            //0 == l.sonProxy.getChengList().length && t.state == proto_sc.SomState.Student && facade.send(l.guideProxy.UPDATE_TRIGGER, 15e3);
        } else {
            this.childSpine.clearKid();
            this.nodeChildName.active = !1;
            this.nodeInfo.active = !1;
            this.nodeLimit.active = !0;
            this.leftNode.active = !1
        }
    },
    onClickLvUp() {
        var t = this.list.selectData;
        t && null != t.sex && (this.checkPeiYang.isChecked ? l.sonProxy.sendAllPlay() : l.sonProxy.sendPlay(t.id));
    },
    onClickWeiShi() {
        var t = this,
        e = this.list.selectData;
        if ((e = l.sonProxy.getSon(e.id)).power <= 0) {
            var o = i.utils.getParamInt("zs_cost_item_hl");
            i.utils.showConfirmItem(
                i18n.t("SON_RESUME_CONFIRM",{
                t: n.uiUtils.getItemNameCount(o, 1)}), 
                o, 
                l.bagProxy.getItemCount(o),
                function() {
                    l.bagProxy.getItemCount(o) <= 0 ? i.alertUtil.alertItemLimit(o) : t.checkHuiFu.isChecked ? l.sonProxy.sendAllFood() : l.sonProxy.sendOnFood(e.id);
                },
                "SON_RESUME_CONFIRM"
            );
        }
    },

    onClickName() {
        var t = this.list.selectData;
        if (t && null != t.sex) {
            l.sonProxy.renameId = t.id;
            i.utils.openPrefabView("child/RenameView",null,{
                heroname: l.playerProxy.getWifeName(t.mom),
                heroid: t.mom,
            });
        }
    },

    onClickKeju() {
        var t = this.list.selectData;
        if (t && null != t.sex) {
            // change new guide --2020.08.11
            //0 == l.sonProxy.getChengList().length && t.state == proto_sc.SomState.Student && facade.send(l.guideProxy.UPDATE_TRIGGER, 15001);
            l.sonProxy.sendKeJu(t.id);
        }
    },

    onClickGoTo() {
        i.utils.closeNameView("main/HouGong");
        i.utils.closeView(this);
        i.utils.openPrefabView("servant/ServantLobbyView");
    },

    sortChild(t, e) {
        return e.level - t.level;
    },
});
