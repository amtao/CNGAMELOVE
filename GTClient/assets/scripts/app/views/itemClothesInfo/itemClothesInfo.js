
var Utils = require("Utils");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        describeLabel: cc.Label,
        roleSpine: UrlLoad,
        bgUrl: UrlLoad,
        nodeProp: cc.Node,
        lblProp: cc.Label,
        propimg: UrlLoad,
        titleLabel: cc.Label,
        lockLabel: cc.Label,
        lbProp: cc.Label,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        var data = this.node.openParam;
        if (!data) return;
        this.showInfo(data);
    },

    showInfo (data) {
        var ID = data.id ? data.id: data.itemid;
        if (data.kind === 95) {
            var table = localcache.getItem(localdb.table_userClothe, ID);
            if (table.des) {
                this.describeLabel.string = table.des;
            }
            if (table.text) {
                this.lockLabel.string = table.text;
            }
            this.titleLabel.string = table.name ? table.name : "";
            this.setProp(table);
            this.setClothes(data);
        } else if (data.kind === 98) {
            var table = localcache.getItem(localdb.table_userjob, ID);
            this.setUserJob(data);
            this.setUserJobProp(table);

            if (table.des) {
                this.describeLabel.string = table.des;
            }
            if (table.text) {
                this.lockLabel.string = table.text;
            }
            this.titleLabel.string = table.name ? table.name : "";
            var background = localcache.getItem(localdb.table_userClothe, Initializer.playerProxy.userClothe.background);
            if (!background) return;
            this.setBackground(background);
        }

    },

    start () {

    },

    onCloseBtn () {
        Utils.utils.closeView(this);
    },

    setClothes (item) {
        var playerClothes = Initializer.playerProxy.userClothe;
        var playerData = Initializer.playerProxy.userData;
        var clothesTable = localcache.getItem(localdb.table_userClothe, item.id);
        if (!clothesTable) return;
        var clothes = {};
        switch (clothesTable.part) {
            case 1:
                clothes.head = item.id;
                break;
            case 2:
                clothes.body = item.id;
                break;
            case 3:
                clothes.ear = item.id;
                break;
            case 4:
                this.setBackground(clothesTable);
                break;
            case 5:
                clothes.effect = item.id;
                break;
            case 6:
                clothes.animal = item.id;
                break;
        }
        clothes.head = clothes.head ? clothes.head: playerClothes.head;
        clothes.body = clothes.body ? clothes.body : playerClothes.body;
        clothes.ear = clothes.ear ? clothes.ear : playerClothes.ear;
        // clothes.effect = clothes.effect ? clothes.effect : playerClothes.effect;
        // clothes.animal = clothes.animal ? clothes.animal : playerClothes.animal;
        Initializer.playerProxy.loadPlayerSpinePrefab(this.roleSpine,{job:playerData.job,level:playerData.level,clothe:clothes});
        if (clothesTable.part !== 4) {
            var background = localcache.getItem(localdb.table_userClothe, playerClothes.background);
            if (!background) return;
            this.setBackground(background);
        }
    },

    setBackground (tableData) {
        if (!tableData.model) return;
        var bg = UIUtils.uiHelps.getStoryBg(tableData.model);
       if (!bg) return;
        this.bgUrl.url = bg;
    },

    setProp (tableData) {
        this.nodeProp.active = tableData.prop && tableData.prop.length > 0;
        if (tableData.prop && tableData.prop.length > 0) {
            this.lblProp.string = "+" + tableData.prop[0]["value"];
            this.propimg.url = UIUtils.uiHelps.getLangSp(tableData.prop[0]["prop"]);
            this.lbProp.string = UIUtils.uiHelps.getPinzhiStr(tableData.prop[0]["prop"]);
        }
    },

    setUserJob (item) {
        var playerData = Initializer.playerProxy.userData;
        this.roleSpine.setClothes(playerData.sex, item.id, playerData.level, Initializer.playerProxy.userClothe);
    },

    setUserJobProp (tableData) {
        if (!tableData) return;
        this.nodeProp.active = tableData.prop ? true : false;
        if (tableData.prop) {
            this.lblProp.string = "+" + tableData.prop["value"];
            this.propimg.url = UIUtils.uiHelps.getLangSp(tableData.prop["prop"]);
            this.lbProp.string = UIUtils.uiHelps.getPinzhiStr(tableData.prop["prop"]);
        }
    }


    // update (dt) {},
});
