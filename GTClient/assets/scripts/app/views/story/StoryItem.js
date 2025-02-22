var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        lblContent: cc.Label,
        btnSelect: cc.Button,
        nodeSelected: cc.Node,
        ndoeSp: cc.Sprite,
        nodeNor: cc.Sprite,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btnSelect);
        facade.subscribe("STORY_SELECTED", this.onSelect, this);
    },
    showData() {        
        this.node.scale = 1;
        var t = this._data;
        if (t) {
            this.lblContent.string = n.playerProxy.getReplaceName(t.context);
            // this.ndoeSp.node.active = 0 != t.tiaojian && l.stringUtil.isBlank(t.para);
            var e = n.timeProxy.isSelectedStory(t.id),
            o = localcache.getItem(localdb.table_storySelect2, t.id),
            i = o.group ? o.group.split("_") : "0";
            e = e && i.length <= 1;
            this.nodeSelected.active = e;
            this.ndoeSp.node.active = e;
            r.shaderUtils.setImageGray(this.ndoeSp, e);
            r.shaderUtils.setImageGray(this.nodeNor, e);
            this.node.stopAllActions();

            if(t.__index % 2 == 0) {
                this.node.position = cc.v2(this.node.position.x - 800, this.node.position.y);
                this.node.runAction(cc.sequence(cc.delayTime(t.__index*0.1), cc.moveBy(0.15, cc.v2(800, 0))));
            } else {                
                this.node.position = cc.v2(this.node.position.x + 800, this.node.position.y);
                this.node.runAction(cc.sequence(cc.delayTime(t.__index*0.1), cc.moveBy(0.15, cc.v2(-800, 0))));
            }

        }
    },

    onSelect() {
        var t = this._data;
        if (t) {
            this.node.stopAllActions();
            if(t.__index % 2 == 0) {                
                this.node.runAction(cc.moveBy(0.15, cc.v2(-800, 0)));
            } else {                                
                this.node.runAction(cc.moveBy(0.15, cc.v2(800, 0)));
            }
        }
    }
});
