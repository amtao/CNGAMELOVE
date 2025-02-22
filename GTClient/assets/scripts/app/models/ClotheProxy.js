
var Initializer= require("Initializer");
var Utils = require("Utils");
var RedDot = require("RedDot");
import { CARD_SLOT_SKILL_TYPE,CARD_SLOT_PROP_TYPE,USER_CUT_LEVELUP_TYPE,CLOTHE_ARCHIEVE_UNLOCK_TYPE,UNLOCK_CARD_BIG_SLOT_TYPE,UNLOCK_CARD_SMALL_SLOT_TYPE } from "GameDefine";

var ClotheProxy = function() {
    
    /**临时存放当前的套装列表id*/
    this.clotheList = [];
    this.pickLv = 0;
    this.brocadeInfoData = null;
    this.equipCardInfoData = null;
    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.clothe.pickAward, this.onPickAwardInfo, this);
        JsonHttp.subscribe(proto_sc.clothe.brocade, this.onBrocadeInfo, this);
        JsonHttp.subscribe(proto_sc.clothe.equipCard, this.onEquipCardInfo, this);
        JsonHttp.subscribe(proto_sc.clothe.sepcial, this.onClotheSpecialInfo, this);
    };
    this.clearData = function() {
       this.clotheList.length = 0;
       this.pickLv = 0;
       this.brocadeInfoData = null;
       this.equipCardInfoData = null;
    };

    /**返回当前已经领取的华服等级*/
    this.onPickAwardInfo = function(data){
        this.pickLv = data.pickLv;
        Initializer.playerProxy.canGetClotheValueReward();
    };

    /**返回裁剪数据*/
    this.onBrocadeInfo = function(data){
        //console.error("onBrocadeInfo:",data)
        this.brocadeInfoData = data;
        facade.send("UPDATE_CLOTHE_BROCADE");
        for (let ii = 0;ii < 4;ii++){
            this.checkClotheTypeRed(ii+1);
        }
    };

    /**返回套装心忆的数据*/
    this.onEquipCardInfo = function(data){
        //console.error("onEquipCardInfo:",data)
        this.equipCardInfoData = data;
        facade.send("UPDATE_CLOTHE_EQUIPCARD");
    };

    /**返回套装的特效信息*/
    this.onClotheSpecialInfo = function(data){
        this.specialClotheInfo = data.sepcial;
        console.error("this.specialClotheInfo:",this.specialClotheInfo)
        facade.send("UPDATE_CLOTHE_SPECIALINFO");
    };

    /**领取华服奖励*/
    this.sendPickHuaFuAward = function(callback){
        var e = new proto_cs.clothe.pickHuaFuAward();
        JsonHttp.send(e,function(){
            Initializer.timeProxy.floatReward();
            callback && callback();
        });
    };

    /**华服升级（裁剪）*/
    this.sendJyUpLv = function(suitId){
        var e = new proto_cs.clothe.jyUpLv();
        e.suitId = suitId;
        JsonHttp.send(e);
    };


    /**放置卡牌
    *@param suitId 套装id
    *@param bSlot 大槽位
    *@param cardId 卡牌
    */
    this.sendPutCard = function(suitId,bSlot,cardId){
        var e = new proto_cs.clothe.putCard();
        e.suitId = suitId;
        e.bSlot = bSlot;
        e.cardId = cardId;
        JsonHttp.send(e);
    };


    /**获取槽位解锁信息
    *@param suitId 套装id
    */
    this.sendGetUnlockInfo = function(suitId){
        var e = new proto_cs.clothe.getUnlockInfo();
        e.suitId = suitId;
        JsonHttp.send(e);
    };

    /**刷新
    *@param suitId 套装id
    *@param bSlot 大槽位
    *@param sSlot 小槽位
    */
    this.sendRefresh = function(suitId,bSlot,sSlot){
        var e = new proto_cs.clothe.refresh();
        e.suitId = suitId;
        e.bSlot = bSlot;
        e.sSlot = sSlot;
        JsonHttp.send(e);
    };

    /**套装装扮和卸下特效
    *@param clotheId 特效id
    *@param isEquip 是否装扮
    */
    this.sendEquipSpecial = function(clotheId,isEquip){
        var e = new proto_cs.clothe.equipSpecial();
        e.clotheId = clotheId;
        e.isEquip = isEquip;
        JsonHttp.send(e);
    };

    /**获取激活的小卡槽的激活的功能描述
    *@param propId 表格property的唯一ID
    */
    this.getActiveSlotDes = function(propId){
        let cfg = localcache.getItem(localdb.table_property,propId);
        switch(cfg.type){
            case CARD_SLOT_PROP_TYPE.SUIT_PROP_ADD:{
                return i18n.t("USER_CLOTHE_CARD_TIPS34",{v1:i18n.t(`COMMON_PROP${cfg.buff[0]}`),v2:cfg.buff[1]})
            }
            break;
            case CARD_SLOT_PROP_TYPE.CARD_PROP_ADD:{
                return i18n.t("USER_CLOTHE_CARD_TIPS35",{v1:i18n.t(`COMMON_PROP${cfg.buff[0]}`),v2:cfg.buff[1]})
            }
            break;
        }
    };

    /**获取激活的小卡槽的描述列表*/
    this.getActiveSlotListDes = function(suitId){
        let slotInfo = this.equipCardInfoData.slotInfo;
        let listdata = [];
        if (slotInfo[suitId] != null){
            for (let slotIdx in slotInfo[suitId]){
                let cg = slotInfo[suitId][slotIdx];
                for (let ii = 1; ii < 4;ii++){
                    if (cg[ii] && cg[ii].isActivated == 1){
                        listdata.push(this.getActiveSlotDes(cg[ii].propId));
                    }
                }
            }
        }
        return listdata;
    };


    /**判断当前卡槽的三个小卡槽是否都已经激活*/
    this.IsCardSlotActive = function(suitId,slotIdx){
        let slotInfo = this.equipCardInfoData.slotInfo;
        if (slotInfo[suitId] && slotInfo[suitId][slotIdx]){
            let cg = slotInfo[suitId][slotIdx];
            for (let ii = 1;ii < 4;ii++){
                if (cg[ii] == null || cg[ii].isActivated == 0){
                    return false;
                }
            }
            return true;
        }
        return false;
    };


    /**判断当前卡槽的三个小槽是否已经有激活的*/
    this.isHasAnySmallCardSlotActive = function(suitId,slotIdx){
        let slotInfo = this.equipCardInfoData.slotInfo;
        if (slotInfo[suitId] && slotInfo[suitId][slotIdx]){
            let cg = slotInfo[suitId][slotIdx];
            for (let ii = 1;ii < 4;ii++){
                if (cg[ii] && cg[ii].isActivated == 1){
                    return true;
                }
            }
        }
        return false;
    };

    /**获取三条小卡槽都激活的大卡槽的激活功能描述
    *@param suitId 套装ID
    *@param slotIdx 卡槽的位置 1,2,3
    */
    this.getActiveCardSlotDes = function(suitId,slotIdx,isColor = false){
        let cfg = localcache.getItem(localdb.table_cardSlot,suitId);
        if (cfg == null){
            return "";
        }
        let need = cfg[`need${slotIdx}`];
        let star = 0;
        let starInfo = this.equipCardInfoData.starInfo;
        if (starInfo[suitId] && starInfo[suitId][slotIdx]){
            star = starInfo[suitId][slotIdx];
        }
        let curBuff,nextBuff = null;
        let needStar = 0;
        for (let ii = 0; ii < need.length;ii++){
            if (need[ii].star <= star){
                curBuff = need[ii].buff;
            }
            else{
                nextBuff = need[ii].buff;
                needStar = need[ii].star - star;
                break;
            }
        }
        if (nextBuff == null){
            let curDes = this.getCardSlotDes(cfg[`type${slotIdx}`],curBuff,isColor);           
            return {curDes:curDes,isMax:true};
        }
        let nextDes = this.getCardSlotDes(cfg[`type${slotIdx}`],nextBuff);
        if (curBuff == null){
            return {nextDes:nextDes};
        }
        else{
            let curDes = this.getCardSlotDes(cfg[`type${slotIdx}`],curBuff,isColor);
            return{nextDes:nextDes,curDes:curDes,needStar:needStar};
        }      
    };

    /**判断当前套装的卡槽是否已经插了卡
    *@param suitId 套装ID
    *@param slotIdx 卡槽的位置 1,2,3
    */
    this.isHasCardInSlot = function(suitId,slotIdx){
        let cardInfo = this.equipCardInfoData.cardInfo;
        if (cardInfo && cardInfo[suitId] && cardInfo[suitId][slotIdx]){
            return true;
        }
        return false;
    };

    /**获取心忆卡槽解锁描述*/
    this.getUnlockXinYiDes = function(type,para1){
        switch(type){
            case UNLOCK_CARD_BIG_SLOT_TYPE.COLLECT_SUIT:{
                return i18n.t("USER_CLOTHE_CARD_TIPS25");
            }
            break;
            case UNLOCK_CARD_BIG_SLOT_TYPE.SUIT_LEVEL:{
                return i18n.t("USER_CLOTHE_CARD_TIPS26",{v1:para1});
            }
            break;
            case UNLOCK_CARD_BIG_SLOT_TYPE.CLOTHE_CUT_LEVEL:{
                return i18n.t("USER_CLOTHE_CARD_TIPS27",{v1:para1});
            }
            break;
        }
    };

    /**根据类型和参数获取描述*/
    this.getCardSlotDes = function(type,buff,isColor = false){
        switch(type){
            case CARD_SLOT_SKILL_TYPE.CARD_PROP_ADDPERCENT:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:i18n.t(`COMMON_PROP${buff[0]}`)}) : i18n.t(`COMMON_PROP${buff[0]}`);
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS36",{v1:para1,v2:para2});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.BANCHAI_ITEM_ADDPERCENT:{
                let cfg = localcache.getItem(localdb.table_item,buff[0]);
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:cfg.name}) : cfg.name;
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS37",{v1:para1,v2:para2});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.FISH_SCORE_ADDPERCENT:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS38",{v1:para1});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.FOOD_SCORE_ADDPERCENT:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS39",{v1:para1});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.SERVANT_STUDY_REDUCE_TIME:{
                let servantCfg = localcache.getItem(localdb.table_hero,buff[0])
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:servantCfg.name}) : servantCfg.name;
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS40",{v1:para1,v2:para2});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.CHILD_ENERGY_RECOVER_REDUCE_TIME:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS41",{v1:para1});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.CHILD_LILIAN_REDUCE_TIME:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS42",{v1:para1});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.CHILD_LILIAN_ITEM_ADDPERCENT:{
                let cfg = localcache.getItem(localdb.table_item,buff[0]);
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:cfg.name}) : cfg.name;
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS43",{v1:para1,v2:para2});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.JIAOYOU_OUTPUT_ADDPERCENT:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS44",{v1:para1});
            }
            break;
            case CARD_SLOT_SKILL_TYPE.TANHE_ADDPERCENT:{
                let cfg = localcache.getItem(localdb.table_item,buff[0]);
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:cfg.name}) : cfg.name;
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS45",{v1:para1,v2:para2});
            }
            break;
        }
        return "";
    };

    /**获取套装裁剪的奖励描述
    *@param type 类型
    *@param buff 参数
    *@param isColor 参数是否加颜色
    */
    this.getCutClotheLevelDes = function(type,buff,isColor = false){
        switch(type){
            case USER_CUT_LEVELUP_TYPE.GET_SPECIAL_EFFECT:{
                return i18n.t("USER_CLOTHE_CARD_TIPS47");
            }
            break;
            case USER_CUT_LEVELUP_TYPE.ADD_PROP:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:i18n.t(`COMMON_PROP${buff[0]}`)}) : i18n.t(`COMMON_PROP${buff[0]}`);
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS48",{v1:para1,v2:para2});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.ADD_CLOTHE_VALUE:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS49",{v1:para1});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.UNLOCK_XINYI:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:i18n.t(`USER_CLOTHE_CARD_TIPS${14+buff[0]}`)}) : i18n.t(`USER_CLOTHE_CARD_TIPS${14+buff[0]}`);
                return i18n.t("USER_CLOTHE_CARD_TIPS50",{v1:para1});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.ADD_SERVANT_PROP:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS51",{v1:para1});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.ADD_BANCHAI_NUM:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS52",{v1:para1});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.ADD_RANDOM_HELLO:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS53",{v1:para1});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.ADD_INVITE_SERVANT:{
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[0]}) : buff[0];
                return i18n.t("USER_CLOTHE_CARD_TIPS54",{v1:para1});
            }
            break;
            case USER_CUT_LEVELUP_TYPE.BANCHAI_ITEM_ADDPERCENT:{
                let cfg = localcache.getItem(localdb.table_item,buff[0]);
                let para1 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:cfg.name}) : cfg.name;
                let para2 = isColor ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:buff[1]}) : buff[1];
                return i18n.t("USER_CLOTHE_CARD_TIPS37",{v1:para1,v2:para2});
            }
            break;           
        }
        return "";
    };

    /**判断档案是否解锁*/
    this.isUnlockClotheAchieve = function(suitId,type,para){
        switch(type){
            case CLOTHE_ARCHIEVE_UNLOCK_TYPE.GET_CLOTHE_PART:{
                return Initializer.playerProxy.isUnlockCloth(para);
            }
            case CLOTHE_ARCHIEVE_UNLOCK_TYPE.CLOTHE_CUT_LEVEL:{
                let brocadeData = this.brocadeInfoData;
                let curLevel = brocadeData.suitBrocadeLv[suitId] ? brocadeData.suitBrocadeLv[suitId] : 0;
                return curLevel >= para;
            }
            case CLOTHE_ARCHIEVE_UNLOCK_TYPE.SUIT_LEVEL:{
                return Initializer.playerProxy.getSuitLv(suitId) >= para;
            }
        }
        return false;
    };

    /**获取档案解锁的条件*/
    this.getUnlockClotheAchieveDes = function(type,para){
        switch(type){
            case CLOTHE_ARCHIEVE_UNLOCK_TYPE.GET_CLOTHE_PART:{
                let cfg = localcache.getItem(localdb.table_userClothe,para)
                return i18n.t("USER_CLOTHE_CARD_TIPS55",{v1:cfg.name});
            }
            case CLOTHE_ARCHIEVE_UNLOCK_TYPE.CLOTHE_CUT_LEVEL:{
                return i18n.t("USER_CLOTHE_CARD_TIPS27",{v1:para});
            }
            case CLOTHE_ARCHIEVE_UNLOCK_TYPE.SUIT_LEVEL:{
                return i18n.t("USER_CLOTHE_CARD_TIPS26",{v1:para});
            }
        }
        return "";
    };

    /**判断当前套装是否有档案*/
    this.isSuitArchieve = function(suitId){
        let cfg = localcache.getItem(localdb.table_usersuit,suitId);
        for (let ii = 0; ii < 5;ii++){
            let storyname = cfg[`storyname${ii+1}`];
            if (storyname && storyname != ""){
                return true
            }
        }
        return false;
    };

    /**判断当前套装是否有专属特效*/
    this.isClotheSuitHaveEffect = function(suitId){
        let listCfg = localcache.getFilters(localdb.table_userSuitLv2,"suit",suitId);
        let hFlag = false;
        let unLockLv = 0;
        for (let ii = 0; ii < listCfg.length;ii++){
            let cg = listCfg[ii];
            if (cg.type == USER_CUT_LEVELUP_TYPE.GET_SPECIAL_EFFECT){
                hFlag = true;
                unLockLv = cg.lv;
                break;
            }
        }
        return {have:hFlag,unLockLv:unLockLv}
    };

    /**判断当前套装是否使用特效*/
    this.isUsingSuitClotheEffect = function(suitId){
        let suitData = localcache.getItem(localdb.table_usersuit, suitId);
        if (suitData == null) return false;
        let clotheid = 0;
        for (let ii = 0 ; ii < suitData.clother.length; ii++){
            let cid = suitData.clother[ii];
            let cg = localcache.getItem(localdb.table_userClothe,cid);
            if (cg && cg.part == Initializer.playerProxy.PLAYERCLOTHETYPE.BODY){
                clotheid = cid;
                break;
            }
        }
        let data = this.specialClotheInfo;
        if (data && data[clotheid] && data[clotheid] != 0){
            return true;
        }
        return false;
    };

    /**根据类型获取裁剪获得奖励值*/
    this.getServantFightEp1AddValue = function(type){
        let sum = 0;
        let brocadeData = this.brocadeInfoData.suitBrocadeLv;
        if (brocadeData != null){
            for (let suitId in brocadeData){
                let curLevel = brocadeData[suitId];
                let userSuitLvlist = localcache.getGroupByKeys(localdb.table_userSuitLv2,{suit:Number(suitId),type:type});
                for (let ii = 0; ii < userSuitLvlist.length; ii++){
                    let cg = userSuitLvlist[ii];
                    if (cg.lv <= curLevel){
                        sum += cg.rwd[0];
                    }
                }
            }
        }
        return sum;
    };


    /**根据类型获取心忆大卡槽的奖励值
    *@param type 类型  CARD_SLOT_SKILL_TYPE枚举
    *@param para1 奖励的前一个参数 例如：伙伴id
    */
    this.getClotheSuitCardSlotRewardValue = function(type,para1){
        let data = this.equipCardInfoData;
        let addSkillProp = data.addSkillProp;
        if (addSkillProp[type] != null){
            if (para1 != null){
                return addSkillProp[type][para1] ? addSkillProp[type][para1] : 0;
            }
            return addSkillProp[type] ? addSkillProp[type] : 0;
        }
        return 0;    
    };

    /**获取当前槽位需要解锁需要的卡牌属性*/
    this.getUnlockCardSlotNeedProp = function(suitId,slotIdx){
        let cfg = localcache.getItem(localdb.table_cardSlot,suitId);
        if (cfg != null){
            for (let ii = 0; ii < 3; ii++){
                let unlock = cfg[`ep${slotIdx}_${ii+1}`];
                switch(unlock[0]){
                    case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP1:
                    case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP2:
                    case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP3:
                    case UNLOCK_CARD_SMALL_SLOT_TYPE.ACHIEVE_PROP4:{
                        return unlock[0];
                    }
                }
            }
        }
        return -1;
    };

    /**获取女主的服装特效*/
    this.getPlayerSuitClotheEffect = function(clotheId){
        if (this.specialClotheInfo && this.specialClotheInfo[clotheId]){
            return this.specialClotheInfo[clotheId];
        }
        return 0;
    };

    /**判断当前的套装是否可以激活裁剪*/
    this.isCanActiveCut = function(suitId){
        let suitLv = Initializer.playerProxy.getSuitLv(suitId);
        let needSuitLv = Utils.utils.getParamInt("suit_lvup");
        if (suitLv < needSuitLv){
            return false;
        }
        let brocadeData = this.brocadeInfoData;
        let curLevel = brocadeData.suitBrocadeLv[suitId] ? brocadeData.suitBrocadeLv[suitId] : 0;
        if (curLevel <= 0) return true;
        return false;
    };

    /**判断某一类型的套装是否有红点
    *@param type 1、锦衣行,2、江湖游,3、人间事,4、异域情
    */
    this.checkClotheTypeRed = function(type){
        let listCfg = localcache.getFilters(localdb.table_usersuit,"type",type);
        RedDot.change(`clothe_suit_sort${type}`,false)
        for (let ii = 0; ii < listCfg.length;ii++){
            let cg = listCfg[ii];
            if (this.isCanActiveCut(cg.id)){
                RedDot.change(`clothe_suit_sort${type}`,true)
                return;
            }
        }
    };


}
exports.ClotheProxy = ClotheProxy;