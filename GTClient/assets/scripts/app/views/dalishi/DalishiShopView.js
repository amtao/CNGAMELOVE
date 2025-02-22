var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblMoney: cc.Label,
        lblAtkAdd: cc.Label,
        lblSkillAdd: cc.Label,
        lblHp: cc.Label,
        checkBox: cc.Toggle,
        prg: cc.ProgressBar,
        list: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(n.dalishiProxy.UPDATE_DALISHI_FIGHT, this.updateFight, this);
        this.updateFight();
        this.checkBox.isChecked = parseInt(n.timeProxy.getLoacalValue("DALISI_SHOP")) == n.dalishiProxy.info.qhid;
    },
    onClickCheck() {
        var t = this.checkBox.isChecked ? n.dalishiProxy.info.qhid: 0;
        n.timeProxy.saveLocalValue("DALISI_SHOP", t + "");
    },
    updateFight() {
        this.lblMoney.string = l.utils.formatMoney(n.dalishiProxy.fight.money);
        this.lblAtkAdd.string = i18n.t("DALISI_ATK_ADD", {
            d: n.dalishiProxy.fight.ackadd
        });
        this.lblSkillAdd.string = i18n.t("DALISI_SKILL_ADD", {
            d: n.dalishiProxy.fight.skilladd
        });
        this.lblHp.string = i18n.t("COMMON_NUM", {
            f: n.dalishiProxy.fight.hp,
            s: n.dalishiProxy.fight.hpmax
        });
        this.prg.progress = n.dalishiProxy.fight.hp / n.dalishiProxy.fight.hpmax;
        this.list.data = n.dalishiProxy.fight.shop;
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
