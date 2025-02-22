var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("UIUtils");
var a = require("Initializer");
var s = require("ShaderUtils");
var c = require("JiBanShow");
cc.Class({
    extends: i,
    properties: {
        letfname: cc.Label,
        rightname: cc.Label,
        leftRole: n,
        rightRole: n,
        leftCaiyi: cc.Sprite,
        rightCaiyi: cc.Sprite,
        iconArr: [cc.SpriteFrame],
        lockNode: cc.Node,
        bgArr: [cc.SpriteFrame],
        bgImg: cc.Sprite,
        leftNode: cc.Node,
        rightNode: cc.Node,
        letfRed: cc.Node,
        rightRed: cc.Node,
        leftJb: c,
        rightJb: c,
    },
    ctor() {
        this.wifeData = null;
        this.leftSp = null;
        this.rightSp = null;
    },
    showData() {
        var t = this._data;
        if (t) {
            this.wifeData = a.wifeProxy.getWifeData(t.wid);
            var e = a.wifeProxy.wifeSys.indexOf(t),
            o = e % 2,
            i = l.stringUtil.isBlank(t.type + "") ? 3 : parseInt(t.type + "") - 1;
            this.leftNode.active = 0 == o;
            this.rightNode.active = 0 != o;
            if (0 == o) {
                this.letfname.string = 2 == a.playerProxy.userData.sex ? t.wname2: t.wname;
                this.leftCaiyi.spriteFrame = this.iconArr[i];
                this.leftRole.url = r.uiHelps.getWifeBody(t.res);
            } else {
                this.rightname.string = 2 == a.playerProxy.userData.sex ? t.wname2: t.wname;
                this.rightCaiyi.spriteFrame = this.iconArr[i];
                this.rightRole.url = r.uiHelps.getWifeBody(t.res);
            }
            var n = e % 6;
            this.bgImg.spriteFrame = this.bgArr[n];
            if (this.lockNode) {
                var c = this;
                if (null == this.wifeData) {
                    s.shaderUtils.setNodeGray(this.lockNode);
                    this.leftRole.loadHandle = function() {
                        c.leftSp = c.leftRole.getComponentInChildren(sp.Skeleton);
                        c.leftSp.animation = "";
                        s.shaderUtils.setSpineGray(c.leftSp);
                    };
                    this.rightRole.loadHandle = function() {
                        c.rightSp = c.rightRole.getComponentInChildren(sp.Skeleton);
                        c.rightSp.animation = "";
                        s.shaderUtils.setSpineGray(c.rightSp);
                    };
                    this.letfRed.active = this.rightRed.active = !1;
                    this.leftJb.node.active = this.rightJb.node.active = !1;
                } else {
                    s.shaderUtils.clearNodeShader(this.lockNode);
                    this.rightRole.loadHandle = function() {
                        c.rightSp = c.rightRole.getComponentInChildren(sp.Skeleton);
                        c.rightSp.animation = "zhengchang";
                        s.shaderUtils.clearNodeShader(c.rightRole.node);
                    };
                    this.leftRole.loadHandle = function() {
                        c.leftSp = c.leftRole.getComponentInChildren(sp.Skeleton);
                        c.leftSp.animation = "zhengchang";
                        s.shaderUtils.clearNodeShader(c.leftRole.node);
                    };
                    this.letfRed.active = this.rightRed.active = a.wifeProxy.hasSkillUp(this.wifeData.id);
                    var _ = a.jibanProxy.getWifeJbLv(this.wifeData.id);
                    this.leftJb.setValue(5, _.level % 1e3);
                    this.rightJb.setValue(5, _.level % 1e3);
                }
            }
        }
    },
    onClickItem() {
        var t = this._data;
        if (null == this.wifeData) l.utils.openPrefabView("wife/WifeInfo", null, t);
        else {
            var e = a.wifeProxy.getMarryList(!1).indexOf(t);
            l.utils.openPrefabView("wife/WifeListView", null, {
                index: e
            });
        }
    },
    onClicCaiYi() {
        facade.send("WIFE_CAI_YI_TXT", this._data);
    },
});
