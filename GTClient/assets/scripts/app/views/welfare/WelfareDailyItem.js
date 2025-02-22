var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Initializer");
var r = require("Utils");
//let shaderUtils = require("ShaderUtils");

cc.Class({
    extends: i,

    properties: {
        nLine: cc.Node,
        nCurCheck: [cc.Node],
        lblDay: cc.Label,
        itemSlot2: n,
        btn: cc.Button,
        lbDouble: cc.Label,
        spDouble: cc.Sprite,
        spDay: cc.Sprite,
        arrNChecked: [cc.Node],
        nUncheck: cc.Node,
        nCount: cc.Node,
        arrCurHide: [cc.Node],
        checkedColor: cc.Color,
        uncheckColor: cc.Color,
        lbCount2: cc.Label,
    },

    ctor() {},

    showData() {
        var t = this._data;
        if (t) {
            this.nLine.active = t.__index % 4 == 0;
            var e = localcache.getItem(localdb.table_qiandaoReward, t.rwdId);
            this.lblDay.string = t.day + "";
            let bChecked = 1 == t.isQiandao;
            let curCheck = t.day == l.welfareProxy.qiandao.days && !bChecked;
            for(let i = 0, len = this.arrCurHide.length; i < len; i++) {
                this.arrCurHide[i].active = !curCheck;
            }
            for(let i = 0, len = this.arrNChecked.length; i < len; i++) {
                this.arrNChecked[i].active = bChecked;
            }
            this.itemSlot2.data = e.qiandaoRwd.length > 0 ? e.qiandaoRwd[0] : null;
            this.lbCount2.string = e.qiandaoRwd.length > 0 ? e.qiandaoRwd[0].count : 0;
            //this.itemSlot2.setGray(0 != t.isQiandao);
            this.btn.interactable = !bChecked;
            for(let i = 0, len = this.nCurCheck.length; i < len; i++) {
                this.nCurCheck[i].active = curCheck;
            }
            this.nUncheck.active = !curCheck && !bChecked;
            this.spDouble.node.active = e.vip > 0;
            this.lbDouble.node.active = e.vip > 0;
            //shaderUtils.shaderUtils.setImageGray(this.spDouble, bChecked);
            //shaderUtils.shaderUtils.setImageGray(this.spDay, bChecked);
            this.lbDouble.string = i18n.t("SEVEN_DAYS_DESC102", { num: e.vip });
            this.nCount.color = bChecked ? this.checkedColor : this.uncheckColor;
        }
    },

    onClickItem() {
        var t = this._data;
        t && !t.isQiandao && 0 == l.welfareProxy.qiandao.qiandao ? l.welfareProxy.sendQiandao() : r.alertUtil.alert18n("WELFARE_QIANDAO_LIMIT");
    },
});
