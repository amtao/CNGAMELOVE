var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        lblRwd: cc.Label,
        winSpine: sp.Skeleton,
    },
    ctor() {
        this.canClose = !1;
        this._curIndex = -1;
    },

    onLoad() {
        let self = this;

        //动画监听
        this.winSpine.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            if (aniName == 'shengli') {
                if(null != self.winSpine) {
                    self.winSpine.setAnimation(0, 'shengli_idle', true);
                }
                if(null != self.list) {
                    self.list.data = [{
                        index: 0
                    },
                    {
                        index: 1
                    },
                    {
                        index: 2
                    },
                    {
                        index: 3
                    },
                    {
                        index: 4
                    },
                    {
                        index: 5
                    }];
                }
            }  
        });

        // this.winSpine.setEventListener((trackEntry, event) => {
        //     if(event.data.name === "card") {
        //         self.list.data = [{
        //             index: 0
        //         },
        //         {
        //             index: 1
        //         },
        //         {
        //             index: 2
        //         },
        //         {
        //             index: 3
        //         },
        //         {
        //             index: 4
        //         },
        //         {
        //             index: 5
        //         }];
        //     }
        // });
        
        facade.subscribe(l.dalishiProxy.UPDATE_DALISHI_WIN, this.onUpdateWin, this);
        this.lblRwd.string = "";
    },

    testWin() {
        l.dalishiProxy.win = {};
        l.dalishiProxy.win.rwd = {};
        l.dalishiProxy.win.rwd.items = [];
        l.dalishiProxy.win.rwd.items.push({
            id: l.dalishiProxy.info.qhid,
            count: 12,
            kind: 5
        });
        this._curIndex = Math.floor(6 * Math.random());
        this.onUpdateWin();
    },

    onUpdateWin() {
        var t = [];
        this.canClose = !0;
        for (var e = 0; e < 6; e++) e == this._curIndex ? t.push(l.dalishiProxy.win.rwd.items[0]) : t.push({
            index: e
        });
        var o = l.dalishiProxy.win.rwd.items[0];
        this.lblRwd.string = i18n.t("DALISI_RWD_TIP", {
            n: l.playerProxy.getKindIdName(o.kind, o.id),
            d: o.count
        });
        this.list.data = t;
        this.scheduleOnce(this.onShowEnd, 1.5);
    },

    onShowEnd() {
        var t = l.dalishiProxy.getAwardReward(l.dalishiProxy.win.rwd.items[0], this._curIndex);
        this.list.data = t;
    },

    onClickItem(t, e) {
        var o = e.data;
        if (null == o.id && -1 == this._curIndex) {
            this._curIndex = o.index;
            //this.testWin();
            l.dalishiProxy.sendRwd();
        }
    },
    
    onClickClost() {
        if (this.canClose) {
            n.utils.closeView(this);
            l.dalishiProxy.openShop();
        }
    },
});
