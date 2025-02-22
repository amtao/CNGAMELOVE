var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var List = require("List");

cc.Class({
    extends: cc.Component,
    properties: {
        lblLockDes:cc.RichText,
        listItem:List,
        lblBtnTitle:cc.Label,
        btnGet:cc.Button,
        nodeTips:cc.Node,
    },
    ctor() {
        
    },
    onLoad() {
        let param = this.node.openParam;
        this.listItem.data = param.rwd;
        this.lblLockDes.string = "";
        this.nodeTips.active = true;
        if (param.cardid && param.cardid != 0 && param.cardid != 1){
            let cfg = localcache.getItem(localdb.table_card,param.cardid);
            if (cfg){
                this.lblLockDes.string = i18n.t("BANCHAI_TIPS10",{v1:cfg.name});
                this.nodeTips.active = false;
            }
        }
        if (Initializer.banchaiProxy.awardData != null && Initializer.banchaiProxy.awardData.pickInfo != null){
            let cData = Initializer.banchaiProxy.awardData.pickInfo;
            if (cData[param.endid] != null){
                if (cData[param.endid].isPick == 1){
                    this.lblBtnTitle.string = i18n.t("COMMON_IS_GOT");
                    this.btnGet.interactable = false;
                }
                else{
                    this.lblBtnTitle.string = i18n.t("COMMON_GET");
                }
            }
            else{
                this.btnGet.node.active = false;
            }
        }
    },
    
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    /**领取奖励*/
    onClickGetReward(){
        let param = this.node.openParam;
        Initializer.banchaiProxy.sendPickFinalAward(param.endid);
        this.onClickClost();
    },

    
});
