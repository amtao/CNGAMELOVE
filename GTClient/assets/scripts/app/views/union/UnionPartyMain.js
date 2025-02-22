let Initializer = require("Initializer");
var Utils = require("Utils");
let UIUtils = require("UIUtils");
let UrlLoad = require("UrlLoad");
import { UNION_PARTY_STATE,UNION_PARTY_HANDUP_STATE } from "GameDefine";
var config = require("Config");

cc.Class({
    extends: cc.Component,
    properties: {
        lblPeopleNum: cc.Label,
        lblEffect:cc.RichText,
        lblNotice:cc.RichText,
        progressBar: cc.ProgressBar,
        lblRedBagNum:cc.Label,
        lblProgressNum:cc.Label,
        iconArr:[UrlLoad],
        nodeHangUp:cc.Node,
        npcUrl:UrlLoad,
        nodeChooseArr:[cc.Node],
        urlEff: UrlLoad,
        foodBacUrl:UrlLoad,
        foodUrl:UrlLoad,
        lblbtnTitle:cc.Label,
    },
    ctor() {
        this.mCurrentTime = 0;
        this.mFixTime = 30;
        this.mStop = false;
        this.mState = 0;
        this.refreshStateTime = 0; //记录下次开启和结束的时间戳
        this.beganHandUpFlag = false;
        this.handUpTime = 0;
        this.handeUpState = 0;
        this.chooseIdx = 0;
        this.msgListData = [];
        this.isPlaying = false;
        this.handLookAllTime = 0;
        this.totalLeftTime = 0;
    },

    onLoad() {      
        facade.subscribe("COMMON_CLOSE_VIEW",this.colseCommonView,this);
        facade.subscribe("UNION_REDBAG_DATA",this.updateRedBag,this);
        facade.subscribe("UNION_RESOURCEBASE",this.initView,this);
        facade.subscribe("UNION_PARTY",this.updatePartyBuff,this);
        facade.subscribe("UNION_CLUB_LOG_UPDATE",this.onAddMsg,this);
        this.lblEffect.string = "";
        this.lblNotice.string = "";
        this.progressBar.node.active = false;
        this.lblProgressNum.string = "";
        this.chooseIdx = Initializer.unionProxy.partyData.musician - 1;
        this.initView();
        this.updatePartyBuff();
        this.exchangeMusician(this.chooseIdx);
        if (this.mState == UNION_PARTY_STATE.CAN_ENTER){
            let self = this;
            Initializer.unionProxy.sendUpdateClubInfo(function () {
                Initializer.unionProxy.requestRedBagFlag = true;
                // self.updateRedBag();
                // self.onAddMsg();
            })
        }

        for (let ii = 0; ii < 4; ii++){
            let cg = localcache.getItem(localdb.table_party_music,ii+1);
            this.iconArr[ii].url = UIUtils.uiHelps.getMusicianHead(cg.icon);
        } 
        this.foodBacUrl.url = UIUtils.uiHelps.getUnionIcon('food_' + Initializer.unionProxy.getPartyLvDes() + '_empty');      
    },

    initView(){
        let state = Initializer.unionProxy.partyState();
        this.mState = state;
        if (this.mState != UNION_PARTY_STATE.CAN_ENTER){
            Initializer.unionProxy.closeParty();
            return;
        }
        switch(state){
            case UNION_PARTY_STATE.OPEN_BUT_NOT_ENTER:{
                this.refreshStateTime = Initializer.unionProxy.getOpenOrEndTime(true);
            }
            break;
            case UNION_PARTY_STATE.CAN_ENTER:{
                this.refreshStateTime = Initializer.unionProxy.getOpenOrEndTime(false);
            }
            break;
            case UNION_PARTY_STATE.END:{
                Initializer.unionProxy.requestRedBagFlag = false;
                this.lblEffect.string = i18n.t("UNION_TIPS42");
            }
            break;
        }
        let data = Initializer.unionProxy.partyResourceData;
        let peopleNum = 0;
        if (data && data.joinPartyPeople && data.joinPartyPeople.length > 0){
            peopleNum = data.joinPartyPeople.length;
        }
        this.lblPeopleNum.string = i18n.t("UNION_TIPS39",{v1:peopleNum});
        
    },

    updatePartyBuff(){
        let cfg = localcache.getItem(localdb.table_party_buff, Initializer.unionProxy.partyData.buff);
        if (cfg){
            this.lblEffect.string = i18n.t("UNION_TIPS44",{v1:cfg.buff});
            this.urlEff.url = config.Config.skin + "/prefabs/effect/" + cfg.eff;
            this.urlEff.node.y = cfg.id == 1 ? 0 : -this.urlEff.node.parent.y;
        }
        else{
            this.lblEffect.string = i18n.t("UNION_TIPS42");
        }
        this.lblbtnTitle.string = i18n.t("UNION_TIPS43");
        let partyMusicCfg = localcache.getItem(localdb.table_party_music,Initializer.unionProxy.partyData.musician);
        if (partyMusicCfg){
            this.npcUrl.url = UIUtils.uiHelps.getMusicianSpine(partyMusicCfg.index);
            Utils.audioManager.playBGM(partyMusicCfg.bgm);
        }
        let club_partyOnhookTime = Utils.utils.getParamInt("club_partyOnhookTime");
        this.handLookAllTime = club_partyOnhookTime;
        if (Initializer.unionProxy.partyData.hookStart > 0 && Initializer.unionProxy.partyData.hookStart + club_partyOnhookTime <= Utils.timeUtil.second){
            this.beganHandUpFlag = false;
            this.progressBar.node.active = false;
            this.lblProgressNum.string = "";
            this.foodUrl.url = "";
            if (Initializer.unionProxy.partyData.isHookPick == 1){
                this.nodeHangUp.active = false;
            }
            else{
                this.handeUpState = UNION_PARTY_HANDUP_STATE.END;
                this.nodeHangUp.active = true;
                this.lblbtnTitle.string = i18n.t("TREASURE_GET_GROUP");
            }
        }
        else if(Utils.timeUtil.second - Initializer.unionProxy.partyData.hookStart < club_partyOnhookTime){
            //this.handUpTime = Initializer.unionProxy.partyData.hookStart + club_partyOnhookTime;
            if (this.totalLeftTime <= 0){
                this.totalLeftTime = Initializer.unionProxy.partyData.hookStart + club_partyOnhookTime - Utils.timeUtil.second;
            }           
            this.beganHandUpFlag = true;
            this.progressBar.node.active = true;
            this.nodeHangUp.active = false;
            if (this.totalLeftTime < club_partyOnhookTime / 2){
                this.foodUrl.url = UIUtils.uiHelps.getUnionIcon('food_' + Initializer.unionProxy.getPartyLvDes() + '_full'); 
            }
            else{
                this.foodUrl.url = UIUtils.uiHelps.getUnionIcon('food_' + Initializer.unionProxy.getPartyLvDes() + '_middle');
            }
        }
        else if(Initializer.unionProxy.partyData.hookStart == 0 && Initializer.unionProxy.partyData.isHookPick != 1){
            this.foodUrl.url = UIUtils.uiHelps.getUnionIcon('food_' + Initializer.unionProxy.getPartyLvDes() + '_full');
        }   
    },

    updateRedBag(){
        let num = Initializer.unionProxy.getRedBagNum();
        this.lblRedBagNum.string = `${num}`;
        this.lblRedBagNum.node.parent.active = num > 0;
    },

    /**点击红包*/
    onClickRedBag(){
        this.closeHandUp();
        let state = Initializer.unionProxy.partyState();
        if (state != 3){
            Utils.alertUtil.alert(i18n.t("UNION_TIPS36"));
            return;
        }
        if (Initializer.unionProxy.getRedBagNum() > 0){
            Initializer.unionProxy.sendRobRedBag(function(){
                Utils.utils.openPrefabView("union/UnionRedPackage");
            });
        }       
    },

    /**点击花签*/
    onClickRoll(){
        this.closeHandUp();
        let state = Initializer.unionProxy.partyState();
        if (state != 3){
            Utils.alertUtil.alert(i18n.t("UNION_TIPS36"));
            return;
        }
        Initializer.unionProxy.isEnterPotOrPotInfo();
    },

    /**提升氛围*/
    onClickAtmosphere(){
        this.closeHandUp();
        let state = Initializer.unionProxy.partyState();
        if (state != 3){
            Utils.alertUtil.alert(i18n.t("UNION_TIPS36"));
            return;
        }
        Utils.utils.openPrefabView("union/UnionMoodUpView");
    },

    /**点击乐师*/
    onClickMusician(t,idx){
        let state = Initializer.unionProxy.partyState();
        if (state != 3){
            Utils.alertUtil.alert(i18n.t("UNION_TIPS36"));
            return;
        }
        let index = Number(idx) - 1;
        if (this.chooseIdx == index) return;
        this.chooseIdx = index;
        this.exchangeMusician(index);
        Initializer.unionProxy.sendChangeMusician(index+1);
    },

    exchangeMusician(index){
        for (let ii = 0; ii < this.nodeChooseArr.length;ii++){
            this.nodeChooseArr[ii].active = (ii == index);
        }       
    },
    
    onClose() {
        Utils.utils.closeView(this);
    },

    /**重置挂机时间*/
    resetHandUp(){
        this.mCurrentTime = 0;
        this.mStop = false;
    },

    /**关闭挂机*/
    closeHandUp(){
        this.mStop = true;
        this.mCurrentTime = 0;       
    },

    update(dt){
        if (this.beganHandUpFlag){
            this.totalLeftTime -= dt;
            let remain = Math.ceil(this.totalLeftTime);
            if (remain < 0) remain = 0;
            this.progressBar.progress = remain / this.handLookAllTime;
            this.lblProgressNum.string = Utils.timeUtil.second2hms(remain);
            if (remain <= 0){
                this.beganHandUpFlag = false;
                this.progressBar.node.active = false;
                this.lblProgressNum.string = "";
                this.foodUrl.url = "";
                if (Initializer.unionProxy.partyData.isHookPick == 1){
                    this.nodeHangUp.active = false;
                }
                else{
                    this.handeUpState = UNION_PARTY_HANDUP_STATE.END;
                    this.nodeHangUp.active = true;
                    this.lblbtnTitle.string = i18n.t("TREASURE_GET_GROUP");
                }
            }
            else if (remain < this.handLookAllTime / 2){
                this.foodUrl.url = UIUtils.uiHelps.getUnionIcon('food_' + Initializer.unionProxy.getPartyLvDes() + '_middle');
            }
        }
        if (this.mState == UNION_PARTY_STATE.END || this.mState == UNION_PARTY_STATE.NONE) return;
        if (this.refreshStateTime <= Utils.timeUtil.second){
            this.initView();
        }
        if (this.mStop || this.mState != UNION_PARTY_STATE.CAN_ENTER) return;
        this.mCurrentTime += dt;
        if (this.mCurrentTime >= this.mFixTime){
            this.closeHandUp();
            let self = this;
            Initializer.unionProxy.sendRandGameUser(function(){
                let randUserData = Initializer.unionProxy.randUserData;
                if (randUserData == null || randUserData.info == null){
                    self.resetHandUp();
                }
                else{
                    let randomData = Initializer.unionProxy.getRandomMinGameIdxAndName();             
                    Utils.utils.showConfirm(i18n.t("UNION_TIPS48", { v1:randUserData.info.name,v2:randomData[1]}), () => {                       
                        Initializer.unionProxy.getRandomMiniGame(randomData[0],randUserData.info);
                    }); 
                }
            });
        }
    },

    /**关闭界面的监听*/
    colseCommonView(){
        let midLayer = cc.find("Canvas/midLayer");
        if (midLayer && midLayer.childrenCount > 0){
            let child = midLayer.children[midLayer.childrenCount - 1];
            let nameArr = child.name.split(",");
            if (child.name == this.node.name){
                this.resetHandUp();
            }
            else{
                this.closeHandUp();
            }
        }
    },

    /**享用美食*/
    onClickHandUp(){
        let state = Initializer.unionProxy.partyState();
        if (state != 3){
            Utils.alertUtil.alert(i18n.t("UNION_TIPS36"));
            return;
        }
        if (this.handeUpState == UNION_PARTY_HANDUP_STATE.DOING) return;
        if (this.handeUpState == UNION_PARTY_HANDUP_STATE.NONE){
            this.handeUpState = UNION_PARTY_HANDUP_STATE.DOING;
            let self = this;
            Initializer.unionProxy.sendStartHook(function(){
                //self.handUpTime = Initializer.unionProxy.partyData.hookStart + self.handLookAllTime;
                self.totalLeftTime = self.handLookAllTime + 0;
                self.beganHandUpFlag = true;
                self.progressBar.node.active = true;
                self.nodeHangUp.active = false;
            });
            
        }
        else if(this.handeUpState == UNION_PARTY_HANDUP_STATE.END){
            Initializer.unionProxy.sendPickHookAward();
            this.handeUpState = UNION_PARTY_HANDUP_STATE.NONE;
        }

    },

    onAddMsg(){
        let msgLog = Initializer.unionProxy.clubLog;
        let listdata = [];
        if (msgLog && msgLog.length > 0){
            listdata = msgLog.filter((data)=>{
                return data.type == 21;
            })
        }
        if (listdata.length > 1){
            listdata.sort((a,b)=>{
                return a.time < b.time ? -1 : 1;
            })
        }
        this.msgListData = listdata;
        this.onPlayMsg();
    },

    onPlayMsg(){
        if (this.msgListData.length <= 0 || this.isPlaying == true) return;
        this.isPlaying = true;
        let data = this.msgListData[this.playIdx];
        if (data == null){
            this.playIdx = 0;
            this.isPlaying = false;
            this.onPlayMsg();
            return;
        }
        let itemcfg = localcache.getItem(localdb.table_party_buff,data.num1)
        let msg = i18n.t("UNION_TIPS47",{v1:data.name,v2:itemcfg ? itemcfg.name : i18n.t("BAG_ITEM_TIP")});
        this.lblNotice.string = msg;
        this.lblNotice.node.stopAllActions();
        let width = this.lblNotice.node.width + 460;
        this.lblNotice.node.runAction(cc.sequence(cc.moveBy(width/80,cc.v2(-width,0)),cc.callFunc(()=>{
            this.lblNotice.node.x = 230;
            this.playIdx++;
            this.isPlaying = false;
            this.onPlayMsg();
        })))
    },

    onDestroy(){
        if (Initializer.guideProxy && Initializer.guideProxy.guideUI){
            Initializer.guideProxy.guideUI.clearRedBagTime();
        }
        Utils.audioManager.playBGM("MainScene",true);    
    },
    
});
