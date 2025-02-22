var i = require("XianYunItem");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblXiwei: cc.Label,
        leftArrow: cc.Node,
        rightArrow: cc.Node,
        xianyunItems: [i],
        tipNode: cc.Node,
    },
    ctor() {
        this.index = 0;
    },
    onLoad() {
        facade.subscribe(l.xianyunProxy.XIAN_YUN_HERO_LIST, this.onXianYunHeroList, this);
        facade.subscribe(l.xianyunProxy.XIAN_YUN_DESK_INFO_LIST, this.updateDeskShow, this);
        facade.subscribe(l.xianyunProxy.XIAN_YUN_DESK_COUNT_UPDATE, this.onDeskCountUpdate, this);
        facade.subscribe("XIAN_YUN_AN_ZHI", this.onTips, this);
        l.xianyunProxy.sendOpenXianYun();
    },
    onXianYunHeroList() {},
    updateDeskShow() {
        if (null != l.xianyunProxy.deskCashList) {
            for (var t = 0; t < this.xianyunItems.length; t++) {
                var e = 4 * this.index + t + 1;
                this.xianyunItems[t].data = l.xianyunProxy.getDeskPrice(e);
            }
            var o = Math.floor(l.xianyunProxy.deskCashList.length / 4);
            this.leftArrow.active = this.index > 0;
            this.rightArrow.active = this.index < o;
            this.lblXiwei.string = i18n.t("XIAN_YUN_WEI_ZHI_TXT", {
                cur: l.xianyunProxy.heroList ? l.xianyunProxy.heroList.length: 0,
                total: l.xianyunProxy.base.desk
            });
        }
    },
    onClickTab(t, e) {
        var o = parseInt(e);
        this.index += o;
        this.updateDeskShow();
    },
    onClickAdd() {
        var t = l.xianyunProxy.getDeskPrice(l.xianyunProxy.base.desk + 1);
        n.utils.showConfirmItem(i18n.t("XIAN_YUN_KAI_QI_TXT", {
            num: t.cash
        }), 1, l.playerProxy.userData.cash,
        function() {
            l.playerProxy.userData.cash < t.cash ? n.alertUtil.alertItemLimit(1) : l.xianyunProxy.sendAddDesk();
        });
    },
    onDeskCountUpdate() {
        this.updateDeskShow();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onTips() {
        this.tipNode.active = !0;
    },
    onClsoeTip() {
        this.tipNode.active = !1;
        l.xianyunProxy.curSelectIndex = 0;
        l.xianyunProxy.curSelectHero = 0;
    },
    onClickSure() {
        l.xianyunProxy.sendFapei(l.xianyunProxy.curSelectHero, l.xianyunProxy.curSelectIndex);
        this.tipNode.active = !1;
    },
});
