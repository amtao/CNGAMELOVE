var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("ShaderUtils");
var a = require("Initializer");
var s = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblMeili: cc.Label,
        servantUrl: n,
        itemNode: cc.Sprite,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblMeili.string = s.utils.formatMoney(t.aep.e4);
            this.servantUrl.loadHandle = () => {
                this.anchorYPos(this.servantUrl);  
            }
            this.servantUrl.url = l.uiHelps.getServantSmallSpine(t.id);
            r.shaderUtils.setImageGray(this.itemNode, 0 == a.bossPorxy.getServantHitCount(t.id));
        }
    },
    onLoad() {
        this.defaultServantY = this.servantUrl.node.position.y;
    },

    anchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },
});
