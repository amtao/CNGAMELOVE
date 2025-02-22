var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("ShaderUtils");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        img:n,
        blank:n,
        btn:cc.Button,
        nodeLock:cc.Node,
        roleSpine:n,
        nodeChoose:cc.Node,
        nodeBg:cc.Node,
    },
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) if (null != t.blankmodel) {
            var e = this;
            this.blank.loadHandle = function() {
                var t = e._data;
                if (null != t.blankmodel) {
                    for (var o = a.playerProxy.isHaveBlank(t.id), i = e.blank.node.getComponentsInChildren(sp.Skeleton), n = 0; n < i.length; n++) i[n].animation = "";
                    if (!o) {
                        var l = e.blank.node.getComponentsInChildren(cc.Sprite);
                        for (n = 0; n < l.length; n++) r.shaderUtils.setImageGray(l[n], !0);
                        for (n = 0; n < i.length; n++) r.shaderUtils.setSpineGray(i[n]);
                    }
                }
            };
            this.blank.url = l.uiHelps.getBlank(t.blankmodel);
            this.img.node.active = !1;
            var o = a.playerProxy.isHaveBlank(t.id);
            this.nodeLock.active = !o;
            this.roleSpine.node.active = !1;
            this.nodeBg.active = false;
        } else {
            this.img.node.active = !0;
            this.nodeLock.active = !1;
            //this.blank.url = l.uiHelps.getBlank(1);
            this.nodeBg.active = true;
            this.blank.url = "";
            if (this._data.id == 0){
                this.roleSpine.node.active = !0;
                a.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
            }
            else{
                this.roleSpine.node.active = !1;
                this.img.url = l.uiHelps.getAvatar(this._data.id);
            }           
        }
    },

    setChoose(flag){
        this.nodeChoose.active = flag;
    },
});
