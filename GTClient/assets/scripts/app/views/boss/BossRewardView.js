var i = require("BossRankItem");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lbltotalHaogan: cc.Label,
        rank_1: i,
        rank_2: i,
        rank_3: i,
        myHaogan: cc.Label,
    },
    ctor() {
        this.items = [];
    },
    onLoad() {
        facade.subscribe(n.bossPorxy.UPDATE_BOSS_HURTRANK, this.onRankData, this);
        facade.subscribe(n.bossPorxy.UPDATE_BOSS_GE2DANMYDMG, this.onMyData, this);
        var t = this.node.openParam;
        n.bossPorxy.sendG2dHitRank();
        if (t) {
            // if (0 == n.bossPorxy.ge2dan.damage || n.bossPorxy.ge2dan.damage == n.bossPorxy.ge2dan.allhp) {
            //     var e = localcache.getItem(localdb.table_hero, n.bossPorxy.ge2dan.heroId);
            //     this.lbltotalHaogan.string = i18n.t("BOSS_OVER_TXT_1", {
            //         name: e ? e.name: ""
            //     });
            // } else this.lbltotalHaogan.string = i18n.t("BOSS_OVER_TXT_2");
            // this.lbltotalHaogan.string = i18n.t("BOSS_REWARD_HAO_GAN", {
            //     num: t.score
            // });
            this.onRankData();
            this.onMyData();
        }
    },
    onRankData() {
        this.rank_1.data = n.bossPorxy.hurtRank[0];
        this.rank_2.data = n.bossPorxy.hurtRank[1];
        this.rank_3.data = n.bossPorxy.hurtRank[2];
    },
    onMyData() {
        this.myHaogan.string = "";
        if (n.bossPorxy.ge2danMyDmg) {
            this.myHaogan.string = i18n.t("BOSS_REWARD_HAO_GAN",{num:n.bossPorxy.ge2danMyDmg.g2dmydamage});
        }
    },
    onClickClose() {
        //this.items && this.items.length > 0 && l.utils.openPrefabView("boss/BossRewardItems", null, this.items);
        l.utils.closeView(this);
    },
});
