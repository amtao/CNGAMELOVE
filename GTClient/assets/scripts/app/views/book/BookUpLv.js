let n = require("Utils");
let l = require("Initializer");
let r = require("ItemSlotUI");
let a = require("UIUtils");
let s = require("formula");
let scItem = require('ServantTalentItem');

cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblCur: cc.Label,
        lblNext: cc.Label,
        itemSlotExp: r,
        itemSlot: r,
        itemName1: cc.Label,
        itemName2: cc.Label,
        lbCount1: cc.Label,
        lbCount2: cc.Label,
        //gailv1: cc.Label,
        //gailv2: cc.Label,
        iconArr: [cc.SpriteFrame],
        txtProp1: cc.Label,
        txtProp2: cc.Label,
        scItems: [scItem],
        anim: cc.Animation,
    },

    ctor() {
        this.costSys = null;
        this.heroData = null;
        this.upSys = null;
        this.costExp = 993;
    },

    onLoad() {
        this.datas = this.node.openParam;
        this.heroData = l.servantProxy.getHeroData(l.servantProxy.curSelectId);
        this.showAll();
        facade.subscribe("SERVANT_UP", this.updateData, this);
        facade.subscribe("UPDATE_BAG_ITEM", this.updateData2, this);
    },

    showAll: function() {
        this.showListData();
        this.onToggleValueChange(null, "1");
    },

    showListData: function() {
        for(let i = 0, len = this.scItems.length; i < len; i++) {
            let item = this.scItems[i];
            if(i < this.datas.length) {
                item.node.active = true;
                item._data = this.datas[i];
                item.showData();
            } else {
                item.node.active = false;
            }
        }
    },

    updateData2: function() {
        this.updateData(true);
    },

    updateData(bNotShow) {
        if (this.heroData == null) return;
        let lastData = {};
        n.utils.copyData(lastData, this.curData);
        let level = this.curData.level;
        this.datas = this.heroData.epskill;
        this.showListData();
        this.onToggleValueChange(null, this.curIndex);
        this.heroData = l.servantProxy.getHeroData(l.servantProxy.curSelectId);
        if (this.heroData) for (var e = 0; e < this.heroData.epskill.length; e++) if (this.curData && this.curData.id == this.heroData.epskill[e].id) {
            this.curData = this.heroData.epskill[e];
            break;
        }
        if(!bNotShow) {
            this.curData && level < this.curData.level ? n.alertUtil.alert(i18n.t("SERVANT_EPSKILL_UP_SUCCESS")) : n.alertUtil.alert(i18n.t("SERVANT_EPSKILL_UP_FAIL"));
        }      
        this.onShowData();
    },

    onShowData() {
        if (this.curData && this.heroData != null) {
            var t = localcache.getItem(localdb.table_epSkill, this.curData.id + ""),
            e = s.formula.partner_prop(this.heroData.level, t.star, this.curData.level - 1),
            o = s.formula.partner_prop(this.heroData.level, t.star, this.curData.level);
            this.lblName.string = t.name + " Lv." + this.curData.level;
            this.lblCur.string = "+" + e;
            this.lblNext.string = "+" + o;
            var i = localcache.getItem(localdb.table_epSkill, this.curData.id);
            this.upSys = localcache.getItem(localdb.table_epLvUp, i.star);
            //this.gailv1.string = i18n.t("SERVANT_UP_GAI_LV") + this.upSys.prob_100 + "%";
            //this.gailv2.string = i18n.t("SERVANT_UP_GAI_LV") + "100%";
            1 == i.ep ? (this.costSys = localcache.getItem(localdb.table_item, 61)) 
             : 2 == i.ep ? (this.costSys = localcache.getItem(localdb.table_item, 62)) 
             : 3 == i.ep ? (this.costSys = localcache.getItem(localdb.table_item, 63)) 
             : 4 == i.ep && (this.costSys = localcache.getItem(localdb.table_item, 64)); 

            let n = new a.ItemSlotData();
            n.id = this.costSys.id;
            n.count = 10;
            this.itemSlot.data = n;

            let expData = new a.ItemSlotData();
            expData.id = this.costExp;
            expData.count = 10;
            this.itemSlotExp.data = expData;

            this.itemName1.string = this.costSys.name;
            this.itemName2.string = i18n.t("COMMON_SJJY");
            //let enoughColor = new cc.Color("#7A849F");
            let count1 = l.bagProxy.getItemCount(this.costSys.id);
            let allCount1 = this.upSys.quantity;
            this.lbCount1.string = count1 + "/" + allCount1;
            this.lbCount1.node.color = count1 >= allCount1 ? cc.Color.WHITE : cc.Color.RED;
            let count2 = this.heroData.zzexp;
            let allCount2 = this.upSys.exp;
            this.lbCount2.string = count2 + "/" + allCount2;
            this.lbCount2.node.color = count2 >= allCount2 ? cc.Color.WHITE : cc.Color.RED;
             
            this.txtProp1.string = this.txtProp2.string = a.uiHelps.getPinzhiStr(i.ep) + ":";
            //this.icon_1.url = this.icon_2.url = a.uiHelps.getLangSp(i.ep);
        }
    },

    onClickUp(t, e) {
        if (1 == parseInt(e)) {
            if (this.heroData.zzexp < this.upSys.exp) {
                n.alertUtil.alert(i18n.t("COMMON_LIMIT", {
                    n: i18n.t("COMMON_SJJY")
                }));
                return;
            }
        } else if (2 == parseInt(e)) {
            if (l.bagProxy.getItemCount(this.costSys.id) < 1) {
                n.alertUtil.alertItemLimit(this.costSys.id);
                return;
            }
        }
        l.servantProxy.sendUpZzSkill(l.servantProxy.curSelectId, this.curData.id, parseInt(e));
        n.audioManager.playSound("levelup", !0, !0);
    },

    onClickClose() {
        n.utils.closeView(this);
    },

    onToggleValueChange(tg, param) {
        if(null != tg) {
            this.anim.play("BookUpLvclick");
        }
        this.curIndex = parseInt(param);
        this.curData = this.datas[this.curIndex - 1];       
        this.onShowData();
        for(let i = 0; i < this.scItems.length; i++) {
            let item = this.scItems[i];
            if (item.node.active){
                let toggle = item.node.getComponent(cc.Toggle);
                toggle.isChecked = i == this.curIndex - 1;
            }  
        }
    },
});
