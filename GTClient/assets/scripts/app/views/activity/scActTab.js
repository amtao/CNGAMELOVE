
let listItem = require("RenderListItem");
let scRedDot = require("RedDot");

cc.Class({
    extends: listItem,

    properties: {
        tgSelf: cc.Toggle,
        lbTitle: cc.Label,
        seColor: cc.Color,
        nonColor: cc.Color,
        nRed: cc.Node,
        scRed: scRedDot,
    },

    showData: function(data) {
        this._data = data;
        if (data) {
            this.lbTitle.string = data.title;
            for(let i = 0, len = this.tgSelf.checkEvents.length; i < len; i++) {
                this.tgSelf.checkEvents[i].customEventData = data.id.toString();
            }
        }
    },

    setData: function(data) {
        this._data = data;
        if (data) {
            this.lbTitle.string = i18n.t("ACT_PEOPLE_NUM", { val: data });
            for(let i = 0, len = this.tgSelf.checkEvents.length; i < len; i++) {
                this.tgSelf.checkEvents[i].customEventData = data.toString();
            }
        }
    },

    onTgValueChange: function(tg, param) {
        this.lbTitle.node.color = this.tgSelf.isChecked ? this.seColor : this.nonColor;  
    },
});
