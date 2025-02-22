
var initializer = require("Initializer");
var RedDot = require("RedDot");

var CookingCompetitionProxy = function () {
    this.data = null;
    this.shop = null;
    this.exchange = null;
    this.gameScore = 0;
    this.onceScore = 1;
    this.gameOver = false;
    this.playTimes = 0;
    this.rankData = null;
    this.myRid = null;
    this.rankList = [];
    this.dhShop = {};
    this.COOKING_COMPETITION_UPDATE = "COOKING_COMPETITION_UPDATE";
    this.COOKING_COMPETITION_RANK_UPDATE = "COOKING_COMPETITION_RANK_UPDATE";
    this.COOKING_COMPETITION_GAME_CLOSE = "COOKING_COMPETITION_GAME_CLOSE";

   this.ctor = function () {
      JsonHttp.subscribe(
          proto_sc.chuyigame.chuyidasai,
          this.onDataUpdate,
          this
      );

      JsonHttp.subscribe(
          proto_sc.chuyigame.exchange,
          this.onExchange,
          this
      );

      JsonHttp.subscribe(
          proto_sc.chuyigame.shop,
          this.onShop,
          this
      );

       JsonHttp.subscribe(
           proto_sc.chuyigame.myQxRid,
           this.onMyQxRid,
           this
       );

       JsonHttp.subscribe(
           proto_sc.chuyigame.qxRank,
           this.onRankData,
           this
       );
       this.getGameTableInfo();
   };

   this.clearData = function () {
        this.data = null;
        this.shop = null;
        this.exchange = null;
        this.gameScore = 0;
        this.playTimes = 0;
        this.gameOver = false;
        this.rankData = null;
        this.myRid = null;
        this.rankList = [];
        this.dhShop = {};
   };

   this.onDataUpdate = function (data) {
      this.data = data;
      RedDot.change("cookingCompetitionReward", this.checkRewardRed(data.rwd));
      facade.send(this.COOKING_COMPETITION_UPDATE);
   };

   this.onShop = function (data) {
      this.shop = data;
   };

   this.onExchange = function (data) {
       var exchange = data;
       this.dhShop = exchange;
       this.dhShop.rwd = exchange;
       this.dhShop.hid = 8006;
       this.dhShop.title = this.data.exchangeTitle;
       this.dhShop.stime = this.data.exchangeEndTime;
       facade.send("ACTIVITY_SHOP_UPDATE",this.dhShop);
   };

   this.onMyQxRid = function (data) {
       this.myRid = data;
       facade.send(this.COOKING_COMPETITION_RANK_UPDATE);
   };

   this.addScore = function () {

      if (this.gameScore >= this.maxScore) return;
      this.gameScore += this.onceScore;
      facade.send("COOKING_SCORE_UPDATE");
   };

   this.onRankData = function (data) {
       this.rankList = data;
   };

   this.clearScore = function () {
      this.gameScore = 0;
   };

   this.sendOpenActivity = function() {
      JsonHttp.send(new proto_cs.huodong.hd8006Info());
   };

   this.setGameOver = function () {
       if (this.gameOver) return;
      this.gameOver = true;
      if (this.gameScore < this.minScore) {
          this.gameScore = this.minScore;
          facade.send("COOKING_SCORE_UPDATE");
      }
       if (this.playTimes === 1) {
           this.sendPlayRequest();
       } else if (this.playTimes === 10) {
           this.sendPlayTenRequest();
       }
      this.clearScore();
   };

   this.setGameStart = function () {
      this.gameOver = false;
      this.clearScore();
   };

   this.setPlayTimes = function (times) {
      this.playTimes = times;
   };

   this.sendPlayRequest = function () {
      var request = new proto_cs.huodong.hd8006Play();
      request.score = this.gameScore;
      JsonHttp.send(request, () => {
          initializer.timeProxy.floatReward();
        //   facade.send("COOKING_COMPETITION_GAME_CLOSE");
      });
   };

   this.sendPlayTenRequest = function () {
        var request = new proto_cs.huodong.hd8006PlayTen();
        request.score = this.gameScore;
        JsonHttp.send(request, () => {
            initializer.timeProxy.floatReward();
            // facade.send("COOKING_COMPETITION_GAME_CLOSE");
        });
   };

    this.sendGetReward = function (ID) {
        var request = new proto_cs.huodong.hd8006Rwd();
        request.id = ID;
        JsonHttp.send(request, () => {
            initializer.timeProxy.floatReward();
        });
    };

    this.sendRankInfo = function (type) {
        var request = new proto_cs.huodong.hd8006paihang();
        request.type = type;
        JsonHttp.send(request);
    };

    this.checkRewardRed = function (rwd) {
        if (!this.data) return false;
         for (var i = 0; i < rwd.length; i++) {
             var item = rwd[i];
             if (item.get === 1) continue;
             if (item.type === 1) return true;
             if (item.type === 2 && this.data.score >= item.num) return true;
             if (item.type === 3 && this.data.game >= item.num) return true;
         }
         return false;   
    };

    this.getGameTableInfo = function () {
        var cooking = localcache.getItem(localdb.table_cooking, 1);
        this.maxScore = cooking ? cooking.maxscore : 15;
        this.minScore = cooking ? cooking.minscore : 5;
        this.onceScore = cooking ? cooking.oncescore : 1;
    }

}
exports.CookingCompetitionProxy = CookingCompetitionProxy;