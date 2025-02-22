let Initializer = require("Initializer");
let Utils = require("Utils");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");

const ANIM_EMPTY = 0;
const ANIM_IDEL = 1 << 1;
const ANIM_HIT = 1 << 2;
const ANIM_DEATH = 1 << 3;
const ANIM_WEEK = 1 << 10;
const ANIM_IDEL_1 = 1 << 11;
const ANIM_WALK_LEFT_1 = 1 << 4;
const ANIM_WALK_RIGHT_1 = 1 << 5;
const ANIM_WALK_LEFT_3 = 1 << 6;
const ANIM_WALK_RIGHT_3 = 1 << 7;
const ANIM_WALK_LEFT_2 = 1 << 8;
const ANIM_WALK_RIGHT_2 = 1 << 9;
const ANIM_WALK_RIGHT = ANIM_WALK_RIGHT_1 + ANIM_WALK_RIGHT_2 + ANIM_WALK_RIGHT_3;
const ANIM_WALK_LEFT = ANIM_WALK_LEFT_1 + ANIM_WALK_LEFT_2 + ANIM_WALK_LEFT_3;
const ANIM_WALK = ANIM_WALK_RIGHT + ANIM_WALK_LEFT;

const ANIM_TO_NAME = cc.Enum({
    "idle4": ANIM_IDEL,
    "yun": ANIM_HIT,
    "idle1_1": ANIM_WALK_LEFT_1,
    "idle1": ANIM_WALK_RIGHT_1,
    "idle2_1": ANIM_WALK_LEFT_2,//嘲讽
    "idle2": ANIM_WALK_RIGHT_2,
    "idle3_1": ANIM_WALK_LEFT_3,
    "idle3": ANIM_WALK_RIGHT_3,
    "die": ANIM_DEATH,
    "huanxing": ANIM_WEEK,
    "idle1_idle": ANIM_IDEL_1,
})

const LOOK_CD_MIN = 5;//最小切换时间
const LOOK_CD_MAX = 11;//最大切换时间 要多加1秒

const SPEED_SLOW = 200;//主界面运动速度
const SPEED_NOR = 250;//正常运动速度
const SPEED_UP_1 = 350;//50%运动速度
const SPEED_UP_2 = 450;//20%血量运动速度

cc.Class({
    extends: cc.Component,
    properties: {
        collisionSize:cc.Size,
        moonAni:cc.Node,//月亮动画节点
        battleNode:cc.Node,//可移动区域节点
        aniSpine: sp.Skeleton,
    },
    onLoad () {
        facade.subscribe("MOON_BATTLE_FIRE_MISS", this.onMissBullet, this);
        facade.subscribe("MOON_BATTLE_FIRE", this.onFireBullet, this);
        
        this.lookCD = this.randomLookCD();
        this.canMove = false;
        this.moveSpeed = SPEED_SLOW;//速度大小
		this._vY = 0;
        this._vX = 0;
        this.resetLoolBulletState();

        let self = this;
        this.aniSpine.setEventListener(function(trackEntry, event){
            let eventName = event.data.name;
            CC_DEBUG && console.log("spine animation over: ", eventName)
            switch(eventName){
                case "huanxing":
                    self.playAnim(ANIM_IDEL_1)
                    break;
                case "yun":
                    self.startLook();
                    break;
                case "die":
                    self.aniSpine.node.active = false;
                    self.dieHandle && self.dieHandle();
                    break;
            }
        })
    },
    endMove(){
        this.canMove = false;
        this.scheduleOnce(()=>{
            this.canMove = true;
        },3);
    },
    setMoonRandAngle(){
        let randAngel = Utils.utils.randomNum(-180,180);
        if(randAngel%90 == 0){
            randAngel = 67;
        }
        this.setAngle(randAngel);
    },
    startMove(){
        this.dieHandle = null;
        this.node.x = -6;
        this.node.y = 298;
        this.canMove = true;
        this.setMoonRandAngle();
        this.startLook();
    },
    stopMove(isOpen){
        this.node.x = -6;
        this.node.y = 298;
        this.canMove = false;
        this.playAnim(isOpen ? ANIM_WEEK : ANIM_IDEL);
    },
    stopLook(){
        this.lookCD = Number.MAX_SAFE_INTEGER;
    },
    startLook(){
        this.lookCD = 0;
    },
    convertAngleIn360(angle){
        let ret = angle;
        while (ret < 0) {
            ret += 360;
        }
        while (ret > 360) {
            ret -= 360;
        }
        return ret;
    },

    randomLookCD(){
        return Utils.utils.randomNum(LOOK_CD_MIN, LOOK_CD_MAX)
    },

    onMissBullet(){
        this.lookCD = this.randomLookCD();
        this.initLookFace(2);
        this.resetLoolBulletState();
    },

    onFireBullet(data){
        this.isLookBullet = true;
        this.changeEarWithX = data.x;
    },

    resetLoolBulletState(){
        this.isLookBullet = false;//月亮是否看向 子弹
        this.changeEarWithX = 0;//月亮 更换 眼睛 方向的 x坐标
    },

    initLookFace(pos){
        let state = ANIM_WALK_LEFT_1;
        if (this.isLookBullet) {
            if (this.node.x < this.changeEarWithX) {
                this.look(state << (2 * pos + 1))
            }else{
                this.look(state << (2 * pos))
            }
        }else{
            if (this._vX < 0) {
                this.look(state << (2 * pos))
            }else{
                this.look(state << (2 * pos + 1))
            }
        }
    },

    lookRight(){
        if ((this.state & ANIM_WALK_LEFT) > 0) {
            this.look(this.state << 1)
        }
    },

    lookLeft(){
        if ((this.state & ANIM_WALK_RIGHT) > 0) {
            this.look(this.state >> 1)
        }
    },

    update(dt){
        if(this.canMove){
            this.lookCD -= dt;
            this.node.x += this._vX * dt;
            this.node.y += this._vY * dt;

            if (this.lookCD <= 0) {
                this.lookCD = this.randomLookCD();
                let pos = Math.floor(2 * Math.random());
                this.initLookFace(pos);
            }
            
            if ((this.state & ANIM_WALK) > 0) {
                if (this.isLookBullet) {
                    if (this.node.x < this.changeEarWithX) {
                        this.lookRight();
                    }else{
                        this.lookLeft();
                    }
                }else{
                    if (this._vX < 0) {
                        this.lookLeft();
                    }else{
                        this.lookRight();
                    }
                }
            }

            let [outScreen, bounceX, bounceY] = this.checkOutScreen();
			if (outScreen) {// 进行反弹
                if (bounceX < 0) {
                    // let nowAngle = this.convertAngleIn360(-this.getAngle());
                    // let angleGap = Math.floor(nowAngle / 90.0)*90;
                    // this.setAngle(Utils.utils.randomNum(angleGap+20,angleGap+70));
                    this.setAngle(-this.getAngle());
                }
                if (bounceY < 0) {//尽量往两测弹
                    // let nowAngle = this.convertAngleIn360(180 - this.getAngle());
                    // if(0 < nowAngle && nowAngle < 90){//向上
                    //     this.setAngle(Utils.utils.randomNum(45,85));
                    // }else if(90 <= nowAngle && nowAngle < 180){//向下
                    //     this.setAngle(Utils.utils.randomNum(95,135));
                    // }else if(180 <= nowAngle && nowAngle < 270){//向下
                    //     this.setAngle(Utils.utils.randomNum(225,265));
                    // }else{//向上
                    //     this.setAngle(Utils.utils.randomNum(275,315));
                    // }
                    this.setAngle(180 - this.getAngle());
                }
			}
        }
    },
    getAngle(){
		return this.node.angle;
	},
	//设置角度-角度变了之后速度向量也得变
	setAngle(ang){
		this.node.angle = ang;
        this.adjustVelocity();
        this.moonAni.angle = -ang;
    },
    angle2Radian(){
        return this.node.angle * 0.017453292519943;    // Math.PI / 180
    },
    //调整速度分量
    adjustVelocity() {
		let angle = this.angle2Radian();
		this._vX = Math.sin(angle) * this.moveSpeed;
		this._vY = Math.cos(angle) * this.moveSpeed;
    },
    //判断是否出界
	checkOutScreen(){
        let battleRangeX_min = (-this.battleNode.width / 2) + 20;
        let battleRangeX_max = (this.battleNode.width / 2 - 20);
        let battleRangeY_min = -this.battleNode.height / 2 + 20;
        let battleRangeY_max = this.battleNode.height / 2 - 20;
		let [ret, bx, by] = [0, 1, 1];
		if (this.node.x < battleRangeX_min || this.node.x > battleRangeX_max) {
			ret = 1;
			if (this.node.x < battleRangeX_min && this._vX < 0) {//速度反向（用于反弹）
				bx = -1;
			}
			if (this.node.x > battleRangeX_max && this._vX > 0) {// 速度反向（用于反弹）
				bx = -1;
			}
		}
		let posYOut = this.node.y > battleRangeY_max;
		if (this.node.y < battleRangeY_min || posYOut) {
			ret = 1;
			if (this.node.y < battleRangeY_min && this._vY < 0) {
				by = -1;
			}
			if (posYOut && this._vY > 0) {
				by = -1;
			}
		}
		return [ret, bx, by];
    },

    hit(){
        this.setMoonRandAngle();
        this.stopLook();
        this.resetLoolBulletState();
        this.playAnim(ANIM_HIT);
    },

    die(callback){
        this.canMove = false;
        this.stopLook();
        this.playAnim(ANIM_DEATH);
        this.dieHandle = callback;
    },

    week(){
        this.playAnim(ANIM_WEEK);
    },

    look(state){
        this.playAnim(state);
    },

    playAnim(state){
        if (this.state != state && (this.state != ANIM_DEATH || state == ANIM_WEEK || state == ANIM_IDEL)) {
            this.aniSpine.node.active = true;
            this.state = state;
            this.aniSpine.animation = ANIM_TO_NAME[this.state]
        }
    },

    setSpeed(precent){
        if (precent >= 0.5) {
            this.moveSpeed = SPEED_NOR;
        }else if(precent >= 0.2){
            this.moveSpeed = SPEED_UP_1;
        }else{
            this.moveSpeed = SPEED_UP_2;
        }
    }
});
