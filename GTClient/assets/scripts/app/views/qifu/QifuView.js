var i = require("Initializer");
var n = require("Utils");
var l = require("TimeProxy");
var CarouselMachine = require("CarouselMachine");
var RenderListItem = require("RenderListItem");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTen: cc.RichText,
        lblFree: cc.Label,
        lblCost: cc.Label,
        nodeFree: cc.Node,
        nodeCost: cc.Node,
        lblTenCost: cc.Label,
        selectedView: cc.Node,
        lblbtntentitle:cc.Label,
        lblbtnonetitle:cc.Label,
        lbOnePayTitle: cc.Label,
        carouselMachine:CarouselMachine,
        nodeCircle:cc.Node,
        listItem:[RenderListItem],
    },
    ctor() {
        this.cost = 0;
        this.isFirst = !0;
        this.isPlaying = !1;
        this.count = 0;
        this.chooseIdx = -1;
    },
    onLoad() {
        facade.subscribe(i.playerProxy.PLAYER_QIFU_UPDATE, this.qifuUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        facade.subscribe("WEIYANGGE_MOVELEFT",this.onClickLeft,this);
        facade.subscribe("WEIYANGGE_MOVERIGHT",this.onClickRight,this);
        facade.subscribe("CAROUSE_SELECTEDFUNCTION", this.updateSelectItem, this);
        this.nodeCircle.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.nodeCircle.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        this.nodeCircle.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.nodeCircle.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);
        this.selectedView.active = false;
        this.qifuUpdate();
        this.jyType = -1;
        this.selectedView.active = true;
    },
    qifuUpdate() {
        // this.jyType =  null;
        var t = localcache.getItem(localdb.table_officer, i.playerProxy.userData.level);
        this.count = i.playerProxy.qifuData.lastTime >= n.timeUtil.getTodaySecond(0) ? t.pray - i.playerProxy.qifuData.free: t.pray;
        this.nodeFree.active = this.count > 0;
        this.nodeCost.active = this.count <= 0;
        if (this.count > 0) this.lblFree.string = i18n.t("QIFU_FREE_COUNT", {
            num: this.count,
            total: t.pray
        });
        // else {
        //     var e = i.playerProxy.qifuData.buy + 1;
        //     this.cost = e * (1 + Math.floor(e / 10)) * 2;
        //     this.lblCost.string = this.cost + "";
        // }
        let bFree = this.count > 0;
        this.lblbtnonetitle.node.parent.active = bFree;
        this.lbOnePayTitle.node.parent.parent.active = !bFree;
        var e = i.playerProxy.qifuData.buy + 1;
        this.cost = e * (1 + Math.floor(e / 10)) * 2;
        this.lblCost.string = this.cost + "";
        var tencost = this.getTenCost();
        this.lblTenCost.string = tencost;

        //COMMON_XIAOHAO
        this.lblbtntentitle.string = i18n.t("COMMON_XIAOHAO", {
            value: tencost
        });
        this.lbOnePayTitle.string = i18n.t("COMMON_XIAOHAO", {
            value: this.cost
        });
        var o = n.utils.getParamInt("qifu_ten_count");
        o - i.playerProxy.qifuData.ten == 0 ? (this.lblTen.string = i18n.t("QIFU_CUR_FREE")) : (this.lblTen.string = i18n.t("QIFU_TEN_TXT", {
            num: o - i.playerProxy.qifuData.ten
        }));
        if (this.isFirst) this.isFirst = !1;
        else {
            // if (0 == i.playerProxy.qifuData.isten) {

            // } else {

            // }
            this.isPlaying = !1;
        }
        // facade.send("RESET_QIFU_ITEM");
    },

    // 抽十次的消费
    getTenCost () {
        var times = 10 - this.count;
        if (times <= 0) return 0;
        var cost = 0;
        for (var k = 1 ;k <= times; k++) {
            var x = i.playerProxy.qifuData.buy + k;
            var y = x * (1 + Math.floor(x / 10)) * 2;
            cost += y;
        }
        return cost;
    },


    onItemClick (e, data) {
        if (!data) return;
        var node = e.target;
        var qiFuItem = node.getComponent("qiFuItem");
        if (qiFuItem.index == this.chooseIdx) return;
        let num = this.chooseIdx - qiFuItem.index;
        if (num == -1 || num == 2){
            this.carouselMachine.MoveLeft();
            this.isPlaying = true;
        }
        else if(num == 1 || num == -2){
            this.carouselMachine.MoveRight();
            this.isPlaying = true;
        }
        // facade.send("RESET_QIFU_ITEM");
        // if (data && qiFuItem) {
        //     var type = parseInt(data);
        //     if (this.jyType === type) {
        //         qiFuItem.setUnSelectedState();
        //         this.jyType = null;
        //         this.onHideSelectedView();
        //     } else {
        //         this.jyType = type;
        //         qiFuItem.setSelectedState();
        //         this.showSelectedView();
        //     }

        // }


    },

    showSelectedView () {
        this.selectedView.active = true;
    },

    onHideSelectedView () {
        this.selectedView.active = false;
        facade.send("RESET_QIFU_ITEM");
    },

    // 祈福一次 e为“1”， 祈福十次e为“10”
    onClickQifu(t, e) {
        if (this.isPlaying || !e || !this.jyType) return;
        var o = this;
        var times = parseInt(e);
        var cost = 0;
        if (times === 1) {
            cost = this.cost;
            if (this.count > 0) {
                i.playerProxy.sendQifu(this.jyType);
                this.isPlaying = !0;
                // this.onHideSelectedView();
                return;
            }
        } else {
            if (this.count >= 10) {
                i.playerProxy.sendQifuTen(this.jyType);
                this.isPlaying = !0;
                // this.onHideSelectedView();
                return;
            }
            cost = this.getTenCost();
        }
        n.utils.showConfirmItem(i18n.t("QIFU_COST_TIP", {num: cost, times: times}),
            1,
            i.playerProxy.userData.cash,
            () => {
                if (i.playerProxy.userData.cash < cost) n.alertUtil.alertItemLimit(1);
                else {
                    // TODO 抽10次
                    times === 1 ? i.playerProxy.sendQifu(this.jyType) : i.playerProxy.sendQifuTen(this.jyType);
                    this.isPlaying = !0;
                }
            },
            "QIFU_COST_TIP");
        // this.onHideSelectedView();

        //
        // if (!this.isPlaying) if (this.count <= 0) n.utils.showConfirmItem(i18n.t("QIFU_COST_TIP", {
        //     num: this.cost
        // }), 1, i.playerProxy.userData.cash,
        // function() {
        //     if (i.playerProxy.userData.cash < o.cost) n.alertUtil.alertItemLimit(1);
        //     else {
        //         i.playerProxy.sendQifu(parseInt(e));
        //         o.isPlaying = !0;
        //     }
        // },
        // "QIFU_COST_TIP");
        // else {
        //     i.playerProxy.sendQifu(parseInt(e));
        //     this.isPlaying = !0;
        // }
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickRecharge() {
        l.funUtils.openView(l.funUtils.recharge.id);
    },

    onClickLeft(){
        if (this.isPlaying) return;
        this.carouselMachine.MoveLeft();
        this.isPlaying = true;
    },

    onClickRight(){
        if (this.isPlaying) return;
        this.carouselMachine.MoveRight();
        this.isPlaying = true;
    },

    updateSelectItem(data){
        this.isPlaying = false;
        this.chooseIdx = data.index + 0;
        for (let ii = 0; ii < this.listItem.length;ii++){
            let item = this.listItem[ii];
            item.data = {idx:data.index}
            if (item.index ==  data.index){
                let btn = item.node.getComponent(cc.Button);
                let events = btn.clickEvents;
                this.jyType = Number(events[0].customEventData)
            }
        }
        // this.jyType = null;
        // this.onHideSelectedView();
    },

    onDragStart: function(event) {
        this.isMoving = false;
        this.startDragPosX = event.currentTouch._point.x;
    },

    onDrag: function(event) {
        this.isMoving = true;
    },

    onDragEnd: function(event) {
        if(!this.isMoving) {
            return;
        }
        this.isMoving = false;
        let endDragPosX = event.currentTouch._point.x;
        if (Math.abs(endDragPosX - this.startDragPosX) > 100){
            (endDragPosX > this.startDragPosX) ? this.onClickRight() : this.onClickLeft();
        }
        this.startDragPosX = 0;
    },
});
