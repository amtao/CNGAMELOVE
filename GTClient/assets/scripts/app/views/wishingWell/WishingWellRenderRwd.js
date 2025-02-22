var i = require("RenderListItem");
var n = require("List");
var initializer = require("Initializer");

cc.Class({
    extends: i,
    properties: {
        lblTitle: cc.Label,
        btnGet: cc.Button,
        btnYlq: cc.Node,
        list: n,
        progress: cc.ProgressBar,
        wishNum: cc.Label,
        wishingAllNum: cc.Label,
        wishPercent: cc.Label,
    },

    ctor() {},

    showData() {
        var data = this.data;
        if (data) {
            let allCons = initializer.wishingWellProxy.allCons;
            let cons = initializer.wishingWellProxy.cons;
            let all = data.cons.all;
            let user = data.cons.user;
            this.btnGet.node.active = 0 == data.isGet;
            this.btnGet.interactable = (allCons >= all && cons >= user)
            this.btnYlq.active = 1 == data.isGet;
            this.list.data = data.items;
            this.progress.progress = allCons / all;
            this.wishPercent.string = allCons + "/" + all;
            this.wishingAllNum.string = i18n.t("WISHING_WELL_RANK_ALL", {num: all});
            this.wishNum.string = i18n.t("WISHING_WELL_RANK_USER", {num: user});
        }
    },

    onClickGet() {
        var data = this.data;
        initializer.wishingWellProxy.sendLingQu(data.id);
    },
});
