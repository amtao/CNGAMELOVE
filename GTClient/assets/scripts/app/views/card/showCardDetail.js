let Utils = require("Utils");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
let cardMerterialItem = require("cardMerterialItem");
let scflowerPoint = require("scCardFlowerPoint");
var config = require("Config");
var ChangeOpacity = require("ChangeOpacity");
/**
 * 已经获得的卡牌详情
 */
cc.Class({
    extends: cc.Component,

    properties: {
        lblCardName: cc.Label,
        nCard: cc.Node,
        urlCard: UrlLoad,
        urlProp: UrlLoad,
        urlQuality: UrlLoad,
        lbQuality: cc.Label,
        urlSpecialFrame: UrlLoad,
        nProps: cc.Node,
        lbParam: [cc.Label],
        lbParamAddTxt: [cc.Label],
        nLevelUpRed: cc.Node,
        nLevelUp: cc.Node,
        lbLevel: cc.Label,
        pgbLevel: cc.ProgressBar,
        tgFiveUp: cc.Toggle,
        cardMerterial1: cardMerterialItem,
        lbGold: cc.Label,
        nMax: cc.Node,
        lbMax: cc.RichText,
        lbLevelStarNeed: cc.Label,
        urlSpecialFrame2: UrlLoad,

        nStarUp: cc.Node,
        urlStar: UrlLoad,
        cardMerterial2: cardMerterialItem,
        lbGoldStar: cc.Label,
        nStarUpRed: cc.Node,
        lbStarLevelNeed: cc.Label,

        tgArr: [cc.Toggle],
        nLeftRight: cc.Node,
        nFlower: cc.Node,

        nYinhenUp: cc.Node,
        nYinhen: cc.Node,
        cardMerterial3: cardMerterialItem,
        lbGoldYinhen: cc.Label,
        nYinhenRed: cc.Node,

        nFlowerUp: cc.Node,
        nUnlockFlower: cc.Node,
        urlUnlockCost: UrlLoad,
        lbFlowerUnlockGold: cc.Label,
        lbUnlockCondition: cc.Label,
        nUnlockRed: cc.Node,
        nFlowerUpDetail: cc.Node,
        nGetCard: cc.Node,
        lbGetCard: cc.Label,
        nGetFrame: cc.Node,
        lbGetFrame: cc.Label,
        nGetEff: cc.Node,
        lbGetEff: cc.Label,
        cardMerterial4: cardMerterialItem,
        lbGoldFlower: cc.Label,
        nFlowerUpRed: cc.Node,
        arrFlowerPoint: [scflowerPoint],
        arrNBranches: [cc.Node],
        lbFlowerCondition: cc.Label,

        secColor: cc.Color,
        norColor: cc.Color, 

        nodeEffect: cc.Node,
        nodeShengxing: cc.Node,
        nodeShengji: cc.Node,
        lbllastlevel: cc.Label,
        lblcurlevel: cc.Label,
        lbllastproarr: [cc.Label],
        lblcurproarr: [cc.Label],
        lbllaststar: cc.Label,
        lblcurstar: cc.Label,
        lbllastlevellimit: cc.Label,
        lblcurlevellimit: cc.Label,

        nodeUnlockEff: cc.Node,
        urlUnlockProp: UrlLoad,
        lbUnlockProp: cc.Label,
        lbUnlockLastProp: cc.Label,
        lbUnlockCurProp: cc.Label,
        urlBigCard: UrlLoad,
        skFlowerUp: sp.Skeleton,
        skFrame: sp.Skeleton,
        partical: cc.ParticleSystem,
        nodeLevelUpRed:cc.Node,
        nodeStarUpRed:cc.Node,
        nodeYinHenRed:cc.Node,
        nodeFlowerRed:cc.Node,
        changeOpacityMask: ChangeOpacity, 
        showFFBtn:cc.Node,
    },

    ctor() {
        this.cardUpArr = [];
        this.cardStarArr = [];
        this.cardStarList = [];
        this.cardYinhenArr = [];
        this.cardYinhenList = [];
        this.flowerUpArr = [];
        this.chooseIdx = 0;
        this.lastlevel = -1;
        this.laststar = -1;
        this.dicFlowerPoint = [];
        this.curPointIndex = 0;
        this.lastFlower = -1;

        this.iGetFramePoint = 10;
        this.iGetEffPoint = 14;
        this.iGetCardPoint = 18;
    },

    onLoad(){
        // this.nodeEffect.active = false;
        this.leftCardData = null;
        this.rightCardData = null;
        // this.starCost = 0;
        this.cardData = this.node.openParam.cardData;
        this.cfgData = this.node.openParam.cfgData;
        this.allListData = Initializer.cardProxy.currentCardList;
        this.cardUpArr.push(this.cardMerterial1);
        this.cardStarArr.push(this.cardMerterial2);
        this.cardStarList.push(this.urlStar);
        this.cardYinhenArr.push(this.cardMerterial3);
        this.cardYinhenList.push(this.nYinhen.getComponent(UrlLoad));
        this.flowerUpArr.push(this.cardMerterial4);
        for(let i = 0, len = this.arrNBranches.length; i < len; i++) {
            this.dicFlowerPoint[i] = [];
            for(let j = 0, jLen = this.arrFlowerPoint.length; j < jLen; j++) {
                let scPoint = this.arrFlowerPoint[j];
                if(!scPoint.bMark && scPoint.node.parent == this.arrNBranches[i]) {
                    this.dicFlowerPoint[i].push(scPoint);
                    scPoint.bMark = true;
                }
            }
        }
        this.changeOpacityMask.active = false;
        if(this.cardData && this.cfgData){
            this.initCardDetail();
        }
        this.checkLeftCardInfo();
        this.checkRightCardInfo();
        facade.subscribe(Initializer.cardProxy.ALL_CARD_RED, this.initCardDetail, this);
        facade.subscribe("CARD_MERTERIAL_ITEM", this.onRefreshChoose, this);
        facade.subscribe(Initializer.bagProxy.UPDATE_BAG_ITEM, this.initCardDetail, this);
        
        this.onClickTab(null, this.chooseIdx);
        this.tgFiveUp.isChecked = Initializer.cardProxy.upgradeFiveFlag;

        let self = this;
        this.skFrame.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";           
            if(null != self.node && self.node.isValid && animationName.indexOf("appear") != -1) {
                this.skFrame.setSkin("default")
                self.skFrame.node.active = false;             
            }
        });

        this.skFlowerUp.setCompleteListener(() => {
            if(null != self.node && self.node.isValid) {
                if (self.changeOpacityMask.node.active){
                    self.skFrame.node.active = true;
                    let skinArr = ["blue","pur","yellow"]
                    self.skFrame.setSkin(skinArr[self.cfgData.quality - 2]);
                    self.skFrame.setAnimation(0, "appear" + (self.cfgData.quality - 1), false);
                    self.skFrame.addAnimation(0,"animation" + (self.cfgData.quality - 1),false);
                }                    
                self.skFlowerUp.node.active = false;
                self.changeOpacityMask.node.active = false;
            }
        });
        this.checkFetters()
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },

    onClickEffectBg(){
        this.nodeEffect.active = false;
        this.nodeUnlockEff.active = false;
    },

    showStarSpine(){
        if(this.cardData && this.cardData.star) {
            Utils.audioManager.playSound("upstar", !0, !0);
        }
    },

    onClickShowItem(touch, event) {
        if(this.cfgData) {
            if(event == 1){//普通卡
                let data = new UIUtils.ItemSlotData();
                data.id = this.cfgData.id;
                data.kind = 106;
                Utils.utils.openPrefabView("ItemInfo", false, data);
            }else{//万能卡
                let omnipotentCardID = Initializer.cardProxy.getOmnipotentCardID(this.cfgData.quality);
                let data = new UIUtils.ItemSlotData();
                data.id = omnipotentCardID;
                data.kind = 106;
                Utils.utils.openPrefabView("ItemInfo", false, data);
            }
        }
    },

    onClickCheck() {
        Initializer.cardProxy.upgradeFiveFlag = this.tgFiveUp.isChecked;
    },

    onClickToLeft() {
        if(this.leftCardData) {
            this.onChangeCard(this.leftCardData);
        }
    },

    onClickToRight(){
        if(this.rightCardData) {
            this.onChangeCard(this.rightCardData);
        }
    },
    
    onClickFetters: function() {
        Utils.utils.openPrefabView("card/CardFetters",null,this.cfgData.id);
    },

    checkFetters(){
        let id = this.cfgData.id
        let clist = localcache.getList(localdb.table_card_skill);
        let len = clist.length
        let bos = false
        for (let i = 0; i < len; i++) {
            let cards = clist[i].card
            let len2 = cards.length
            for (let j = 0; j < len2; j++) {
                if(cards[j] === id){
                    bos = true
                    break;
                }
            }
            if(bos){
                break;
            }
        }
        this.showFFBtn.active = bos
    },

    onChangeCard: function(cardData) {
        //this.starCost = 0;
        this.lastlevel = -1;
        this.laststar = -1;
        this.lastFlower = -1;
        this.curPointIndex = 0;
        this.cardData = cardData;
        this.cfgData = cardData.cfgData;
        if(this.cardData && this.cfgData) {
            this.initCardDetail();
        }
        this.checkFetters();
        this.checkLeftCardInfo();
        this.checkRightCardInfo();
        // let normalCardIcon = UIUtils.uiHelps.getCardIconFrame(this.cfgData.id);
        // this.scNormal.resetResType(this.cfgData.id, normalCardIcon);//普通原卡数量
        this.skFrame.node.active = false;
        this.skFlowerUp.node.active = false;
    },

    checkLeftCardInfo() {
        if(this.allListData) {
            let idx = -1;
            for(let i = this.allListData.length - 1; i >= 0; i--) {
                if(this.allListData[i].id == this.cfgData.id) {
                    idx = i;
                    break;
                }
            }
            idx--;
            if (idx < 0) {
                idx = this.allListData.length - 1;
            }
            this.leftCardData = Initializer.cardProxy.getCardInfo(this.allListData[idx].id);
        }
    },

    checkRightCardInfo() {
        if(this.allListData) {
            let idx = -1;
            for(let i = 0, len = this.allListData.length; i < len; i++) {
                if(this.allListData[i].id == this.cfgData.id){
                    idx = i;
                    break;
                }
            }
            idx++;
            if (idx >= this.allListData.length) {
                idx = 0;
            }
            this.rightCardData = Initializer.cardProxy.getCardInfo(this.allListData[idx].id);
        }
    },

    initCardDetail() {
        this.cardData = Initializer.cardProxy.cardMap[this.cfgData.id];
        let bYinhenNFlower = this.cfgData.hero > 0;
        this.tgArr[2].node.active = this.tgArr[3].node.active = bYinhenNFlower;
        if(this.chooseIdx > 1 && !bYinhenNFlower) {
            this.chooseIdx = 0;
            this.tgArr[this.chooseIdx].check();
            this.tgArr[this.chooseIdx]._emitToggleEvents();
        }

        if (this.lastlevel != -1 && this.lastlevel < this.cardData.level) {
            this.onShowLevelEffect();
        } else if(this.laststar != -1 && this.laststar < this.cardData.star) {
            this.onShowStarEffect();
        } else if(this.lastFlower != -1 && this.lastFlower.length <= 0 && null != this.cardData.flowerPoint && this.cardData.flowerPoint.length == 1) { //解锁效果
            this.onShowFlowerUnlockEffect();
            this.checkFlowerUpAni();
        } else if(this.lastFlower != -1 && this.lastFlower.length > 0 && null != this.cardData.flowerPoint && this.cardData.flowerPoint.length > this.lastFlower.length) {
            this.checkFlowerUpAni();
            this.checkFrameAni();
        }
        this.lastlevel = this.cardData.level;
        this.laststar = this.cardData.star;
        let flowerPoint = [];
        if(null != this.cardData.flowerPoint) {
            Utils.utils.copyList(flowerPoint, this.cardData.flowerPoint);
        }
        this.lastFlower = flowerPoint;
        //卡图片
        this.lblCardName.string = this.cfgData.name;
        this.lbQuality.string = i18n.t("XINDONG_QUALITY_" + this.cfgData.quality);
        this.urlQuality.url = UIUtils.uiHelps.getQualityLbFrame(this.cfgData.quality);
        this.urlCard.url = this.urlBigCard.url = UIUtils.uiHelps.getCardFrame(this.cfgData.picture);
        this.urlProp.url = UIUtils.uiHelps.getUICardPic("kpsj_icon_" + this.cfgData.shuxing);
        // this.spShowCard.url = UIUtils.uiHelps.getCardFrame(this.cfgData.picture);
        //更新属性
        this.updateProp();
        this.nodeLevelUpRed.active = Initializer.cardProxy.checkCardLevelUp(this.cfgData,this.cardData);
        this.nodeStarUpRed.active = Initializer.cardProxy.checkCardStarUp(this.cfgData,this.cardData);
        this.nodeYinHenRed.active = Initializer.cardProxy.checkCardYinHenLevelUp(this.cfgData,this.cardData);
        this.nodeFlowerRed.active = Initializer.cardProxy.checkCardFlowerLevelUp(this.cfgData,this.cardData);
    },

    checkFrameAni: function() {
        if(this.lastFlower != -1 && null != this.cardData.flowerPoint) {
            let self = this;
            let bLastHas = this.lastFlower.filter((data) => {
                return data == self.iGetFramePoint;
            });
            //之前没有刚解锁播放特效
            if(null == bLastHas || bLastHas.length <= 0) {
                let bNowHas = this.cardData.flowerPoint.filter((data) => {
                    return data == self.iGetFramePoint;
                });
                if(bNowHas && bNowHas.length > 0) {
                    if (this.changeOpacityMask){
                        this.changeOpacityMask.node.active = true;
                        this.changeOpacityMask.opacity = 0;
                        this.changeOpacityMask.onFadeInOpcaty(255,0.8);
                    }
                } 
            }
        }
    },

    checkFlowerUpAni: function() {
        if(this.lastFlower == -1) {
            return;
        }
        let targetNode = null;
        if(this.lastFlower.length <= 0) {
            targetNode = this.arrFlowerPoint[0];
        } else if(null != this.cardData.flowerPoint && this.cardData.flowerPoint.length > this.lastFlower.length) {
            let index = -1;
            let self = this;
            for(let i = 0, len = this.cardData.flowerPoint.length; i < len; i++) {
                let bHas = this.lastFlower.filter((data) => {
                    return data == self.cardData.flowerPoint[i];
                });
                if(null == bHas || bHas.length <= 0) {
                    index = this.cardData.flowerPoint[i];
                    break;
                }
            }
            if(index > -1) {
                targetNode = this.arrFlowerPoint[index - 1];
            }
        }
        if(null != targetNode) {
            let worldPos = targetNode.node.parent.convertToWorldSpaceAR(targetNode.node.position);
            let targetPos = this.skFlowerUp.node.parent.convertToNodeSpaceAR(worldPos);
            this.skFlowerUp.node.position = targetPos;
            this.skFlowerUp.node.active = true;
            this.skFlowerUp.setAnimation(0, "appear", false);
        }
    },

    onShowLevelEffect(){
        this.nodeEffect.active = true;
        this.nodeShengxing.active = false;
        this.lbllastlevel.string = String(this.lastlevel);
        this.lblcurlevel.string = String(this.cardData.level);
        this.nodeShengji.active = true;
        
        let add = (1 + this.lastlevel * (0.2 + this.cardData.star * 0.1))
        let nextadd = (1 + this.cardData.level * (0.2 + this.cardData.star * 0.1));
        for (var ii = 0; ii < this.lbllastproarr.length;ii++) {
            let lbl = this.lbllastproarr[ii];
            let curlbl = this.lblcurproarr[ii];
            let paramBaseValue = this.cfgData['ep' + (ii + 1)];
            lbl.string = "" + Math.ceil(paramBaseValue * add);
            curlbl.string = "" + Math.ceil(paramBaseValue * nextadd);
        }
    },

    onShowStarEffect(){
        this.nodeEffect.active = true;
        this.nodeShengji.active = false;
        this.lbllaststar.string = String(this.laststar + 1);
        this.lblcurstar.string = String(this.cardData.star + 1);
        let starParamCfg = localcache.getFilter(localdb.table_card_starup,'quality',
            this.cfgData.quality,'star',this.cardData.star);
        let laststarParamCfg = localcache.getFilter(localdb.table_card_starup,'quality',
            this.cfgData.quality,'star',this.laststar);
        this.lbllastlevellimit.string = "" + laststarParamCfg.lvmax;
        this.lblcurlevellimit.string = "" + starParamCfg.lvmax;
        this.nodeShengxing.active = true;
        let add = (1 + this.cardData.level * ( 0.2 + this.laststar * 0.1))
        let nextadd = (1 + this.cardData.level * ( 0.2 + this.cardData.star * 0.1));
        for (var ii = 0; ii < this.lbllastproarr.length;ii++){
            let lbl = this.lbllastproarr[ii];
            let curlbl = this.lblcurproarr[ii];
            let paramBaseValue = this.cfgData['ep' + (ii + 1)];
            lbl.string = "" + Math.ceil(paramBaseValue * add);
            curlbl.string = "" + Math.ceil(paramBaseValue * nextadd);
        }
    },

    onShowFlowerUnlockEffect: function() {
        this.nodeUnlockEff.active = true;
        let cfgData = localcache.getFilter(localdb.table_card_flower, "pinzhi", this.cfgData.quality, "flower_point", 1);
        if(cfgData) {
            for(let i = 1; i <= 4; i++) {
                if(cfgData["ep" + i] > 0) {
                    this.urlUnlockProp.url = UIUtils.uiHelps.getPinzhiPicNew(i);
                    this.lbUnlockProp.string = UIUtils.uiHelps.getPinzhiStr(i);
                    this.lbUnlockLastProp.string = this.calProp(i, this.cardData.level, this.cardData.star, this.cardData.imprintLv, this.lastFlower); 
                    this.lbUnlockCurProp.string = this.calProp(i, this.cardData.level, this.cardData.star, this.cardData.imprintLv, this.cardData.flowerPoint); 
                    break;
                }
            }
        }
    },

    onClickTab(t, e) {
        this.chooseIdx = Number(e);
        for (let i = 0, len = this.tgArr.length; i < len; i++) {
            let flag = this.chooseIdx == i;
            let btn = this.tgArr[i];
            btn.node.getChildByName("Label").color = flag ? this.secColor : this.norColor;
        }
        this.updateProp();
    },

    updateProp (bForceFlower) {
        this.cardData = Initializer.cardProxy.cardMap[this.cfgData.id];
        let starParamCfg = localcache.getFilter(localdb.table_card_starup, 'quality',
         this.cfgData.quality, 'star', this.cardData.star);
        let starParamList = localcache.getFilters(localdb.table_card_starup, 'quality',
         this.cfgData.quality);
        starParamList.sort((a, b) => {
            return b.star - a.star;
        });
        if(null != this.cardData.flowerPoint && this.cardData.flowerPoint.length > 0) {
            let self = this;
            let hasFrame = this.cardData.flowerPoint.filter((data) => {
                return data == self.iGetFramePoint;
            });
            if(hasFrame && hasFrame.length > 0) {
                this.urlSpecialFrame.url = this.urlSpecialFrame2.url = UIUtils.uiHelps.getCardSpecialFrame(this.cfgData.quality);
                this.urlSpecialFrame.node.active = this.urlSpecialFrame2.node.active = true;
            } else {
                this.urlSpecialFrame.node.active = this.urlSpecialFrame2.node.active = false;
            }
            let hasParticle = this.cardData.flowerPoint.filter((data) => {
                return data == self.iGetEffPoint;
            });
            if(hasParticle && hasParticle.length > 0) {
                let url = config.Config.skin + "/res/particle/" + this.cfgData.texiao;
                cc.resources.load(url, cc.SpriteFrame,
                    function(o, i) {
                        if (null == o && null != i) {
                            self.node && (this.partical.spriteFrame = i);
                        }
                });
                this.partical.node.active = true;
            } else {
                this.partical.node.active = false;
            }
        } else {
            this.urlSpecialFrame.node.active = this.urlSpecialFrame2.node.active = false;
            this.partical.node.active = false;
        }
        let maxStar = starParamList[0].star;
        //等级
        this.lbLevel.string = i18n.t("CARD_LEVEL_TIPS", { v1: this.cardData.level, v2: starParamCfg.lvmax });
        this.pgbLevel.progress = this.cardData.level / starParamCfg.lvmax;
        this.pgbLevel.barSprite.node.active = this.cardData.level > 0;
        let arrProp = [];
        for(let i = 1; i <= 4; i++) {
            let pIndex = i - 1;
            arrProp[i] = this.calProp(i, this.cardData.level, this.cardData.star, this.cardData.imprintLv, this.cardData.flowerPoint);
            this.lbParam[pIndex].string = arrProp[i];
        }
        switch(this.chooseIdx) {
            case 0:
                this.nCard.active = true;
                this.nFlower.active = false;
                this.nFlowerUp.active = false;
                this.nYinhenUp.active = false;
                this.nStarUp.active = false;
                this.nodeLevelUpRed.active = false;
                this.nLevelUp.active = this.cardData.level < starParamList[0].lvmax;
                this.nMax.active = !this.nLevelUp.active;
                this.lbLevelStarNeed.string = this.cardData.level >= starParamCfg.lvmax ? i18n.t("CARD_COUNDITION_TIPS2", { v2: this.cardData.star + 1}) : " "; 
                let nextstarParamCfg = localcache.getFilter(localdb.table_card_starup, 'quality', this.cfgData.quality, 'star', this.cardData.star + 1);
                if(null != nextstarParamCfg) {
                    this.lbMax.string = i18n.t("CARD_COMMON_TIPS5", {v1: nextstarParamCfg.lvmax});  
                } else {
                    this.lbMax.string = i18n.t("CARD_COMMON_TIPS6");
                }
                this.nProps.active = !this.nMax.active;
                if(this.nProps.active) {
                    for(let i = 1; i <= 4; i++) {
                        let pIndex = i - 1;
                        let val = this.calProp(i, this.cardData.level + 1, this.cardData.star, this.cardData.imprintLv, this.cardData.flowerPoint);
                        this.lbParamAddTxt[pIndex].string = i18n.t("COMMON_ADD_3", { num: val - arrProp[i] });
                    }

                    let cardlvCfg = localcache.getFilter(localdb.table_card_lv, "pinzhi", this.cfgData.quality, "lv", this.cardData.level);
                    this.showDetail(cardlvCfg.cost, this.cardUpArr, this.lbGold, this.cardMerterial1, this.nLevelUpRed, this.cardData.level < starParamCfg.lvmax);
                    this.nodeLevelUpRed.active = this.nLevelUpRed.active;
                }
                break;
            case 1:
                this.nCard.active = true;
                this.nFlower.active = false;
                this.nFlowerUp.active = false;
                this.nYinhenUp.active = false;
                this.nLevelUp.active = false;
                this.nodeStarUpRed.active = false;
                this.nMax.active = this.cardData.star >= maxStar;
                this.nStarUp.active = !this.nMax.active;
                this.lbStarLevelNeed.string = this.cardData.level < starParamCfg.lvmax ? i18n.t("CARD_COUNDITION_TIPS", { v1: starParamCfg.lvmax }) : " ";
                if(this.cardData.star >= maxStar) {
                    this.lbMax.string = i18n.t("CARD_COMMON_TIPS4");
                } else {
                    this.lbMax.string = i18n.t("CARD_COMMON_TIPS3", {v1: starParamCfg.lvmax});
                }
                this.nProps.active = !this.nMax.active;
                if(this.nProps.active) {
                    for(let i = 0; i < maxStar + 1; i++) {
                        if(i < this.cardStarList.length) {
                            this.cardStarList[i].node.active = true;
                            this.cardStarList[i].url = UIUtils.uiHelps.getCardStar(i < this.cardData.star + 1);
                        } else {
                            let item = cc.instantiate(this.urlStar.node);
                            item.active = true;
                            let urlStar = item.getComponent(UrlLoad);
                            urlStar.url = UIUtils.uiHelps.getCardStar(i < this.cardData.star + 1);
                            this.urlStar.node.parent.addChild(item);
                            this.cardStarList.push(urlStar);
                        }
                    }      
                    for(let i = 1; i <= 4; i++) {
                        let pIndex = i - 1;
                        let val = this.calProp(i, this.cardData.level, this.cardData.star + 1, this.cardData.imprintLv, this.cardData.flowerPoint);
                        this.lbParamAddTxt[pIndex].string = i18n.t("COMMON_ADD_3", { num: val - arrProp[i] });
                    }
                    this.showStarDetail(starParamCfg);
                }
                break;
            case 2:
                this.nCard.active = true;
                this.nFlower.active = false;
                this.nFlowerUp.active = false;
                this.nLevelUp.active = false;
                this.nStarUp.active = false;
                this.nodeYinHenRed.active = false;
                let yinhenParamList = localcache.getFilters(localdb.table_card_yinhen, 'pinzhi', this.cfgData.quality);
                yinhenParamList.sort((a, b) => {
                   return b.yinheng - a.yinheng;
                });
                let maxYinhen = yinhenParamList[0].yinheng;
                this.nMax.active = this.cardData.imprintLv >= maxYinhen;
                if(this.nMax.active) {
                    this.lbMax.string = i18n.t("CARD_MARK4");
                }
                this.nYinhenUp.active = this.nProps.active = !this.nMax.active;
                if(this.nProps.active) {
                    for(let i = 0; i < maxYinhen; i++) {
                        if(i < this.cardYinhenList.length) {
                            this.cardYinhenList[i].node.active = true;
                            this.cardYinhenList[i].url = UIUtils.uiHelps.getCardYinhen(i < this.cardData.imprintLv);
                        } else {
                            let item = cc.instantiate(this.nYinhen);
                            item.active = true;
                            let urlYinhen = item.getComponent(UrlLoad);
                            urlYinhen.url = UIUtils.uiHelps.getCardYinhen(i < this.cardData.imprintLv);
                            this.nYinhen.parent.addChild(item);
                            this.cardYinhenList.push(urlYinhen);
                        }
                    }   
                    for(let i = 1; i <= 4; i++) {
                        let pIndex = i - 1;
                        let val = this.calProp(i, this.cardData.level, this.cardData.star, this.cardData.imprintLv+1, this.cardData.flowerPoint);
                        this.lbParamAddTxt[pIndex].string = i18n.t("COMMON_ADD_3", { num: val - arrProp[i] });
                    }
                }
                let yinhenCfg = localcache.getFilter(localdb.table_card_yinhen, "pinzhi", this.cfgData.quality, "yinheng", this.cardData.imprintLv + 1);
                if(null != yinhenCfg) {
                    this.showDetail(yinhenCfg["item" + this.cfgData.hero], this.cardYinhenArr, this.lbGoldYinhen, this.cardMerterial3, this.nYinhenRed);
                    this.nodeYinHenRed.active = this.nYinhenRed.active;
                }
                break;
            case 3:
                let flowerCfgList = localcache.getFilters(localdb.table_card_flower, "pinzhi", this.cfgData.quality);
                flowerCfgList.sort((a, b) => {
                    return b.flower_point - a.flower_point;
                });
                let maxPointId = flowerCfgList[0].flower_point;
                this.nLevelUp.active = false;
                this.nStarUp.active = false;
                this.nYinhenUp.active = false;
                this.nFlowerUp.active = true;
                this.nUnlockFlower.active = null == this.cardData.flowerPoint || this.cardData.flowerPoint.length == 0;
                this.nProps.active = !this.nUnlockFlower.active && null != this.cardData.flowerPoint && this.cardData.flowerPoint.length < maxPointId;
                this.nMax.active = null != this.cardData.flowerPoint && this.cardData.flowerPoint.length >= maxPointId;
                this.nFlowerUpDetail.active = !this.nUnlockFlower.active && !this.nMax.active;
                if(this.nMax.active) {
                    this.lbMax.string = i18n.t("CARD_FLOWER14");
                }
                this.nCard.active = false;
                this.nFlower.active = true;
                this.nodeFlowerRed.active = false;
                if(this.nUnlockFlower.active) {
                    this.setSelectFlowerPoint();
                    let curFlowerCfgData = localcache.getFilter(localdb.table_card_flower, "pinzhi", this.cfgData.quality
                     , "flower_point", this.curPointIndex);
                    let unlockCost = curFlowerCfgData.cost[0];
                    let num = Initializer.bagProxy.getItemCount(unlockCost.itemid);
                    this.urlUnlockCost.url = UIUtils.uiHelps.getItemSlot(unlockCost.itemid);
                    this.lbFlowerUnlockGold.string = i18n.t("COMMON_NUM", { f: num, s: unlockCost.count });
                    this.lbUnlockCondition.string = i18n.t("CARD_FLOWER11", { num: curFlowerCfgData.yinhen });
                    this.nUnlockRed.active = num >= unlockCost.count && this.cardData.imprintLv >= curFlowerCfgData.yinhen;       
                } else if(this.nFlowerUpDetail.active) {
                    if(!bForceFlower) {
                        this.setSelectFlowerPoint();
                    }
                    let curFlowerCfgData2 = localcache.getFilter(localdb.table_card_flower, "pinzhi", this.cfgData.quality
                     , "flower_point", this.curPointIndex);
                    this.setGetSpecialPoint(this.nGetFrame, 2, this.iGetFramePoint, this.lbGetFrame);
                    this.setGetSpecialPoint(this.nGetEff, 3, this.iGetEffPoint, this.lbGetEff);
                    this.setGetSpecialPoint(this.nGetCard, 4, this.iGetCardPoint, this.lbGetCard);

                    if(null != curFlowerCfgData2) {
                        this.showDetail(curFlowerCfgData2.cost, this.flowerUpArr, this.lbGoldFlower, this.cardMerterial4, this.nFlowerUpRed, this.cardData.imprintLv >= curFlowerCfgData2.yinhen);
                        this.nodeFlowerRed.active = this.nFlowerUpRed.active;
                        this.lbFlowerCondition.string = this.cardData.imprintLv >= curFlowerCfgData2.yinhen ? " " : i18n.t("CARD_FLOWER11", { num: curFlowerCfgData2.yinhen });
                        let tmpArray = [];
                        Utils.utils.copyList(tmpArray, this.cardData.flowerPoint);
                        tmpArray.push(this.curPointIndex);
                        for(let m = 1; m <= 4; m++) {
                            let pIndex = m - 1;
                            let val = this.calProp(m, this.cardData.level, this.cardData.star, this.cardData.imprintLv, tmpArray);
                            this.lbParamAddTxt[pIndex].string = i18n.t("COMMON_ADD_3", { num: val - arrProp[m] });
                        }
                    }  
                } else {
                    this.setSelectFlowerPoint();
                }
                
                for(let k = 0, len = this.arrNBranches.length; k < len; k++) {
                    let bShow = k < this.cfgData.quality;
                    this.arrNBranches[k].active = bShow;
                    let arrPoint = this.dicFlowerPoint[k];
                    if(bShow) {
                        for(let j = 0, jLen = arrPoint.length; j < jLen; j++) {
                            let scPoint = arrPoint[j];
                            let id = Number(scPoint.node.name.replace("flower", ""));
                            let cfgData = localcache.getFilter(localdb.table_card_flower, "pinzhi", this.cfgData.quality
                             , "flower_point", id);
                            scPoint.setData(id, this.cardData.flowerPoint, cfgData, this.cardData, this.curPointIndex);
                        }
                    }
                }
                break;
        }
    },

    setGetSpecialPoint: function(node, quality, pointId, lbCondition) {
        node.active = this.cfgData.quality >= quality;
        let bCondition = this.cardData.flowerPoint.filter((a) => {
            return a == pointId;
        });
        lbCondition.string = i18n.t(bCondition && bCondition.length > 0 ? "CARD_FLOWER10" : "CARD_FLOWER9");
        lbCondition.node.color = bCondition && bCondition.length > 0 ? new cc.Color(81, 45, 19, 255) : new cc.Color(235, 86, 98, 255);
    },

    //帮忙选择卡牌
    setSelectFlowerPoint: function() {
        if(this.cardData.flowerPoint == null || this.cardData.flowerPoint.length <= 0) {
            this.curPointIndex = 1;
        } else {
            let canChoiceList = Initializer.cardProxy.getCanFlowerUpPoint(this.cfgData.quality, this.cardData.flowerPoint);
            if(canChoiceList.length <= 0) {
                this.curPointIndex = 0;
            } else if(canChoiceList.length == 1) {
                this.curPointIndex = canChoiceList[0].flower_point; 
            } else {
                let data = Initializer.cardProxy.getNearestCanUpPoint(canChoiceList);
                this.curPointIndex = data ? data.flower_point : 0;
            }
        }
    },

    showDetail: function(arrCostData, arrCostNode, lbGold, pbItem, nRed, bFlag = true) {
        for (var ii = 0; ii < arrCostNode.length; ii++) {
            arrCostNode[ii].node.active = false;
        }
        let flag = true;
        let index = 0;
        for (var ii = 0; ii < arrCostData.length; ii++) {
            if (Initializer.bagProxy.getItemCount(arrCostData[ii].itemid) < arrCostData[ii].count) {
                flag = false;
            }
            let itemdata = { id: arrCostData[ii].itemid, count: arrCostData[ii].count, kind: arrCostData[ii].kind};
            if(itemdata.id == 3) {
                lbGold.string = itemdata.count;
                continue;
            }
            if (index < arrCostNode.length) {
                arrCostNode[index].node.active = true;
                arrCostNode[index].data = itemdata;
                index++;
                continue;
            }
            let item = cc.instantiate(pbItem.node);
            item.active = true;
            let cardmerterialitem = item.getComponent(cardMerterialItem);
            cardmerterialitem.data = itemdata;
            pbItem.node.parent.addChild(item);
            arrCostNode.push(cardmerterialitem);
            index++;
        }
        flag = flag && bFlag; 
        nRed.active = flag;
    },

    showStarDetail: function(starParamCfg) {
        this.lbGoldStar.string = Utils.utils.formatMoney(starParamCfg.yinliang);
        for (var ii = 0; ii < this.cardStarArr.length; ii++) {
            this.cardStarArr[ii].node.active = false;
        }
        let num = Initializer.bagProxy.getItemCount(this.cfgData.item);
        let OmnipotentCardID = Initializer.cardProxy.getOmnipotentCardID(this.cfgData.quality);
        let oNum = Initializer.bagProxy.getItemCount(OmnipotentCardID);
        let listdata = [this.cfgData.item, OmnipotentCardID];
        for (var ii = 0; ii < 2; ii++) {
            let item = this.cardStarArr[ii];
            if (item == null) {
                let node = cc.instantiate(this.cardMerterial2.node);
                this.cardMerterial2.node.parent.addChild(node);
                item = node.getComponent(cardMerterialItem);
                this.cardStarArr.push(item);
            }
            item.node.active = true;
            item.data = {id: listdata[ii], kind: 1, count: starParamCfg.cost, idx: ii};
        }
        if (num > 0) {
            this.onRefreshChoose(0);
        } else if (oNum > 0) {
            this.onRefreshChoose(1);
        }
        this.nStarUpRed.active = ((num + oNum) >= starParamCfg.cost) && (this.cardData.level >= starParamCfg.lvmax);
        this.nodeStarUpRed.active = this.nStarUpRed.active;
    },

    calProp: function(prop, lv, star, yinhengLv, arrFlowerPoint) {
        let paramBaseValue = this.cfgData['ep' + prop];
        let val = Math.ceil(paramBaseValue * (1 + lv * (0.2 + star * 0.1)));
        let pinzhiValue = localcache.getFilter(localdb.table_card_yinhen, 'pinzhi',
         this.cfgData.quality, 'yinheng', yinhengLv);
        if(null != pinzhiValue) {
            val += pinzhiValue['ep' + prop];
        }
        if(null != arrFlowerPoint && arrFlowerPoint.length > 0) {
            for(let j = 0, len = arrFlowerPoint.length; j < len; j++) {
                let flowerData = localcache.getFilter(localdb.table_card_flower, 'pinzhi',
                 this.cfgData.quality, 'flower_point', arrFlowerPoint[j]);
                if(null != flowerData) {
                    val += flowerData['ep' + prop];
                }
            }
        }
        return val;
    },

    onRefreshChoose(idx) {
        this.chooseMerteralIdx = idx;
        for (var ii = 0; ii < this.cardStarArr.length;ii++){
            this.cardStarArr[ii].setChoose(idx == ii);
        }
    },

    onClickLevelUp() {
        let cardlvCfg = localcache.getFilter(localdb.table_card_lv, "pinzhi", this.cfgData.quality, "lv", this.cardData.level);
        if (cardlvCfg == null) return;
        for (var ii = 0; ii < cardlvCfg.cost.length; ii++){
            let data = cardlvCfg.cost[ii];
            let num = Initializer.bagProxy.getItemCount(data.itemid);
            if (num < data.count) {
                if (data.itemid == 3){
                    Utils.utils.showConfirm(i18n.t("CARD_COMMON_TIPS1"), () => {
                        Utils.utils.openPrefabView("qifu/QifuView");                        
                    });
                } else {
                    let cfg = localcache.getItem(localdb.table_item,data.itemid);
                    Utils.utils.showConfirm(i18n.t("CARD_COMMON_TIPS2",{v1:data.count - num,v2:cfg.name}), () => {
                        Utils.utils.openPrefabView("tanhe/MainTanHeView");                     
                    });
                }
                return;
            }
        }
        if (Initializer.cardProxy.upgradeFiveFlag){
            Initializer.cardProxy.sendUpgradeCardFive(this.cfgData.id);
        } else {
            Initializer.cardProxy.sendUpgradeCard(this.cfgData.id);
        }
    },

    onClickUpgradeStar() {
        let starParamCfg = localcache.getFilter(localdb.table_card_starup, 'quality',
         this.cfgData.quality,'star',this.cardData.star);
        if (starParamCfg && starParamCfg.lvmax > this.cardData.level) {
            Utils.alertUtil.alert(i18n.t("CARD_COMMON_TIPS3",{v1:starParamCfg.lvmax}))
            return;
        }
        let cardCount = Initializer.bagProxy.getItemCount(this.cfgData.item);
        if(cardCount < this.starCost) {//原卡不足
            let omnipotentCardID = Initializer.cardProxy.getOmnipotentCardID(this.cfgData.quality);
            let omnipotentcardCount = Initializer.bagProxy.getItemCount(omnipotentCardID);
            if((cardCount + omnipotentcardCount) >= this.starCost) {//弹二次确认
                let needCard = this.starCost - cardCount;
                let tip = Initializer.cardProxy.getOmnipotentCardName(this.cfgData.quality); 
                Utils.utils.showConfirm(i18n.t("SHOW_CARD_1") + needCard + i18n.t("SHOW_CARD_2")+tip+i18n.t("SHOW_CARD_3")
                +i18n.t("SHOW_CARD_4")+omnipotentcardCount, () => {
                    Initializer.cardProxy.upgradeCardStar(this.cfgData.id, () => {
                        this.showStarSpine();
                    });
                });
                return;
            } else {//万能卡不足
                Utils.alertUtil.alert(i18n.t("SHOW_CARD_5"));//弹提示-材料不足
                return;
            }
        }
        Initializer.cardProxy.upgradeCardStar(this.cfgData.id, () => {
            this.showStarSpine();
        });
    },

    onClickYinhenUp: function() {
        let yinhenParamList = localcache.getFilters(localdb.table_card_yinhen, 'pinzhi', this.cfgData.quality);
        yinhenParamList.sort((a, b) => {
           return b.yinheng - a.yinheng;
        });
        let maxYinhen = yinhenParamList[0].yinheng;

        let yinhenCfg = localcache.getFilter(localdb.table_card_yinhen, "pinzhi", this.cfgData.quality, "yinheng", this.cardData.imprintLv + 1);
        if(null == yinhenCfg || this.cardData.imprintLv >= maxYinhen) {
            Utils.alertUtil.alert(i18n.t("CARD_MARK5"));
            return;
        } else if(this.cfgData.hero > 0) {
            let cost = yinhenCfg["item" + this.cfgData.hero];
            for (var ii = 0; ii < cost.length; ii++) {
                let data = cost[ii];
                let num = Initializer.bagProxy.getItemCount(data.itemid);
                if (num < data.count) {
                    if (data.itemid == 3) {
                        Utils.utils.showConfirm(i18n.t("CARD_COMMON_TIPS1"), () => {
                            Utils.utils.openPrefabView("qifu/QifuView");                        
                        });
                    } else {
                        let self = this;
                        let cfg = localcache.getItem(localdb.table_item, data.itemid);
                        Utils.utils.showConfirm(i18n.t("CARD_COMMON_TIPS8", { name: cfg.name }), () => {
                            Utils.utils.openPrefabView("jiaoyou/JiaoyouChapterView", null, { servantId: self.cfgData.hero });                     
                        });
                    }
                    return;
                }
            }
    
            Initializer.cardProxy.sendCardImprintUpLv(this.cfgData.id, () => {
                this.showStarSpine();
            });
        }
    },

    //点击选择升华升级节点
    onClickFlowerPoint: function(event, param) {
        this.curPointIndex = Number(param);
        this.updateProp(true);
    },

    //点击解锁升华
    onClickFlowerUnlock: function() {
        let curFlowerCfgData = localcache.getFilter(localdb.table_card_flower, "pinzhi", this.cfgData.quality
         , "flower_point", this.curPointIndex);
        if (curFlowerCfgData == null) return;
        if(curFlowerCfgData.yinhen > this.cardData.imprintLv) {
            Utils.alertUtil.alert(i18n.t("CARD_FLOWER11", { num: curFlowerCfgData.yinhen }));
            return;
        }
        for (var ii = 0; ii < curFlowerCfgData.cost.length; ii++) {
            let data = curFlowerCfgData.cost[ii];
            let num = Initializer.bagProxy.getItemCount(data.itemid);
            if (num < data.count) {
                if (data.itemid == 3) {
                    Utils.utils.showConfirm(i18n.t("CARD_COMMON_TIPS1"), () => {
                        Utils.utils.openPrefabView("qifu/QifuView");                        
                    });
                } else {
                    let cfg = localcache.getItem(localdb.table_item, data.itemid);
                    Utils.utils.showConfirm(i18n.t("CARD_COMMON_TIPS7", { name: cfg.name }), () => {
                        Utils.utils.openPrefabView("card/CardResolveView");                     
                    });
                }
                return;
            }
        }
        
        Initializer.cardProxy.sendCardFlowerPoint(this.cfgData.id, this.curPointIndex, () => {
            this.showStarSpine();
        });
    },

    //点击升级升华
    onClickFlowerUp: function() {
        this.onClickFlowerUnlock();
    },

    onClickCard: function() {
        this.urlBigCard.node.active = true;
    },

    onClickBigCard: function() {
        this.urlBigCard.node.active = false;
    },
});
