let List = require("List");
let Utils = require("Utils");
let Initializer = require("Initializer");
import { EndViewType } from 'GameDefine';

cc.Class({
    extends: cc.Component,
    properties: {
        list: List,
        winTip:cc.Label,
    },
    onLoad() {
        this.winInfo = this.node.openParam;
        if(this.winInfo.type == EndViewType.CrushEnd) {
            this.list.data = Initializer.crushProxy.getEndBonus();
        } else if(this.winInfo.type == EndViewType.BeachTreasureEnd) {
            this.winTip.node.y = 100;
        }
        this.list.node.x = -this.list.node.width / 2 + 30;
    },

    onClickView() {
        let self = this;
        if(this.winInfo.type == EndViewType.CrushEnd) {
            Initializer.crushProxy.checkMap(() => {
                Utils.utils.closeView(self);
            })
        }else if(this.winInfo.type == EndViewType.BeachTreasureEnd) {
            let battleType = this.winInfo.battleType;
            let score = this.winInfo.score;
            let isSkill = this.winInfo.isSkill;
            Initializer.beachTreasureProxy.sendEndGameMsg(battleType,score,isSkill, () => {
                Utils.utils.closeView(self);
                facade.send(Initializer.beachTreasureProxy.UPDATE);
            });
        }
    },

    onDestroy(){
        Initializer.timeProxy.itemReward = null;
    },
});
