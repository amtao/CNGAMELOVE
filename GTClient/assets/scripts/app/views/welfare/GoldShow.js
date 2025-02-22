var i = require("TimeProxy");
var n = require("Utils");
var l = require("Initializer");
var r = require("ItemSlotUI");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        lblGold: cc.Label,
        item: r,
        res:1,
        paramKey:"",
        nodeRes: cc.Node,
        icon:UrlLoad,
    },
    ctor() {
        // this.res = 1;
        // this.paramKey = "";
        this.lastNum = 0;
        this.isDelay = false;
    },
    onLoad() {
        this.res = n.stringUtil.isBlank(this.paramKey) ? this.res: n.utils.getParamInt(this.paramKey);
        this.nodeRes && (this.nodeRes.active = 1 == this.res || null == this.item);
        this.item && (this.item.node.active = 1 != this.res);
        this.item && (this.item.data = {
            id: this.res,
            kind: 1
        });
        this.onUpdateGold();
        if (this.res != null && (this.res <= 4 || this.res == 10) && this.res > 0){
            facade.subscribe(l.playerProxy.PLAYER_USER_UPDATE, this.onUpdateGold, this);
        }
        else if (this.res == 117 || this.res == 119){
            facade.subscribe("UPDATE_SEARCH_INFO", this.onUpdateGold, this);
        }
        else if(this.res == 118){
            facade.subscribe("UPDATE_MEMBER_INFO", this.onUpdateGold, this);
        }else if(this.res == 208){
            facade.subscribe("FURNITURE_INTERGRAL", this.onUpdateGold, this);
        }
        else{
            facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onUpdateGold, this);
        }
    },
    onClickItem() {
        n.utils.openPrefabView("ItemInfo", !1, {
            id: this.res
        });
    },
    onUpdateGold() {
        if(this.res == 208){
            let curNum = l.famUserHProxy.intergral.score
            //this.score.string = utils.utils.formatMoney(curNum)
            UIUtils.uiUtils.showNumChange(this.lblGold, this.lastNum, curNum);
            this.lastNum = curNum + 0;
            return
        }

        if (this.isDelay) return;
        let curNum = l.bagProxy.getItemCount(this.res);
        UIUtils.uiUtils.showNumChange(this.lblGold, this.lastNum, curNum);
        this.lastNum = curNum + 0;
        //this.lblGold.string = n.utils.formatMoney(l.bagProxy.getItemCount(this.res));
    },
    onClickOpen() {
        1 == this.res ? i.funUtils.openView(i.funUtils.recharge.id) : l.shopProxy.isHaveItem(this.res) && i.funUtils.openView(i.funUtils.shopping.id, {
            id: this.res
        });
    },

    onClickRes() {
        let isHave = l.shopProxy.isHaveItem(this.res, 1);
        if (isHave) {
            n.utils.openPrefabView("shopping/ShopBuy", !1, isHave);
        }
    },

    onRefreshRes(itemid){
        this.res = itemid;
        this.icon.url = UIUtils.uiHelps.getItemSlot(itemid);
        this.lastNum = 0;
        //this.onUpdateGold();
    },

    onDelayRefresh(){
        this.isDelay = false;
        this.onUpdateGold();
        this.isDelay = true;
    },
});
