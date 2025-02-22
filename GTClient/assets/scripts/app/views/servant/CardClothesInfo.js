let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");
var Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        servantShow: UrlLoad,//角色
        lbName:cc.Label,//hero名字
        lbDressName: cc.Label,//服装名字
        tipInfo:cc.Label,
        btnGet:cc.Button,
    },
    onLoad(){
        let cardCfg = this.node.openParam;
        if(cardCfg){
            this.cardCfg = cardCfg;
            this.showHeroClothesInfo();
        }
    },
    showHeroClothesInfo() {
        let dressID = this.cardCfg.clothe.itemid;
        let cfgData = localcache.getFilter(localdb.table_heroDress, "id", dressID);
        if (null != cfgData){
            let heroCfg = localcache.getItem(localdb.table_hero,cfgData.heroid);
            this.lbName.string = heroCfg.name;
            this.lbDressName.string = cfgData.name;
            this.servantShow.url = UIUtils.uiHelps.getServantSkinSpine(cfgData.model);

            //播放音效
            if(cfgData.voice != ""){
                let voiceArray = cfgData.voice.split('|');
                let chooseVoice = voiceArray[Math.floor(Math.random() * voiceArray.length)];
                if (chooseVoice) {
                    Utils.audioManager.playSound(chooseVoice, !0, !0);
                }
            }else{
                let voiceSys = r.voiceProxy.randomHeroVoice(cfgData.heroid);
                if (this.voiceSys) {
                    Utils.audioManager.playSound("servant/" + voiceSys.herovoice, !0, !0);
                }
            }
        }
        let haveGet = Initializer.servantProxy.checkDressHaveGet(dressID);
        if(haveGet){//服装已经领取
            this.tipInfo.string = i18n.t("CARD_CLOTHESINFO_1");
            this.btnGet.node.active = false;
        }else{
            this.btnGet.node.active = false;//卡未解锁
            let unlockStar = this.cardCfg.clotheunlockstar?this.cardCfg.clotheunlockstar:0;
            if(unlockStar > 0){
                this.tipInfo.string = i18n.t("CARD_CLOTHESINFO_2")+unlockStar+i18n.t("CARD_CLOTHESINFO_3");//星级解锁条件
            }
            let cardInfo = Initializer.cardProxy.getCardInfo(this.cardCfg.id);
            if(cardInfo){//服装已经解锁,但还未领取
                if(cardInfo.star < unlockStar){
                }else{
                    this.tipInfo.string = '';
                    this.btnGet.node.active = true;//卡已解锁
                }
            }
        }
    },
    onClickBack() {
        Utils.utils.closeView(this);
    },
    onClickGet() {
        Initializer.cardProxy.unLockClothe(this.cardCfg.id,()=>{
            this.tipInfo.string = i18n.t("CARD_CLOTHESINFO_1");
            this.btnGet.node.active = false;
            let dressID = this.cardCfg.clothe.itemid;
            Utils.utils.openPrefabView("AlertItemShow", !1,{id:dressID,count:1,kind:111});
            facade.send(Initializer.cardProxy.CARD_DATA_UPDATE);
        });
    },
});
