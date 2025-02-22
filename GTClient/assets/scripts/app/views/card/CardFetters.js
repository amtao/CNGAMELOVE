let Utils = require("Utils");
let renderItem = require("RenderListItem");
cc.Class({
    extends: cc.Component,

    properties: {
        fetters:renderItem,
        fetters1:renderItem,
        noSkill:cc.Node,
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

    onLoad () {
        this.cid = this.node.openParam
        this.fMods1 = null
        this.fMods2 = null
    },

    start () {
        let clist = localcache.getList(localdb.table_card_skill);
        let len = clist.length
        for (let i = 0; i < len; i++) {
            let cards = clist[i].card
            let len2 = cards.length
            let bos = false
            for (let j = 0; j < len2; j++) {
                if(cards[j] === this.cid){
                    bos = true
                    break;
                }
            }
            if(bos){
                clist[i].unlock === 1?(this.fMods2 = clist[i]):(this.fMods1 = clist[i])
            }
        }
        this.fMods1 ? (this.fetters.data = this.fMods1):(this.fetters.node.active = false)
        this.fMods2 ? (this.fetters1.data = this.fMods2):(this.fetters1.node.active = false)

        if(!this.fMods1 && !this.fMods2){
            this.noSkill && (this.noSkill.active = true)
        }
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },
    // update (dt) {},
});
