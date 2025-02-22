var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
var a = require("CreateProxy");
var s = require("UrlLoad");
var c = require("ApiUtils");
var _ = require("Config");

cc.Class({
    extends: cc.Component,
    properties: {
        editName: cc.EditBox,
        list: i,
        animation: cc.Animation,
        spine: s,
        face: cc.Sprite,
        faces: [cc.SpriteFrame],
        lblName: cc.Label,
        nodeFace: cc.Node,
    },

    ctor() {
        this.femaleData = new Array();
        //this.maleData = new Array();
        this.count = 3;
        this.faceStr = ["zhengchang", "beishang", "jianyi", "kaixin"];
        this.soundStr = ["", "001", "002", "003"];
    },

    onLoad() {
        var t = this;
        // this.editName.placeholder = i18n.t("COMMON_INPUT_TXT");
        l.utils.setCanvas();
        l.utils.clearLayer();
        l.utils.findTopLayer();
        l.utils.setWaitUI();
        for (var e = this,
        o = localcache.getItem(localdb.table_officer, 0), i = 1; i <= this.count; i++) {
            this.femaleData.push(new a.CreateData(2, i, o.shizhuang));
            //this.maleData.push(new a.CreateData(1, i, o.shizhuang));
        }
        //for (i = 0; _.Config.addShowCreateHeadId && i < _.Config.addShowCreateHeadId.length; i++) this.femaleData.push(new a.CreateData(2, _.Config.addShowCreateHeadId[i], o.shizhuang));
        this.list.selectHandle = function(o) {
            if (null != o) {
                //e.spine.setCreateClothes(o.sex, o.job, 0);
                n.playerProxy.loadPlayerSpinePrefab(e.spine,{creatorjob:o.job});
                // e.spine.setClothes(o.sex, o.job, 0, clothes);
                //e.spine.actionString(t.faceStr[0]);
            } else {
                e.list.selectIndex = 0;
                l.alertUtil.alert18n("CREATE_UNOPEN");
            }
        };
        this.onClickRandom();
        this.onClickSex(null, 2);
        facade.subscribe(n.createProxy.CREATE_RANDOM_NAME, this.update_Name, this);
        facade.subscribe("USER_DATA_OVER", this.onRoleData, this);
        l.utils.showEffect(this, 0);
        //this.spine.actionString(this.faceStr[0]);
        cc.sys.isMobile ? this.node.parent.on(cc.Node.EventType.TOUCH_START, this.onClick, this, !0) : this.node.parent.on(cc.Node.EventType.MOUSE_DOWN, this.onClick, this, !0);
        //cc.sys.isMobile ? this.node.parent.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this, !0) : this.node.parent.on(cc.Node.EventType.MOUSE_MOVE, this.onDrag, this, !0); 
        //cc.sys.isMobile ? (this.node.parent.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this, !0) &&
        //this.node.parent.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this, !0)) : this.node.parent.on(cc.Node.EventType.MOUSE_UP, this.onDragEnd, this, !0); 
        //JSHS 2020-1-20 加打点
        n.playerProxy.sendFlag(n.playerProxy.ENTER_CREATE_OVER);
    },

    onClickSex(t, e) {
        var o = parseInt(e);
        //this.face.node.active = 2 == o;
        this.list.data = this.femaleData;
        // var i = this.list.data.length,
        // n = this.list.selectIndex;
        // n = -1 == n ? Math.floor(Math.random() * i) : n > i ? i - 1 : n;
        let defaultChoose = l.utils.getParamInt("chuangjuelian")
        this.list.selectIndex = defaultChoose - 1;
    },

    onClickFace() {
        // 屏蔽更换表情
        // if (this.face.node.active) {
        //     for (var t = Math.floor(Math.random() * this.faces.length), e = 0; e < 10 && this.face.spriteFrame == this.faces[t]; e++) t = Math.floor(Math.random() * this.faces.length);
        //     var o = this.soundStr[t];
        //     l.stringUtil.isBlank(o) || l.audioManager.playSound("m" + o, !0, !0);
        //     this.face.spriteFrame = this.faces[t];
        //     this.spine.actionString(this.faceStr[t]);
        // } else this.spine.actionString(this.faceStr[0]);
    },

    onClickRandom() {
        n.createProxy.sendRandomName();
    },

    onClickCreate() {
        if (l.stringUtil.isBlank(this.editName.string)) l.alertUtil.alert(i18n.t("CREATE_IS_LIMIT"));
        else {
            var t = this.list.selectData;
            n.createProxy.sendCreate(t.sex, t.job, this.editName.string);
        }
    },

    onRoleData() {
        if (!l.stringUtil.isBlank(n.playerProxy.userData.name)) {
			//JSHS 2020-1-20 加打点
            n.playerProxy.sendFlag(n.playerProxy.CREATE_SUCCESS);
            // cc.director.loadScene("PreloadScene");
            let uuid = cc.director.getScene().uuid;
            cc.director.loadScene("PreloadScene", (error, scene)=>{
                CC_DEBUG && console.log("加载 PreloadScene：", scene);
                MemoryMgr.saveAssets(scene);
                MemoryMgr.releaseAsset({uuid:uuid});
            });
            c.apiUtils.callSMethod4("Creat_Role");
            c.apiUtils.createSuccess();
            if(_.Config.login_by_sdk)
            {
                c.apiUtils.callSMethod3("finshCreateRole");
            }
            var recordStep = new proto_cs.user.recordSteps();
            recordStep.stepId = 0;
            JsonHttp.send(recordStep, function() {
            });
        }
    },

    onTestChange() {
        this.lblName.string = this.editName.string;
    },

    onClickClost() {},

    update_Name() {
        this.lblName.string = this.editName.string = n.createProxy.randomName;
    },

    onClick(t) {
        let self = this;
        r.clickEffectUtils.showEffect(t, (node, particle) => {
            self.clickEff = node;
            //self.clickEffParticle = particle;
        });
        //this.startTime = cc.sys.now();
        l.audioManager.playClickSound();
    },

    // onDrag: function(event) {
    //     if(null != this.clickEffParticle) {
    //         !this.clickEffParticle.active && (this.clickEffParticle.active = true);
    //         this.clickEffParticle.x += event.getDeltaX();
    //         this.clickEffParticle.y += event.getDeltaY();
    //     }
    // },

    // onDragEnd: function() {
    //     let self = this;
    //     let finishFunc = () => {
    //         if(null != self.clickEff) {
    //             self.clickEff.active = !1;
    //             self.clickEff = null;
    //         }
    //         if(null != self.clickEffParticle) {
    //             self.clickEffParticle.active = !1;
    //             self.clickEffParticle = null;
    //         }
    //     }
    //     if(null != this.startTime) {
    //         let now = cc.sys.now();
    //         let time = now - this.startTime;
    //         if(time >= 1000) {
    //             finishFunc();
    //         } else if(null != self.clickEff) {
    //             if(null != self.clickEffParticle) {
    //                 self.clickEffParticle.active = !1;
    //                 self.clickEffParticle = null;
    //             }
    //             let comp = self.clickEff.getComponent(cc.Component);
    //             comp.unscheduleAllCallbacks();
    //             comp && comp.scheduleOnce(finishFunc, (1000 - time) / 1000);
    //         } else {
    //             finishFunc();
    //         }
    //         this.startTime = null;
    //     } else {
    //         finishFunc();
    //     } 
    // },

    update(dt){
        MemoryMgr.onUpdateCheckRemove(dt);
    },

    onDestroy() {
        l.utils.clearLayer();
    },
});
