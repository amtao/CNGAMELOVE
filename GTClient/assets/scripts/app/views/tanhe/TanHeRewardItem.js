var i = require("RenderListItem");
var Initializer = require("Initializer");
var Utils= require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var List = require("List");
var ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: i,
    properties: {
        nodeGotMask:cc.Node,
        listView:List,
        itemArr:[ItemSlotUI],
        nodeGotArr:[cc.Node],
        nodeChoose:cc.Node,
        nodeChoose2:cc.Node,
        lblTitle:cc.Label,
    },
    ctor() {
        
    },

    showData() {
        var t = this._data;
        if (t) {
            for (let ii = 0; ii < 3;ii++){
                let cg = t.firstrwd[ii];
                let item = this.itemArr[ii];
                if (cg == null){
                    item.node.active = false;
                }
                else{
                    item.data = cg;
                    item.node.active = true;
                }
            }
            this.listView.data = t.rwd;
            this.lblTitle.string = i18n.t("TANHE_TIPS11",{v1:t.id});
            this.nodeGotMask.active = Initializer.tanheProxy.baseInfo.maxCopy >= t.id;
            for (let ii = 0; ii < this.nodeGotArr.length;ii++){
                this.nodeGotArr[ii].active = Initializer.tanheProxy.baseInfo.maxCopy >= t.id;
            }
            this.nodeChoose.active = Initializer.tanheProxy.baseInfo.currentCopy == t.id;
            this.nodeChoose2.active = this.nodeChoose.active;
        }
    },


});
