var i = require("RenderListItem");
var n = require("RedDot");
var l = require("UrlLoad");
var r = require("TimeProxy");
var a = require("Initializer");
var s = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        reddot: n,
        btnimg: l,
        //btnEff: l,
    },
    ctor() {},

    onLoad () {
        facade.subscribe(a.thirtyDaysProxy.THIRTY_DAY_DATA_UPDATE, this.updateShow, this);
    },
    onClickItem() {
        var t = this._data;
        if (t) {
            var e = a.limitActivityProxy.getActivityData(t.id);
            if(e && e.id == a.limitActivityProxy.SNOWMAN_ID && e.hdtype && 2 == e.hdtype)
            {
                r.funUtils.openView(r.funUtils.spring.id)
            }else if(e && e.id == a.limitActivityProxy.GAO_DIAN_ID)
            {
                r.funUtils.openView(r.funUtils.gaodian.id)
            }else{
                r.funUtils.openView(t.funitem.id)
            }
            // e && e.id == a.limitActivityProxy.SNOWMAN_ID && e.hdtype && 2 == e.hdtype ? r.funUtils.openView(r.funUtils.spring.id) : e && e.id == a.limitActivityProxy.GAO_DIAN_ID ? r.funUtils.openView(r.funUtils.gaodian.id) : r.funUtils.openView(t.funitem.id);
        }
    },
    updateShow() {
        var t = this._data;
        var isOpen = false;
        t && (isOpen = a.limitActivityProxy.isHaveTypeActive(t.id) && r.funUtils.isOpenFun(t.funitem));
        if (this._data.id === a.limitActivityProxy.THIRTYDAYS_ID) {
            // 45天登录活动
            this.node.active = isOpen && !a.thirtyDaysProxy.checkIsFinished();
        } else {
            this.node.active = isOpen;
        }
    },
    showData() {
        var t = this._data;
        if (t) {
            this.reddot.addBinding(t.binding);
            this.updateShow();
            var e = a.limitActivityProxy.getActivityData(t.id);
            var o = e && e.id == a.limitActivityProxy.SNOWMAN_ID && e.hdtype && 2 == e.hdtype;
            var i = t.url.split("|");
            this.btnimg.node.active = 2 != t.isEff || o;
            // this.btnEff.node.active = 2 == t.isEff;
            // if(2 == t.isEff){
            //     o ? (this.btnimg.url = s.uiHelps.getActivityBtn(i[1])) : (this.btnEff.url = s.uiHelps.getActivityUrl(i[0]));
            // }else if(3 == t.isEff){//图片为动画
            //     this.btnEff.url = s.uiHelps.getActivityUrl(i[0]);
            //     this.btnimg.node.active = false;
            //     this.btnEff.node.active = true;
            // }else{
            //     this.btnimg.url = s.uiHelps.getActivityBtn(i[0])
            // }
        }
    },
});
