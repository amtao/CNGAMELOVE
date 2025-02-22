



let utils = require("Utils");
var Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        lbstring0:cc.Label,
        lbstring01:cc.Label,
        


        // foo: {
        //     // ATTRIBUTES:
        //     default: null,        // The default value will be used only when the component attaching
        //                           // to a node for the first time
        //     type: cc.SpriteFrame, // optional, default is typeof default
        //     serializable: true,   // optional, default is true
        // },
        // bar: {
        //     get () {
        //         return this._bar;
        //     },
        //     set (value) {
        //         this._bar = value;
        //     }
        // },
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {
        let dataw = localcache.getItem(localdb.table_furniture_feast, Initializer.famUserHProxy.intergral.warmLv)
        this.lbstring0.string = i18n.t("HOMEPART_HOMEEDITOR_GETNUMBER", {
            number: dataw.maxjifen
        });
        this.lbstring01.string = i18n.t("HOMEPART_HOMEEDITOR_GETNUMBER1", {
            number: dataw.jifen
        });
        
    },

    
    onClickClose() {
        utils.utils.closeView(this);
    },

    // update (dt) {},
});
