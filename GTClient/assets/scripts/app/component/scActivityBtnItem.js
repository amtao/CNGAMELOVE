let redDot = require("RedDot");
let urlLoad = require("UrlLoad");
let timeProxy = require("TimeProxy");
let initializer = require("Initializer");
let uiUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        btnSelf: cc.Button,
        urlIcon: urlLoad,
        redDotRotation: redDot,
        redDotFlower: redDot,
        nTitleDi: cc.Node,
        lbName: cc.Label,
    },

    showData: function() { //banner_title和iconopen都必须配置
        if(this.data) {
            let data = this.data;
            let bindings = data.binding ? data.binding : [];
            this.redDotRotation.addBinding(this.data.tab == 5 ? [] : bindings, true);
            this.redDotFlower.addBinding(bindings, true);
            this.lbName.string = data.funitem.title;
            this.btnSelf.clickEvents[0].customEventData = data.funitem.id;
            this.urlIcon.url = uiUtils.uiHelps.getActIcon(data.url);
            this.nTitleDi.active = this.data.type == 2;
        }
    },

    updateShow: function() {
        if(this.data) {
            let data = this.data;
            if(null != data.funitem) {
                this.node.active = timeProxy.funUtils.isOpenFun(timeProxy.funUtils[data.funitem.name])
                 && initializer.limitActivityProxy.isHaveIdActive(data.id);
            } else {
                this.node.active = initializer.limitActivityProxy.isHaveIdActive(data.id);
            }
        } else {
            this.node.active = false;
        }
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    // update (dt) {},
});
