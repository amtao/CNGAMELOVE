var RenderListItem = require("RenderListItem");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Utils = require("Utils");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
var ShaderUtils = require("ShaderUtils");
var SkillItemDetail = cc.Class({
    extends: RenderListItem,
    properties: {
        
    },
    ctor() {

    },
       
    showData() {
        var t = this._data;
        if (t) {
            for(var i=1; i<=t.eps.length; i++) {
                this.node.getChildByName("item").getChildByName("icon"+i).getChildByName(t.eps[i-1]).active = true;
            }

            this.node.getChildByName("optimizeNode").getChildByName("lb_desc").getComponent(cc.Label).string = i18n.t("TANHE_SKILL_TIP"+(t.index+1), {num:t.xishu});
        }
    },
});
