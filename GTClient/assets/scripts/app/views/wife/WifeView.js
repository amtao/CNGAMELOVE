var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        roleImage: cc.Sprite,
        nameImage: cc.Sprite,
        text_Instro: cc.Label,
        text_ChatContent: cc.Label,
        lblQinMi: cc.Label,
        lblZiSi: cc.Label,
        lblMeiLi: cc.Label,
        lblJingYan: cc.Label,
    },
    ctor() {},
    start() {
        facade.subscribe("WIFE_LIST_UPDATE", this.showWifeData, this);
        this.showWifeData();
    },
    showWifeData() {
        var t = localcache.getItem(localdb.table_wife, n.wifeProxy.curSelectWife.id);
        this.text_Instro.string = t.info;
        this.text_ChatContent.string = t.talk[0];
        this.lblQinMi.string = i18n.t("WIFE_QIN_MI_DU", {
            value: n.wifeProxy.curSelectWife.love
        });
        this.lblMeiLi.string = i18n.t("WIFE_MEILI", {
            value: n.wifeProxy.curSelectWife.flower
        });
        this.lblZiSi.string = i18n.t("WIFE_ZI_SI", {
            value: n.wifeProxy.curSelectWife.num
        });
        this.lblJingYan.string = i18n.t("WIFE_JING_YAN", {
            value: n.wifeProxy.curSelectWife.exp
        });
    },
    onClickLove() {
        var t = 10 * n.wifeProxy.curSelectWife.love > 1e3 ? 1e3: 10 * n.wifeProxy.curSelectWife.love,
        e = localcache.getItem(localdb.table_item, 1);
        i.utils.showConfirmItem(i18n.t("WIFE_XO_TIP", {
            name: e.name,
            price: t
        }), 1, n.playerProxy.userData.cash,
        function() {
            n.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : n.wifeProxy.sendXXOO(n.wifeProxy.curSelectWife.id);
        },
        "WIFE_XO_TIP");
    },
    onClickGift() {
        i.utils.openPrefabView("GiftView");
    },
    onClickSkill() {},
    onClickClose() {
        i.utils.closeView(this);
    },
});
