let initializer = require("Initializer");

cc.Class({
    extends: cc.Component,
    properties: {
        collisionSize:cc.Size,
        moonAni:sp.Skeleton,//月亮spine动画
        battleNode:cc.Node,//可移动区域节点
    },
    onLoad () {
        this.canMove = false;
        this.moveSpeed = 100;//速度大小
        this.acceleration = 600;//向上的加速度
        this.startPosY = - 0.5 * cc.winSize.height + 50;
    },
    endMove(){
        this.node.active = false;
    },
    startMove(touchPos){
        this.node.active = true;
        this.moveSpeed = 100;//速度大小
        this.acceleration = 800;//向上的加速度
        this.node.x = touchPos.x;
        this.node.y = this.startPosY;
        this.canMove = true;
    },
    update(dt){
        if(this.canMove){
            this.node.y += (this.moveSpeed*dt + 0.5*this.acceleration*dt*dt);
            this.moveSpeed = this.moveSpeed + this.acceleration*dt;
            let outScreen = this.checkOutScreen();
            if (outScreen) {// 进行反弹
                initializer.moonBattleProxy.sendAct(0);
                facade.send("MOON_BATTLE_FIRE_MISS");
                this.canMove = false;
                this.node.active = false;
			}
        }
    },
    //判断是否出界
	checkOutScreen(){
        // let battleRangeY_min = -this.battleNode.height / 2 + 20;
        let battleRangeY_max = this.battleNode.height / 2 - 20;
		return (this.node.y > battleRangeY_max);
	}
});
