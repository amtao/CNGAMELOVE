var i = require("UrlLoad");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
var s = require("DalishiServantItem");
cc.Class({
    extends: cc.Component,
    properties: {
        servant: i,
        lblEnemyName: cc.Label,
        lblEnemyCount: cc.Label,
        lblScore: cc.Label,
        lblServant: cc.Label,
        lblZZ: cc.Label,
        lblAtk: cc.Label,
        lblSkill: cc.Label,
        prg: cc.ProgressBar,
        lblPrg: cc.Label,
        head: i,
        items: [s],
    },
    ctor() {
        this._curSelect = !1;
    },
    onLoad() {
        facade.subscribe("UPDATE_DALISHI_ZHUISHA", this.onUpdateZhuisha, this);
        facade.subscribe("UPDATE_DALISHI_FIGHT", this.onUpdateFight, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickBack, this);
        this.onUpdateFight();
        this.onUpdateZhuisha();
        var t = r.dalishiProxy.info;
        //t && t.fuser && this.head.setUserHead(t.fuser.job, t.fuser.headavatar);
        this.lblEnemyName.string = t.fuser ? t.fuser.name: "";
        2 == r.dalishiProxy.fight.fstate && n.utils.openPrefabView("dalishi/AwardDView");
        t && t.fuser && r.playerProxy.loadUserHeadPrefab(this.head,t.fuser.headavatar,{job:t.fuser.job,level:t.fuser.level,clothe:t.fuser.clothe},false); 
    },
    onUpdateZhuisha() {
        this.lblScore.string = i18n.t("DALISI_SCORE", {
            d: r.dalishiProxy.zhuisha ? r.dalishiProxy.zhuisha.score: 0
        });
    },
    onUpdateFight() {
        if (0 != r.dalishiProxy.fight.hid) {
            for (var t = 0; t < this.items.length; t++) this.items[t].data = r.dalishiProxy.fight.fheros.length > t ? r.dalishiProxy.fight.fheros[t] : null;
            var e = r.dalishiProxy.fight.fheronum - r.dalishiProxy.fight.killnum;
            e = e < 0 ? 0 : e;
            this.lblEnemyCount.string = i18n.t("DALISI_SERVANT_NUM", {
                v1: e,
                v2: r.dalishiProxy.fight.fheronum
            });
            var o = r.servantProxy.getHeroData(r.dalishiProxy.fight.hid),
            i = localcache.getItem(localdb.table_hero, r.dalishiProxy.fight.hid);
            this.lblServant.string = i18n.t("DALISI_NAME_SERVANT", {
                n: i ? i.name: "",
                d: o ? o.level: 1
            });
            this.lblZZ.string = i18n.t("SERVANT_ZHZZ", {
                zz: o ? o.zz.e1 + o.zz.e4 + o.zz.e3 + o.zz.e2: 0
            });
            this.lblAtk.string = i18n.t("DALISI_ATK_ADD", {
                d: r.dalishiProxy.fight.ackadd
            });
            this.lblSkill.string = i18n.t("DALISI_SKILL_ADD", {
                d: r.dalishiProxy.fight.skilladd
            });
            this.lblPrg.string = i18n.t("COMMON_NUM", {
                f: r.dalishiProxy.fight.hp,
                s: r.dalishiProxy.fight.hpmax
            });
            this.prg.progress = r.dalishiProxy.fight.hp / r.dalishiProxy.fight.hpmax;
            this.servant.url = l.uiHelps.getServantHead(r.dalishiProxy.fight.hid);
            this._curSelect = !1;
        }
    },
    onClickClost() {
        n.utils.closeView(this);
        n.utils.closeNameView("dalishi/DalishiView");
    },
    onClickBack() {
        n.utils.closeView(this);
    },
    onClickServant(t, e) {
        var o = this,
        i = e.data;
        if (i && !this._curSelect) if (null == r.timeProxy.getLoacalValue("DALISI_ATTACT_CONFIRM")) {
            n.utils.showConfirm(i18n.t("DALISI_ATTACT_CONFIRM"),
            function() {
                o._curSelect = !0;
                r.dalishiProxy.sendFight(i.id);
            });
            r.timeProxy.saveLocalValue("DALISI_ATTACT_CONFIRM", "1");
        } else {
            this._curSelect = !0;
            r.dalishiProxy.sendFight(i.id);
        }
    },
    onClickUser() {
        var t = r.dalishiProxy.info.fuser;
        t && r.playerProxy.sendGetOther(t.uid);
    },
    onClickAdd() {
        n.utils.openPrefabView("dalishi/ShopDView");
    },
});
