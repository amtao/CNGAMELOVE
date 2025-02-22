var userClothe = require("UserClothe");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends:userClothe,
    properties: {

    },
    onLoad() {
        var t = n.clothePveProxy.pvpClothe;
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
            var e = this;
            if (
                n.clothePveProxy.pvpClothe &&
                0 != n.clothePveProxy.pvpClothe.body
            )
                l.utils.showConfirm(
                    i18n.t("CLOTHE_PVP_CHANGE_CLOTHE"),
                    function() {
                        n.clothePveProxy.sendPvpEnter(
                            t.head,
                            t.body,
                            t.ear,
                            t.background,
                            t.effect,
                            t.animal
                        );
                        e.onClickClost();
                    }
                );
            else {
                n.clothePveProxy.sendPvpEnter(
                    t.head,
                    t.body,
                    t.ear,
                    t.background,
                    t.effect,
                    t.animal
                );
                this.onClickClost();
            }
        }
    },
    onClickClost() {
        l.utils.closeView(this, !0);
    },

});