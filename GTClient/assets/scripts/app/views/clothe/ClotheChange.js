var i = require("UserClothe");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: i,
    properties: {

    },
    ctor() {
        
    },
    onLoad() {
        this._curGate = this.node.openParam;
        var t = n.clothePveProxy.getScore(this._curGate.id);
        null == t &&
            null != (t = n.timeProxy.getLoacalValue("CLOTHE_PVE_SAVE")) &&
            (t = JSON.parse(t + ""));
        this.updateCurClothe(null == t ? n.playerProxy.userClothe : t);
        this.setRoleShow();
        this.onClickBack();
        facade.subscribe(
            n.playerProxy.PLAYER_CLOTH_UPDATE,
            this.updateShow,
            this
        );
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        this.nodeInfo.active = !1;
    },
    onClickOver() {
        if (this.checkBuyItem()) {
            var t = {};
            t.ear = this._ear;
            t.body = this._body;
            t.animal = this._animal;
            t.background = this._bg;
            t.effect = this._eff;
            t.head = this._head;
            n.timeProxy.saveLocalValue(
                "CLOTHE_PVE_SAVE",
                JSON.stringify(t)
            );
            n.clothePveProxy.sendFight(
                this._curGate.id,
                t.head,
                t.body,
                t.ear,
                t.background,
                t.effect,
                t.animal
            );
            this.onClickClost();
        }
    },
    onClickReferr() {
        n.clothePveProxy.sendReferr(this._curGate.id);
    },
    onClickClost() {
        l.utils.closeView(this, !0);
    },
});

