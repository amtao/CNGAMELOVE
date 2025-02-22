/* explain:
 前期就两个活动, 所以都放在预设体里调用,
 后期活动增加多了之后保存成不同的预设体,
 切换不同的标签页讲不同的预设体生成到这里
 每个脚本都继承scActivityItem进行处理
 --Diamanta 2020.04.02
*/

let scUtils = require("Utils");
let initializer = require("Initializer");
let scActivityItem = require("scActivityItem");
let timeProxy = require('TimeProxy');
let scUIUtils = require("UIUtils");
let playerProxy = require("PlayerProxy");
var List = require("List");
let apiUtils = require("ApiUtils");
let scConfig = require("Config");

var PayTye = {
    YUANBAO:1,
    RMB:2
}

cc.Class({
    extends: cc.Component,

    properties: {
        lb_limit_buy: cc.Label,
        lb_buy_price: cc.Label,
        lb_buy_origin: cc.Label,
        list_box: List,
        list_item: List,
        btn_buyBox: cc.Button,
        lb_giftbox_name: cc.Label,
    },

    ctor() {
        
    },

    onLoad() {      
        facade.subscribe("RUSH2LIST_GIFTBOX_UPDATE", this.onUpdate, this);              

        var e = this.node.openParam;
        this.exchange = e;
        
        // console.log(data);

        this.initListBox();
        this.initListItems();        
    },

    initListBox() {
        var list = this.getExchangeByPay(PayTye.RMB);
        if(list.length > 0) {
            var box = list[0];
            this.list_box.repeatX = box.items.length;
            this.list_box.data = box.items;
            this.lb_limit_buy.string = i18n.t("LEVEL_GIFT_XIAN_TXT_2", {num:box.limit-box.buy+"/"+box.limit});
            this.lb_buy_origin.string = box.prime;
            this.lb_buy_price.string = box.sign + box.present + " " + i18n.t("SHOP_BUY_TIP");
            this.list_box.node.position = cc.v2(-(box.items.length*80+box.items.length*this.list_box.spaceX)/2, 198);
            this.box = box; 
            if(box.buy>=box.limit)
                this.btn_buyBox.interactable = false;
            else      
                this.btn_buyBox.interactable = true;

            this.lb_giftbox_name.string = box.name;
        }
        
    },

    initListItems() {
        var list = this.getExchangeByPay(PayTye.YUANBAO);
        this.list_item.getComponent("List").repeatX = list.length;
        this.list_item.data = list;
    },

    onUpdate: function() {
        this.exchange = initializer.limitActivityProxy.cbGbExchange;
        this.initListBox();
        this.initListItems();          
    },

    getExchangeByPay(payType) {
        var list = [];
        if(this.exchange) {
            for(var i=0; i<this.exchange.length; i++) {
                var ex = this.exchange[i];
                if(ex.isPay == payType)
                    list.push(ex);
            }
        }

        return list;
    },

    onClickClose() {        
        scUtils.utils.closeView(this);
        initializer.welfareProxy.sendOrderBack();
    },

    onClickBuyBox: function() {
        if(this.box) {
            var g = 10 * this.box.grade + 1e6 + 1e4 * this.box.id;
            apiUtils.apiUtils.recharge(
                initializer.playerProxy.userData.uid,
                scConfig.Config.serId,
                g,
                this.box.grade,
                i18n.t("RUSH2LIST_GIFBOX_DESC2"),
                0,
                g,
                this.box.cpId,
                this.box.dollar,
                0
            );            
        }
    }
});
