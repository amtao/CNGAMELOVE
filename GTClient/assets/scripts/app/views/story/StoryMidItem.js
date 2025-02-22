var i = require("RenderListItem");
var n = require("ShaderUtils");
var l = require("Initializer");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        bgs: [cc.Sprite],
        btn: cc.Button,
        sp:UrlLoad,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = null == t.mname;
            this.lblName.string = e ? t.name: t.mname;
            var o = e ? l.playerProxy.userData.bmap > t.id: l.playerProxy.userData.mmap > t.id;
            this.btn.interactable = o;
            for (var i = 0; i < this.bgs.length; i++) n.shaderUtils.setImageGray(this.bgs[i], !o);
            let idx = t.bmap ? t.bmap : t.id;
            idx = idx % 6
            if (idx == 0){
                idx = 6;
            }
            this.sp.url = UIUtils.uiHelps.getStoryRecordBg(idx);
        }
    },
});
