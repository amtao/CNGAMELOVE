var i = require("List");
var n = require("Utils");
let initializer = require("Initializer");

cc.Class({
    extends: cc.Component,
    properties: {
        itemList: i,
        tabs: [cc.Button],
        nSelected: [cc.Node],
        anim: cc.Animation,
    },
    ctor() {
        this.totalArr = [];
        this.curSelect = "1";
        this.totalLen = 0;
    },
    onLoad() {
        facade.subscribe("SERVANT_UP", this.updateData, this);
        facade.subscribe(initializer.bagProxy.UPDATE_BAG_ITEM, this.updateData, this);
        let heroId = this.node.openParam.id;
        let listAllData = initializer.servantProxy.getTrainItemMaxNum(heroId);
        this.totalArr = listAllData;
        // var t = n.utils.getParamStr("ep_wl_item"),
        // e = n.utils.getParamStr("ep_zl_item"),
        // o = n.utils.getParamStr("ep_zz_item"),
        // i = n.utils.getParamStr("ep_ml_item"),
        // l = n.utils.getParamStr("ep_all_item") + "|" + t + "|" + e + "|" + o + "|" + i;
        // this.totalArr = l.split("|");
        this.onClickTab(null, this.curSelect);
    },
    onClickTab(t, e) {
        if(null != t) {
            this.anim.play("ServantTrainViewclick");
        }
        for (var o = parseInt(e), i = 0; i < this.tabs.length; i++) i > 0 && (this.tabs[i].interactable = i != o);
        for(let j = 0, len = this.nSelected.length; j < len; j++) {
            let node = this.nSelected[j];
            node && (node.active = o == j);
        }
        this.curSelect = e;
        this.onShowData();
    },
    onShowData() {
        //"0" == this.curSelect ? (this.itemList.data = this.totalArr) : "1" == this.curSelect ? (this.itemList.data = n.utils.getParamStrs("ep_wl_item")) : "2" == this.curSelect ? (this.itemList.data = n.utils.getParamStrs("ep_zl_item")) : "3" == this.curSelect ? (this.itemList.data = n.utils.getParamStrs("ep_zz_item")) : "4" == this.curSelect && (this.itemList.data = n.utils.getParamStrs("ep_ml_item"));
        switch(this.curSelect){
            case "0":{
                this.itemList.data = this.totalArr;
            }
            break;
            case "1":
            case "2":
            case "3":
            case "4":
            {
                let dur = this.totalArr.length / 4;
                let listdata = [];
                for (var ii = dur * (Number(this.curSelect)-1) ; ii < dur * Number(this.curSelect);ii++){
                    listdata.push(this.totalArr[ii]);
                }
                this.itemList.data = listdata;
            }
            break;
        }

    },


    updateData() {
        this.itemList.updateItemShow();
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
