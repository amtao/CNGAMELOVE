let RenderListItem = require("RenderListItem");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: RenderListItem,

    properties: {
        imgSlot: UrlLoad,
        colorFrame: UrlLoad,
        lbName: cc.Label,
        lbllevel: cc.Label,
        lblpropname1: cc.Label,
        lblpropname2: cc.Label,
        lblpropnum1: cc.Label,
        lblpropnum2: cc.Label,
        nBtnEnter: cc.Node,
        nSelected: cc.Node,
    },

    onLoad: function() {
        facade.subscribe(Initializer.fuyueProxy.TEMP_REFRESH_SELECT, this.updateSelect, this);
    },

    showData() {
        let data = this._data;
        if (data) {
            this.imgSlot.url = UIUtils.uiHelps.getItemSlot(data.icon);
            this.lbName.string = data.name;
            let tokensInfo = Initializer.servantProxy.getTokensInfo(Initializer.fuyueProxy.getFriendID());
            let lv = tokensInfo[data.id].lv;
            this.lbllevel.string = `LV${lv}`;

            let rad = 1;
            if (lv > 1){
                for (let kk = 2; kk <= lv; kk++) {
                    let _mm = localcache.getItem(localdb.table_tokenlvup, kk);
                    if (_mm == null) {
                        console.error("kk:", kk);
                    } else {
                        rad *= (1 + _mm.attri / 100);
                    }
                }            
            }
            let proplist = data.type[2];
            if (proplist.length == 2) {
                this.lblpropname1.string = Initializer.servantProxy.getPropName(proplist[0].prop);
                this.lblpropnum1.string = Math.ceil(proplist[0].value * rad);
                this.lblpropname2.string = Initializer.servantProxy.getPropName(proplist[1].prop);
                this.lblpropnum2.string = Math.ceil(proplist[1].value * rad);
            } else {
                this.lblpropname1.string = Initializer.servantProxy.getPropName(proplist[0].prop);
                this.lblpropnum1.string = Math.ceil(proplist[0].value * rad);
                this.lblpropnum2.string = " ";
                this.lblpropname2.string = " ";
            }

            this.nBtnEnter.active = this.node.parent.getComponent("List").chooseId != this._data.id;
            this.nSelected.active = !this.nBtnEnter.active;
        }
    },

    updateSelect: function(data) {
        if(this._data && data.set == Initializer.fuyueProxy.conditionType.token) {
            this.nBtnEnter.active = this._data.id != data.id;
            this.nSelected.active = !this.nBtnEnter.active;
        }
    },

    onClickEnter: function() {
        let fuyueProxy = Initializer.fuyueProxy;
        facade.send(fuyueProxy.TEMP_REFRESH_SELECT, { set: fuyueProxy.conditionType.token, type: this.node.parent.getComponent("List").iOpen, id: this._data.id });

    },

    // onClickItem: function() {  
    //     facade.send(Initializer.fuyueProxy.REFRESH_TOKEN, { type: this.node.parent.getComponent("List").iOpen, id: this._data.id });
    //     Utils.utils.closeNameView("fuyue/FuyueTokenListView");
    // },
});
