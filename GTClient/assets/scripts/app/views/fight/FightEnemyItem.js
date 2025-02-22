var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("SpineItem");
var a = require("Utils");
cc.Class({
    extends: i,
    properties: {
        action:n,
    },
    ctor() {
        this.nc = null;
        this.sp = null;
        this.aStr = "";
        this.hp = 2;
        this.atkRank = 10;
        this.isCd = !1;
        this.isDead = !1;
        this.target = null;
    },
    showData() {
        var t = this._data;
        if (t) {
            if (null == this.action.loadHandle) {
                this.action.loadHandle = this.onLoadOver;
                this.action.target = this;
            }
            this.action.url = l.uiHelps.getEnemy(t.job);
            this.action.node.scaleX = (t.index >= 1e3 ? -1 : 1) * this.action.node.scaleX;
            this.hp = t.hp;
            this.atkRank = t.atkRank;
        }
    },
    isCanAttack() {
        return ! (this.isCd || this.isDead || null == this.target || this.target.isDead || this.atkRank < cc.pDistance(this.node.position, this.target.node.position));
    },
    onLoadOver() {
        var t = this._data;
        if (t) {
            var e = this.action.node.getComponentInChildren(r);
            if (e) {
                this.sp = e;
                this.sp.spine.node.color = 2 == t.isGray ? cc.Color.WHITE.fromHEX("#D6D6D6") : 1 == t.isGray ? cc.Color.WHITE.fromHEX("#DADADA") : a.utils.WHITE;
                this.idle();
            }
        }
    },
    actionString(t) {
        if (this.aStr != t) {
            this.aStr = t;
            this._data;
            this.nc && this.nc.play(t);
            this.sp;
        }
    },
    hit() {
        this.hp -= 1;
        this.hp <= 0 && !this.isDead && this.dead();
    },
    run() {
        this.isDead || this.actionString("run");
    },
    atk(t) {
        void 0 === t && (t = !0);
        if (!this.isDead) {
            this.isCd = !0;
            var e = this,
            o = 0.5;
            if (this.nc) for (var i = this.nc.getClips(), n = 0; n < i.length; n++) if ("atk" == i[n].name) {
                o = i[n].duration;
                break;
            }
            this.scheduleOnce(function() {
                e.idle();
            },
            o);
            this.scheduleOnce(function() {
                e.isCd = !1;
                e.target && e.target.hit();
            },
            o + 0.5);
            t && this.actionString("atk");
        }
    },
    idle() {
        this.isDead || this.actionString("idle");
    },
    dead() {
        var t = this,
        e = 0.5;
        if (this.nc) for (var o = this.nc.getClips(), i = 0; i < o.length; i++) if ("dead" == o[i].name) {
            e = o[i].duration;
            break;
        }
        if (this.sp) {
            this.sp.node.runAction(cc.fadeTo(0.5, 0));
            this.sp.spine.loop = !1;
        }
        t.isDead = !0;
        t.target = null;
        this.actionString("dead");
        this.scheduleOnce(function() {
            facade.send("FIGHT_ENEMY_DEAD", t._data.index);
        },
        e + 0.5);
    },
});
