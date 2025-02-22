let RenderListItem = require("RenderListItem");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: RenderListItem,

    properties: {
        nSelected: cc.Node,
        imgSlot: UrlLoad,
        colorFrame: UrlLoad,
        lbName: cc.Label,
    },

    onLoad: function() {
        facade.subscribe(Initializer.fuyueProxy.TEMP_REFRESH_SELECT, this.updateSelect, this);
    },

    showData() {
        let data = this._data;
        if (data) {
            this.imgSlot.url = UIUtils.uiHelps.getBaowuIcon(data.picture);
            this.colorFrame.url = UIUtils.uiHelps.getItemColor(data.quality + 1);
            this.lbName && (this.lbName.string = data.name);

            this.nSelected && (this.nSelected.active = this.node.parent.getComponent("List").chooseId == this._data.id);
        }
    },

    onClickItem: function() {
        //行商
        if (this.node.parent.getComponent("List").m_type != null && this.node.parent.getComponent("List").m_type == Initializer.businessProxy.BAOWULIST_TYPE.BUSINESS){
            facade.send(Initializer.fuyueProxy.REFRESH_BAOWU, {type:this.node.parent.getComponent("List").iOpen, id:this._data.id});
            Utils.utils.closeNameView("fuyue/FuyueBaowuListView");   
            return;
        }  
        let fuyueProxy = Initializer.fuyueProxy;
        facade.send(fuyueProxy.TEMP_REFRESH_SELECT, { set: fuyueProxy.conditionType.baowu, type: this.node.parent.getComponent("List").iOpen, id: this._data.id });
        // facade.send(Initializer.fuyueProxy.REFRESH_BAOWU, {type:this.node.parent.getComponent("List").iOpen, id:this._data.id});
        // Utils.utils.closeNameView("fuyue/FuyueBaowuListView");   
    },

    updateSelect: function(data) {
        if(this._data && data.set == Initializer.fuyueProxy.conditionType.baowu) {
            this.nSelected && (this.nSelected.active = this._data.id == data.id);
        }
    },
});
