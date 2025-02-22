let Initializer = require("Initializer");
let RedDot = require("RedDot");
let CrushProxy = function() {
    this.data = null;
    this.shop = null;
    this.dhShop = null;
    this.rankInfo = null;
    this.allStep = 50;//总步数
    this.UPDATE = "UPDATE_CRUSH_INFO";//更新数据消息
    this.BUYLIFE = "BUY_CRUSH_LIFE";//更新数据消息
    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.sanxiao.sanxiaohuodong,this.onDataUpdate,this);
        JsonHttp.subscribe(proto_sc.sanxiao.shop,this.onShop,this);
        JsonHttp.subscribe(proto_sc.sanxiao.exchange,this.onExchange,this);
        JsonHttp.subscribe(proto_sc.sanxiao.qxRank,this.onRankInfo,this);
        JsonHttp.subscribe(proto_sc.sanxiao.myQxRid,this.onMyRankInfo,this);
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
        facade.send("Update_BonusView_Info");
    };
    this.getCrushPassBonus = function(){
        let bonusInfo = [];
        if(this.data && this.data.rwd && this.data.rwd.length > 0){
            for(let i = 0;i < this.data.rwd.length;i++){
                bonusInfo.push(this.data.rwd[i]);
            }
        }
        return bonusInfo;
    };
    this.getHeroID = function(stageID){
        if(this.data && this.data.hero){
            if(this.data.hero.length >= stageID){
                return this.data.hero[stageID-1];
            }
            return this.data.hero[0];
        }
        return 0;
    },
    this.getLeftBloodRate = function(){
        if(this.data && this.data.blood && this.data.maxBlood){
            return (this.data.blood / (this.data.maxBlood*1.0));
        }
        return 0;
    },
    this.getLeftBloodInfo = function(){
        if(this.data && this.data.blood && this.data.maxBlood){
            let bossblood = this.data.blood;
            (bossblood < 0) && (bossblood = 0);
            return bossblood+"/"+this.data.maxBlood;
        }
        return "";
    },
    this.getStateID = function(){
        if(this.data && this.data.pId){
            return this.data.pId;
        }
        return 1;
    },
    this.getLeftStepRound = function(){
        if(this.data && this.data.round != null){
            return (this.data.maxRound - this.data.round);
        }
        return 0;
    },
    this.getMaxLifeCount = function(){
        if(this.data && this.data.maxPoint){
            return this.data.maxPoint;
        }
        return 0;
    },
    this.getLifeCount = function(){
        if(this.data && this.data.point){
            return this.data.point;
        }
        return 0;
    },
    this.checkLifeCount = function(){
        if(this.getLifeCount() <= 0){
            facade.send(this.BUYLIFE);
            return false;
        }
        return true;
    },
    this.getEndBonus = function(){
        if(this.data && this.data.fixed){
            return this.data.fixed;
        }
        return [];
    },
    this.getLifeCD = function(){
        if(this.data && this.data.pointTime && this.data.cdTime){
            return (this.data.pointTime + this.data.cdTime);
        }
        return 0;
    },
    this.getChessInfo = function(){
        if(this.data && this.data.chess){
            return this.data.chess;
        }
        return [];
    },
    this.getMapID = function(){
        if(this.data && this.data.pId){
            return this.data.pId;
        }
        return 1;
    },
    this.checkMapEnd = function(){
        if(this.data.blood <= 0){
            return 1;
        }else if(this.getLeftStepRound() <= 0){
            return -1;
        }
        return 0;
    },
    this.checkMap = function(cb){
        let endFlag = this.checkMapEnd();
        if(endFlag == 1){//胜利了
            let msg = new proto_cs.huodong.hd8018Next();
            msg.chess = [];
            JsonHttp.send(msg,()=>{
                //Initializer.timeProxy.floatReward();
                cb && cb();
            });
        }else if(endFlag == -1){//输了
            let msg = new proto_cs.huodong.hd8018Fail();
            msg.chess = [];
            JsonHttp.send(msg,()=>{
                cb && cb();
            });
        }else{
            cb && cb();
        }
    },
    this.checkRefreshCrush = function(){
        let freshTag = cc.sys.localStorage.getItem("RefreshCrush");
        return freshTag
    },
    this.resetRefreshCrush = function(){
        cc.sys.localStorage.setItem("RefreshCrush",0);
    },
    this.refreshCrush = function(cb){
        let msg = new proto_cs.huodong.hd8018Reset();
        JsonHttp.send(msg,()=>{
            cc.sys.localStorage.setItem("RefreshCrush",1);
            cb && cb();
        });
    },
    this.playCrush = function(playData,combo,cb){
        if(combo > 1 && this.checkMapEnd() != 0){//连击过程中，如果已经失败或成功不发消息
            cb && cb();
            return;
        }
        let msg = new proto_cs.huodong.hd8018Play();
        msg.list = playData;
        msg.combo = combo;
        msg.chess = [];
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    },
    this.recoveryLife = function(count,cb){
        let msg = new proto_cs.huodong.hd8018Recovery();
        msg.num = count;
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    },
    this.onShop = function(data){
        this.shop = data;
    };
    this.checkRedDot= function(){
        let rwdRedDot = false;//积分奖励红点
        if(this.data && this.data.rwd && this.data.rwd.length > 0){
            for(let i = 0;i < this.data.rwd.length;i++){
                let myScore = this.data.pId;
                if(this.data.rwd[i].get == 0 && myScore >= this.data.rwd[i].need){
                    rwdRedDot = true;
                }
            }
        }
        let bHasPoint = this.data && this.data.point > 0;
        RedDot.change('Crush_Bonus', rwdRedDot);
        //限时活动是否有
        let limitActRedDot = false;
        let actData = Initializer.limitActivityProxy.getHuodongList(this.getLimitActType());
        for(let i = 0; i < actData.length; i++) {
            if(1 == actData[i].news) {
                limitActRedDot = true;
                break;
            }
        }
        RedDot.change('CrushLimitAct', limitActRedDot);
        RedDot.change('Crush_Red', limitActRedDot || rwdRedDot || bHasPoint);
    };
    this.onExchange = function (data) {
        if(this.dhShop == null){
            this.dhShop = {};
            this.dhShop.hid = Initializer.limitActivityProxy.CRUSH_ACT_ID;
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
    this.getPveRankRwd = function(){
        if(this.data && this.data.pveRwd){
            return this.data.pveRwd;
        }
        return [];  
    };
    this.getLimitActType = function(){
        return 1010;
    };
    this.getCrushRank = function(type,cb){
        let msg = new proto_cs.huodong.hd8018paihang();
        msg.type = type;
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    };
    this.getPveCrushRank = function(cb){
        let msg = new proto_cs.huodong.hd8018Pvepaihang();
        JsonHttp.send(msg,()=>{
            cb && cb();
        });
    };
    this.saveChessInfo = function(chess){
        let msg = new proto_cs.huodong.hd8018Save();
        msg.chess = chess;
        JsonHttp.send(msg,()=>{
        });
    }
}
exports.CrushProxy = CrushProxy;
