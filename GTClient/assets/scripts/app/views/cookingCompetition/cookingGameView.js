
var utils = require("Utils");
var initializer = require("Initializer");
var cookingGameItem = require("cookingGameItem");

cc.Class({
    extends: cc.Component,

    properties: {
        numberSpriteFrame: [cc.SpriteFrame],
        timeSprite: cc.Sprite,
        scoreLabel: cc.Label,
        countDownLabel: cc.Label,
        foods: [cookingGameItem],
        anim: cc.Animation,
        floatItemPrefab: cc.Prefab,
        floatParent: cc.Node
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        facade.subscribe("COOKING_FOOD_RECLAIM", this.reclaimFood, this);
        facade.subscribe("COOKING_SCORE_UPDATE", this.onScoreUpdate, this);
        facade.subscribe("TIME_RUN_FUN", this.onClickClose, this);
        facade.subscribe("COOKING_FLOAT_RECLAIM", this.putIntoPool, this);
        this.remainingFood = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        this.isGameStart = false;
        this.gameTime = 6;             // 游戏时常
        this.addFoodSpeed = 0.3;          //  增加食物的速度  单位 秒        前5s每秒1个，后5s 每0.5s1个
        this.updateTimeInterval = 1;        // 倒计时刷新的间隔
        this.gameInterval = this.gameTime;  // 游戏当前进行倒计时
        this.addFoodInterval = this.addFoodSpeed;        // 为0时增加食物
        this.readyTime = 3;
        this.countDownLabel.string = this.gameTime;
        this.anim.play("cooking01");
        this.createPool();
        this.getTableInfo();
        // console.log(localcache.getItem(localdb.table_cooking, 1));
    },

    start () {
        this.ready();
    },

    getTableInfo () {
        var cooking = localcache.getItem(localdb.table_cooking, 1);
        this.addFoodSpeed = cooking.interval;
    },

    createPool () {
        var initCount = 10;
        this.floatPool = new cc.NodePool();
        for (var i = 0; i < initCount; i++) {
            var item = cc.instantiate(this.floatItemPrefab);
            this.floatPool.put(item);
        }
    },

    putIntoPool (itemNode) {
        this.floatPool.put(itemNode);
    },

    createFloatItem () {
        var floatItem = null;
        if (this.floatPool.size() > 0) {
            floatItem = this.floatPool.get();
            floatItem.opacity = 255;
            floatItem.y = 0;
            floatItem.getComponent(cc.Animation).play();
        } else {
            floatItem = cc.instantiate(this.floatItemPrefab);
        }
        floatItem.parent = this.floatParent;
    },

    ready () {
        this.timer3 = setInterval(() => {
            this.readyTime--;
            this.timeSprite.spriteFrame = this.numberSpriteFrame[this.readyTime - 1];
            if (this.readyTime <= 0) {
                this.timeSprite.node.active = false;
                this.gameStart();
                clearInterval(this.timer3);
            }
        }, 1000)
    },

    gameStart () {
        this.anim.play("cooking02");
        initializer.cookingCompetitionProxy.setGameStart();
        this.isGameStart = true;
        this.gameInterval = this.gameTime;
        // this.addFoodSpeed = 0.3;
        this.updateTimeInterval = 1;
        this.count = 6;
        this.countDown();
        this.timer = setInterval(() => {
            this.addFood();
        }, 300)

        this.timer2 = setInterval(() => {
            this.count--;
            this.countDown();
            if (this.count <= 0) {
                this.gameOver();
            }
        }, 1000)
    },

    gameOver () {
        this.isGameStart = false;
        clearInterval(this.timer);
        clearInterval(this.timer2);
        initializer.cookingCompetitionProxy.setGameOver();
        // initializer.cookingCompetitionProxy.clearScore();
    },

    onScoreUpdate () {
        this.createFloatItem();
        this.scoreLabel.string = initializer.cookingCompetitionProxy.gameScore;
    },

    addFood () {
        if ( this.remainingFood.length <= 0 ) return;
        var foodIndex = this.shuffle(this.remainingFood).pop();
        var food = this.foods[foodIndex];
        food.appear();
    },

    // 回收食物
    reclaimFood (food) {
        this.remainingFood.push(food);
    },

    countDown () {
        this.countDownLabel.string = this.count;
    },

    onClickClose () {
        clearInterval(this.timer3);
        this.gameOver();
        utils.utils.closeView(this);
    },

    shuffle (arr) {
        for (var i = arr.length - 1; i >= 0; i--) {
            var randomIndex = Math.floor(Math.random() * (i + 1));
            var itemAtIndex = arr[randomIndex];
            arr[randomIndex] = arr[i];
            arr[i] = itemAtIndex;
        }
        return arr;
    },

    update (dt) {
        if (!this.isGameStart) return;
        this.gameInterval -=dt;
        this.updateTimeInterval -=dt;
        // if (this.updateTimeInterval <= 0) {
        //     this.updateTimeInterval = 1;
        //     this.countDown();
        // }
        // if (this.gameInterval <= 0) {
        //     this.gameOver();
        //     return;
        // }
        //
        // this.addFoodInterval -=dt;
        // if (this.addFoodInterval <= 0) {
        //     this.addFoodInterval = this.addFoodSpeed;
        //     this.addFood();
        // }

    },
});
