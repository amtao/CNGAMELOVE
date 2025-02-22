let scRenderListItem = require("RenderListItem");
let scUrlLoad = require("UrlLoad");
let scUIUtils = require("UIUtils");
let scShaderUtils = require("ShaderUtils");
let scInitializer = require("Initializer");
import { UNLOCK_CARD_SMALL_SLOT_TYPE,CARD_SLOT_PROP_TYPE } from "GameDefine";
cc.Class({
    extends: scRenderListItem,

    properties: {
        nodeUnLock:cc.Node,
        nodeNormal:cc.Node,
        nSelected: cc.Node,
        lblDes:cc.RichText,
        lblUnlock:cc.Label,
        starBg:scUrlLoad,
        spStarArr:[cc.Node],
    },

    showData() {
        let data = this._data;
        if(data) {
            let equipCardData = scInitializer.clotheProxy.equipCardInfoData;
            let slotInfo = equipCardData.slotInfo;
            if (slotInfo[data.suitid] && slotInfo[data.suitid][data.slotIdx] && slotInfo[data.suitid][data.slotIdx][data.smallSlotIdx]){
                let cg = slotInfo[data.suitid][data.slotIdx][data.smallSlotIdx];
                if (cg.isActivated == 0){
                    this.onShowLockDes();
                }
                else{
                    this.starBg.node.active = true;
                    this.nodeUnLock.active = false;
                    this.nodeNormal.active = true;
                    this.lblUnlock.string = "";
                    this.nSelected.active = false;
                    let cfg = localcache.getItem(localdb.table_property,cg.propId);
                    for (let ii = 0; ii < this.spStarArr.length;ii++){
                        this.spStarArr[ii].active = cfg.star > ii;
                    }
                    this.lblDes.string = scInitializer.clotheProxy.getActiveSlotDes(cg.propId);
                    let imgNameArr = ["sz_xy_bg_yixing","sz_xy_bg_yixing","sz_xy_bg_sanxingf","sz_xy_bg_sixingf","sz_xy_bg_wuxingf"];
                    this.starBg.url = scUIUtils.uiHelps.getUserclothePic(imgNameArr[cfg.star-1]);
                }
            }
            else{
                this.onShowLockDes();
            }
        }
    },

    onShowLockDes(){
        let data = this._data;
        this.starBg.node.active = false;
        this.nodeUnLock.active = true;
        this.nodeNormal.active = false;
        this.nSelected.active = false;
        this.lblDes.string = "";
        switch(data.unlock[0]){
            case UNLOCK_CARD_SMALL_SLOT_TYPE.PALACE_CARD:{
                this.lblUnlock.string = i18n.t("USER_CLOTHE_CARD_TIPS28");
            }
            break;
            case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP1:
            case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP2:
            case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP3:
            case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP4:
            {
                this.lblUnlock.string = i18n.t(`USER_CLOTHE_CARD_TIPS${28 + data.unlock[0]}`,{v1:data.unlock[1]});
            }
            break;
            case UNLOCK_CARD_SMALL_SLOT_TYPE.CARD_LEVEL:{
                this.lblUnlock.string = i18n.t("USER_CLOTHE_CARD_TIPS33",{v1:data.unlock[1]});
            }
            break;
        }
    },

    onSetSelect(flag){
        if (this.nodeUnLock.active) return;
        this.nSelected.active = flag;
        this.nodeNormal.active = !flag;
    },

    onClickItem(){
        if (this.nodeUnLock.active) return;
        facade.send("CLOTHE_XINYI_ACTIVEITEM_SELECT",{data:this._data});
    },

});
