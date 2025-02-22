var Utils = require("Utils");
var Initializer = require("Initializer");
var WYGItem = require("WYGItem");
var CarouselMachine = require("CarouselMachine");
let ShaderUtils = require("ShaderUtils");
var List = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        lblRank:cc.Label,
        progress:cc.ProgressBar,
        lblprogress:cc.Label,
        itemArr:[WYGItem],
        carouselMachine:CarouselMachine,
        nodeTouchMove:cc.Node,
        lblClotheCollect:cc.Label,
        lblSuitCollect:cc.Label,
        lblRewardTitle:cc.Label,
        listItem:List,
        btnGet:cc.Button,
        nodeBtnRed:cc.Node,
    },

    ctor() {
        this.userSuitTypeList = [4, 1, 2, 3];
        this.suitTypeDic = {};
        this.isPlaying = false;
        this.currentChooseIdx = 0;
        this.isMoving = false;
        this.startDragPosX = 0;
        this.currentIdx = 1;
    },

    onLoad() {
        facade.subscribe("CAROUSE_SELECTEDFUNCTION", this.updateYMItem, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_CLOTHE_SCORE, this.updateScore, this);
        Initializer.rankProxy.sendClotheRank(true);
        this.initView();
        //this.updateYMItem();
        facade.subscribe("WEIYANGGE_MOVELEFT",this.onClickLeft,this);
        facade.subscribe("WEIYANGGE_MOVERIGHT",this.onClickRight,this);
        this.updateScore();
    },

    initView(){
        this.suitTypeDic = Initializer.playerProxy.getallClotheSuitProp();
        let tmpDic = {}
        for (let key in this.suitTypeDic) {
            let cg = this.suitTypeDic[key];
            for (let k in cg){
                if (tmpDic[k] == null) tmpDic[k] = 0;
                tmpDic[k] += cg[k];
            }
        }
        // let allshili = 0;
        // for (var ii = 0; ii < 4; ii++){
        //     //this["lblEp" + (ii + 1)].string = tmpDic["ep" + (ii+1)] + "";
        //     allshili += tmpDic["ep" + (ii+1)];
        // }
        //this.lblShili.string = allshili + "";
        let clotheCfg = localcache.getList(localdb.table_userClothe);
        this.lblClotheCollect.string = i18n.t("COMMON_NUM",{f:Initializer.playerProxy.clothes ? Initializer.playerProxy.clothes.length : 0,s:clotheCfg.length})
        this.lblSuitCollect.string = i18n.t("COMMON_NUM",{f:tmpDic.curnum,s:tmpDic.max});
    },

    updateScore() {
        let curLevel = Initializer.playerProxy.getClotheLevel();
        this.lblRank.string = curLevel + "";
        let pickLevel = Initializer.clotheProxy.pickLv;       
        let nextHuaFuCfg = localcache.getItem(localdb.table_huafu,pickLevel+1);
        this.btnGet.interactable = curLevel > pickLevel;
        this.nodeBtnRed.active = curLevel > pickLevel;       
        if (nextHuaFuCfg == null){
            let curHuaFuCfg = localcache.getItem(localdb.table_huafu,curLevel);
            this.lblprogress.string =  i18n.t("COMMON_NUM",{f:Initializer.playerProxy.clotheScore,s:curHuaFuCfg.score});
            this.progress.progress = 1;
            this.lblRewardTitle.string = i18n.t("USER_CLOTHE_CARD_TIPS5",{v1:curLevel})        
            this.listItem.data = curHuaFuCfg.rwd;
            return;
        }
        let nextHuaFuCfg2 = localcache.getItem(localdb.table_huafu,curLevel+1);
        if (nextHuaFuCfg2 == null){
            nextHuaFuCfg2 = localcache.getItem(localdb.table_huafu,curLevel);
        }
        this.lblprogress.string =  i18n.t("COMMON_NUM",{f:Initializer.playerProxy.clotheScore,s:nextHuaFuCfg2.score});
        this.progress.progress = Initializer.playerProxy.clotheScore/nextHuaFuCfg.score;
        this.lblRewardTitle.string = i18n.t("USER_CLOTHE_CARD_TIPS5",{v1:pickLevel+1})
        this.listItem.data = nextHuaFuCfg.rwd;
    },

   
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    onClickLeft(){
        if (this.isPlaying) return;
        // let cIdx = this.userSuitTypeList[0] + 1;
        // if (cIdx > 4) cIdx = 1;  
        // for (var ii = 0; ii < this.userSuitTypeList.length;ii++){
        //     let idx = cIdx + ii;
        //     if (idx > 4) idx = idx - 4;
        //     this.userSuitTypeList[ii] = idx;
        // }
        let chooseIdx = this.currentChooseIdx + 1;
        if (chooseIdx > 4){
            chooseIdx = 1;
        }
        for (var ii = 0; ii < this.itemArr.length;ii++){
            let cg = this.itemArr[ii];
            //cg.data = {idx:this.userSuitTypeList[ii],data:this.suitTypeDic[this.userSuitTypeList[ii]],isChoose:chooseIdx == (ii + 1)};
            cg.setChoose(chooseIdx == (ii + 1))
        }
        this.carouselMachine.MoveLeft();
        this.isPlaying = true;
    },

    onClickRight(){
        if (this.isPlaying) return;
        // let cIdx = this.userSuitTypeList[0] - 1;
        // if (cIdx <= 0) cIdx = 4;  
        // for (var ii = 0; ii < this.userSuitTypeList.length;ii++){
        //     let idx = cIdx + ii;
        //     if (idx > 4) idx = idx - 4;
        //     this.userSuitTypeList[ii] = idx;
        // }
        let chooseIdx = this.currentChooseIdx - 1;
        if (chooseIdx <= 0){
            chooseIdx = 4;
        }
        for (var ii = 0; ii < this.itemArr.length;ii++){
            let cg = this.itemArr[ii];
            //cg.data = {idx:this.userSuitTypeList[ii],data:this.suitTypeDic[this.userSuitTypeList[ii]],isChoose:chooseIdx == (ii + 1)};
            cg.setChoose(chooseIdx == (ii + 1))
        }
        this.carouselMachine.MoveRight();
        this.isPlaying = true;
    },

    updateYMItem(idx){
        if (!this.isPlaying){
            let chooseIdx = idx.index;
            for (var ii = 0; ii < this.itemArr.length;ii++){
                let cg = this.itemArr[ii];
                cg.data = {idx:this.userSuitTypeList[ii],data:this.suitTypeDic[this.userSuitTypeList[ii]],isChoose:chooseIdx == (ii + 1)};
            }
        }
        this.currentChooseIdx = idx.index;    
        this.isPlaying = false;
    },

    /**点击打开升级界面*/
    onClickLevelUp() {
        if(!this.bCanLevelUp) {
            Utils.alertUtil.alert18n("SUIT_NOT_LEVEL_UP");
            return;
        }
        Utils.utils.openPrefabView("user/UserSuitDetail");
    },

    onClickGetReward(){
        let self = this;
        Initializer.clotheProxy.sendPickHuaFuAward(()=>{
            self.updateScore();
        });
    },

    /**点击属性*/
    onClickProp() {
        Utils.utils.openPrefabView("user/UIUserCardListView");
    },

    /**点击华服榜*/
    onClickClotheRank(){
        Initializer.rankProxy.sendClotheRank();
    },
  
});
