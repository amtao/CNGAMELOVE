

var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
let UIUtils = require("UIUtils")
import { Combine_Shop_TYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {

        scrollView: cc.ScrollView,
        listitem:List,
        roleNode:cc.Node,
        
        titleStr:cc.Label,

        nomalNode:cc.Node,
        editorNode:cc.Node,
        partyNode:cc.Node,
        photoNode:cc.Node,

        roleSpine:UrlLoad,
        hroleSpine:UrlLoad,


        wxDengji:cc.Label,
        wxDengjiMax:cc.Label,
        wxNumber:cc.Label,
        progress:cc.Node,


        score:cc.Label,

        wxjifenLabel:[cc.Label],


        lbjfneed:cc.Label,
        enNumber:cc.Label,


        iamgebgs:[cc.Node],

        itemsfuNodes:cc.Node,

        iamgesbgs:cc.Node,

        mwidget:cc.Node,

        bgline:cc.Node,

        roleBtnsNode:cc.Node,

        jfNode:[cc.Node],

        pointN:cc.Node,

        cinstance:cc.Node,

        dazBtn:cc.Node,

        points:[cc.Node],
        

        imagemu:cc.Node





        

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

    isEditerType(){
        if(this.activeType != 3){
            this.partyNode.active = this.activeType === 3;
        }else{
            this.hroleSpine.node.active = true
        }
        this.roleNode.active = !(this.activeType === 2)
        this.nomalNode.active = this.activeType === 1;
        this.editorNode.active = this.activeType === 2;
        this.photoNode.active = this.activeType === 4;
        this.titleStr.string = this.titleMap[this.activeType];
        this[this.mapfunc.get(this.activeType)]()
    },

    nomalNodeInit(){

    },

    editorNodeInit(){

    },

    //getParamInt
    partyNodeInit(){
        Initializer.famUserHProxy.sendMessageGetFeastInfo((e)=>{
            this.partyNode.active = true
            let fdata = e.a.furniture.familyFeast
            let isJoin = fdata.isJoin
            this.famfdata = e.a.furniture.jyopen
            this.showfamilyFeast()
            if(isJoin === 0){
                this.partyNode.active = true
            }else{
                //直接播放动画
                this.showStory(fdata)
            }
            if(e.a.furniture.feastStory && e.a.furniture.feastStory.saveInfo){
                this.saveInfo = e.a.furniture.feastStory.saveInfo
            }
        })
    },

    showfamilyFeast(){
        let maxc = utils.utils.getParamInt("furniture_times")
        let now = (maxc - this.famfdata.openCount) + "/" + maxc
        let jfneed = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv).needitem[0].count
        this.canEnNumber = maxc - this.famfdata.openCount
        this.lbjfneed.string = now
        this.enNumber = jfneed
        
    },

    photoNodeInit(){

    },

    //部分情况隐藏自己减少dc
    showThisOrNot(bools = true){
        this.node.active = bools
    },
    
    resetJfNode(){
        let len = this.jfNode.length
        for (let i = 0; i < len; i++) {
            this.jfNode[i].active = false
        }
    },
    
    onLoad () {
        this.resetJfNode()

        this.iamgesbgs.zIndex = 100

        this.bgline.zIndex = 50

        this.needIndex = 101

        this.roleBtnsNode.zIndex = 100000

        this.mwidth = 1440
        this.mheight = 1280

        this.list = []

        this.selectType = 0

        this.heroid = -1

        this.amxw = 300

        this.timeIndex = 0
        this.MaxTime = 60

        this.isReSee = false

        this.famfdata = null
        this.canEnNumber = 0

        this.putFurniture = {}

        this.cincentancArray = []

        this.cinstance.active = false

        this.runLvAndWxin = false

        this.needexp = 0


        this.isFirst = true


        this.oldLv = 0
        this.oldWxin = 0
        this.itemsfu = {
            "101":{},
            "102":{},
            "103":{},
            "104":{},
            "201":{},
            "202":{},
            "301":{},
            "401":{},
        }

        this.itemsZindex = {
            "101":6,
            "102":7,
            "103":8,
            "104":3,
            "201":4,
            "202":2,
            "301":1,
            "401":5,
        }

        this.initFus()

        this.mapfunc = new Map()
        this.mapfunc.set(1,"nomalNodeInit")
        this.mapfunc.set(2,"editorNodeInit")
        this.mapfunc.set(3,"partyNodeInit")
        this.mapfunc.set(4,"photoNodeInit")

        this.saveInfo = null

        this.isStory = false


        Initializer.famUserHProxy.sendFirstEnter()
        Initializer.famUserHProxy.sendGoodsView()
        Initializer.famUserHProxy.sendMessageFinfo()
        Initializer.famUserHProxy.sendMessageGetFeastInfo()
        
        //1普通状态，2编辑状态，3宴会状态，4照相状态
        this.activeType = 1;
        this.titleMap = {1:i18n.t("HOMEPART_HOMEEDITOR"),2:i18n.t("HOMEPART_HOMEEDITOR"),
        3:i18n.t("HOMEPART_HOMEEDITOR_LIANGCHENGJIAYAN"),4:i18n.t("HOMEPART_HOMEEDITOR_TAKEPHONO")}

        facade.subscribe(Initializer.playerProxy.PLAYER_LEVEL_UPDATE, this.updateRoleShow, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.updateRoleShow, this);

        facade.subscribe("FURNITURE_INTERGRAL", this.updateAlls, this);
        facade.subscribe("SHOWUSERHOME", this.showThisOrNot, this);
        facade.subscribe("FURNITURE_WAREHOUSE", this.reShowSelectType, this);
        facade.subscribe("FURNITURE_DISPLAY", this.initPutFs, this);


        facade.subscribe("STORY_END", this.sendEndmessage, this);
        this.resetCounTent()

        

    },

    resetCounTent(){
        let scah = cc.winSize.height/1280;
        this.mwidget.width = this.mwidget.width*scah
        this.mwidget.height = this.mwidget.height*scah
    },

    initFus(){
        let keys = Object.keys(this.itemsfu)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let urlload = this.itemsfuNodes.getChildByName("i"+keys[i]).getComponent(UrlLoad)
            urlload.nselect = urlload.node.getChildByName("n").getComponent(UrlLoad)
            urlload.nselect.node.active = false
            this.itemsfu[keys[i]] = urlload
        }
    },


    showfurnituresSelect(address,id){
        let images = this.itemsfu[address]
        let data = localcache.getItem(localdb.table_furniture, id)
        let url = UIUtils.uiHelps.getFurnituresItemSence(address,data.picture)
        images.url = url
        images.nselect.url = url
        images.node.active = true
        return images
    },

    
    showPutFs(){
        let fmap = this.putFurniture
        let keys = Object.keys(this.itemsfu)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let images = this.itemsfu[keys[i]]
            if(fmap[parseInt(keys[i])] && fmap[parseInt(keys[i])] !== 0){
                this.showfurnituresSelect(keys[i],fmap[parseInt(keys[i])])
            }else{
               let nstring = utils.utils.getParamStr("furniture_address_" + keys[i])
               if(nstring != "undefined"){
                let url = UIUtils.uiHelps.getFurnituresItemSence(keys[i],nstring)
                images.url = url
                images.nselect.url = url
                images.node.active = true
               }else{
                    images.node.active = false
               }
            }
        }
    },
    

    initPutFs(){
        let fmap = Initializer.famUserHProxy.display.putFurniture
        this.putFurniture = Array.isArray(fmap)?{}:JSON.parse(JSON.stringify(fmap))
        this.showPutFs()
    },

    runLvAndWxinFun(dt){
        if(this.oldLv < Initializer.famUserHProxy.intergral.warmLv){
            this.progress.width+=dt*350
        }else if(this.oldWxin < Initializer.famUserHProxy.intergral.warmValue){
            this.oldWxin = this.oldWxin+dt*100
            this.oldWxin = this.oldWxin>=Initializer.famUserHProxy.intergral.warmValue?Initializer.famUserHProxy.intergral.warmValue:this.oldWxin
            let pre = this.oldWxin/this.needexp
            this.progress.width = this.amxw*pre
        }
        if(this.progress.width>=this.amxw){
            this.oldLv++;
            this.wxDengji.string = this.oldLv
            this.progress.width = 0
        }
        if(this.oldLv === Initializer.famUserHProxy.intergral.warmLv && this.oldWxin === Initializer.famUserHProxy.intergral.warmValue){
            this.runLvAndWxin = false
        }
        this.wxNumber.string = parseInt(this.oldWxin) + "/" + this.needexp
    },

    winxinShow(){
        let ss = Initializer.famUserHProxy.intergral.score
        this.score.string = utils.utils.formatMoney(ss)
        let winxinlist = localcache.getList(localdb.table_furniture_feast);
        this.wxDengjiMax.string = "/"+winxinlist.length
        
        let noww = Initializer.famUserHProxy.intergral.warmValue
        if(Initializer.famUserHProxy.intergral.warmLv<winxinlist.length){
            let dataw = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv)
            let pre = noww/dataw.needexp
            pre = pre>1?1:pre
            this.needexp = dataw.needexp
            if(this.isFirst){
                this.isFirst = false
                this.wxNumber.string = noww + "/" + dataw.needexp
                this.progress.width = this.amxw*pre
                this.oldLv = Initializer.famUserHProxy.intergral.warmLv
                this.oldWxin = Initializer.famUserHProxy.intergral.warmValue
            }else if(this.oldLv<Initializer.famUserHProxy.intergral.warmLv || this.oldWxin < Initializer.famUserHProxy.intergral.warmValue){
                this.oldWxin = 0
                this.runLvAndWxin = true
            }
        }else{
            this.progress.width = this.amxw
            this.wxNumber.node.active = false
        }
        this.wxDengji.string = this.oldLv
        this.timeIndex = (utils.timeUtil.second - Initializer.famUserHProxy.intergral.lastPickTime)%60

        //lastPickTime
    },



    //----------家宴----------

    sendEndmessage(){
        if(!this.isStory){
            return
        }
        this.isStory = false
        
        if(this.isReSee){
            return
        }
        Initializer.famUserHProxy.sendMessageEndFeast((e)=>{
            if(e.a.furniture.feastStory && e.a.furniture.feastStory.saveInfo){
                this.saveInfo = e.a.furniture.feastStory.saveInfo
            }
            this.famfdata.openCount++;
            this.jyopen.openCount++;
            Initializer.famUserHProxy.redjyopen()
            this.showfamilyFeast()
        })
    },

    showStory(familyFeast){
        let startLv = familyFeast.startLv
        let stroyId = familyFeast.stroyId

        this.isStory = true
        Initializer.playerProxy.addStoryId(stroyId);
        utils.utils.openPrefabView("StoryView");

        //Initializer.playerProxy.addStoryId(stroyId);
        //utils.utils.openPrefabView("StoryView");
    },

    sendMessagePart(){
        Initializer.famUserHProxy.sendMessageOpenFamilyFeast(this.heroid,(e)=>{
            let fdata = e.a.furniture.familyFeast
            this.showStory(fdata)
        })
    },

    onClickBeginPart(){
        if(this.canEnNumber <= 0){

            return
        }
        if(this.heroid === -1){
            //i.alertUtil.alert(i18n.t("SON_LI_LIAN_NO_SONPOSITION"))
            //utils.alertUtil.alert(i18n.t("HOMEPART_HOMEEDITOR_HEROSELECT"))
            this.selectHero(null,null,()=>{
                this.sendMessagePart()
            })
            return
        }
        this.sendMessagePart()

    },

    seeReward(){
        utils.utils.openPrefabView("familyparty/PartyReady")
    },

    reShowHeroId(){
        this.hroleSpine.url = UIUtils.uiHelps.getServantSpine(this.heroid);
        this.hroleSpine.node.active = true
    },

    storyVideo(){
        this.isReSee = true
        utils.utils.openPrefabView("familyparty/Storyreview",null,{saveInfo:this.saveInfo,callBack:()=>{
            this.isReSee = false
        }})
    },

    selectHero(t,e,callBackd = null){
        Initializer.famUserHProxy.selectMode = 2
        utils.utils.openPrefabView("familyparty/PurchaseSelect", null ,{ id: 1,callBack:(hid)=>{
            if(hid == null){
                return
            }
            this.heroid = hid
            this.reShowHeroId()
            if(callBackd){
                callBackd()
            }
        } });
    },


    //----------end家宴end----------
    showBigOrSmall(numbers){
        len = this.jfNode.length
        for (let i = 0; i < len; i++) {
            let ns = this.jfNode[i];
            if(true == ns.active && numbers <= 0){
                this.runAnimateScaleSmall(ns)
            }else if(false == ns.active && numbers >0 ){
                this.runAnimateScaleBig(ns)
            }
        }
        return 
    },

    updatejf(){
        let len = 0
        let dataw = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv)
        let lastPickTime = Initializer.famUserHProxy.intergral.lastPickTime
        let time = utils.timeUtil.second - lastPickTime
        let max = dataw.maxjifen
        let score = Initializer.famUserHProxy.intergral.score
        let numbers = 0
        if(max<=score){
            this.showBigOrSmall(numbers)
            return
        }

        numbers = parseInt(time/60) * dataw.jifen
        let lic = score+numbers-max
        if(lic>0){
            numbers -= lic
        }
        numbers = numbers>max?max:numbers
        numbers = numbers<0?0:numbers
        let keys = Object.keys(this.itemsfu)
        len = keys.length
        let nn = 0
        for (let i = 0; i < len; i++) {
            let fu = this.itemsfu[keys[i]]
            if(fu.node.active == true){
                nn++
            }
        }

        numbers = parseInt(numbers/nn)
        this.showBigOrSmall(numbers)
        len = this.wxjifenLabel.length
        for (let i = 0; i < len; i++) {
            this.wxjifenLabel[i].string = "+" + numbers
        }
    },

    updateAlls(){
        this.winxinShow()
        this.updatejf()
    },

    updateRoleShow() {
        Initializer.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },

    reSetSize(){
        this.iamgesbgs.active = false
        let keys = Object.keys(this.itemsfu)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let urlload = this.itemsfu[keys[i]]
            urlload.node.zIndex = this.itemsZindex[keys[i]]
            urlload.nselect.node.active = false
        }
    },

    start () {
        this.updateRoleShow()
        this.isEditerType()
        //this.reShowSelectType()
        this.winxinShow()
        this.reSetSize()
        this.scheduleOnce(()=>{
            let scah = cc.winSize.height/1280;
            let vi = (scah-1)*cc.winSize.width
            this.scrollView.scrollToOffset(cc.v2(this.roleNode.x-cc.winSize.width/2+vi,0), 0.4,true);
            this.scheduleOnce(()=>{
                this.updatejf()
            },0.4)
        },0.2)
    },

    //i.utils.openPrefabView("shopping/ShopCombineView",false,showType);
    //--------------------edib---------------
    onClickClear(){
        this.reSetSize()
        let fmap = this.putFurniture
        let keys = Object.keys(fmap)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let address = keys[i]
            fmap[address] = 0
        }
        this.showPutFs()
    },

    onClickSave(){
        this.reSetSize()
        let ara = []
        let keys = Object.keys(this.putFurniture)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let id = this.putFurniture[keys[i]];
            let address = keys[i]
            let dat = {}
            dat[address] = id
            ara.push(dat)
        }
        Initializer.famUserHProxy.sendMessagePutFurniture(ara)
    },

    onClickReset(){
        this.reSetSize()
        this.putFurniture = {}
        this.initPutFs()
    },


    onClickItems(t,e){
        let data = e._data
        let address = data.address

        this.putFurniture[address] = data.id
        this.showfurnituresSelect(address,data.id)
        let images = this.itemsfu[address]
        images.node.zIndex = this.needIndex
        images.nselect.node.active = true
        this.iamgesbgs.active = true
        let scah = cc.winSize.height/1280
        let vi = (scah-1)*cc.winSize.width
        this.scrollView.scrollToOffset(cc.v2(images.node.x-cc.winSize.width/2+vi,0), 0.2,true)
    },



    //--------------------edie---------------
    

    reShowSelectType(){
        let len = this.iamgebgs.length
        for (let i = 0; i < len; i++) {
            if(this.selectType === i){
                this.iamgebgs[i].active = true
            }else{
                this.iamgebgs[i].active = false
            }
        }

        this.list = []
        let furniture = Initializer.famUserHProxy.warehouse.haveFurniture
        let keys = Object.keys(furniture)
        len = keys.length
        for (let i = 0; i < len; i++) {
            let id = keys[i]
            let data = localcache.getItem(localdb.table_furniture, id)
            if(data && furniture[id]>0){
                this.list.push(data)
            }
        }

        let arrays = []
        for (let i = 0; i < this.list.length; i++) {
            let md = this.list[i];
            if(md.stytle === this.selectType+1){
                arrays.push(md)
            }
        }
        this.dazBtn.active = arrays.length === 0
        this.listitem.data = arrays
    },

    onClickSelectBack(t,e){
        this.selectType = parseInt(e)
        this.reShowSelectType()
    },

    onClickShop(){
        utils.utils.openPrefabView("shopping/ShopCombineView",false,Combine_Shop_TYPE.ShopHomeJF);
    },



    showCreateFurniture(){
        this.showThisOrNot(false)
        utils.utils.openPrefabView("familyparty/Createfurniture");
    },

    showFurnitures(){
        this.showThisOrNot(false)
        utils.utils.openPrefabView("familyparty/Furnitures");
    },

    getSomes(){
        Initializer.famUserHProxy.showGetGoodsView()
    },


    onClickChange(t, e){
        return
        if (utils.stringUtil.isBlank(e)) utils.alertUtil.alert(i18n.t("MAIN_FUN_UNOPEN"));
        else if (r.funUtils.isCanOpenViewUrl(e) || a.Config.DEBUG) {
            facade.send("MAIN_TOP_HIDE_PAO_MA");
            utils.utils.showEffect(this, 0,
            function() {
                r.funUtils.openViewUrl(e + "", !0);
            });
        }
    },
    

    onClickOneGet(){
        let count = Initializer.famUserHProxy.buyScore?Initializer.famUserHProxy.buyScore.buyCount:0
        let max = utils.utils.getParamInt("farniture_rwdlimt")
        let numbers = 100
        let string = ""
        let dataw = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv)
        let number1 = dataw.jifen*120
        if(count>=max){
            string = i18n.t("HOMEPART_TXT3")
        }else{
            let nums = utils.utils.getParamStr("farniture_rwdcost")
            numbers = nums.split("|")[count]
            string = i18n.t("HOMEPART_TXT0",{number:numbers,number1:number1})
        }
        utils.utils.openPrefabView("familyparty/TransferWindf",null,{
            string:string,
            callBack:()=>{
                if(count<max){
                    Initializer.famUserHProxy.sendMessagebuyScoreByGold()
                    this.showflyOne(this.points)
                } 
            }
        });
    },

    onClickGetjf(){
        let dataw = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv)
        let lastPickTime = Initializer.famUserHProxy.intergral.lastPickTime
        let time = utils.timeUtil.second - lastPickTime
        let max = dataw.maxjifen
        let numbers = time/60 * dataw.jifen
        let score = Initializer.famUserHProxy.intergral.score
        let lic = score+numbers-max
        if(lic>=0){
            utils.alertUtil.alert(i18n.t("HOMEPART_TXT4"))
        }
        this.runAnimatefly()
        Initializer.famUserHProxy.sendMessageGetScores(()=>{
            
        })
    },

    consPointToRPoint(nodes){
        let point = nodes.convertToWorldSpaceAR(cc.v2(0.5, 0.5));
        let p2 = this.node.convertToNodeSpaceAR(point);
        return p2
    },


    runAnimateffs(node){
        let f1 = utils.utils.randomNumCanf(1.7,2.3)
        let movebyadd = cc.moveBy(f1, cc.v2(0,+25)).easing(cc.easeInOut(f1));
        let movebyre = cc.moveBy(f1, cc.v2(0,-25)).easing(cc.easeInOut(f1));
        let sq = cc.sequence(movebyadd,movebyre)
        var repeat = cc.repeatForever(sq);
        node.runAction(repeat)
    },


    runAnimateScaleBig(no){
        no.active = true
        no.setScale(0.2)
        let scaleto = cc.scaleTo(0.3,1.15).easing(cc.easeIn(0.3));
        let scaleto2 = cc.scaleTo(0.05,1)
        let sq = cc.sequence(scaleto,scaleto2,cc.callFunc((nodes)=>{
            this.runAnimateffs(nodes)
        }))
        no.runAction(sq)
    },

    runAnimateScaleSmall(no){
        no.stopAllActions()
        no.active = true
        no.setScale(1)
        let scaleto = cc.scaleTo(0.3,0.2).easing(cc.easeIn(0.3));
        let sq = cc.sequence(scaleto,cc.callFunc((nodes)=>{
            nodes.active = false
        }))
        no.runAction(sq)
    },

    showflyOne(points){
        let len1 = points.length
        let len2 = this.cincentancArray.length
        for (let i = 0; i < len1; i++) {
            let nos = null
            if(i<len2){
                nos = this.cincentancArray[i];
            }else{
                nos = cc.instantiate(this.cinstance)
                this.node.addChild(nos)
            }
            nos.active = true
            nos.x = points[i].x
            nos.y = points[i].y
            this.runbezier(nos,this.pointN)
        }
    },


    runAnimatefly(){
        let keys = Object.keys(this.itemsfu)
        let len = keys.length
        let points = []
        for (let i = 0; i < len; i++) {
            let fu = this.itemsfu[keys[i]]
            if(fu.node.active == true){
                let node = fu.node.getChildByName("jfbg")
                let ns = this.consPointToRPoint(node)
                points.push(ns)
            }
        }
        this.showflyOne(points)
        
        //this.cincentancArray
    },

    runbezier(node,mpoint){
        let bezier = [cc.v2(node.x,node.y), cc.v2(utils.utils.randomNumBoth(node.x-600,node.x+600), utils.utils.randomNumBoth(node.y+100,node.y+300)), 
            cc.v2(mpoint.x, mpoint.y)];
        let time = utils.utils.randomNumCanf(0.5,1)
        let bezierForward = new cc.BezierTo(time, bezier).easing(cc.easeInOut(0.7));
        let sq = cc.sequence(bezierForward,cc.callFunc((nodes)=>{
            nodes.active = false
        }))
        node.runAction(sq)
    },

    onClickEnterEditorOrOthers(e,t){
        this.showMuShow(()=>{
            this.activeType = parseInt(t)
            this.isEditerType()
        },this)
    },

    showMuShow(funcs,taget){
        this.imagemu.active = true
        this.imagemu.opacity = 0
        let  action = cc.fadeIn(1.3);
        let  action1 = cc.fadeOut(1.3);
        this.scheduleOnce(()=>{
            if(funcs){
                funcs.bind(taget)()
            }
        },1.3)
        let sq = cc.sequence(action,cc.delayTime(0.1),action1,cc.callFunc((nodes)=>{
            nodes.active = false
        }))
        this.imagemu.runAction(sq)
    },

    showOthermod(){
        this.reSetSize()
        this.initPutFs()
        this.activeType = 1
        this.isEditerType()
        this.hroleSpine.node.active = false
    },


    onClickClose() {
        if(this.activeType != 1){
            this.showMuShow(this.showOthermod,this)
            return
        }
        utils.utils.closeView(this);
    },

    updatetimes(dt){
        this.timeIndex+=dt
        if(this.timeIndex>=this.MaxTime){
            this.timeIndex-=this.MaxTime
            this.updatejf()
        }
    },

    update (dt) {
        this.updatetimes(dt)
        if(this.runLvAndWxin){
            this.runLvAndWxinFun(dt)
        }
    },
});
