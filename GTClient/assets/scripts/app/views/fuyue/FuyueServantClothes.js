let List = require("List");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
let ShaderUtils = require("ShaderUtils");

cc.Class({
    extends: cc.Component,
    
    properties: {
        list: List,//可选列表
        nodeRole: cc.Node,
        nodeRight: cc.Node,
        bgUrl: UrlLoad,//背景图
        servantShow: UrlLoad,//角色
        nodeBot: cc.Node,//底部节点
        btnSave: cc.Button,//保存按钮
        normalcolor: cc.Color,
        selectColor: cc.Color,
        lblEps: [cc.Label],     // 属性
        lblShiLi: cc.Label,     // 势力属性
        lblTotalZZ: cc.Label,   // 总资质属性
        lblLv: cc.Label,    // 等级
        nodeHeads: [cc.Node], // 头像们
    },
    ctor() {
        this._curHero = null;
        this._orgNodeRoleX = 0;
        this.currentChooseIdx = 0;
        this._curData = null;   
        this.pPreHeadComp = null;   
        this.iChooseDress = 0;
    },

    onLoad() {        
        this.nodeRole && (this._orgNodeRoleX = this.nodeRole.x);
        //this._curHero = this.node.openParam;
        var heroid = this.node.openParam.id;
        this._curHero = Initializer.servantProxy.getHeroData(heroid);
        if(this.node.openParam.dress == 0)
            this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);                
        this.showHeadsList();
        this.showHeroProperties(this._curHero);
        this.showClothesList(); 
        // this.bgUrl.url = UIUtils.uiHelps.getPartnerZoneBgImg(this._curHero.id);
        // this.onClickButtonAll(null, "1");
    },

   
    onClickBack() {
        Utils.utils.closeView(this);
    },
    
    //保存
    onClickSave() {
        // if (this._curData && (this._curData.have)) {
            Initializer.fuyueProxy.iSelectHeroId = this._curHero.id;
            Initializer.fuyueProxy.iSelectHeroDress = this.iChooseDress;
            console.log("heroId:"+this._curHero.id+"    dress:"+this.iChooseDress);
            facade.send(Initializer.fuyueProxy.REFRESH_FRIEND);
            Utils.utils.closeView(this);            
        // }
    },

    showHeadsList() {
        var props = Initializer.fuyueProxy.pHerosProps;
        var _heroId = this.node.openParam.id;

        for(var i=0; i <this.nodeHeads.length; i++) {
            var nodeHead = this.nodeHeads[i];
            if(props[i] != null) {
                nodeHead.active = true;
                var heroId = props[i].id;
                var heroInfo = localcache.getItem(localdb.table_hero, heroId + "");
                nodeHead.getComponent("FuyueChooseFriendItem").showData({heroid:heroId, name:heroInfo.name});     
                if(heroId == _heroId) {
                    nodeHead.getChildByName("selectNode").active = true;
                    this.pPreHeadComp = nodeHead;
                }
            } else {
                nodeHead.active = false;
            }
        }                
    },

    onClickHead(touchevent, eventData) {
        if(this.pPreHeadComp != null) {
            this.pPreHeadComp.getChildByName("selectNode").active = false;
        }
        this.pPreHeadComp = touchevent.target;
        this.pPreHeadComp.getChildByName("selectNode").active = true;
        var props = Initializer.fuyueProxy.pHerosProps;
        var heroId = props[Number(eventData)].id;
        this._curHero = Initializer.servantProxy.getHeroData(heroId)
        this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);
        this.showHeroProperties(this._curHero);
        this.showClothesList();
    },
    
    showClothesList(){
        let heroDress = Initializer.servantProxy.getHeroAllDress(this._curHero.id);
        if(heroDress){
            let index = -1;            
            let chooseDressID = this.node.openParam.dress;
            let listData = new Array();
            for(let i = 0,len = heroDress['ownerDress'].length;i < len;i++){
                listData.push({
                    have:true,
                    cfg:heroDress['ownerDress'][i]
                });
                if(heroDress['ownerDress'][i].id == chooseDressID){
                    index = i;
                }
            }            
            this.list.data = listData;
            if(index >= 0){
                this.list.selectIndex = index;
                this.onClickChoose(null,{data:listData[index]});
            } else
                this.list.selectIndex = -1;
        }
    },
    //选中处理
    onClickChoose(touchevent, eventData) {
        let chooseData = eventData.data;
        if (chooseData) {            
            this.updateShow(chooseData);
        }
    },
    //更新显示
    updateShow(chooseData) {
        //this.btnStory.node.parent.active = false;
        if (null != chooseData){
            this._curData = chooseData;
            let unLock = this._curData.have;
            let cfgData = this._curData['cfg'];
            //修改伙伴图像显示
            this.servantShow.url = UIUtils.uiHelps.getServantSkinSpine(cfgData.model);
            let chooseDressID = Initializer.servantProxy.getHeroDress(this._curHero.id);
            let isDressed = chooseDressID == cfgData.id;
            this.btnSave.node.active = !isDressed && unLock;   
            this.iChooseDress = cfgData.id;
            this.playVoice(cfgData);         
        } else {
           
        }
    },
    playVoice(cfgData) {
        if (!Utils.audioManager.isPlayLastSound()) {
            if(cfgData && cfgData.voice != "" && cfgData.voice != "0"){
                let voiceArray = cfgData.voice.split('|');
                let chooseVoice = voiceArray[Math.floor(Math.random() * voiceArray.length)];
                if (chooseVoice) {
                    Utils.audioManager.playSound(chooseVoice, !0, !0);
                }
            }else{
                let voiceSys = Initializer.voiceProxy.randomHeroVoice(cfgData.heroid);
                if (voiceSys) {
                    Utils.audioManager.playSound("servant/" + voiceSys.herovoice, !0, !0);
                }
            }
        }
    },

    // 显示角色属性值
    showHeroProperties(heroData) {
        var props = Initializer.fuyueProxy.getHeroPropById(heroData.id).prop;
        var count = 0;
        for (var l = 0; l < this.lblEps.length; l++, count++) {
            this.lblEps[l].string = props[l];
        }
        this.lblLv.string = i18n.t("COMMON_LV", {
            lv: heroData.level
        });
        this.lblShiLi.string = props[count++];            
        this.lblTotalZZ.string = props[count];        
    },
    

});
