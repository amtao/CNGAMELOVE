var i = require("UrlLoad");
var n = require("UIUtils");
var l = require("Initializer");
var r = require("Config");

cc.Class({
    extends: cc.Component,
    properties: {
        body: i,
        head: i,
        headDesF: i,
        headDesH: i,
        bianZi: i,
        earH: i,
        earF: i,
        effF: i,
        effH: i,
        animalH: i,
        animalF: i,
        shou:i,
        bodyH:i,
        botEff:i,
        topEff:i,
    },
    ctor() {
        this.listArr = [];
        this.allClothePartNum = 0;
        this.mCurrentNum = 0;
    },


    onLoad() {
        this.listArr = [this.body,this.headDesF,this.headDesH,this.bianZi,this.earF,this.earH,this.shou,this.bodyH,this.botEff,this.topEff];
        let listPartsArr = [this.body,this.head,this.headDesF,this.headDesH,this.bianZi,this.earH,this.earF,this.effF,this.effH,this.animalF,this.animalH,this.shou,this.bodyH,this.botEff,this.topEff]
        let self = this;
        for (let ii = 0; ii < listPartsArr.length;ii++){
            let item = listPartsArr[ii];
            if (item){
                item.loadHandle = ()=>{
                    //if (item.url != ""){
                        self.mCurrentNum++;
                        if (self.mCurrentNum != 0 && self.mCurrentNum >= self.allClothePartNum){                          
                            self.scheduleOnce(()=>{
                                self.showAllSpine();
                            },0.1)
                        }
                    //}                   
                }
            }
        }
    },
    setRoleLevel(t) {
        var e = l.playerProxy ? l.playerProxy.userData: null,
        o = null;
        null != l.playerProxy.userClothe && 0 != l.playerProxy.userClothe.ear && (o = {
            body: 0,
            head: 0,
            ear: l.playerProxy.userClothe.ear
        });
        this.setClothes( e.job, t, o);
    },
    setLevel(t, e, o) {
        this.setClothes( e, o, null);
    },

    setCreateClothes ( key) {
        // 修复在加载界面切换账号出的问题
        if(this.node != null && this.node.isValid) {
            let cfg = localcache.getItem(localdb.table_usercreate,key);
            this.allClothePartNum = 0;
            this.mCurrentNum = 0;
            this.allClothePartNum++;
            this.clearAllPart();
            this.head.url = n.uiHelps.getRoleSpinePart("head_0_" + key);
            let modelArr = cfg.head.split("|");
            let curListPart = [this.headDesF,this.headDesH,this.bianZi]
            for (var ii = 0;ii < modelArr.length;ii++){
                if (curListPart[ii]){
                    this.allClothePartNum++;
                    curListPart[ii].url = n.uiHelps.getRoleSpinePart(modelArr[ii]);
                }
            }
            let bodyArr = cfg.body.split("|");
            let curListPart2 = [this.body,this.shou];
            for (var ii = 0;ii < bodyArr.length;ii++){
                if (curListPart2[ii]){
                    this.allClothePartNum++;
                    curListPart2[ii].url = n.uiHelps.getRoleSpinePart(bodyArr[ii]);
                }
            }
            let spineArr = this.body.node.getComponentsInChildren(sp.Skeleton);
            for (let spine of spineArr){
                if (spine.findAnimation("animation") != null)
                    spine.animation = "animation";
            }
            //this.headDesF.url = n.uiHelps.getRoleSpinePart("headf_0_2");
        }
    },

    setUserFace(faceid){
        this.clearAllPart();
        this.head.url = "";
        this.head.url = n.uiHelps.getRoleSpinePart("head_0_" + faceid);
    },

    /**
    *设置女主服饰
    *param job 女主的脸
    *param level 女主等级 用于晋升的服饰
    */
    setClothes( job, level, clothes,clotheSpecial) {
        this.head.url = "";  
        this.clearAllPart();
        this.allClothePartNum = 0;
        this.mCurrentNum = 0;
        this.allClothePartNum++;
        this.head.url = n.uiHelps.getRoleSpinePart("head_0_" + job);  
        if (clothes == null || clothes.head == 0){
            if (level == null) return;
            let officerCfg = localcache.getItem(localdb.table_officer, level);
            if (null == officerCfg) return;
            let  shizhuangCfg = localcache.getItem(localdb.table_roleSkin, officerCfg.shizhuang);
            if (null == shizhuangCfg) return;
            let clothArr = shizhuangCfg.clotheid.split("|");
            for (var ii = 0; ii < clothArr.length;ii++){
                if (clothArr[ii] == 0) continue;
                let cg = localcache.getItem(localdb.table_userClothe,clothArr[ii]);
                if (cg){
                    this.setClothPartUrl(cg);
                }
            }
        }
        else{
            for (let key in clothes){
                let cid = clothes[key];
                if (cid == 0) continue;
                let cg = localcache.getItem(localdb.table_userClothe,cid);
                if (cg){
                    this.setClothPartUrl(cg);
                }
            }
            if (clotheSpecial != null && clotheSpecial != 0){
                let cg = localcache.getItem(localdb.table_userClothe,clotheSpecial);
                if (cg){
                    this.setClothPartUrl(cg);
                }
            }
        }    
    },

    //主角衣服套装
    setSuitClothe: function(id) {
        let suitData = localcache.getItem(localdb.table_usersuit, id);
        if(null == suitData) {
            return;
        }
        let clothers = suitData.clother;       
        this.head.url = "";
        this.clearAllPart();
        this.allClothePartNum = 0;
        this.mCurrentNum = 0;
        this.allClothePartNum++;
        this.head.url = n.uiHelps.getRoleSpinePart("head_0_" + l.playerProxy.userData.job);
        let effectid = 0;
        for (let cid of clothers){
            let cg = localcache.getItem(localdb.table_userClothe,cid);
            if (cg){
                this.setClothPartUrl(cg);
                if (cg.part == l.playerProxy.PLAYERCLOTHETYPE.BODY){
                    effectid = l.clotheProxy.getPlayerSuitClotheEffect(cg.id);
                }
            }
        }
        if (effectid != 0){
            let cg = localcache.getItem(localdb.table_userClothe,effectid);
            if (cg){
                this.setClothPartUrl(cg);
            }
        }
    },


    setGray: function(bGray) {
        // if((!this.bodySp && this.body.url != "") || (!this.headFSp && this.headDesF.url != "") || (!this.headHSp && this.headDesH.url != "")
        //  || (!this.earFSp && this.earF.url != "") || (!this.earHSp && this.earH.url != "") || (!this.headSp && this.head.url != "")) {
        //     //等所有部位加载完
        //     let self = this;
        //     this.scheduleOnce(() => {
        //         self.setGray(bGray);
        //     }, 0.1);
        //     return;
        // }
        // if(bGray) {
        //     shaderUtils.shaderUtils.setSpineGray(this.bodySp);
        //     shaderUtils.shaderUtils.setSpineGray(this.headFSp);
        //     shaderUtils.shaderUtils.setSpineGray(this.headHSp);
        //     shaderUtils.shaderUtils.setSpineGray(this.earFSp);
        //     shaderUtils.shaderUtils.setSpineGray(this.earHSp);
        //     shaderUtils.shaderUtils.setSpineGray(this.headSp);
        // } else {
        //     shaderUtils.shaderUtils.setSpineNormal(this.bodySp);
        //     shaderUtils.shaderUtils.setSpineNormal(this.headFSp);
        //     shaderUtils.shaderUtils.setSpineNormal(this.headHSp);
        //     shaderUtils.shaderUtils.setSpineNormal(this.earFSp);
        //     shaderUtils.shaderUtils.setSpineNormal(this.earHSp);
        //     shaderUtils.shaderUtils.setSpineNormal(this.headSp);
        // }
    },

    /**显示女主的所有部位*/
    showAllSpine(){
        let listArr = [this.head,this.body,this.headDesF,this.headDesH,this.bianZi,this.earF,this.earH,this.shou,this.bodyH,this.botEff,this.topEff];        
        for (var ii = 0; ii < listArr.length;ii++) {
            listArr[ii] && (listArr[ii].node.active = true);
        }        
    },

    /**清除所有的部位*/
    clearAllPart(){
        let listArr = [this.head,this.body,this.headDesF,this.headDesH,this.bianZi,this.earF,this.earH,this.shou,this.bodyH,this.botEff,this.topEff];
        for (var ii = 0; ii < listArr.length;ii++) {
            if (listArr[ii]){
                listArr[ii].url = "";
                listArr[ii].node.active = false;
            }
            //this.listArr[ii] && (this.listArr[ii].url = "");
        }
    },

    /**spine动画还原到起始动作*/
    setSpineToSetupPose(nodeUrl){
        if (nodeUrl == null || nodeUrl.node == null) return;
        let spineArr = nodeUrl.node.getComponentsInChildren(sp.Skeleton);
        for (let spine of spineArr){
            if (spine.findAnimation("animation") != null)
                spine.animation = "animation";
        }
    },

    /**重新播放所有的动画*/
    ResetSpineAnimation(){
        for (var ii = 0; ii < this.listArr.length;ii++) {
            this.listArr[ii] && this.setSpineToSetupPose(this.listArr[ii]);
        }
    },


    /**设置人物的部位*/
    setClothPartUrl(cfg){
        if (cfg == null) return;
        switch(cfg.part){
            case l.playerProxy.PLAYERCLOTHETYPE.HEAD:{
                let modelArr = cfg.model.split("|");
                let curListPart = [this.headDesF,this.headDesH,this.bianZi]
                for (var ii = 0;ii < modelArr.length;ii++){
                    if (curListPart[ii] && modelArr[ii] != ""){
                        this.allClothePartNum++;
                        //console.error("modelArr[ii]:",modelArr[ii])
                        curListPart[ii].url = n.uiHelps.getRoleSpinePart(modelArr[ii]);
                    }
                }
            }
            break;
            case l.playerProxy.PLAYERCLOTHETYPE.BODY:{
                let modelArr = cfg.model.split("|");
                let curListPart = [this.body,this.shou,this.bodyH]
                for (var ii = 0;ii < modelArr.length;ii++){
                    if (curListPart[ii] && modelArr[ii] != ""){
                        this.allClothePartNum++;
                        //console.error("==============modelArr[ii]:",modelArr[ii])
                        curListPart[ii].url = n.uiHelps.getRoleSpinePart(modelArr[ii]);
                    }
                }
            }
            break;
            case l.playerProxy.PLAYERCLOTHETYPE.EAR:{
                let modelArr = cfg.model.split("|");
                let curListPart = [this.earF,this.earH]
                for (var ii = 0;ii < modelArr.length;ii++){
                    if (curListPart[ii] && modelArr[ii] != ""){
                        this.allClothePartNum++;
                        //console.error("--------------modelArr[ii]:",modelArr[ii])
                        curListPart[ii].url = n.uiHelps.getRoleSpinePart(modelArr[ii]);
                    }
                }
            }
            break;
            case l.playerProxy.PLAYERCLOTHETYPE.SUIT_EFFECT:{
                let modelArr = cfg.model.split("|");
                let curListPart = [this.topEff,this.body,this.botEff]
                for (var ii = 0;ii < modelArr.length;ii++){
                    if (curListPart[ii] && modelArr[ii] != ""){
                        this.allClothePartNum++;
                        //console.error("################modelArr[ii]:",modelArr[ii])
                        curListPart[ii].url = n.uiHelps.getRoleSpinePart(modelArr[ii]);
                    }
                }
            }
            break;           
        }
    },
});
