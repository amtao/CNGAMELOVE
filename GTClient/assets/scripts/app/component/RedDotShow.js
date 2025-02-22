var redDot = require("RedDot");
var uIUtils = require("UIUtils");

var RedDotShow = cc.Class({
    extends:cc.Component,

    properties: {
        binding:[cc.String],
        context:"",
        lblContext:cc.Label,
    },

    ctor(){
        this._isShow = false;
    },

    onLoad : function () {
        facade.subscribe("RED_DOT", this.updateData, this, false);
        this.node.active = false;
        RedDotShow.addShow(this);
        this.updateData(null);
    },

    onDestroy : function () {
        this.unscheduleAllCallbacks();
        facade.remove(this);
        RedDotShow.removeShow(this);
    },

    updateData : function (name) {
        var arr = this.binding;
        if (name == null || arr.indexOf(name) != -1) {
            var len = arr.length;
            for (var i = 0; i < len; i++) {
                if (redDot._MAP[arr[i].toString()]) {
                    this._isShow = true;
                    RedDotShow.showNext();
                    return;
                }
            }
            this.playNextShow();
            this._isShow = false;
        }
    },

    playShow : function () {
        RedDotShow._isShow = true;
        this.node.active = true;
        this.lblContext.string = "";
        uIUtils.uiUtils.showText(this.lblContext, i18n.t(this.context));
        this.scheduleOnce(this.playNextShow, 3);
    },

    playNextShow : function () {
        RedDotShow._isShow = false;
        this.node.active = false;
        RedDotShow.showNext();
    },

});

RedDotShow._curIndex = 0;
RedDotShow._isShow = false;
RedDotShow._showList = [];
RedDotShow.addShow = function (item) {
    var index = -1;
    for (var i = 0; i < RedDotShow._showList.length; i++) {
        if (RedDotShow._showList[i] != null && item["__name"] == RedDotShow._showList[i]["__name"]) {
            RedDotShow._showList[i] = null;
            index = i;
            break;
        }
    }
    if (index != -1)
        return;
    RedDotShow._showList.push(item);
    RedDotShow.showNext();
};
RedDotShow.removeShow = function (item) {
    if (item == null)
        return;
    var index = -1;
    if (item.node.active) {
        RedDotShow._isShow = false;
    }
    for (var i = 0; i < RedDotShow._showList.length; i++) {
        if (RedDotShow._showList[i] != null && item["__name"] == RedDotShow._showList[i]["__name"]) {
            RedDotShow._showList[i] = null;
            break;
        }
    }
    var list = [];
    for (var i = 0; i < RedDotShow._showList.length; i++) {
        if (RedDotShow._showList[i] != null) {
            list.push(RedDotShow._showList[i]);
        }
    }
    RedDotShow._showList = list;
    if (index == -1)
        return;
    RedDotShow.showNext();
};
RedDotShow.showNext = function () {
    if (RedDotShow._isShow || RedDotShow._showList.length == 0)
        return;
    RedDotShow._curIndex++;
    RedDotShow._curIndex = RedDotShow._showList.length <= RedDotShow._curIndex ? 0 : RedDotShow._curIndex;
    var item = RedDotShow._showList[RedDotShow._curIndex];
    if (item && item._isShow) {
        item.playShow();
    }
    else {
        for (var i = RedDotShow._curIndex + 1; i < RedDotShow._showList.length; i++) {
            item = RedDotShow._showList[i];
            if (item._isShow) {
                RedDotShow._curIndex = i;
                item.playShow();
            }
        }
        if (!item._isShow) {
            for (var i = 0; i < RedDotShow._showList.length; i++) {
                item = RedDotShow._showList[i];
                if (item._isShow) {
                    RedDotShow._curIndex = i;
                    item.playShow();
                }
            }
        }
    }
};
RedDotShow.clearData = function () {
    RedDotShow._showList = [];
    RedDotShow._isShow = false;
    RedDotShow._curIndex = 0;
};