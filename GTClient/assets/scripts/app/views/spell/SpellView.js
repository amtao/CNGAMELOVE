var i = require("List");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTime: cc.Label,
        light: cc.Node,
        records: i,
        spellList: i,
        scroll: cc.ScrollView,
        lblRwd: cc.Node,
        lblNum: cc.Label,
    },
    ctor() {
        this._oldNum = 0;
    },
    onLoad() {
        facade.subscribe(n.spellProxy.SPELL_DATA_UPDAT, this.onSpellData, this);
        facade.subscribe(n.spellProxy.SPELL_DATA_RECORDS, this.onRecords, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        this.spellList.selectHandle = function(t) {
            var e = t;
            if (e) if (e.num > 0) {
                if (r.timeUtil.second > n.spellProxy.cfg.info.eTime) {
                    r.alertUtil.alert18n("ACTHD_OVERDUE");
                    return;
                }
                r.utils.openPrefabView("spell/SpellSend", null, {
                    itemId: e.id
                });
            } else {
                var o = localcache.getItem(localdb.table_item, e.id);
                r.alertUtil.alert(i18n.t("SPELL_ITEN_LIMIT", {
                    name: o.name
                }));
            }
        };
        n.spellProxy.sendOpenActivity();
        this.onItemUpdate();
    },
    onSpellData() {
        if (null != n.spellProxy.cfg) {
            var t = this;
            l.uiUtils.countDown(n.spellProxy.cfg.info.eTime, this.lblTime,
            function() {
                t.lblTime.string = i18n.t("ACTHD_OVERDUE");
            });
            this.spellList.data = n.spellProxy.cfg.debris;
            this.light.active = n.spellProxy.isEnough();
            n.spellProxy.isEnough() && l.uiUtils.scaleRepeat(this.lblRwd, 0.9, 1.1);
            var e = JSON.stringify(n.spellProxy.cfg.debris);
            n.timeProxy.saveLocalValue("spell", e);
        }
    },
    onRecords() {
        this.records.data = n.spellProxy.records;
        this.scroll.scrollToBottom();
    },
    onClickRwd() {
        r.timeUtil.second > n.spellProxy.cfg.info.eTime ? r.alertUtil.alert18n("ACTHD_OVERDUE") : n.spellProxy.sendGetRwd();
    },
    onClickBox() {
        if (0 != n.bagProxy.getItemCount(930)) {
            var t = {
                id: 930
            };
            r.utils.openPrefabView("bag/BagUse", !1, t);
        } else r.alertUtil.alertItemLimit(930);
    },
    onItemUpdate() {
        var t = n.bagProxy.getItemCount(930);
        this._oldNum > t && n.spellProxy.sendOpenActivity();
        this.lblNum.string = t + "";
        this._oldNum = t;
    },
    onClickClose() {
        r.utils.closeView(this);
    },
});
