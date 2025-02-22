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
        nodeInfo: cc.Node,//底部说明节点
        btnSave: cc.Button,//保存按钮
        normalcolor: cc.Color,
        selectColor: cc.Color,
        proplist:List,
        lblbtnalltitle:cc.Label,
        lblbtncurtitle:cc.Label,
        lbldes:cc.Label
    },
    ctor() {
        this._curHero = null;
        this._orgNodeRoleX = 0;
        this.currentChooseIdx = 0;
        this._curData = null;
    },
    onLoad() {
        facade.subscribe("PLAYER_HERO_SHOW", this.initClothesList, this);
        this.nodeRole && (this._orgNodeRoleX = this.nodeRole.x);
        //this._curHero = this.node.openParam;
        var heroid = this.node.openParam.id;
        this._curHero = Initializer.servantProxy.getHeroData(heroid)
        this.servantShow.url = UIUtils.uiHelps.getServantSpine(this._curHero.id);
        this.btnSave.node.active = false;
        this.lbldes.node.active = false;
        this.initClothesList(); 
        // let heroClothesInfo = Initializer.playerProxy.userClothe;
        // let bgID = heroClothesInfo ? heroClothesInfo.background: 0;
        // this.bgUrl.node.active = 0 != bgID;
        // if (0 != bgID) {
        //     let o = localcache.getItem(localdb.table_userClothe,bgID);
        //     o && (this.bgUrl.url = UIUtils.uiHelps.getStoryBg(o.model));
        // }
        let bgid = Initializer.servantProxy.getServantBgId(this.node.openParam.id);
        let cfg = localcache.getItem(localdb.table_herobg,bgid);
        this.bgUrl.url = UIUtils.uiHelps.getPartnerZoneBgImg(cfg.icon);
        this.onClickButtonAll(null, "1");
    },

   
    onClickBack() {
        Utils.utils.closeView(this);
    },
    //保存
    onClickSave() {
        if (this._curData && (this._curData.have)) {
            //保存
            let cfgData = this._curData['cfg'];
            let msg = new proto_cs.hero.setClothe();
            msg.id = this._curHero.id;
            msg.dressId = cfgData.id;
            JsonHttp.send(msg,()=>{
                Utils.alertUtil.alert18n("USER_CLOTHE_SET");
            });
        }
    },
    
    initClothesList(){
        let heroDress = Initializer.servantProxy.getHeroAllDress(this._curHero.id);
        //console.error("heroDress:",heroDress)
        //let heroDress = this._curHeroInfo['heroDress'];
        if(heroDress){
            let index = -1;
            let chooseDressID = Initializer.servantProxy.getHeroDress(this._curHero.id);
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
            for(let i = 0,len = heroDress['noHaveDress'].length;i < len;i++){
                listData.push({
                    have:false,
                    cfg:heroDress['noHaveDress'][i]
                });
            }
            this.list.data = listData;
            if(index >= 0){
                this.list.selectIndex = index;
                this.onClickChoose(null,{data:listData[index]});
            }
        }
    },
    //选中处理
    onClickChoose(touchevent, eventData) {
        let chooseData = eventData.data;
        if (chooseData) {
            // this.nodeUp.active = false;
            // this.nodeDown.active = true;
            // this.nodeInfo.active = true;
            // let unLock = chooseData.have;
            // Utils.utils.showEffect(this.effectSprite, unLock ? 2 : 0);
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
            this.lbldes.string = cfgData.text;
            this.lbldes.node.active = !unLock;
            // if(cfgData.dressstory && cfgData.dressstory != ""){//剧情按钮
            //     this.btnStory.node.parent.active = true;
            //     let storySprite = this.storyIcon.node.getComponent(cc.Sprite);
            //     if(unLock){
            //         this.btnStory.interactable = true;
            //         ShaderUtils.shaderUtils.setImageGray(storySprite,false);
            //         this.storyIcon.url = UIUtils.uiHelps.getServantStoryIcon('01');
            //     }else{
            //         this.btnStory.interactable = false;
            //         ShaderUtils.shaderUtils.setImageGray(storySprite,true);
            //         this.storyIcon.url = UIUtils.uiHelps.getServantStoryIcon('01');
            //     }
            // }
            // //完善底部说明
            // this.lblLock.string = cfgData.text;//解锁条件
            // this.lblInfo.string = cfgData.des;//详情说明
            // this.btnLock.node.active = (!unLock && 0 != cfgData.unlock && null != cfgData.money.itemid);//解锁按钮
            // this.btnGo.node.active = (!unLock && cfgData.iconopen && 0 != cfgData.iconopen);//前往按钮
            // this.lblLimitTime.string = "";
            // this.lblRemainTime.string = "";
            // if(cfgData.limiticon == 1){
            //     this.limitTag.node.active = true;
            // }
            // //声音
            // // if(cfgData.voice != "0"){
            // //     let voiceArray = cfgData.voice.split('|');
            // //     let chooseVoice = voiceArray[Math.floor(Math.random() * voiceArray.length)];
            // //     if (chooseVoice) {
            // //         Utils.audioManager.playSound(chooseVoice, !0, !0);
            // //     }
            // // }
            this.playVoice(cfgData);
            // //属性说明
            // this.ndParam.active = cfgData.prop && cfgData.prop.length > 0;
            // if (cfgData.prop && cfgData.prop.length > 0){
            //     for(let i = 0;i < this.spParam.length;i++){
            //         this.spParam[i].node.parent.active = false;
            //         if(i < cfgData.prop.length){
            //             this.spParam[i].node.parent.active = true;
            //             if (1 == cfgData.prop_type) {
            //                 this.lbParam[i].string = "+" + cfgData.prop[i].value;
            //                 this.spParam[i].url = UIUtils.uiHelps.getLangSp(cfgData.prop[i].prop);
            //             } else {
            //                 this.lbParam[i].string = "+" + cfgData.prop[i].value / 100 + "%";
            //                 this.spParam[i].url = UIUtils.uiHelps.getClotheProImg(cfgData.prop_type, cfgData.prop[i].prop);
            //             }
            //         }
            //     }
            // }
        } else {
            // let heroDressID =  initializer.servantProxy.getHeroDress(this._curHero.id);
            // if(0 != heroDressID){
            //     let cfgDataArray = localcache.getFilters(localdb.table_heroDress, "id", heroDressID);
            //     if(cfgDataArray && cfgDataArray[0]){
            //         this.servantShow.url = UIUtils.uiHelps.getServantSkinSpine(cfgDataArray[0].model);
            //     }
            // }
            //Utils.utils.showEffect(this.effectSprite, 1);
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
    onClickRole(touchevent, eventData) {
        if (null != this.nodeRole && (null == eventData || 1 != eventData || 0 == this.nodeRole.x)){
            if (0 == this.nodeRole.x) {
                Utils.utils.showNodeEffect(this.nodeRight, 0);
                Utils.utils.showNodeEffect(this.nodeRole, 0);
            } else if (this.nodeRole.x == this._orgNodeRoleX) {
                Utils.utils.showNodeEffect(this.nodeRight, 1);
                Utils.utils.showNodeEffect(this.nodeRole, 1);
            }
        }
    },

    onClickButtonAll(tg, index) {
        let pIndex = parseInt(index);
        let bAll = pIndex == 1;
        this.lblbtnalltitle.node.color = bAll ? this.selectColor : this.normalcolor;
        this.lblbtncurtitle.node.color = bAll ? this.normalcolor : this.selectColor;
        if(bAll) {
            this.initAllProp();      
        } else {
            this.initCurrentChooseDress();
        }    
    },

    initAllProp(){
        var data_ = [[],[]];
        for (var _gg = 0;_gg < 4;_gg++){
            var ss_ = Initializer.servantProxy.getPropName(_gg+1);
            var vv_ = this._curHero.aep["e" + (_gg+1)];
            data_[Math.floor(_gg/2)].push({name:ss_+ "：",value:vv_});
        }
        this.proplist.data = data_;
    },

    onClickButtonCur(){
        if (this._curData == null) return;
        // this.btnall.interactable = true;
        // this.btncur.interactable = false;
        this.lblbtnalltitle.node.color = this.normalcolor;
        this.lblbtncurtitle.node.color = this.selectColor;
        this.initCurrentChooseDress();
    },

    initCurrentChooseDress(){
        if (this._curData == null) return;
        let cfgData = this._curData['cfg'];
        var prop = cfgData.prop;
        var data_ = [];
        for (var _gg = 0; _gg < prop.length;_gg++){
            if (data_[Math.floor(_gg/2)] == null){
                data_[Math.floor(_gg/2)] = [];
            }
            var ss_ = Initializer.servantProxy.getPropName(prop[_gg].prop);
            data_[Math.floor(_gg/2)].push({name:ss_ + "：",value:prop[_gg].value});
        }
        this.proplist.data = data_;
    },

});
