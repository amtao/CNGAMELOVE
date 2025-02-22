


var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Config");
var UIUtils = require("UIUtils");
cc.Class({
    extends: cc.Component,

    properties: {
        scrollView: cc.ScrollView,
        mainNode:cc.Node,

        miansNode:cc.Node,

        lbarm:cc.Label,

        heros:[UrlLoad],


        nextNode:cc.Node,
        endNode:cc.Node,
        nameLabel:cc.Label



        //camera:cc.Camera
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

    onLoad () {

        this.isChanges = false

        this.timesShow = []
        this.rtimeIndex = 0

        this.btns = []
        this.isunlockImages = []
        this.lbnames = []
        this.timesImages = []
        this.timesImages2 = []
        
        this.roads = []

        this.maxTime = 0
        this.roadsChilds = null
        this.runBegin = false
        this.timeIndex = 0
        this.roadIndex = 0
        this.roadsOneId = 0


        this.oldarm = 0

        this.scalesn = 1

        this.listLen = 0

        facade.subscribe("FURNITURE_FIGHTWIN", this.messageBackWin, this);
        facade.subscribe("FURNITURE_HOOK", this.hookback, this);

        

        this.initThis()
        this.hookback()


        facade.subscribe(Initializer.playerProxy.PLAYER_USER_UPDATE, this.update_UserData, this);
        this.oldarm = Initializer.playerProxy.userData.army
        this.update_UserData()

    },
    update_UserData(){
        UIUtils.uiUtils.showNumChange(this.lbarm, this.oldarm, Initializer.playerProxy.userData.army);
    },
    hookback(){
        this.timesShow.length = 0
        let hookinfo = Initializer.famUserHProxy.hook.hookInfo || {}
        let clist = localcache.getList(localdb.table_furniture_battle);
        let len = clist.length
        for (let i = 0; i < len; i++) {
            let id = clist[i].id;
            this.heros[i].node.parent.parent.active = false
            if(hookinfo[id] && hookinfo[id].hookEndTime - utils.timeUtil.second > 0){
                let tmod = {
                    index:i,
                    endTime: hookinfo[id].hookEndTime - utils.timeUtil.second 
                }
                this.timesShow.push(tmod)
                let hid = hookinfo[id].heroId
                this.heros[i].node.parent.parent.active = true
                this.heros[i].url = UIUtils.uiHelps.getServantHead(hid);
            }else if(i<=Initializer.famUserHProxy.open.currentCopy-1){
                this.heros[i].node.parent.parent.active = false
                this.timesImages2[i].active = true
            }else{
                this.heros[i].node.parent.parent.active = false
            }
        }
    },
    changeAnchor(){
        if(this.isChanges){
            return
        }
        if(Initializer.famUserHProxy.open.currentCopy>=5){
            this.miansNode.anchorY = 1
            this.mainNode.y -= 1500
            this.isChanges = true
        }
    },
    initThis(){
        let clist = localcache.getList(localdb.table_furniture_battle);
        this.listLen = clist.length
        for (let i = 0; i < 8; i++) {
            let btns = this.mainNode.getChildByName('btn_v' + (i+1));
            let icon = btns.getChildByName('icon');
            let isunlock = icon.getChildByName('isunlock');
            let lbname = icon.getChildByName('tiliValText').getComponent(cc.Label)
            isunlock.icon = icon
            let imagebg = icon.getChildByName('iamgebg');
            let iamgebg2 = icon.getChildByName('imagebg2');
            iamgebg2.active = false
            let time = imagebg.getChildByName('time').getComponent(cc.Label)

            let roadsn = this.mainNode.getChildByName('roads' + (i+1));
            if(roadsn){
                roadsn.active = true
                let children = roadsn.children
                let len2 = children.length
                if(i>=Initializer.famUserHProxy.open.currentCopy){
                    for (let j = 0; j < len2; j++) {
                        children[j].active = false
                    }
                }
                this.roads.push(roadsn)
            }
            if(roadsn && i>=this.listLen-1){
                roadsn.active = false
            }
            if(i>=this.listLen){
                icon.active = false
                continue;
            }

            lbname.string = clist[i].name

            imagebg.Label_time = time

            imagebg.active = false
            isunlock.active = i<=Initializer.famUserHProxy.open.currentCopy
            icon.active = i<=Initializer.famUserHProxy.open.currentCopy

            this.btns.push(btns)
            this.isunlockImages.push(isunlock)
            this.lbnames.push(lbname)
            this.timesImages.push(imagebg)
            this.timesImages2.push(iamgebg2)
        }
    },

    messageBackWin(){
        this.hookback()
        this.changeAnchor()
        let leve = Initializer.famUserHProxy.open.currentCopy-1
        leve = leve<0?0:leve
        leve = leve>=this.listLen-1?this.listLen-1:leve
        if(leve >= this.listLen-1){
            this.endNode.active = true
            return
        }
        this.scheduleOnce(()=>{
            this.reToPosition(leve)
            this.scheduleOnce(()=>{
                if(leve === this.listLen-1){
                    return
                }
                this.runAnimateRoads(leve)
            },0.2)
        },0.2)
    },

    selectOneBtn(t,e){
        let index = parseInt(e)
        if(index>this.listLen){
            return
        }
        let hookinfo = Initializer.famUserHProxy.hook.hookInfo || {}
        if((hookinfo[index] && hookinfo[index].hookEndTime - utils.timeUtil.second>0) || index === Initializer.famUserHProxy.open.currentCopy+1){
            utils.utils.openPrefabView("familyparty/Purchase", null, { id: index });
            return
        }
        if(index > Initializer.famUserHProxy.open.currentCopy+1){

            //文字 通关前面的关卡
            return
        }
        Initializer.famUserHProxy.selectCity = index
        utils.utils.openPrefabView("familyparty/PurchaseSelect", null ,{ id: index });
    },

    start () {
        let leve = Initializer.famUserHProxy.open.currentCopy 
        leve = leve<0?0:leve
        leve = leve>=this.listLen-1?this.listLen-1:leve


        this.scheduleOnce(()=>{
            this.reToPosition(leve)
        },0.2)
    },

    onClickCloseTc(){
        this.nextNode.active = false
        this.endNode.active = false
    },

    scalenoll(backs,index){
        let actionScale = cc.scaleTo(0.5, 1, 1);
        this.scalesn = 1

        let actionMove = cc.sequence(actionScale, cc.callFunc(() => {

            let ldata = localcache.getItem(localdb.table_furniture_battle, index+1);
            this.nextNode.active = true
            this.nameLabel.string = ldata.name
            if(backs){
                backs()
            }
        }));
        this.isunlockImages[index].active = true
        this.isunlockImages[index].icon.active = true
        this.reToPosition(index,0.5);
        this.miansNode.runAction(actionMove);
    },

    scaleBig(backs,index){
        let actionScale = cc.scaleTo(0.5, 1.5, 1.5);
        this.scalesn = 1.5
        let moveBy = cc.moveBy(0.3,cc.v2(0,0))
        let actionMove = cc.sequence(actionScale, moveBy,cc.callFunc(() => {
            if(backs){
                backs()
            }
        }));
        this.reToPosition(index,0.5);
        this.miansNode.runAction(actionMove);
    },

    runAnimateRoads(index){
        this.roadsOneId = index
        this.scaleBig(()=>{
            this.roadsChilds = this.roads[index].children
            this.rtimeIndex = 0
            this.roadIndex = 0
            this.maxTime = 1.5/this.roadsChilds.length
            this.runBegin = true
            this.reToPosition(this.roadsOneId+1,1.5)
        },this.roadsOneId)
    },

    rx(index){
        let rxs = 720/2
        let x = this.btns[index].x*this.scalesn - rxs
        x = x>0?x:0
        return x
    },
    ry(index){
        let y = -this.btns[index].y*this.scalesn
        y = y<-220*this.scalesn?-220*this.scalesn:y;
        y = y>0*this.scalesn?0*this.scalesn:y
        return y
    },

    reToPosition(index,needTime = 0.2){
        let x = this.rx(index)
        let y = this.ry(index)
        this.scrollView.scrollToOffset(cc.v2(x,y), needTime,false);
    },

    onClickClose() {
        utils.utils.closeView(this);
        facade.send("SHOWUSERHOME")
    },

    runRoads(dt){
        this.rtimeIndex+=dt
        if(this.rtimeIndex>=this.maxTime){
            this.rtimeIndex-=this.maxTime
            if(this.roadIndex<this.roadsChilds.length){
                this.roadsChilds[this.roadIndex].active = true
                this.roadIndex++
            }else{
                this.runBegin = false
                this.scalenoll(null,this.roadsOneId+1)
            }
        }
    },

    showTimed(){
        for (let i = 0; i < this.timesShow.length; i++) {
            let tmod = this.timesShow[i];
            tmod.endTime--
            let timem = tmod.endTime
            let strings = ""
            if(timem>0){
                let h = utils.utils.fullZero(parseInt(timem/60/60),2)
                let s = utils.utils.fullZero(parseInt(timem%60),2) 
                let m = utils.utils.fullZero(parseInt((timem/60)%60),2)  
                strings += " "+h+":"+m+":"+s
                this.timesImages[tmod.index].active = true
                this.timesImages2[tmod.index].active = false
                this.timesImages[tmod.index].Label_time.string = strings
            }else{
                this.timesImages[tmod.index].active = false
                this.timesImages2[tmod.index].active = true
                this.timesShow.splice(i,1);
            }
        }
    },

    reshowTime(dt){
        this.timeIndex +=dt
        if(this.timeIndex>=1){
            this.timeIndex-=1
            this.showTimed()
        }
    },

    update (dt) {
        this.reshowTime(dt)
        if(this.runBegin){
            this.runRoads(dt)
        }
    },
});
