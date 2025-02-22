var i = require("RenderListItem");
var l = require("ItemSlotUI");
var r = require("Initializer");
var a = require("Utils");
var urlLoad = require("UrlLoad");
var uIUtils = require("UIUtils");
var shaderUtils = require("ShaderUtils");

var JOB_STATE = {
    NONE: 0,
    USING: 1,           // 使用中
    OWN: 2,             // 已购买
    ACTIVITY:3,         // 活动获取
    CAN_BUY:4           // 可购买

};


cc.Class({
    extends: i,
    properties: {
        item:l,
        spine:urlLoad,
        propNode: cc.Node,
        propImg: urlLoad,
        lbPropAdd: cc.Label,
        lbName: cc.Label,
        selectedEffectNode: cc.Node,
        btnBuy: cc.Node,
        btnUse: cc.Node,
        lockNode: cc.Node,
        activityNode: cc.Node,
        activityLabel: cc.Label,
        lbProp: cc.Label,
    },
    ctor() {
        // this.item = null;
        // this.spine = null;

        // 玩家已拥有的脸型

    },

    onLoad () {
        this.jobState = JOB_STATE.NONE;
        facade.subscribe(r.playerProxy.PLAYER_ALLJOBS_UPDATE, this.updateState, this);
        facade.subscribe(r.playerProxy.PLAYER_RESET_JOB, this.updateState, this);
    },

    showData() {
        var t = this._data;
        if (t) {
            this.lbName.string = t.name;
            var e = r.playerProxy.userData;
            if (t.cost) {
                this.item.node.active = true;
                this.item.data = t.cost;
            } else {
                this.item.node.active = false;
            }
            r.playerProxy.loadPlayerSpinePrefab(this.spine,{job:t.id});
            //this.spine.setClothes(e.sex, t.id, e.level, r.playerProxy.userClothe);
            if(t.prop) {
                this.propNode.active = true;
                this.lbPropAdd.string = "+" + t.prop.value;
                this.propImg.url = uIUtils.uiHelps.getLangSp(t.prop["prop"]);
                this.lbProp.string = uIUtils.uiHelps.getPinzhiStr(t.prop["prop"]);
            } else {
                this.propNode.active = false;
            }

            this.updateState();
        }
    },



    updateState () {


        if (this.checkIsOwn()) {
            // 已经拥有
            if (this._data.id == r.playerProxy.userData.job) {
                // 正在使用
                this.setStateUsing();
            } else {
                // 可使用
                this.setStateCanUse();
            }

        } else {
            if (this._data.cost) {
                // 可购买
                this.setStateCanBuy();
            } else {
                // 活动获得
                this.setStateActivity();
            }

        }
        // 使用中


    },

    setStateUsing () {
        this.jobState = JOB_STATE.USING;
        this.refreshUI();
    },

    setStateCanUse () {
        this.jobState = JOB_STATE.OWN;
        this.refreshUI();
    },

    setStateCanBuy () {
        this.jobState = JOB_STATE.CAN_BUY;
        this.refreshUI();
    },


    setStateActivity () {
        this.jobState = JOB_STATE.ACTIVITY;
        this.setActivityLabel();
        this.refreshUI();
    },

    refreshUI () {
        // this.lockNode.active = this.jobState === JOB_STATE.ACTIVITY;
        this.lockNode.active = false;
        this.activityNode.active = this.jobState === JOB_STATE.ACTIVITY;
        this.selectedEffectNode.active = this.jobState === JOB_STATE.USING;
        this.btnBuy.active = this.jobState === JOB_STATE.CAN_BUY;
        this.btnUse.active = this.jobState === JOB_STATE.OWN;
        this.item.node.active = this.jobState === JOB_STATE.CAN_BUY;
        // if (this.jobState === JOB_STATE.ACTIVITY) {
        //     shaderUtils.shaderUtils.setNodeGray(this.node.getChildByName("item"));
        //     this.lbPropAdd.node.color = a.utils.GRAY;
        //     this.lbName.node.color = a.utils.GRAY;
        // } else {
        //     shaderUtils.shaderUtils.clearNodeShader(this.node.getChildByName("item"));
        //     this.lbPropAdd.node.color = a.utils.BLACK_RED;
        //     this.lbName.node.color = a.utils.BLACK_RED;
        // }
    },

    setActivityLabel () {
        this.activityLabel.string = this._data.text ? this._data.text : '';
        // switch (this._data.cost) {
        //     case 997:
        //         this.activityLabel.string = i18n.t("USER_CLOTHE_RANK_ACTIVITY");
        //         break;
        //     case 998:
        //         this.activityLabel.string = i18n.t("USER_CLOTHE_GIFT_ACTIVITY");
        //         break;
        //     case 999:
        //         this.activityLabel.string = i18n.t("USER_CLOTHE_FESTIVAL");
        //         break;
        // }
    },

    checkIsOwn () {
        var own = false;
        var allJob = r.playerProxy.getAllJobs();
        allJob.forEach((job) => {
            if (job === this._data.id) own = true;
        });
        return own;
    },

    onClickItem() {
        var t = this._data;
        if (t) {
            if (this.jobState === JOB_STATE.USING) return;
            if (this.jobState === JOB_STATE.CAN_BUY) {
                var e = t.cost.itemid,
                    o = t.cost.count,
                    i = r.bagProxy.getItemCount(e);
                a.utils.showConfirmItem(i18n.t("USER_CLOTHE_COST_CHANGE_FACE", {
                        n: r.playerProxy.getKindIdName(1, e),
                        c: o
                    }), e, i,
                    function() {
                        if (i < o) a.alertUtil.alertItemLimit(e);
                        else {
                            // r.playerProxy.sendResetJob(t.id);
                            r.playerProxy.sendBuyJob(t.id);
                            // facade.send("USER_JOB_CHANGE_CLOST");
                        }
                    },
                    "USER_CLOTHE_COST_CHANGE_FACE");
            } else if (this.jobState === JOB_STATE.OWN) {
                a.utils.showConfirm(i18n.t("USER_CLOTHE_RESET_TIP"), () => {
                    r.playerProxy.sendResetJob(t.id);
                })
            } else {
                a.alertUtil.alert(i18n.t("USER_CLOTHE_GET_BY_ACTIVITY"));
            }

        }
    },
});
