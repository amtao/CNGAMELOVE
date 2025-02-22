let UrlLoad = require("UrlLoad");
let MoonBullet = require("MoonBullet");
let MoonBoss = require("MoonBoss");
let utils = require("Utils");
import { EItemType } from 'GameDefine';
let initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        leftTimelbl:cc.Label,
        moveTouchNode:cc.Node,//
        line: cc.Node,//分数分割线
        moonBoss:MoonBoss,//月亮
        moonBullet:MoonBullet,//月亮子弹
        countDown:UrlLoad,//倒计时
        moveWorldNode:cc.Node,//移动世界节点
        fireSpine: sp.Skeleton,
        //testNode: cc.Node,
    },
    onLoad(){
        this.moonBullet.node.active = false;//JSHS 2020/7/3 默认隐藏 避免预制误操作
        this.ALLTIME = 15;//总共15秒
        this.addTouchMoveEvent();
        let self = this;
        this.fireSpine.setCompleteListener(function(){
            self.fireSpine.node.active = false;
        })
        this.fireSpine.node.active = false;
        initializer.moonBattleProxy.shotOver();
        //this.testNode.active = false;
    },
    //开启战斗
    startBattle(){
    },
    addTouchMoveEvent(){
        this.moveTouchNode.on(cc.Node.EventType.TOUCH_START,(event)=>{
        }, this);
        this.moveTouchNode.on(cc.Node.EventType.TOUCH_MOVE,(event)=>{
        }, this);
        this.moveTouchNode.on(cc.Node.EventType.TOUCH_END,(event)=>{
            let touchPos = this.moveWorldNode.convertToNodeSpaceAR(event.touch.getLocation());
            this.shootBullet(touchPos);
        }, this);
        this.moveTouchNode.on(cc.Node.EventType.TOUCH_CANCEL,(event)=>{
        }, this);
    },

    shootBullet(touchPos){
        if (initializer.moonBattleProxy.isShotting()) {
            // utils.alertUtil.alert(i18n.t("MOON_BATTLE_ATK_FREQUENTLY"));
            //this.testNode.active = true;
            return;
        }
        //this.testNode.active = false;
        if (!initializer.moonBattleProxy.isOpenActivity()) {
            utils.alertUtil.alert(i18n.t("ACTHD_OVERDUE"));
            return;
        }
        let itemId = EItemType.MoonBoom;
        var count = initializer.bagProxy.getItemCount(itemId);
        if (count <= 0) {
            utils.alertUtil.alertItemLimit(itemId);
            return;
        }
        initializer.moonBattleProxy.shot();
        if(touchPos){
            let bullet = null;
            for(let i = 0,len = this.moveWorldNode.childrenCount;i < len;i++){
                let childNode = this.moveWorldNode.children[i];
                if(childNode.active == false){
                    let bulletScript = childNode.getComponent('MoonBullet');
                    if(bulletScript){
                        bullet = bulletScript;
                        break;
                    }
                }
            }
            if(!bullet){
                let newBulletNode = cc.instantiate(this.moonBullet.node);
                if(newBulletNode){
                    newBulletNode.parent = this.moveWorldNode;
                    newBulletNode.active = false;
                    let bulletScript = newBulletNode.getComponent('MoonBullet');
                    if(bulletScript){
                        bullet = bulletScript;
                    }
                }
            }
            if(bullet){
                bullet.startMove(touchPos);
                facade.send("MOON_BATTLE_FIRE", {x:touchPos.x});
            }
        }
        utils.audioManager.playSound("moonbattle_fire", !0, !0);
    },
    update(dt){
        this.onCollide();
    },
    //逻辑碰撞
    onCollide: function(){
        for(let i = 0,len = this.moveWorldNode.childrenCount;i < len;i++){
            let childNode = this.moveWorldNode.children[i];
            if(childNode.active){
                let bulletScript = childNode.getComponent('MoonBullet');
                if(bulletScript){
                    this.checkCollide(bulletScript);
                }
            }
        }
    },
    checkCollide(bullet){
        let bulletPos = bullet.node.parent.convertToWorldSpaceAR(bullet.node.position);
        let moonPos = this.moonBoss.node.parent.convertToWorldSpaceAR(this.moonBoss.node.position);
        if(this.isCollide(moonPos,this.moonBoss.collisionSize,bulletPos,bullet.collisionSize)){
            let linePos = this.line.parent.convertToWorldSpaceAR(this.line.position);
            bullet.endMove();
            this.moonBoss.hit();
            let pos = this.fireSpine.node.parent.convertToNodeSpaceAR(cc.v2(bulletPos.x, bulletPos.y + 0.5 * this.line.height))
            this.fireSpine.node.setPosition(pos);
            this.fireSpine.animation = "animation";
            this.fireSpine.node.active = true;
            facade.send("MOON_BATTLE_HIT", {hit: linePos.y > bulletPos.y + 0.5 * this.line.height ? 100 : 200})
        }
    },
    //修正碰撞的X,Y位置偏移量
    isCollide: function(posA,rectA,posB,rectB){
        let _newAX_ = posA.x;
        let _newAY_ = posA.y;
        let _newBX_ = posB.x;
        let _newBY_ = posB.y;
        if(Math.abs(_newAX_ - _newBX_) < (rectA.width/2 + rectB.width/2) //横向判断
          &&
          Math.abs(_newAY_ - _newBY_) < (rectA.height/2 + rectB.height/2) //纵向判断
        ){
            return true;
        }
        return false;
    },
    showEndView(isWin){
        //清除子弹
        for(let i = 0,len = this.moveWorldNode.childrenCount;i < len;i++){
            let childNode = this.moveWorldNode.children[i];
            if(childNode.active){
                let bulletScript = childNode.getComponent('MoonBullet');
                if(bulletScript){
                    bulletScript.node.active = false;
                }
            }
        }
    }
});
