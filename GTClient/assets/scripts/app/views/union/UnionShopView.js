/**
 * 商场融合类
 */
let List = require("List");
let Utils = require("Utils");
let Initializer = require("Initializer");
let UIUtils = require("UIUtils");
let UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,
    properties: {
        roleUrl:UrlLoad,//商店角色
        shopTip:cc.Label,//商场说明
        shopList: List,//商场list
        refreshTime:cc.Label,//刷新时间
        chooseTabBtn:[cc.Button],//商场选择button
    },

    ctor(){
        this.chooseShopType = 1;
    },

    onClickClose() {
        Utils.utils.closeView(this);
    },
    onLoad() {
        facade.subscribe("UPDATE_SHOP_LIST",this.initShopList,this);
        let self = this;
        Initializer.unionProxy.sendShopList(()=>{
            self.onClickChooseTab(null,"1")
        });
        //默认商场
        //this.initShopList();
        //初始化人物/tip
        let roleUrl = localcache.getItem(localdb.table_param,"shangdian_juese_0"+1);
        this.roleUrl.url = UIUtils.uiHelps.getServantSpine(roleUrl.param);
        let shopTip = localcache.getItem(localdb.table_param,"shangdian_taici_0"+1);
        this.shopTip.string = shopTip.param;
        //let minus = Initializer.playerProxy.nextDayZeroTimeStamp - Utils.timeUtil.second;
        
        UIUtils.uiUtils.countDown(Initializer.playerProxy.nextDayZeroTimeStamp,this.refreshTime);
        // this.refreshTime.string = i18n.t("FUYUE_DESC5", {
        //     time: Utils.timeUtil.second2hms(minus)
        // });
    },
    //初始化内容
    initShopList(){
        let listdata = Initializer.unionProxy.shopList.filter((data)=>{
            return data.page == this.chooseTabIndex;
        })
        this.shopList.data = listdata;
    },

     resetTabChooseBtn(){
        for(let i = 0;i < this.chooseTabBtn.length;i++){
            this.chooseTabBtn[i].interactable = true;
            let btnIconNode = cc.find("BtnBG",this.chooseTabBtn[i].node);
            let btnUrl = btnIconNode.getComponent("UrlLoad");
            btnUrl.url = UIUtils.uiHelps.getCombineShopTabIcon("ty_btn2_normal");
            // this.lblTabInfo[i].node.color = new cc.Color(255,218,123,255);
            if(i+1 == this.chooseTabIndex){
                this.chooseTabBtn[i].interactable = false;
                btnUrl.url = UIUtils.uiHelps.getCombineShopTabIcon("ty_btn2_selected");
                // this.lblTabInfo[i].node.color = new cc.Color(96,79,53,255);
            }
        }
    },
    //页签选择
    onClickChooseTab(touch,event){
        this.chooseTabIndex = Number(event);
        this.resetTabChooseBtn();
        this.initShopList();
    },

});