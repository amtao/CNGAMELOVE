let Initializer = require("Initializer");
let RedDot = require("RedDot");
let TofuProxy = function() {
    this.data = null;
    this.shop = null;
    this.dhShop = null;
    this.rankInfo = null;
    this.UPDATE = "UPDATE_TOFU_INFO";//更新数据消息
    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.doufu.doufuhuodong,this.onDataUpdate,this);
        JsonHttp.subscribe(proto_sc.doufu.shop,this.onShop,this);
        JsonHttp.subscribe(proto_sc.doufu.exchange,this.onExchange,this);
        JsonHttp.subscribe(proto_sc.doufu.qxRank,this.onRankInfo,this);
        JsonHttp.subscribe(proto_sc.doufu.myQxRid,this.onMyRankInfo,this);
    };
    this.clearData = function() {
        this.data = null;
        this.shop = null;
        this.dhShop = null;
        this.rankInfo = null;
    };
    this.onDataUpdate = function(data) {
        this.data = data;
        this.checkRedDot();
        facade.send(this.UPDATE);
        facade.send('Update_BonusView_Info');
    };
    this.checkRedDot= function(){
        let rwdRedDot = false;//积分奖励红点
        if(this.data && this.data.rwd && this.data.rwd.length > 0){
            for(let i = 0;i < this.data.rwd.length;i++){
                let myScore = this.data.max;
                if(this.data.rwd[i].get == 0 && myScore >= this.data.rwd[i].need){
                    rwdRedDot = true;
                }
            }
        }
        RedDot.change('Tofu_Bonus',rwdRedDot);
        //限时活动是否有
        // let limitActRedDot = false;
        // let actData = Initializer.limitActivityProxy.getHuodongList(this.getLimitActType());
        // for(let i = 0;i < actData.length;i++){
        //     if(1 == actData[i].news){
        //         limitActRedDot = true;
        //         break;
        //     }
        // }
        let limitActRedDot = Initializer.limitActivityProxy.checkLimitTimeActRed(this.getLimitActType());
        RedDot.change('TofuLimitAct',limitActRedDot);
        RedDot.change('Tofu_Red',limitActRedDot || rwdRedDot);
    };
    this.onShop = function(data){
        this.shop = data;
    };
    this.onExchange = function (data) {
        if(this.dhShop == null){
            this.dhShop = {};
            this.dhShop.hid = Initializer.limitActivityProxy.Tofu_ACT_ID;
            this.dhShop.title = this.data.exchangeTitle;
            this.dhShop.stime = this.data.exchangeEndTime;
        }
        this.dhShop.rwd = data;
    };
    this.onRankInfo = function(data){
        (this.rankInfo == null)&&(this.rankInfo = {});
        this.rankInfo.rank = data;
    };
    this.onMyRankInfo = function(data){
        (this.rankInfo == null)&&(this.rankInfo = {});
        this.rankInfo.myrank = data;
    };
    this.getMyLeftTimes = function(){
        if(this.data){
            let leftTimes = this.data.playNum + this.data.buy - this.data.num;
            if(leftTimes < 0){
                leftTimes = 0;
            }
            return leftTimes;
        }
        return 0;
    },
    this.getPassBonus = function(){
        let bonusInfo = [];
        if(this.data && this.data.rwd && this.data.rwd.length > 0){
            for(let i = 0;i < this.data.rwd.length;i++){
                bonusInfo.push(this.data.rwd[i]);
            }
        }
        return bonusInfo;
    };
    //游戏结束
    this.endGame = function(jump,cb){
        let msg = new proto_cs.huodong.hd8022Play();
        msg.jump = jump;
        JsonHttp.send(msg,()=>{
            Initializer.timeProxy.floatReward();
            cb && cb();
        });
    },
    this.checkBuyTimesNeed = function(){
        if(this.data){
            if(this.data.need.length > this.data.buy){
                return this.data.need[this.data.buy];
            }
        }
        return -1;
    },
    this.buyPlayTimes = function(cb){
        let msg = new proto_cs.huodong.hd8022Recovery();
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    },
    this.getAllRankList = function(){
        if(this.rankInfo && this.rankInfo.rank){
            return this.rankInfo.rank;
        }
        return [];
    };
    this.getMyScore = function(){
        if(this.rankInfo && this.rankInfo.myrank){
            return this.rankInfo.myrank.score;
        }
        return 0;
    };
    this.getMyRank = function(){
        if(this.rankInfo && this.rankInfo.myrank){
            return this.rankInfo.myrank.rid;
        }
        return 0;
    };
    this.getRankRwd = function(){
        if(this.data && this.data.rankRwd){
            return this.data.rankRwd;
        }
        return [];  
    };
    this.getLimitActType = function(){
        return 1011;
    };
    this.getTofuRank = function(type,cb){
        let msg = new proto_cs.huodong.hd8022paihang();
        msg.type = type;
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    };
}
exports.TofuProxy = TofuProxy;
