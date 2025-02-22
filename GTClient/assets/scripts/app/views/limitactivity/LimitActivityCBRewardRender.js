var i = require("RenderListItem");
let initializer = require("Initializer");
var l = require("Utils");
var r = require("List");
var UserHeadItem = require("UserHeadItem");
var TimeProxy = require("TimeProxy");
var UrlLoad = require("UrlLoad");

cc.Class({

    extends: i,

    properties: {
        lbName: cc.Label,
        lbLevel: cc.Label, 
        lbVIP: cc.Label,       
        list: r,
        topNode: cc.Node,
        otherNode: cc.Node,
        head: UrlLoad,
        playerInfo: cc.Node,
    },

    ctor() {},

    showData() {
        let t = this._data;
        if (t) {
            let actProxy = initializer.limitActivityProxy;
            let e = actProxy.curSelectData;
            var topNode = this.node.getChildByName("item").getChildByName("itembg").getChildByName("topNode");            
            var otherNode = this.node.getChildByName("item").getChildByName("itembg").getChildByName("otherNode");            
            topNode.getChildByName("sp_top1").active = false;
            topNode.getChildByName("sp_top2").active = false;
            topNode.getChildByName("sp_top3").active = false;
            switch(t.rand.rs) {
                case 1: {
                    topNode.active = true;
                    otherNode.active = false;
                    this.delayShowHead(t.rand.rs);
                    topNode.getChildByName("sp_top1").active = true;
                    break;
                }
                case 2: {
                    topNode.active = true;
                    otherNode.active = false;
                    this.delayShowHead(t.rand.rs);
                    topNode.getChildByName("sp_top2").active = true;
                    break;
                }
                case 3: {
                    topNode.active = true;
                    otherNode.active = false;
                    this.delayShowHead(t.rand.rs);
                    topNode.getChildByName("sp_top3").active = true;
                    break;
                }
                default: {
                    this.playerInfo.active = false;
                    otherNode.active = true;
                    topNode.active = false;
                    this.lbLevel.string = "";
                    this.lbName.string = "";
                    this.lbVIP.string = "";
                    otherNode.getChildByName("lb_rse").getComponent(cc.Label).string = i18n.t("AT_LIST_RAND_TXT_2", {num: t.rand.rs+"-"+t.rand.re});
                    break;
                }
            }            
            
            this.list.data = t.member;
            // if(null == e) { 
            //     // this.lblTitle.string = i18n.t("BANK_TITLE", { val: localcache.getItem(localdb.table_officer, t.set).name });
            //     // let level = initializer.playerProxy.userData.level;
            //     // let pickInfo = initializer.purchaseProxy.bankInfo.pickInfo;
            //     // let bGot = null != pickInfo && pickInfo[t.id];
            //     // this.btnGet.interactable = level >= t.set && !bGot && initializer.purchaseProxy.bankInfo.buyTime;
            //     // this.btnGet.node.active = !bGot;
            //     // this.btnYLQ.active = bGot;
            //     // this.list.data = t.rwd;               
            // } else {
                
                
                
            // }
        }
    },

    delayShowHead(index) {
        var data = initializer.limitActivityProxy.cbRankList[index - 1];
        if(data) {
            this.playerInfo.active = true;
            if(this.head) {
                initializer.playerProxy.loadUserHeadPrefab(this.head, data.headavatar, { job: data.job, level: data.level, clothe: data.headavatar }, false);
            } 
            this.lbVIP.string = i18n.t("COMMON_VIP_NAME", { v: data.vip });
            var t = localcache.getItem(localdb.table_officer, data.level?data.level:1);
            this.lbLevel.string = t.name;
            this.lbName.string = data.name;
            this.uid = data.uid;
        } else {
            this.playerInfo.active = false;
            this.lbLevel.string = " ";
            this.lbName.string = " ";
            this.uid = null;
        }
    },

    onClickItem() {
        this.uid && (this.uid == initializer.playerProxy.userData.uid ? TimeProxy.funUtils.openView(TimeProxy.funUtils.userView.id) : initializer.playerProxy.sendGetOther(this.uid));
    },

    setRoleHead() {
        this.head.updateUserHead();
    }
});
