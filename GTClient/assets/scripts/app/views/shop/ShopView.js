var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        nodeList: cc.Node,
        //lblTime: cc.Label,
        lbTabs: [cc.Label],
        seColor: cc.Color,
        nonColor: cc.Color,
        tab: cc.Node,
    },
    ctor() {
        this._curIndex = 4;
    },

    onLoad() {
        var t = this.node.openParam;
        if (t && 0 != t.id) {
            var e = l.shopProxy.isHaveItem(t.id);
            i.utils.openPrefabView("shopping/ShopBuy", !1, e);
        }
        this.onClickListTab(null, "1");
        facade.subscribe(
            l.shopProxy.UPDATE_SHOP_LIST,
            this.updateCurShow,
            this
        );
    },

    updateCurShow() {
        this.onClickListTab(null, this._curIndex.toString());
    },

    onClickListTab(t, index) {
        let pIndex = parseInt(index);
        this._curIndex = pIndex;
        for(let i = 0, len = this.lbTabs.length; i < len; i++) {
            this.lbTabs[i].node.color = pIndex == i + 1 ? this.seColor : this.nonColor;
        }
        this.list.data = this.getShopList(pIndex);
        this.list.updateRenders();
    },

    getShopList(index) {
        let array = [], list = l.shopProxy.list;
        for (let i = 0, len = list.length; i < len; i++) {
            let item = list[i];
            if(item.type == index) {
                array.push(item);
            }
        }

        let sortFunc = ((a, b) => {
            return a.id - b.id;
        });

        let result = array.filter((data) => {
            return data.islimit == 0 || (data.islimit != 0 && data.limit > 0);
        });
        result.sort(sortFunc);
        let tmpList = array.filter((data) => {
            return (data.islimit != 0 && data.limit <= 0);
        });
        tmpList.sort(sortFunc);
        result = result.concat(tmpList);
        return result;
    },

    onClickClost() {
        i.utils.closeView(this);
    },

    onClickRecharge() {
        r.funUtils.openView(r.funUtils.recharge.id);
    },
});