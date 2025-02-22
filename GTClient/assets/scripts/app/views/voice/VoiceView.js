var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        voiceList: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("VOICE_DATA_UPDATE", this.voiceDataUpdate, this);
        l.voiceProxy.sendOpenVoice();
    },
    voiceDataUpdate() {
        this.voiceList.data = l.voiceProxy.voiceCfg;
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
