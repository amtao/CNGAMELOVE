
var List = require("List");
var utils = require("Utils");
var initializer = require("Initializer");

let s_allItems = null
cc.Class({
    extends: cc.Component,

    properties: {
        itemList: List,
        rmbNumber: cc.Label,
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
        this.showItemList()
        this.showRMBNumbers()
    },
    readListItemdata(){
        if(s_allItems){
            return
        }else{
            s_allItems = []
        }
        let itemDatas = localcache.getList("magnate_new_rwd")
        let lens = itemDatas.length
        let itmesMap = {}
        for (let i = 0; i < lens; i++) {
            let jjitmes = itemDatas[i].jj_rwd
            let lens2 = jjitmes.length
            for (let j = 0; j < lens2; j++) {
                let mod = jjitmes[j]
                if(mod.id in itmesMap){
                    itmesMap[mod.id].count+=mod.count
                }else{
                    let m = {count:mod.count}
                    if("kind" in mod){
                        m.kind = mod.kind
                    }
                    itmesMap[mod.id] = m
                }
            }
        }

        let keks = Object.keys(itmesMap)
        lens = keks.length
        for (let i = 0; i < lens; i+=3) {
            let ar =[]
            for (let j = i; j < i+3 && j<lens; j++) {
                let m = {id:keks[j],count:itmesMap[keks[j]].count}
                if("kind" in itmesMap[keks[j]]){
                    m.kind = itmesMap[keks[j]].kind
                }
                ar.push(m)
            }
            s_allItems.push(ar)
        }
    },
    showItemList(){
        this.readListItemdata()
        this.itemList.data = s_allItems
    },
    showRMBNumbers(){
        let purchaseDatas = initializer.welfareProxy.rshop.filter((tmpData) => {
            return tmpData.type == 7;
        });
        if (!purchaseDatas || purchaseDatas.length === 0) return;
        var pData = purchaseDatas[0];
        this.rmbNumber.string = i18n.t("MONTH_CARD_PRICE",{value:pData.rmb})
    },
    
    onCloseThis(){
        utils.utils.closeView(this);
    },

    btnGetback(){
        initializer.nobleOrderProxy.rechargeRshopType7();
        this.onCloseThis();
    },
    // update (dt) {},
});
