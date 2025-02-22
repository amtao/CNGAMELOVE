var i = require("List");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("Utils");
var a = require("SacrificeItem");
var s = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTime: cc.Label,
        records: i,
        spellList: [a],
        scroll: cc.ScrollView,
        lblRwd: cc.Node,
        lblNoRwd: cc.Node,
        lblNum: cc.Label,
        tipNode: cc.Node,
        lblBall: cc.Label,
        btnSacrifice: cc.Button,
        noSacrificeNode: cc.Node,
        btnLinQu: cc.Button,
        jiangli: cc.Sprite,
        animationNode: cc.Node,
    },
    ctor() {
        this._oldNum = 0;
    },
    onLoad() {
        facade.subscribe(n.zhongyuanProxy.SCARIFICE_DATA_UPDAT, this.onSpellData, this);
        facade.subscribe(n.zhongyuanProxy.SCARIFICE_DATA_RECORDS, this.onRecords, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        l.uiUtils.scaleRepeat(this.tipNode, 0.9, 1.1);
        l.uiUtils.scaleRepeat(this.lblRwd, 0.9, 1.1);
        this.lblRwd.active = !(this.lblNoRwd.active = !0);
        s.shaderUtils.setImageGray(this.jiangli);
        this.onItemUpdate();
        this.onSpellData();
    },
    onSpellData() {
        if (null != n.zhongyuanProxy.data) {
            var t = this;
            l.uiUtils.countDown(n.zhongyuanProxy.data.info.eTime, this.lblTime,
            function() {
                t.lblTime.string = i18n.t("ACTHD_OVERDUE");
            });
            for (var e = 0; e < n.zhongyuanProxy.data.debris.length; e++) this.spellList[e].data = n.zhongyuanProxy.data.debris[e];
            this.noSacrificeNode.active = !(this.btnSacrifice.interactable = n.zhongyuanProxy.isEnough());
            var o = JSON.stringify(n.zhongyuanProxy.data.debris);
            n.timeProxy.saveLocalValue("sacrifice", o);
            this.lblBall.string = n.bagProxy.getItemCount(n.zhongyuanProxy.data.need) + "";
            this.tipNode.active = 0 == n.bagProxy.getItemCount(n.zhongyuanProxy.data.need);
        }
    },
    onRecords() {
        this.records.data = n.zhongyuanProxy.records;
        this.scroll.scrollToBottom();
    },
    onClickSacrifice() {
        this.btnSacrifice.interactable = !1;
        for (var t = 0; t < n.zhongyuanProxy.data.debris.length; t++) {
            n.zhongyuanProxy.data.debris[t].num--;
            this.spellList[t].data = n.zhongyuanProxy.data.debris[t];
        }
        this.showNodeAnimation1();
    },
    showNodeAnimation1() {
        this.animationNode.getComponent(cc.Animation) && r.utils.showNodeEffect(this.animationNode, 0);
        this.scheduleOnce(this.showNodeAnimation2, 0.8);
    },
    onUpdateRewardBtn() {
        s.shaderUtils.setImageGray(this.jiangli, !1);
        this.btnLinQu.interactable = !0;
        this.lblRwd.active = !(this.lblNoRwd.active = !1);
    },
    showNodeAnimation2() {
        this.animationNode.getComponent(cc.Animation) && r.utils.showNodeEffect(this.animationNode, 1);
        this.scheduleOnce(this.onUpdateRewardBtn, 0.15);
    },
    onClickAdd() {
        r.utils.openPrefabView("ActivitySpecialBuy", null, {
            data: n.zhongyuanProxy.shop[0],
            activityId: n.zhongyuanProxy.data.info.id
        });
        n.shopProxy.openShopBuy(n.zhongyuanProxy.data.need);
    },
    onClickRwd() {
        n.zhongyuanProxy.sendRwd();
        this.lblRwd.active = !(this.lblNoRwd.active = !0);
        s.shaderUtils.setImageGray(this.jiangli);
        this.btnLinQu.interactable = !1;
        this.btnSacrifice.interactable = !0;
    },
    onClickBox() {
        if (0 != n.bagProxy.getItemCount(1040)) {
            var t = {
                id: 1040
            };
            r.utils.openPrefabView("bag/BagUse", !1, t);
        } else r.alertUtil.alertItemLimit(1040);
    },
    onItemUpdate() {
        var t = n.bagProxy.getItemCount(1040);
        this._oldNum > t && n.zhongyuanProxy.sendInfo();
        this.lblNum.string = t + "";
        this._oldNum = t;
        if (n.zhongyuanProxy.data) {
            var e = n.bagProxy.getItemCount(n.zhongyuanProxy.data.need);
            this.tipNode.active = 0 == e;
            this.lblBall.string = e + "";
        }
    },
    onClickClose() {
        r.utils.closeView(this);
    },
});
