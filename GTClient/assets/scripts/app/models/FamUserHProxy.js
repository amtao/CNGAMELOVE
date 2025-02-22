


let Initializer = require("Initializer");
let Utils = require("Utils");
let RedDot = require("RedDot");
var TimeProxy = require("TimeProxy");

import { FIGHTBATTLETYPE ,BATTLE_ATTACK_OWNER} from "GameDefine";

   

function FamUserHProxy() {


    // isStart: 0
    // lastPickTime: 1607068411         //上次收货的时间
    // score: 0                         //积分值
    // warmLv: 1                        //温馨等级
    // warmValue: 0                     //温馨值
    this.intergral = null

    //currentCopy:0             //已经打到多少关
    this.open = null

    //list                      //挂机列表
    this.hook = null

    //haveDrawing
    //haveFurniture
    //haveMaterial
    this.warehouse = null       //家具信息

    this.msgwin = null          //家具道具信息

    this.jyopen = null
    

    //buyCount
    this.buyScore = null


    //shops
    //buyInfo    
    this.shop = null       //玉牌商店信息

    //putFurniture
    this.display = null     //布局信息

    //老的图纸
    this.oldDrawing = null

    //老图纸显示data
    this.oldDrawData = null
    


    //记录之前的等级
    this.wlv = 0

    //战斗使用
    this.battleInfo = null      //


    //选中的英雄id
    this.selectHid = null

    //选中的城池
    this.selectCity = 0

    //选中界面模式
    this.selectMode = 1         //1设定采办   2选择角色家宴

    //this.

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.furniture.intergral, this.intergralFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.open, this.openFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.hook, this.hookFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.copy, this.copyFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.warehouse, this.warehouseFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.buyScore, this.buyScoreFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.shop, this.shopFunc, this);
        JsonHttp.subscribe(proto_sc.furniture.display, this.displayFunc, this);
        //JsonHttp.subscribe(proto_sc.furniture.msgwin, this.msgwinFunc, this);
        
        
        //JsonHttp.subscribe(proto_sc.furniture.fight, this.hookFunc, this);
    };

    this.clearData = function() {

        this.intergral = null

        this.open = null
        this.hook = null
        this.warehouse = null    
        this.msgwin = null   
        this.buyScore = null
        this.shop = null     


        this.battleInfo = null
        this.selectHid = null

        this.selectCity = 0
        
    };

    this.displayFunc = function(e){
        if(e.putFurniture){
            this.display = e
        }else{
            this.display = {"putFurniture":{}}
        }
        facade.send("FURNITURE_DISPLAY")

    }

    this.shopFunc = function(e){
        if(this.shop === null){
            this.shop = e
        }
        if(e.shops){
            this.shop.shops = e.shops
        }
        if(e.buyInfo){
            this.shop.buyInfo = e.buyInfo
        }
        
    }

    this.buyScoreFunc = function(e){
        this.buyScore = e
    }
    
    this.msgwinFunc = function(e){
        this.msgwin = e.items
    }

    this.warehouseFunc = function(e){
        this.warehouse = e
        let keys = Object.keys(e.haveDrawing)
        let len = keys.length
        for (let i = 0; i < len; i++) {
            let type = keys[i]
            let id = e.haveDrawing[keys[i]]
            if(this.oldDrawing && this.oldDrawing[type] && this.oldDrawing[type] != id){
                this.oldDrawData = id
                break;
            }
        }
        this.oldDrawing = JSON.parse(JSON.stringify(e.haveDrawing)) 
        facade.send("FURNITURE_WAREHOUSE")

        let listw = localcache.getList(localdb.table_furniture_drawing)
        let len2 = listw.length
        let style1 = 0
        let style2 = 0
        let style3 = 0
        let style4 = 0
        for (let i = 0; i < len2; i++) {
            let data = listw[i];
            let compose = data.compose
            let maxNumbers = 999999999
            for (let i = 0; i < compose.length-1; i++) {
                let compdata = compose[i]
                let hasd = 0
                if(Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]){
                    hasd = Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]
                    let numbers = parseInt(Initializer.famUserHProxy.warehouse.haveMaterial[compdata.id]/compdata.count)
                    maxNumbers = maxNumbers>numbers?numbers:maxNumbers
                }else{
                    maxNumbers = 0
                }
            }
            if(data.stytle === 1 && maxNumbers>0){
                style1 = 1
            }
            if(data.stytle === 2 && maxNumbers>0){
                style2 = 1
            }
            if(data.stytle === 3 && maxNumbers>0){
                style3 = 1
            }
            if(data.stytle === 4 && maxNumbers>0){
                style4 = 1
            }
        }
        RedDot.change("creatf", style1===1||style2===2||style3===3||style4===4);
        RedDot.change("f_style1", style1===1);
        RedDot.change("f_style2", style1===2);
        RedDot.change("f_style3", style1===3);
        RedDot.change("f_style4", style1===4);
    }

    this.copyFunc = function(e){
        this.battleInfo = e
    }

    this.openFunc = function(e){
        this.open = e
    }
    this.hookFunc = function(e){
        this.hook = e
        facade.send("FURNITURE_HOOK")
    }
    this.intergralFunc = function(e){
        e.warmValue = 0
        if(e.warmValues){
            let keys = Object.keys(e.warmValues)
            let len = keys.length
            for (let i = 0; i < len; i++) {
                e.warmValue += e.warmValues[keys[i]]
            }
            if(this.intergral && this.intergral.warmValue<e.warmValue){
                Utils.utils.openPrefabView("familyparty/AddWinxin", null, {number:e.warmValue - this.intergral.warmValue});
            }
        }
        this.intergral = e
        if(this.wlv>0 && this.wlv != e.warmLv ){
            Utils.utils.openPrefabView("familyparty/FurnituresLVup",null);
        }
        this.wlv = e.warmLv
        facade.send("FURNITURE_INTERGRAL")
        this.redjyopen()
    }

    this.sendWinMessage = function(){
        facade.send("FURNITURE_FIGHTWIN")
    };

    this.sendFight = function(cardId,callback){
        var e = new proto_cs.furniture.fight()
        e.cardId = cardId
        JsonHttp.send(e,(rep)=>{
            callback && callback(rep);
        })
    };


    
    //摆放家具
    this.sendMessagePutFurniture = function(array){
        let es = new proto_cs.furniture.putFurniture()
        es.ids = array
        JsonHttp.send(es,(e)=>{
            Initializer.timeProxy.floatReward();
            facade.send("FURNITURE_GETGOODS")
            Utils.alertUtil.alert(i18n.t("HOMEPART_HOMEEDITOR_SAVE_SU"))
        })
    }

    

    //商店购买
    this.sendMessageExchangeShop = function(id){
        let es = new proto_cs.furniture.exchangeShop()
        es.id = id
        JsonHttp.send(es,(e)=>{
            Initializer.timeProxy.floatReward();
            facade.send("FURNITURE_GETGOODS")
        })
    }

    
    //商店列表购买详情
    this.sendMessageGetShopList = function(callBack){
        let es = new proto_cs.furniture.getShopList()
        JsonHttp.send(es,(e)=>{
            if(callBack){callBack(e)}
        })
    }


    //元宝买积分
    this.sendMessagebuyScoreByGold = function(callBack){
        let es = new proto_cs.furniture.buyScoreByGold()
        JsonHttp.send(es,(e)=>{
            if(callBack){
                callBack(e)
            }
        })
    }

    
    //结束宴会
    this.sendMessageEndFeast = function(callBack){
        let es = new proto_cs.furniture.endFeast()
        JsonHttp.send(es,(e)=>{
            Initializer.timeProxy.floatReward();
            if(callBack){
                callBack(e)
            }
        })
    }

    //开启宴会
    this.sendMessageOpenFamilyFeast = function(hid,callBack){
        let es = new proto_cs.furniture.openFamilyFeast()
        es.heroId = hid
        JsonHttp.send(es,(e)=>{
            if(callBack){
                callBack(e)
            }
        })
    }
    

    this.redjyopen = function(e){
        if(!this.jyopen){
            return
        }
        let maxc = Utils.utils.getParamInt("furniture_times")
        let jfneed = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv).needitem[0].count
        let canEnNumber = maxc - this.jyopen.openCount
        if(canEnNumber>0 && jfneed <= Initializer.famUserHProxy.intergral.score){
            RedDot.change("f_jyopen",true)
        }else{
            RedDot.change("f_jyopen",false)
        }
    }



    //getFeastInfo 发送获取宴会信息消息
    this.sendMessageGetFeastInfo = function(callBack){
        let es = new proto_cs.furniture.getFeastInfo()
        JsonHttp.send(es,(e)=>{
            this.jyopen = e.a.furniture.jyopen
            Initializer.famUserHProxy.redjyopen()
            if(callBack){
                callBack(e)
            }
        })
    }


    //发送收取积分相关
    this.sendMessageGetScores = function(){
        let es = new proto_cs.furniture.getScores()
        JsonHttp.send(es,(e)=>{
        })
    }




    //发送分解家具
    this.sendMessagedecomposeFurniture = function(id,con,callBack){
        let es = new proto_cs.furniture.decomposeFurniture()
        es.id = id
        es.dcount = con
        JsonHttp.send(es,(e)=>{
            Initializer.timeProxy.floatReward();
            if(callBack){
                callBack()
            }
        })

    }


    //发送创建家具
    this.sendMessageCreate= function(mod,callBack){
        let es = new proto_cs.furniture.createFurniture()
        es.fInfos = mod
        JsonHttp.send(es,(e)=>{
            if(callBack){
                callBack()
            }
        })
    }



    //发送获取家具消息
    this.sendMessageFinfo = function(){
        let es = new proto_cs.furniture.getFurnitureInfo()
        JsonHttp.send(es,(e)=>{
        })
    }


    //发送补货消息
    this.sendMessagebuhuo = function(copyid){
        let es = new proto_cs.furniture.clearHookTime()
        es.copyId = copyid
        JsonHttp.send(es,(e)=>{
            utils.alertUtil.alert(i18n.t("HOMEPART_HOMEEDITOR_BUHUOSU"))
        })
    }


    //发送挂机消息
    this.sendMessageOnHook = function(hid,copyid,index){
        let es = new proto_cs.furniture.onHook()
        es.heroId = hid
        es.copyId = copyid
        es.index = index
        JsonHttp.send(es,(e)=>{
            if(this.oldDrawData != null){
                Utils.utils.openPrefabView("familyparty/DrawingUp", null, {id:this.oldDrawData});
                this.oldDrawData = null;
            }
            Initializer.timeProxy.floatReward();
        })
    }

    //发起战斗的消息相关的
    this.getBattlesinfo = function(id){
        let es = new proto_cs.furniture.getFightInfo()
        JsonHttp.send(es,(e)=>{
            Utils.utils.openPrefabView("battle/BattleBaseView", null, {type:FIGHTBATTLETYPE.FURNITURE,ids:id});
        })
    };

    this.redGoods = function() {
        let clist = localcache.getList(localdb.table_furniture_battle);
        let len = clist.length
        let fff = false
        let hookinfo = Initializer.famUserHProxy.hook.hookInfo || {}
        for (let i = 0; i < len; i++) {
            let id = clist[i].id;
            if(hookinfo[id] && hookinfo[id].hookEndTime - utils.timeUtil.second > 0){
                fff = true
                break
            }else if((i<=Initializer.famUserHProxy.open.currentCopy)){
                fff = true
                break
            }
        }
        RedDot.change("getSoon",fff)
    }

    this.sendGoodsView = function(){
        var o = new proto_cs.furniture.getFightBaseInfo();
        JsonHttp.send(o, function(e) {
            Initializer.famUserHProxy.redGoods()
        });
    }


    this.showGetGoodsView = function(){
        var o = new proto_cs.furniture.getFightBaseInfo();
        JsonHttp.send(o, function(e) {
            facade.send("SHOWUSERHOME",false)
            Utils.utils.openPrefabView("familyparty/GetGoods");
        });    
    }

    //发送第一次进入家园
    this.sendFirstEnter = function(){
        if(this.intergral.lastPickTime<=0){
            var o = new proto_cs.furniture.firstJoin();
            JsonHttp.send(o, function(e) {
            });    
        }
    }

    this.getHeroList = function(){
        let herolist = Initializer.servantProxy.servantList
        // let cfgData = localcache.getItem(localdb.table_hero, role.id);
        // let tmpData = hasData.filter((data) => {
        //     return data.id == role.id;
        // });
        // role.setData(cfgData, tmpData && tmpData.length > 0, i);
    }

}
exports.FamUserHProxy = FamUserHProxy;

