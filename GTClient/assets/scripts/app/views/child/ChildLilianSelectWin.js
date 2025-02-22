var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("ChildSpine");
cc.Class({
    extends: cc.Component,
    properties: {
        sonUrl:n,
        xingliUrl:n,
        chuxingUrl:n,
        lblcost:cc.Label,
        childSpine:a,
        childSpineSmall:a,
        jiahao:cc.Node,
        rightNode:cc.Node,
        headNode:cc.Node,

        time:cc.Label,
        diriction:cc.Label,
        gongli:cc.Label,

    },
    ctor() {
        this.cost = 0;
    },
    onLoad() {
        facade.subscribe("CHILD_LI_LIAN_SELECT_UPDATE", this.onSeletUpdate, this);
    },
    onClickSelectSon() {
        i.utils.openPrefabView("child/ChildLilianSonSelect");
    },
    onClickXingli() {
        l.sonProxy.lilianData.sid && 0 != l.sonProxy.lilianData.sid ? i.utils.openPrefabView("child/ChildLilianXingliSelect") : i.alertUtil.alert18n("SON_LI_LIAN_QING_XIAN_XUAN_ZE");
    },
    onClickFangShi() {
        l.sonProxy.lilianData.sid && 0 != l.sonProxy.lilianData.sid ? i.utils.openPrefabView("child/ChildLilianTravelSelect") : i.alertUtil.alert18n("SON_LI_LIAN_QING_XIAN_XUAN_ZE");
    },
    onClickStart() {
        if (0 == l.sonProxy.lilianData.sid) i.alertUtil.alert18n("SON_LI_LIAN_QING_XIAN_XUAN_ZE");
        else if (0 == l.sonProxy.lilianData.luggage) i.alertUtil.alert18n("SON_LI_LIAN_XUAN_ZE_XING_LI");
        else if (0 == l.sonProxy.lilianData.travel) i.alertUtil.alert18n("SON_LI_LIAN_XUAN_ZE_CHU_XING_FANG_SHI");
        else {
            if (l.playerProxy.userData.food < this.cost) {
                i.alertUtil.alertItemLimit(3);
                return;
            }
            l.feigeProxy.getSonFeige().length > 100 ? i.utils.showConfirm(i18n.t("SON_FEI_GE_SHAN_CHU"),
            function() {
                l.sonProxy.sendDeleteMail();
                l.sonProxy.sendLilianSon(l.sonProxy.lilianData.sid, l.sonProxy.lilianData.did, l.sonProxy.lilianData.luggage, l.sonProxy.lilianData.travel, l.playerProxy.userEp.e2);
            }) : l.sonProxy.sendLilianSon(l.sonProxy.lilianData.sid, l.sonProxy.lilianData.did, l.sonProxy.lilianData.luggage, l.sonProxy.lilianData.travel, l.playerProxy.userEp.e2);
            this.saveOneKeyLilian();
            i.utils.closeView(this);
        }
    },
    onSeletUpdate() {
        this.jiahao.active = 0 == l.sonProxy.lilianData.sid;
        this.rightNode.active = 0 != l.sonProxy.lilianData.sid;
        if (0 != l.sonProxy.lilianData.sid) {
            var t = l.sonProxy.getSon(l.sonProxy.lilianData.sid);
            this.childSpine.node.active = t.state > 3;
            this.childSpineSmall.node.active = t.state <= 3;
            t.state > 3 ? this.childSpine.setKid(t.id, t.sex) : this.childSpineSmall.setKid(t.id, t.sex, !1);
            this.headNode.x = -146;
        }
        this.cost = 0;
        if (0 != l.sonProxy.lilianData.luggage) {
            var e = localcache.getItem(localdb.table_practiceItem, l.sonProxy.lilianData.luggage);
            let min = e.min*60
            let max = e.max*120
            let lmin = e.min*100
            let lmax = e.max*100
            this.time.string = i18n.t("TIME__TIME",{
                mi: min,ma:max
            });
            this.gongli.string = i18n.t("GONGLI_GONGLI",{
                mi: lmin,ma:lmax
            });
            if (0 == e.itemid) {
                t = l.sonProxy.getSon(l.sonProxy.lilianData.sid);
                var o = Math.ceil(((30 * e.max) / Math.ceil(l.playerProxy.userEp.e2 / 800)) * 0.5 * l.playerProxy.userEp.e2 * t.talent * 0.3);
                this.cost += o;
                this.xingliUrl.url = r.uiHelps.getXingLiIcon(e.icon);
            } else this.xingliUrl.url = r.uiHelps.getItemSlot(e.itemid);
        }
        if (0 != l.sonProxy.lilianData.travel) {
            var i = localcache.getItem(localdb.table_practiceTravel, l.sonProxy.lilianData.travel);
            let string = i.direction === 0?i18n.t("COMMON_NULL"):i18n.t("TUDI_DI"+i.direction)
            this.diriction.string = string
            this.chuxingUrl.url = r.uiHelps.getChuXingIcon(i.id);
            this.cost += i.money;
        }
    },
    
    onClickClose() {
        i.utils.closeView(this);
    },
    saveLilian() {
        var t = l.timeProxy.getLoacalValue("CHILD_LI_LIAN_DATA"),
        e = JSON.parse(t);
        null == e && (e = {});
        e[l.sonProxy.lilianData.sid.toString()] = l.sonProxy.lilianData;
        l.timeProxy.saveLocalValue("CHILD_LI_LIAN_DATA", JSON.stringify(e));
    },
    saveOneKeyLilian() {
        var t = l.timeProxy.getLoacalValue("CHILD_ONE_KEY_LI_LIAN"),
        e = JSON.parse(t);
        null == e && (e = {});
        for (var o in e) null != e[o] && e[o].sid == l.sonProxy.lilianData.sid && (e[o] = null);
        e[l.sonProxy.lilianData.did] = l.sonProxy.lilianData;
        var i = JSON.stringify(e);
        l.timeProxy.saveLocalValue("CHILD_ONE_KEY_LI_LIAN", i);
    },
});
