var i = require("Utils");
var n = require("Initializer");
var l = require("UrlLoad");
var r = require("UIUtils");
var a = require("CellHead");
var s = require("List");
var c = require("LangSprite");
cc.Class({
    extends: cc.Component,
    properties: {
        prisoner: l,
        lblName: cc.Label,
        hp: cc.ProgressBar,
        lblCount: cc.Label,
        lblSw: cc.Label,
        lblDaily: cc.Label,
        lblHp: cc.Label,
        checkBox: cc.Toggle,
        btnSp: [cc.Sprite],
        sprites: [cc.SpriteFrame],
        cellHeads: [a],
        nodeBg: cc.Node,
        changeSp: sp.Skeleton,
        baojiSp: sp.Skeleton,
        heartSp: sp.Skeleton,
        nodeItem: cc.Node,
        addItem: cc.Node,
        lblNames: [cc.Label],
        petList: s,
        icons: [cc.Sprite],
        iconFarme: [cc.SpriteFrame],
        lblLv: cc.Label,
        lblPro: cc.Label,
        lvUpEff: c,
        lblCost: cc.Label,
        proUrl: l,
        pro5: cc.Node,
        costMoney: cc.Label,
    },
    ctor() {
        this._lastHp = -1;
        this._lastHit = -1;
        this._petList = [];
        this._oldLv = 0;
        this._costMoney = 0;
        this._costFood = 0;
    },
    onLoad() {
        facade.subscribe(n.cellProxy.LAO_FANG_MING_WAMG_UPDATE, this.mwUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        facade.subscribe(n.cellProxy.LAO_FANG_PET_LIST, this.onPetsList, this);
        this.nodeItem.active = !1;
        this.initCellData();
        this.dataUpdate();
        this.mwUpdate();
    },
    initCellData() {
        this._petList = localcache.getList(localdb.table_prisoner_pic);
        this.petList.data = this._petList;
        this.petList.selectIndex = 0;
        var t = localcache.getList(localdb.table_prisoner);
        this.curData = t[0];
        var e = n.cellProxy.getPestInfo(this.curData.id);
        this._oldLv = e ? e.lv: 1;
    },
    dataUpdate() {
        this.showCellIndex();
        for (var t = localcache.getItem(localdb.table_prisoner_pic, this.getCurId()), e = 0; e < this.btnSp.length; e++) {
            this.btnSp[e].spriteFrame = this.sprites[t["foodicon" + (e + 1)] - 1];
            this.lblNames[e].string = t["foodname" + (e + 1)];
            var o = n.cellProxy.getPestInfo(this.getCurId());
            n.playerProxy.isUnlockCloth(3005) && o && (this.icons[e].spriteFrame = e + 1 == o.bjid ? this.iconFarme[0] : this.iconFarme[1]);
        }
        var i = 1;
        if (n.playerProxy.isUnlockCloth(3003)) {
            i -= localcache.getItem(localdb.table_userClothe, 3003).pet_data / 1e4;
        }
        this._costFood = Math.ceil(this.curData.power * i);
        this.lblCost.string = i18n.t("CELL_FEED_COST", {
            num: this._costFood
        });
        var l = Math.floor((0.1 * this.getCurId() + 1) * (0.2 * n.playerProxy.userData.level + 1) * 200);
        this._costMoney = Math.ceil(l * i);
        this.costMoney.string = i18n.t("CELL_COST_MONEY", {
            num: this._costMoney
        });
    },
    showCellIndex() {
        this.curData = localcache.getItem(localdb.table_prisoner, this.getCurId());
        for (var t = localcache.getList(localdb.table_prisoner), e = t.indexOf(this.curData), o = t.length, i = 0; i < this.cellHeads.length; i++) {
            var n = (i + e) % o;
            this.cellHeads[i].data = t.length > n ? t[n] : null;
        }
    },
    getCurId() {
        return this.curData.id;
    },
    mwUpdate() {
        var t = n.cellProxy.getPetExp(this.getCurId()),
        e = n.cellProxy.getPetExp(this.getCurId(), !0);
        if (null != t) {
            var o = n.cellProxy.getPestInfo(this.getCurId());
            if (null != o) {
                var l = e ? 1 - (e.food - o.exp) / e.food: 1;
                this.lblHp.string = e ? i18n.t("COMMON_NEED", {
                    f: o.exp,
                    s: e.food
                }) : i18n.t("LEADER_MAX_LEVEL");
                this.hp.progress = l;
                this.lblDaily.string = i18n.t("CELL_CUR_MING_WANG", {
                    value: n.cellProxy.mingWangData.mw,
                    max: n.cellProxy.mingWangData.maxmw
                });
                this.lblSw.string = i18n.t("CELL_DAILY_GET", {
                    value: n.cellProxy.mingWangData.eday
                });
                var a = localcache.getItem(localdb.table_prisoner_pic, this.getCurId());
                this.lblName.string = a.name;
                var s = i.utils.getParamInt("pet_levelup");
                if (this._oldLv < s && o.lv == s) {
                    this.changeSp.node.active = !0;
                    this.changeSp.animation = "animation";
                }
                if (this._oldLv < o.lv) {
                    this.lvUpEff.setActive = !0;
                    this.lvUpEff.animation = "animation";
                    this.scheduleOnce(this.onHideSpine, 2);
                    i.audioManager.playSound("levelup", !0, !0);
                }
                this._oldLv = o.lv;
                var c = i.utils.getParamInt("pet_levelup");
                this.prisoner.url = r.uiHelps.getCellBody(o.lv < c ? a.mod1: a.mod2);
                this._lastHp = l;
                this.lblLv.string = i18n.t("COMMON_LV", {
                    lv: o.lv
                });
                this.lblPro.string = i18n.t("COMMON_ADD_3", {
                    num: t.ep
                });
                this.pro5.active = 5 == t.ep_type;
                this.proUrl.node.active = 5 != t.ep_type;
                this.proUrl.url = r.uiHelps.getLangSp(t.ep_type);
            }
        }
    },
    onClickHit(t, e) {
        var o = parseInt(e);
        if (this.checkBox && this.checkBox.isChecked) n.cellProxy.sendBianDa(5, this.curData.id);
        else {
            if (n.cellProxy.mingWangData.mw < this._costFood) {
                i.alertUtil.alert18n("JAIL_RENOWN_SHORT");
                return;
            }
            if (n.playerProxy.userData.food < this._costMoney) {
                i.alertUtil.alertItemLimit(3);
                return;
            }
            var l = n.cellProxy.getPetExp(this.getCurId(), !0),
            r = localcache.getItem(localdb.table_item, 3);
            if (l && n.playerProxy.userData.food < l.food) {
                i.alertUtil.alertItemLimit(3);
                return;
            }
            i.alertUtil.alert("CELL_COST", {
                n: r.name,
                v: this._costMoney
            });
            this.showEffectIndex(o);
            var a = n.cellProxy.getPestInfo(this.curData.id);
            a && n.cellProxy.sendBianDa(o, a.id);
        }
    },
    showEffectIndex(t) {
        var e = cc.instantiate(this.nodeItem);
        e.active = !0;
        this.addItem.addChild(e);
        var o = e.getComponent(cc.Sprite),
        n = this.btnSp[t - 1];
        e.x = n.node.x;
        e.y = n.node.y;
        o.spriteFrame = n.spriteFrame;
        i.utils.showEffect(o, t - 1,
        function() {
            e.removeFromParent(!0);
            e.destroy();
        });
    },
    onPetsList() {
        this.petList.updateItemShow();
        this.dataUpdate();
    },
    onClickPets(t, e) {
        var o = e.data,
        l = n.cellProxy.getPestInfo(o.id);
        if (l) {
            var r = localcache.getItem(localdb.table_prisoner, o.id);
            this.curData = r;
            this._oldLv = l.lv;
            this.dataUpdate();
            this.mwUpdate();
            for (var a = 0; a < this._petList.length; a++) this._petList[a].id == o.id && (this.petList.selectIndex = a);
        } else {
            var s = localcache.getItem(localdb.table_prisoner, o.id),
            c = localcache.getItem(localdb.table_bigPve, s.bmap);
            c && i.alertUtil.alert(i18n.t("CELL_OPEN_TIP", {
                n: i18n.t("FIGHT_BIG_TIP", {
                    s: c.id
                }) + c.name
            }));
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onClickTq() {
        i.utils.openPrefabView("cell/CellTqWindow");
    },
    onHideSpine() {
        this.lvUpEff.setActive = !1;
    },
});
