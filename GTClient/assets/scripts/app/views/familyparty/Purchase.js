

var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
var UIUtils = require("UIUtils");
let ItemSlotUI = require("ItemSlotUI")

import { CARDFRAME_TYPE,FIGHTBATTLETYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {

        fNode:cc.Node,
        sNode:cc.Node,

        fitemsNode:cc.Node,
        srole:UrlLoad,
        frole:UrlLoad,

        titleLabel:cc.Label,


        fitems:[ItemSlotUI],
        sitems:[ItemSlotUI],


        laString:cc.RichText,

        


        btns:[cc.Button],
        images:[UrlLoad],

        label1:cc.Label,
        label2:cc.Label,
        labeln:cc.Label,
        labeltime:cc.Label,

        cbBtn:cc.Node,
        buBtn:cc.Node,
        timeNode:cc.Node,


        lbarm:cc.Label,

        // foo: {
        //     // ATTRIBUTES:
        //     default: null,        // The default value will be used only when the component attaching
        //                           // to a node for the first time
        //     type: cc.SpriteFrame, // optional, default is typeof default
        //     serializable: true,   // optional, default is true
        // },
        // bar: {
        //     get () {
        //         return this._bar;
        //     },
        //     set (value) {
        //         this._bar = value;
        //     }
        // },
    },

    // LIFE-CYCLE CALLBACKS:
    //n.uiHelps.getServantSkinSpine(localcache.getItem(localdb.table_heroDress, herodress).model)
    onLoad () {

        this.timeIndex = 0;
        this.time = 0;

        this.showTime = false

        this.selectIndex = 0
        this.ids = this.node.openParam.id;
        this.hid = Initializer.famUserHProxy.selectHid
        let hookinf = Initializer.famUserHProxy.hook.hookInfo
        if(hookinf[this.ids] && hookinf[this.ids].hookEndTime - utils.timeUtil.second > 0){
            this.hid = Initializer.famUserHProxy.hook.hookInfo[this.ids].heroId
        }
        this.sdata = localcache.getItem(localdb.table_furniture_battle, this.ids);

        facade.subscribe("FURNITURE_HOOK", this.hookback, this);


        facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.update_UserData, this);
        this.oldarm = Initializer.playerProxy.userData.army
        this.update_UserData()

    },

    update_UserData(){
        UIUtils.uiUtils.showNumChange(this.lbarm, this.oldarm, Initializer.playerProxy.userData.army);
    },

    start () {
        this.fNode.active = false
        this.sNode.active = false
        if(this.ids<=Initializer.famUserHProxy.open.currentCopy){
            this.sNode.active = true
            this.titleLabel.string = i18n.t("HOMEPART_HOMEEDITOR_PURCHASE")
            this.showHooks()
        }else{
            this.fNode.active = true
            this.showFirst()
            this.titleLabel.string = i18n.t("HOMEPART_HOMEEDITOR_PURCHASE2")
        }
        this.setMnumber()
    },

    hookback(){
        if(Initializer.famUserHProxy.hook.hookInfo[this.ids]){
            let mods = Initializer.famUserHProxy.hook.hookInfo[this.ids]
            let time = mods.hookEndTime - utils.timeUtil.second 
            if(time > 0){
                this.time = time
                this.showTime = true
                this.timeIndex = 0;
                this.showTimehow()
            }
            this.cbBtn.active = !(time > 0)
            this.buBtn.active = time > 0
            this.timeNode.active = time > 0
        }else{
            this.cbBtn.active = true
            this.buBtn.active = false
            this.timeNode.active = false
        }
        if(this.hid == null){
            return
        }
        this.srole.url = UIUtils.uiHelps.getServantSpine(this.hid);
    },

    showHooks(){

        //1 几率 2数量

        let buff = this.sdata.buff
        let len1 = buff.length
        let lb1 = 0
        let lb2 = 0
        for (let i = 0; i < len1; i++) {
            let bufd = buff[i];
            if(bufd.type == 1){
                lb1 = bufd.num
            }
            if(bufd.type == 2){
                lb2 = bufd.num
            }
        }
        let cardlvx = utils.utils.getParamInt("furniture_cardlevel")
        let cardqx = utils.utils.getParamInt("furniture_cardquality")
        let hx = 0
        let hn = 0
        let string = utils.utils.getParamStr("furniture_parteprob")
        let string2 = utils.utils.getParamStr("furniture_partenumb")

        let prop = this.sdata.rwd[0].probab/100
        let hhid = this.getWakdata(this.sdata.heroneed)

        if(this.hid === null || hhid != this.hid){
            hx = parseInt(string.split("|")[0])
            hn = parseInt(string2.split("|")[0])
        }else{
            hx = parseInt(string.split("|")[1])
            hn = parseInt(string2.split("|")[1])
        }
        //	实际几率 = 基础prob * (1+卡牌品质*卡牌品质系数/100）*（1+卡牌等级^卡牌等级系数/100）* (1+伙伴几率影响系数/100)										
        //	实际数量 = 基础count * (1+卡牌品质*卡牌品质系数/100）*（1+卡牌等级^卡牌等级系数/100）* (1+伙伴数量影响系数/100)										
        let nnumber = 0
        let nx = prop
        //label1
        let cards = this.sdata.cards
        let len = cards.length
        for (let i = 0; i < len; i++) {
            let cid = cards[i];
            this.images[i].url = Initializer.cardProxy.getCardFrameUrl(cid, CARDFRAME_TYPE.SMALL_LONG);
            let level = 0
            let quality = 0
            if(Initializer.cardProxy.cardMap[cid]){
                let card = Initializer.cardProxy.cardMap[cid]
                level = card.level
                quality = card.cfgData.quality
                this.btns[i].interactable = true
                
            }else{
                this.btns[i].interactable = false
            }
            nnumber += (1+quality*cardqx/100)*(1+Math.pow(level,cardlvx/100))*(1+hn/100) - 1
            nx += lb1/100*(1+quality*cardqx/100)*(1+Math.pow(level,cardlvx/100))*(1+hx/100)
        }
        this.label1.string =  parseInt(nnumber*100) + "%"
        this.label2.string =  parseInt(nx*100) + "%"

        this.showItems(this.sitems,this.sdata.srwdshow)
        this.laString.string = this.sdata.partertalk
        this.hookback()
    },

    showFirst(){
        this.frole.url = UIUtils.uiHelps.getServantSpine(this.sdata.model)
        this.showItems(this.fitems,this.sdata.frwdshow)
        this.laString.string = this.sdata.npctalk
    },

    showItems(items,arrays){
        let len = arrays.length
        for (let i = 0; i < len; i++) {
            let data = arrays[i]
            items[i].data = data
        }
    },


    //点击卡片
    onClickBtnOne(t,e){
        let index = parseInt(e)
        let cards = this.sdata.cards
        let cid = cards[index];
        if(Initializer.cardProxy.cardMap[cid]){
            let cardData = Initializer.cardProxy.cardMap[cid]
            Initializer.cardProxy.currentCardList = Initializer.cardProxy.getNewCardList();
            utils.utils.openPrefabView("card/showCardDetail", null, {
                cardData: cardData,
                cfgData: cardData.cfgData,
            });
        }else{
            utils.utils.openPrefabView("draw/drawMainView");
        }    
    },

    onClickToteam(){
        utils.utils.openPrefabView("battle/BattleTeamView", null, { type: FIGHTBATTLETYPE.NORMAL });
    },


    //第一次走战斗
    onClickXiejia(){
        facade.subscribe("STORY_END", ()=>{
            Initializer.famUserHProxy.getBattlesinfo(this.ids)
            this.onClickClose()
        }, this);

        Initializer.playerProxy.addStoryId(this.sdata.openstory);
        utils.utils.openPrefabView("StoryView");

    },

    //挂机
    onClickhook(){
        Initializer.famUserHProxy.sendMessageOnHook(this.hid,this.ids,this.selectIndex)
        Initializer.famUserHProxy.selectHid = null
        this.onClickClose()
    },
    setMnumber(){
        let cost = this.sdata.cost
        this.labeln.string = cost[this.selectIndex]
    },

    onClickaddNumbers(){
        let cost = this.sdata.cost
        this.selectIndex++
        this.selectIndex = this.selectIndex < cost.length?this.selectIndex:cost.length-1
        this.setMnumber()
    },

    onClickHero(){
        utils.utils.openPrefabView("familyparty/PurchaseSelect", null ,{ id: this.ids,isOpen:true,callBack:(id)=>{
            this.hid = id
            this.showHooks()
        }});
    },
    onClickreduceNumber(){
        this.selectIndex--
        this.selectIndex = this.selectIndex < 0?0:this.selectIndex
        this.setMnumber()
    },

    onClickBuhuo(){
        let numbers = utils.utils.getParamInt("furniture_cost")
        let string = i18n.t("HOMEPART_TXT2",{number:numbers})
        utils.utils.openPrefabView("familyparty/TransferWindf",null,{
            string:string,
            callBack:()=>{
                Initializer.famUserHProxy.sendMessagebuhuo(this.ids)
            }
        });
        
    },

    onClickClose() {
        utils.utils.closeView(this);
    },

    showTimehow(){
        if(this.time<=0){
            this.showTime = false
            this.cbBtn.active = true
            this.buBtn.active = false
            this.timeNode.active = false
        }else{
            let timem = this.time
            let strings = ""
            let h = utils.utils.fullZero(parseInt(timem/60/60),2)
            let s = utils.utils.fullZero(parseInt(timem%60),2) 
            let m = utils.utils.fullZero(parseInt((timem/60)%60),2)  
            strings += " "+h+":"+m+":"+s
            this.labeltime.string = strings
        }
    },
    showTimeFun(dt){
        this.timeIndex+=dt
        if(this.timeIndex>=1){
            this.timeIndex-=1
            this.time--
            this.showTimehow()
        }
    },

    onDestroy(){
        let cardProxy = Initializer.cardProxy;
        cardProxy.currentCardList = [];
        cardProxy.resetSelect();
    },

    update (dt) {
        if(this.showTime){
            this.showTimeFun(dt)
        }
    },
});
