var i = require("List");
var Initializer = require("Initializer");
var l = require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var CarouselMachine = require("CarouselMachine");
var ServantBgShowItem = require("ServantBgShowItem");
cc.Class({
    extends: cc.Component,
    properties: {
        leftbgIcon:UrlLoad,
        lblDes:cc.Label,
        rightbgIcon:UrlLoad,
        middlebgIcon:UrlLoad,
        nodeLock:cc.Node,
        nodeBuy:cc.Node,
        nodePreview:cc.Node,
        servantSpine:UrlLoad,
        bg:UrlLoad,
        btnUse:cc.Button,
        btnUse2:cc.Button,
        carouselMachine:CarouselMachine,
        itemArr:[ServantBgShowItem],
    },
    ctor() {
        this.currentChooseIdx = 0;
        this.listdata = [];
        this.isPlaying = false;
    },
    onLoad() {
        let heroId = this.node.openParam.heroid;
        facade.subscribe("CAROUSE_SELECTEDFUNCTION", this.updateItemData, this);
        this.nodePreview.active = false;
        this.listdata = localcache.getFilters(localdb.table_herobg, "belong_hero",heroId);
        //this.onLoadData();
        this.servantSpine.url = UIUtils.uiHelps.getServantSpine(heroId);       
    },

    closeBtn() {
        l.utils.closeView(this);
    },

    onClickLeft(){
        if (this.isPlaying) return;
        this.nodeBuy.active = false;
        this.nodeLock.active = false;
        this.currentChooseIdx++;
        if (this.currentChooseIdx >= this.listdata.length){
            this.currentChooseIdx = 0;
        }
        //this.onLoadData();
        this.carouselMachine.MoveLeft();
        this.isPlaying = true;
    },

    onClickRight(){
        if (this.isPlaying) return;

        this.nodeBuy.active = false;
        this.nodeLock.active = false;
        this.currentChooseIdx--;
        if (this.currentChooseIdx < 0){
            this.currentChooseIdx = this.listdata.length -1;
        }
        //this.onLoadData();
        this.carouselMachine.MoveRight();
        this.isPlaying = true;
    },

    /**前往购买*/
    onClickBuy(){
        let heroId = this.node.openParam.heroid;
        l.utils.openPrefabView("partner/ServantShopView",null,{id:heroId,type:2});
    },

    /**点击预览*/
    onClickScane(){
        this.nodePreview.active = true;
        let data = this.listdata[this.currentChooseIdx];
        this.bg.url = UIUtils.uiHelps.getPartnerZoneBgImg(data.icon);
    },

    /**点击使用*/
    onClickUse(){
        let data = this.listdata[this.currentChooseIdx];
        Initializer.servantProxy.sendSetBlanks(data.belong_hero,data.id);
        this.closeBtn();
    },

    onClickClosePreview(){
        this.nodePreview.active = false;
    },

    updateItemData(index){
        let idx = index.index;
        this.isPlaying = false;
        let leftIdx = this.currentChooseIdx - 1;
        leftIdx = leftIdx < 0 ?  this.listdata.length -1 : leftIdx;
        let rightIdx = this.currentChooseIdx + 1;
        rightIdx = rightIdx >= this.listdata.length ? 0 : rightIdx;
        this.itemArr[idx-1].data = {icon:this.listdata[this.currentChooseIdx].icon,isChoose:true};
        switch(idx){
            case 1:{
                this.itemArr[2].data = {icon:this.listdata[leftIdx].icon,isChoose:false};
                this.itemArr[1].data = {icon:this.listdata[rightIdx].icon,isChoose:false};
            }
            break;
            case 2:{
                this.itemArr[0].data = {icon:this.listdata[leftIdx].icon,isChoose:false};
                this.itemArr[2].data = {icon:this.listdata[rightIdx].icon,isChoose:false};
            }
            break;
            case 3:{
                this.itemArr[1].data = {icon:this.listdata[leftIdx].icon,isChoose:false};
                this.itemArr[0].data = {icon:this.listdata[rightIdx].icon,isChoose:false};
            }
            break;
        }
        this.onLoadData();
    },

    onLoadData(){
        let heroId = this.node.openParam.heroid;
        let listHeroYokeUnlockCfg = localcache.getFilters(localdb.table_hero_yoke_unlock, "hero_id",heroId);
        let heroBgYokeCfg = listHeroYokeUnlockCfg.filter((data)=>{
            return data.type == 1;
        })
        let id = this.listdata[this.currentChooseIdx].id;
        let jibanLevel = Initializer.jibanProxy.getHeroJbLv(heroId).level
        this.nodeBuy.active = false;
        this.nodeLock.active = false;
        this.btnUse.interactable = true;
        this.btnUse2.interactable = true;
        for (var ii = 0; ii < heroBgYokeCfg.length;ii++){
            let cg = heroBgYokeCfg[ii];
            if (cg.set[0] == id){
                if (cg.yoke_level > jibanLevel){
                    this.lblDes.string = i18n.t("PARYNER_ROOMTIPS32",{v1:cg.yoke_level%1000});
                    this.nodeLock.active = true;
                    this.btnUse.interactable = false;
                    this.btnUse2.interactable = false;
                }
                else{
                    if (Initializer.servantProxy.heroBlankData.blanks[heroId].indexOf(id) == -1){
                        this.nodeBuy.active = true;
                        this.lblDes.string = i18n.t("PARYNER_ROOMTIPS30")
                        this.nodeLock.active = true;
                        this.btnUse.interactable = false;
                        this.btnUse2.interactable = false;
                    }
                }
                break;
            }
        }
    }


});
