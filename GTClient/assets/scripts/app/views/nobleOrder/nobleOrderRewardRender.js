

var renderListItem = require("RenderListItem");
var List = require("List");

cc.Class({
    extends: renderListItem,

    properties: {
        normalList: List,
        specialList: List,
        levelLabel: cc.Label
    },

    // LIFE-CYCLE CALLBACKS:

    // onLoad () {},

    start () {

    },

    showData() {
        var data = this._data;
        this.levelLabel.string = data.lv ? data.lv : "";
        var normalList = [];
        this.normalList.data = data.pt_rwd ? this.creatList(data.pt_rwd, data.lv, false, this._data.surprise) : [];
        this.normalList.updateRenders();
        this.specialList.data = data.jj_rwd ? this.creatList(data.jj_rwd, data.lv, true, this._data.surprise) : [];
        this.specialList.updateRenders();
    },
     creatList (list, level, isSpecial, isSurprise) {
        var arr = [];
         list.forEach((item, index) => {
             var reward = {
                 level: level,
                 itemSlot: item,
                 isSpecial: isSpecial,
                 index: index,
                 isSurprise: isSurprise
             }
             arr.push(reward);
         })
         return arr;
     }

    // update (dt) {},
});
