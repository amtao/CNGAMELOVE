var i = require("Utils");
var n = require("Initializer");
let scItem = require('ServantSkillItem');
let scItemSlotUI = require("ItemSlotUI");
let scUIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,
    properties: {
        lblUpNeed: cc.Label,
        lblCurEff: cc.Label,
        lblNextEff: cc.Label,
        lblName: cc.Label,
        lblExp: cc.Label,
        lbCurEffTxt: cc.Label,
        lbNextEffTxt: cc.Label,
        scItems: [scItem],
        anim: cc.Animation,
        itemSlot: scItemSlotUI,
    },

    ctor() {
        this._curSkillData = null;
        this._curHero = null;
        this.skill = null;
        this._oldLv = 0;
        this.expId = 994;
    },

    onLoad() {
        facade.subscribe("SERVANT_UP", this.onDataUpdate, this);
        var t = this.node.openParam;
        this.skills = t._skill;
        this._curHero = t._hero;
        this.showSkillData();
        this.onToggleValueChange(null, "1");
    },

    showSkillData: function() {
        for(let i = 0, len = this.scItems.length; i < len; i++) {
            let item = this.scItems[i];
            if(this.skills && i < this.skills.length) {
                item.node.active = true;
                item._data = this.skills[i];
                item.showData();
            } else {
                item.node.active = false;
            }      
        }

        let expData = new scUIUtils.ItemSlotData();
        expData.id = this.expId;
        this.itemSlot.data = expData;
    },

    onDataUpdate() {
        if(this._curHero) {
            this.skills = n.servantProxy.getHeroData(this._curHero.id).pkskill;
            this.showSkillData();
            this.onToggleValueChange(null, this.curIndex);
            this._curHero = n.servantProxy.getHeroData(this._curHero.id);
            for (var t = 0; t < this._curHero.pkskill.length; t++) if (this._curHero.pkskill[t].id == this.skill.id) {
                this._oldLv < this._curHero.pkskill[t].level && i.alertUtil.alert(i18n.t("SERVANT_TE_CHANG_LEVEL_UP"));
                this.skill = this._curHero.pkskill[t];
                break;
            }
        }
        this.onSkillUpdate();
    },

    onSkillUpdate() {
        if (this.skill) {
            this._curSkillData = this.skill;
            var t = localcache.getItem(localdb.table_pkSkill, this.skill.id),
            e = localcache.getItem(localdb.table_pkLvUp, this.skill.level);
            this.lblName.string = i18n.t("SERVANT_SKILL_NAME_TXT", {
                name: t.name,
                lv: this._curSkillData.level
            });
            this.lblUpNeed.string = this._curHero.pkexp + "/" + e.exp;
            if(this && this._curHero) {
                this.lblExp.string = "";//this._curHero.pkexp + "";
            }
            this.lblCurEff.string = (t.base + t.upgrade * this.skill.level) / 100 + "%";
            this.skill.level < t.maxLevel ? (this.lblNextEff.string = (t.base + t.upgrade * (this.skill.level + 1)) / 100 + "%") : (this.lblNextEff.string = i18n.t("SERVANT_MAX_LEVEL"));
            this.lbCurEffTxt.string = this.lbNextEffTxt.string = t.comm + ":";
        }
    },

    onClickSkillUp(t, e) {
        var o = this._curSkillData,
        l = localcache.getItem(localdb.table_pkSkill, o.id),
        r = localcache.getItem(localdb.table_pkLvUp, o.level);
        if(this && this._curHero) {
            if (o.level < l.maxLevel) if (r.exp > this._curHero.pkexp) i.alertUtil.alert(i18n.t("SERVANT_SKILL_EXP_LIMIT"));
            else {
                n.servantProxy.sendUpPkSkill(this._curHero.id, o.id);
                i.audioManager.playSound("levelup", !0, !0);
                i.alertUtil.alert(i18n.t("SERVANT_EPSKILL_UP_SUCCESS"));
            }
        }
    },

    onClickClose() {
        i.utils.closeView(this);
    },

    onToggleValueChange: function(tg, param) {
        if(null != tg) {
            this.anim.play("ServantSkillUpclick");
        }
        this.curIndex = parseInt(param);
        if(this.skills) {
            this.skill = this.skills[this.curIndex - 1];
        }
        this._oldLv = this.skill.level;
        this.onSkillUpdate();
    },
});
