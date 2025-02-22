
var Initializer = require("Initializer");
var RedDot = require("RedDot");
var utils = require("Utils");
let timeProxy = require('TimeProxy');

var DrawCardProxy = function () {
    this.data = null;
    this.settlementData = null;
    this.DRAW_CARD_SETTLEMENT = "DRAW_CARD_SETTLEMENT";
    this.POOL_UPDATE = "POOL_UPDATE";
    this.ALL_CARD_RED = "ALL_CARD_RED";
    this.SHOW_TIAN_CI = "SHOW_TIAN_CI";
    this.SHOW_JINDU = "SHOW_JINDU";
    this.poolData = null;
    this.timer = null;
    this.cfgData = null;
    this.actData = null;

    this.ctor = function () {
        JsonHttp.subscribe(
            proto_sc.card.drawCard,
            this.onDrawCard,
            this
        );

        JsonHttp.subscribe(
            proto_sc.card.cardsys,
            this.onPoolData,
            this
        );

        JsonHttp.subscribe(
            proto_sc.card.cfg,
            this.onCfgData,
            this
        );

        JsonHttp.subscribe(
            proto_sc.card.act,
            this.onActData,
            this
        );

    };

    this.clearData = function() {
        this.data = null;
        this.settlementData = null;
        this.poolData = null;
    };

    this.clearSettlementData = function () {
        this.settlementData = null;
    };

    this.onCfgData = function(data){
        this.cfgData = data;
        facade.send(this.SHOW_TIAN_CI);
    };

    this.onActData = function(data){
        this.actData = data;
        facade.send(this.SHOW_JINDU);
    };

    this.onPoolData = function (t) {
        this.poolData = t;
        facade.send(this.POOL_UPDATE);
        var free = this.checkFree();
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.drawCard);
        RedDot.change("freeDrawCard", bOpen && free);
        facade.send(this.ALL_CARD_RED);
        this.countDownFree();
    };

    // freetime 为0可免费抽
    this.checkFree = function () {
        if (!this.poolData) return false;
        var t = this.poolData.poolstate[1].freetime;
        var s = t - utils.timeUtil.second;
        return s <= 0 || t === 0;
    };

    this.count = function(freeTime) {
        var s = freeTime - utils.timeUtil.second;
        if (s <= 0) {
            facade.send(this.ALL_CARD_RED);
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.drawCard);
            RedDot.change("freeDrawCard", bOpen);
            if(this.timer)
            {
                clearInterval(this.timer);
                this.timer = null;
            }  
        }
    };

    /**用于主界面加载完刷新红点*/
    this.onUpdateRed = function(){
        var free = this.checkFree();
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.drawCard);
        RedDot.change("freeDrawCard", bOpen && free);
    };

    this.countDownFree = function (){
        if (!this.poolData) return
        var freeTime = this.poolData.poolstate[1].freetime;
        var leftTime = freeTime - utils.timeUtil.second;
        if (leftTime >= 0) {
            if(!this.timer)
            this.timer = setInterval(()=>{
                this.count(freeTime);
            },1000);
            this.count(freeTime);
        }else{
            facade.send(this.ALL_CARD_RED);
            let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.drawCard);
            RedDot.change("freeDrawCard", bOpen);
        }
    };

    this.onDrawCard = function (data) {
        this.settlementData = data;
        //var list = this.getGainCardArray();
        facade.send(this.DRAW_CARD_SETTLEMENT);
        facade.send(this.ALL_CARD_RED);
        // console.log(list);
    };

    // type: 0  单次 , 1 十次
    this.sendDrawCard = function (type,id) {
        var request = new proto_cs.card.drawCard();
        request.drawtype = type;
        let poolid = id || 1;
        request.poolid = poolid;
        JsonHttp.send(request);
    };


    this.sendDrawInfo = function () {
        // var request = new proto_cs.huodong.hd6500Info();
        // JsonHttp.send(request);
    };

    this.getGainCardArray = function () {
        if (!this.settlementData) return;
        var cardList = [];
        var drawMap = this.settlementData.drawids;
        if (drawMap != null){
            for (var key in this.settlementData.drawids) {
                var card = {};
                card.id = parseInt(Object.keys(drawMap[key])[0]);
                card.state = drawMap[key][card.id];
                cardList.push(card);
            }
        }
        
        if (cardList.length !== 0) {
            cardList = this.shuffle(cardList);
        }
        if (this.settlementData.drawItems != null){
            for (var ii = 0 ; ii < this.settlementData.drawItems.length;ii++){
                cardList.push(this.settlementData.drawItems[ii]);
            }
        }
        return cardList;
    };
    
    this.sendBuyItem = function (count) {
        var request = new proto_cs.card.quick_buy();
        request.num = count;
        JsonHttp.send(request, (e) => {
            Initializer.timeProxy.floatReward();
        });
    };

    this.checkCardChip = function (arr) {
          for (var i = 0; i < arr.length; i++) {
              if (arr[i].state === 0) return true;
          }
          return false;
    };

    this.shuffle = function (arr) {
        for (var i = arr.length - 1; i >= 0; i--) {
            var randomIndex = Math.floor(Math.random() * (i + 1));
            var itemAtIndex = arr[randomIndex];
            arr[randomIndex] = arr[i];
            arr[i] = itemAtIndex;
        }
        return arr;
    };

    this.send6242Info = function() { 
        var req6242Info = new proto_cs.huodong.hd6242Info();
        JsonHttp.send(req6242Info, (e) => {
            console.log("e is "+e);
        });
    };

}
exports.DrawCardProxy = DrawCardProxy;