let RenderListItem = require("RenderListItem");
let List = require("List");
let Initializer = require("Initializer");
let ShaderUtils = require("ShaderUtils");
import { BonusViewType } from 'GameDefine';

cc.Class({
    extends: RenderListItem,

    properties: {
        titleInfo: cc.Label,
        getBtn: cc.Button,
        btnIcon: cc.Sprite,
        btnInco: cc.Label,
        list: List,
        countInfo: cc.Label,
    },
    
    showData() {
        let bonusData = this._data;
        if (bonusData) {
            let itemData = bonusData.data;
            let haveGetCount = 0;
            this.titleInfo.string = "";
            switch(bonusData.type){
                case BonusViewType.FishScoreBonus:
                    haveGetCount = Initializer.fishingProxy.data.score;
                    break;
                case BonusViewType.CrushPassBonus:
                    haveGetCount = Initializer.crushProxy.data.pId;
                    this.titleInfo.string = i18n.t("CRUSH_RWD_TITLE", { num: itemData.need });
                    break;
                case BonusViewType.TofuPassBonus:
                    haveGetCount = Initializer.tofuProxy.data.max;
                    this.titleInfo.string = i18n.t("ToFU_BONUS_TIP");
                    break;
                case BonusViewType.BeachTreasureTask:
                    haveGetCount = Initializer.beachTreasureProxy.data.inGame;
                    itemData.need = itemData.num;
                    this.titleInfo.string = i18n.t("BeachTreasure_Task_Info",  { num:itemData.need } );
                    break;
                case BonusViewType.BeachTreasureAchieve:
                    haveGetCount = itemData.progress;
                    itemData.need = itemData.num;
                    (itemData.get == 1) && ((itemData.get = 0));
                    this.titleInfo.string = i18n.t("BeachTreasure_Achieve_Info_" + itemData.type, { num: itemData.need });
                    break;
                case BonusViewType.MoonBattleTask:
                    haveGetCount = Initializer.moonBattleProxy.getMoonNums();
                    itemData.need = itemData.num;
                    this.titleInfo.string = i18n.t("MOON_BATTLE_TASK_RENDER_TITLE", { num: itemData.need });
                    break;
            }
            //数量
            this.countInfo.string = "(" + haveGetCount + "/" + itemData.need + ")";
            //物品
            this.list.data = itemData.items;
            //领取
            let canGet = (itemData.get == 0) && (haveGetCount >= itemData.need);
            ShaderUtils.shaderUtils.setImageGray(this.btnIcon, !canGet);
            this.btnInco.string = itemData.get ? i18n.t("ACTIVITY_GROWTH_TIP6") : i18n.t("COMMON_GET");
            this.getBtn.interactable = canGet;
            this.bonusData = bonusData;
        }
    },
    onClickGet() {
        if(this.bonusData) {
            let msg = null;
            switch(this.bonusData.type) {
                case BonusViewType.FishScoreBonus:
                    msg = new proto_cs.huodong.hd8017Rwd();
                    break;
                case BonusViewType.CrushPassBonus:
                    msg = new proto_cs.huodong.hd8018Rwd();
                    break;
                case BonusViewType.TofuPassBonus:
                    msg = new proto_cs.huodong.hd8022Rwd();
                    break;
                case BonusViewType.BeachTreasureTask:
                    msg = new proto_cs.huodong.hd8026Rwd();
                    break;
                case BonusViewType.BeachTreasureAchieve:
                    msg = new proto_cs.huodong.hd8026TaskRwd();
                    break;
                case BonusViewType.MoonBattleTask:
                    msg = new proto_cs.huodong.hd8029Rwd();
                    break;
            }
            if (!!msg) {
                msg.id = this.bonusData.data.id;
                JsonHttp.send(msg, () => {
                    Initializer.timeProxy.floatReward();
                });
            }
        }
    },
});
