/**
 * 商场融合类
 */
let List = require("List");
let Utils = require("Utils");
let Initializer = require("Initializer");
let UIUtils = require("UIUtils");
let UrlLoad = require("UrlLoad");
import { Combine_Shop_TYPE } from "GameDefine";


//ShopHomeJF
cc.Class({
    extends: cc.Component,
    properties: {
        roleUrl:UrlLoad,//商店角色
        shopTip:cc.Label,//商场说明
        shopList: List,//商场list
        ShopTingdou: List,//廷斗兑换商场
        ShopFuyue: List,//赴约兑换商场
        ShopXianli: List,//献礼兑换商场
        ShopClothes: List,//服装商场
        ShopHomeJFList: List,//家具商城
        resNode:[cc.Node],//兑换资源
        lblScore:cc.Label,//献礼积分
        chooseShopBtn:[cc.Button],//商场选择button
        tabNode:cc.Node,//页签总节点
        chooseTabBtn:[cc.Button],//页签选择按钮
        lblTabInfo:[cc.Label],//页签选择提示说明

        lbhjfsoure:cc.Label,
    },
    ctor(){
        this.chooseShopType = Combine_Shop_TYPE.NoType;//所选的商场类型
        this.chooseTabIndex = 0;//页签索引

        this.Typejf = []
    },
    onClickClose() {
        Utils.utils.closeView(this);
    },
    onLoad() {
        facade.subscribe(Initializer.playerProxy.PLAYER_CLOTH_UPDATE,this.initShopList,this);
        facade.subscribe(Initializer.shopProxy.UPDATE_SHOP_LIST,this.initShopList,this);
        facade.subscribe("REFRESH_EXCHANGESHOPLIST", this.initShopList, this);
        facade.subscribe("UPDATE_BOSS_SHOP", this.initShopList, this);
        facade.subscribe("FURNITURE_GETGOODS", this.initShopList, this);
        
        //默认商场
        let showType = this.node.openParam?this.node.openParam:Combine_Shop_TYPE.NormalShop;
        this.onClickChooseShop(null,showType);
    },
    resetShopActiveInfo(){
        this.shopList.node.parent.active = false;
        this.ShopTingdou.node.parent.active = false;
        this.ShopFuyue.node.parent.active = false;
        this.ShopXianli.node.parent.active = false;
        this.ShopClothes.node.parent.active = false;
        this.ShopHomeJFList.node.parent.active = false;
        for(let i = 0;i < this.resNode.length;i++){
            this.resNode[i].active = false;
        }
    },

    unPArrays(array){
        return Array.from(new Set(array))
    },

    onClickChooseShop(touch,event){
        this.resetShopActiveInfo();
        let type = Number(event);
        let roleUrl = localcache.getItem(localdb.table_param,"shangdian_juese_0"+type);
        let shopTip = localcache.getItem(localdb.table_param,"shangdian_taici_0"+type);
        this.shopTip.string = shopTip?shopTip.param:"";
        this.roleUrl.url = UIUtils.uiHelps.getServantSpine(roleUrl?roleUrl.param:22);
        // this.roleUrl.node.scaleX = -0.8;
        switch(type){//不同商店显示形式不同需要加以区分
            case Combine_Shop_TYPE.NormalShop:{
                this.shopList.node.parent.active = true;
                this.tabNode.active = true;
                if(this.resNode.length > 1){
                    this.resNode[0].active = true;//元宝
                }
                this.initShopInfo(type);
            }break;
            case Combine_Shop_TYPE.ClotheShop:{//服装商场
                this.ShopClothes.node.parent.active = true;
                this.tabNode.active = true;
                if(this.resNode.length > 5){
                    this.resNode[0].active = true;//元宝
                    this.resNode[4].active = true;
                }
                this.initShopInfo(type);
            }break;
            case Combine_Shop_TYPE.FuyueShop:{
                this.ShopFuyue.node.parent.active = true;
                this.tabNode.active = false;
                if(this.resNode.length > 4){
                    this.resNode[0].active = true;//元宝
                    this.resNode[3].active = true;
                }
                this.initShopInfo(type);
            }break;
            case Combine_Shop_TYPE.XianliShop:{//献礼
                this.ShopXianli.node.parent.active = true;
                this.tabNode.active = false;
                if(this.resNode.length > 5){
                    this.resNode[0].active = true;//元宝
                    this.resNode[5].active = true;
                }
                this.initShopInfo(type);
            }break;
            case Combine_Shop_TYPE.TingdouShop:{//廷斗
                this.ShopTingdou.node.parent.active = true;
                this.tabNode.active = false;
                if(this.resNode.length > 3){
                    this.resNode[0].active = true;//元宝
                    this.resNode[2].active = true;
                }
                this.initShopInfo(type);
            }break;
            case Combine_Shop_TYPE.ShopHomeJF:{
                Initializer.famUserHProxy.sendMessageGetShopList((e)=>{
                    this.ShopHomeJFList.node.parent.active = true;
                    this.tabNode.active = true;
                    this.resNode[6].active = true;
                    this.initShopInfo(type);
                })
                // this.ShopFuyue.node.parent.active = true;
                // this.tabNode.active = false;
                // if(this.resNode.length > 4){
                //     this.resNode[0].active = true;//元宝
                //     this.resNode[3].active = true;
                // }
                // this.initShopInfo(type);
            }break;
        }
    },

    showShopHomeJFList(index){
        let type = this.Typejf[index-1]
        let shops = Initializer.famUserHProxy.shop.shops
        let lens = shops.length
        let arrays = []
        for (let i = 0; i < lens; i++) {
            if( shops[i].type === type)
            arrays.push(shops[i])
        }
        this.ShopHomeJFList.data = arrays
    },

    showShopHomeJFTabel(){
        let shops = Initializer.famUserHProxy.shop.shops
        let lens = shops.length
        let arrays = []
        for (let i = 0; i < lens; i++) {
            arrays.push(shops[i].type)
        }
        let tss = this.unPArrays(arrays)
        //HOMEPART_HOMEEDITOR_SHOP1
        let arrstring = []
        for (let index = 0; index < tss.length; index++) {
            let ind = tss[index];
            arrstring.push("HOMEPART_HOMEEDITOR_SHOP"+ind)
        }
        this.Typejf = tss
        return arrstring
    },
    initShopInfo(type){
        this.chooseShopType = type;
        this.chooseTabIndex = 1;
        this.resetShopChooseBtn();
        if(this.tabNode.active){
            this.resetTabLabelInfo();
            this.resetTabChooseBtn();//默认选第一个页签
        }
        this.initShopList();
    },
    //重设商场按钮选择
    resetShopChooseBtn(){
        for(let i = 0;i < this.chooseShopBtn.length;i++){
            this.chooseShopBtn[i].interactable = true;
            let btnIconNode = cc.find("BtnBG",this.chooseShopBtn[i].node);
            let btnUrl = btnIconNode.getComponent("UrlLoad");
            btnUrl.url = UIUtils.uiHelps.getCombineShopBtnIcon("ty_huanzhuang_yq_2");
            if(i+1 == this.chooseShopType){
                this.chooseShopBtn[i].interactable = false;
                btnUrl.url = UIUtils.uiHelps.getCombineShopBtnIcon("ty_huanzhuang_yq_1");
            }
        }
    },
    //页签选择
    onClickChooseTab(touch,event){
        this.chooseTabIndex = Number(event);
        this.resetTabChooseBtn();
        this.initShopList();
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
    //修改页签文字描述
    resetTabLabelInfo(){
        let lblInfoArr = [];
        switch(this.chooseShopType){
            case Combine_Shop_TYPE.NormalShop:{
                lblInfoArr = ["SHOP_ONE_TIP1","SHOP_ONE_TIP2","SHOP_ONE_TIP3","SHOP_ONE_TIP5"];
            }break;
            case Combine_Shop_TYPE.ClotheShop:{
                // "头饰","衣服","耳饰","背景","特效",
                lblInfoArr = ["USER_CLOTHE_1","USER_CLOTHE_2","USER_CLOTHE_3"];
            }break;
            case Combine_Shop_TYPE.ShopHomeJF:{
                lblInfoArr = this.showShopHomeJFTabel()
            }break;
        }
        for(let i = 0;i < this.lblTabInfo.length;i++){
            this.lblTabInfo[i].node.parent.active = false;
            if(i < lblInfoArr.length){
                this.lblTabInfo[i].node.parent.active = true;
                this.lblTabInfo[i].string = i18n.t(lblInfoArr[i]);
            }
        }
    },
    //初始化内容
    initShopList(){
        switch(this.chooseShopType){
            case Combine_Shop_TYPE.NormalShop:{
                this.shopList.data = this.getShopList(this.chooseTabIndex);
                this.shopList.updateRenders();
            }break;
            case Combine_Shop_TYPE.ClotheShop:{
                this.ShopClothes.data = this.getShopClotheList(this.chooseTabIndex);
            }break;
            case Combine_Shop_TYPE.FuyueShop:{
                this.ShopFuyue.data = this.getFuyueList();
            }break;
            case Combine_Shop_TYPE.XianliShop:{
                this.ShopXianli.data = this.getXianliList();
                this.lblScore.string = i18n.t("BOSS_SCORE_TXT", {
                    value: Initializer.bossPorxy.shop.score
                });
            }break;
            case Combine_Shop_TYPE.TingdouShop:{
                this.ShopTingdou.data = this.getTingdouList();
            }break;
            case Combine_Shop_TYPE.ShopHomeJF:{
                this.showShopHomeJFList(this.chooseTabIndex)
            }break;
        }
    },
    //获取使用玉如意购买的衣服
    getShopClotheList(partIndex) {
        let partClothes = localcache.getGroup(localdb.table_userClothe, "part",partIndex);
        let canUse = [];
        for (let o = 0; o < partClothes.length; o++) {
            if(partClothes[o].unlock == 2){
                if(Initializer.playerProxy.userData.level >= partClothes[o].para){
                    canUse.push(partClothes[o]);
                }
            }
        }
        let i = {};
        if (canUse.length > 0){
            canUse.sort((t, e)=>{
                null == i[t.id] && (i[t.id] = Initializer.playerProxy.isUnlockCloth(t.id) ? 1 : 0);
                null == i[e.id] && (i[e.id] = Initializer.playerProxy.isUnlockCloth(e.id) ? 1 : 0);
                var o = i[t.id],
                n = i[e.id];
                return o != n ? o - n: t.id - e.id;
            });
        }
        return canUse;
    },
    //过滤普通商场不同页签显示内容
    getShopList(index) {
        let list = Initializer.shopProxy.list;
        if (list == null) return [];
        let array = [];
        for (let i = 0, len = list.length; i < len; i++) {
            let item = list[i];
            if(item.type == index) {
                array.push(item);
            }
        }
        let sortFunc = ((a, b) => {
            return a.id - b.id;
        });
        let result = array.filter((data) => {
            return data.islimit == 0 || (data.islimit != 0 && data.limit > 0);
        });
        result.sort(sortFunc);
        let tmpList = array.filter((data) => {
            return (data.islimit != 0 && data.limit <= 0);
        });
        tmpList.sort(sortFunc);
        result = result.concat(tmpList);
        return result;
    },
    //获取赴约兑换商场list
    getFuyueList(){
        let listcfg = localcache.getList(localdb.table_duihuan);
        //先按是否已经达到限购上限然后再根据id排序
        listcfg.sort((a,b)=>{
            if (Initializer.fuyueProxy.getFYExchangeIsInLimit(a.id,a.set) == Initializer.fuyueProxy.getFYExchangeIsInLimit(b.id,b.set)){
                return a.id < b.id ? -1 : 1;
            }else{
                return Initializer.fuyueProxy.getFYExchangeIsInLimit(a.id,a.set) < Initializer.fuyueProxy.getFYExchangeIsInLimit(b.id,b.set) ? -1 : 1;
            }
        })
        return listcfg;
    },
    //获取献礼兑换商场list
    getXianliList(){
        return localcache.getList(localdb.table_scoreChange);
    },
    //获取廷斗兑换商场list
    getTingdouList(){
        return localcache.getList(localdb.table_exchange);
    },
});