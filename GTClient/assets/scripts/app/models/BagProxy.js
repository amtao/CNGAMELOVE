var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var r = require("TimeProxy");
var UIUtils = require("UIUtils");
var BagProxy = function() {

    this.UPDATE_BAG_HECHENG = "UPDATE_BAG_HECHENG";
    this.UPDATE_BAG_ITEM = "UPDATE_BAG_ITEM";
    this.UPDATE_BAG_CHENGHAO = "UPDATE_BAG_CHENGHAO";
    this.heChengList = null;
    this.chInfo = null;
    this.itemList = null;
    this.itemObjs = {};

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.item.itemList, this.onItemList, this);
        JsonHttp.subscribe(proto_sc.item.hecheng, this.onHeCheng, this);
    };
    this.clearData = function() {
        this.itemObjs = {};
        this.itemList = null;
        this.heChengList = null;
    };
    this.onItemList = function(t) {
        this.itemObjs = {};
        if (null == this.itemList) this.itemList = t;
        else {
            i.utils.copyList(this.itemList, t, "id", !0, "count");
            for (
                var e = !1, o = 0;
                o < t.length && !(e = 1 == t[o].isNew);
                o++
            );
            l.change("bagview", e);
        }
        for (o = 0; o < this.itemList.length; o++) {
            var a = this.itemList[o];
            this.itemObjs[a.id] = a;
        }
        facade.send(this.UPDATE_BAG_ITEM);
        n.treasureProxy.updateTreasureRed();
        n.cardProxy.checkAllCardRedPot();
        n.baowuProxy.checkBaowuAllRedPot();

        r.funUtils.isOpenFun(r.funUtils.girlsDay) &&
            n.girlsDayProxy.updateItemNum();
    };

    this.clearRedDot = function() {
        for (let o = 0; o < this.itemList.length; o++) {
            let itemData = this.itemList[o];
            if(itemData.isNew) {
                itemData.isNew = !1;
            }
        }
        l.change("bagview", !1);
    };

    this.onHeCheng = function(t) {
        this.initHeChengList();
        i.utils.copyList(this.heChengList, t, "itemid");
        facade.send(this.UPDATE_BAG_HECHENG);
    };
    this.initHeChengList = function() {
        if (null == this.heChengList) {
            this.heChengList = [];
            for (
                var t = localcache.getList(localdb.table_group), e = 0;
                e < t.length;
                e++
            ) {
                var o = t[e],
                    i = {};
                i.itemid = o.itemid;
                i.outtime = 0;
                i.times = 0;
                i.totonum = 0;
                i.need = o.need;
                this.heChengList.push(i);
            }
        }
    };
    this.sendUse = function(t, e) {
        var o = new proto_cs.item.useitem();
        o.id = t;
        o.count = e;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendCompose = function(t, e) {
        void 0 === e && (e = 1);
        var o = new proto_cs.item.hecheng();
        o.id = t; //合成后的道具id
        o.count = e;
        JsonHttp.send(o, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendFashion = function(t) {
        var e = new proto_cs.chenghao.setChengHao();
        e.chid = t;
        JsonHttp.send(e);
    };
    this.sendCancelFashion = function(t) {
        n.playerProxy.userData.chenghao = 0;
        var e = new proto_cs.chenghao.offChengHao();
        e.chid = t;
        JsonHttp.send(e);
    };
    this.sendUseItemHero = function(t, e, o) {
        var i = new proto_cs.item.useforhero();
        i.count = e;
        i.heroid = o;
        i.id = t;
        JsonHttp.send(i, function() {
            n.timeProxy.floatReward();
        });
    };
    this.getItemCount = function(t) {
        var e = this.itemObjs[t];
        if (e) return e.count;
        switch (t) {
            case 1:
                return n.playerProxy.userData.cash;

            case 2:
                return n.playerProxy.userData.coin;

            case 3:
                return n.playerProxy.userData.food;

            case 4:
                return n.playerProxy.userData.army;

            case 5:
                return n.playerProxy.userData.exp;
            case 10:
                return n.playerProxy.userData.dresscoin;
            case 117:
                return n.unionProxy.clubInfo ? n.unionProxy.clubInfo.fund : 0;
            case 118:
                return n.unionProxy.memberInfo ? n.unionProxy.memberInfo.leftgx : 0;
            case 119:
                return n.unionProxy.clubInfo ? n.unionProxy.clubInfo.exp : 0;
            case 120:
                return n.unionProxy.partyResourceData ? n.unionProxy.partyResourceData.totalResource : 0;
        }
        return 0;
    };

    this.getItemIco = function(t){
        switch (t) {
            case 1:
                return UIUtils.uiHelps.getResIcon("1");

            case 2:
                return UIUtils.uiHelps.getResIcon("2");

            case 3:
                return UIUtils.uiHelps.getResIcon("3");

            case 4:
                return UIUtils.uiHelps.getResIcon("4");
        }
        return UIUtils.uiHelps.getItemSlot(t);
    };

    this.getItemList = function() {
        for (var t = [], e = {}, o = 0; o < this.itemList.length; o++) {
            var i = this.itemList[o];
            if (null != i && 0 != i.count) {
                var n = localcache.getItem(localdb.table_item, i.id);
                if (null != n) {
                    e[i.id] = 0 == n.classify ? 99 : n.classify;
                    if(DataType.CARD_SUIPIAN != n.kind) {
                        t.push(i);
                    }
                }
            }
        }
        t.sort(function(t, o) {
            var i = e[t.id],
                n = e[o.id];
            return i == n ? t.id - o.id : i - n;
        });
        return t;
    };
    this.getFoodList = function() {
        for (var t = [], e = 0; e < this.itemList.length; e++) {
            var o = localcache.getItem(
                localdb.table_item,
                this.itemList[e].id
            );
            o &&
                101 == o.kind &&
                this.itemList[e].count > 0 &&
                t.push(this.itemList[e]);
        }
        return t;
    };
    this.getHecheng = function(t) {
        null == this.heChengList && this.initHeChengList();
        for (var e = 0; e < this.heChengList.length; e++)
            for (var o = this.heChengList[e], i = 0; i < o.need.length; i++)
                if (o.need[i].id == t) return o;
        return null;
    };
    this.getCanHechengItem = function(t) {
        null == this.heChengList && this.initHeChengList();
        for (var e = 0; e < this.heChengList.length; e++) {
            var o = this.heChengList[e];
            if (o.itemid == t) return o;
        }
        return null;
    };
}
exports.BagProxy = BagProxy;
var DataType;
(function (DataType) {
    DataType[DataType["NONE"] = 0] = "NONE";
    DataType[DataType["ITEM"] = 1] = "ITEM";
    DataType[DataType["ENUM_ITEM"] = 2] = "ENUM_ITEM";
    DataType[DataType["WIFE_LOVE"] = 3] = "WIFE_LOVE";
    DataType[DataType["WIFE_FLOWER"] = 4] = "WIFE_FLOWER";
    DataType[DataType["BOOK_EXP"] = 5] = "BOOK_EXP";
    DataType[DataType["SKILL_EXT"] = 6] = "SKILL_EXT";
    DataType[DataType["HERO"] = 7] = "HERO";
    DataType[DataType["WIFE"] = 8] = "WIFE";
    DataType[DataType["WIFE_HAOGAN"] = 9] = "WIFE_HAOGAN";
    DataType[DataType["CHENGHAO"] = 10] = "CHENGHAO";
    DataType[DataType["HUODONG"] = 11] = "HUODONG";
    DataType[DataType["WIFE_EXP"] = 12] = "WIFE_EXP";
    DataType[DataType["HERO_SW"] = 90] = "HERO_SW";
    DataType[DataType["ROLE_SW"] = 91] = "ROLE_SW";
    DataType[DataType["HERO_JB"] = 92] = "HERO_JB";
    DataType[DataType["WIFE_JB"] = 93] = "WIFE_JB";
    DataType[DataType["HEAD_BLANK"] = 94] = "HEAD_BLANK";
    DataType[DataType["CLOTHE"] = 95] = "CLOTHE";
    DataType[DataType["JB_ITEM"] = 96] = "JB_ITEM";
    DataType[DataType["HERO_CLOTHE"] = 97] = "HERO_CLOTHE";
    DataType[DataType["USER_JOB"] = 98] = "USER_JOB";
    DataType[DataType["USER_SUIT"] = 99] = "USER_SUIT";
    DataType[DataType["CARD_SUIPIAN"] = 106] = "CARD_SUIPIAN";
    DataType[DataType["HERO_DRESS"] = 111] = "HERO_DRESS";
    DataType[DataType["HERO_BG"] = 112] = "HERO_BG";
    DataType[DataType["HERO_EMOJI"] = 113] = "HERO_EMOJI";
    DataType[DataType["CLUB_MONEY"] = 114] = "CLUB_MONEY";
    DataType[DataType["CLUB_DONATE"] = 115] = "CLUB_DONATE";
    DataType[DataType["CLUB_EXP"] = 116] = "CLUB_EXP";
    DataType[DataType["HERO_XINWU"] = 200] = "HERO_XINWU";
    DataType[DataType["BAOWU_SUIPIAN"] = 201] = "BAOWU_SUIPIAN";
    DataType[DataType["BAOWU_ITEM"] = 202] = "BAOWU_ITEM";
    DataType[DataType["FISHFOOD_ITEM"] = 400] = "FISHFOOD_ITEM";  
    DataType[DataType["HERO_EP"] = 999] = "HERO_EP";
    DataType[DataType["BUSINESS_ITEM"] = 1000] = "BUSINESS_ITEM";
    //205   家具 //206  图纸 // 207 材料 // 208  积分
    DataType[DataType["HOMEPART_F_205"] = 205] = "HOMEPART_F_205";
    DataType[DataType["HOMEPART_W_206"] = 206] = "HOMEPART_W_206";
    DataType[DataType["HOMEPART_D_207"] = 207] = "HOMEPART_D_207";
    DataType[DataType["HOMEPART_J_208"] = 208] = "HOMEPART_J_208";

})(DataType = exports.DataType || (exports.DataType = {}));

var ItemType;
(function (ItemType) {
    ItemType[ItemType["NORMAL"] = 1] = "NORMAL";
    ItemType[ItemType["WIFE_LOVE"] = 3] = "WIFE_LOVE";
    ItemType[ItemType["WIFE_FLOWER"] = 4] = "WIFE_FLOWER";
    ItemType[ItemType["HERO_PROP_EXP"] = 5] = "HERO_PROP_EXP";
    ItemType[ItemType["HERO_SKILL_EXP"] = 6] = "HERO_SKILL_EXP";
    ItemType[ItemType["HERO_PROP_UP"] = 7] = "HERO_PROP_UP";
    ItemType[ItemType["CHENGHAO"] = 10] = "CHENGHAO";
    ItemType[ItemType["HUODONG"] = 11] = "HUODONG";
    ItemType[ItemType["PROP_ADD"] = 13] = "PROP_ADD";
    ItemType[ItemType["ADD_PROP"] = 14] = "ADD_PROP";
    ItemType[ItemType["GIFT"] = 15] = "GIFT";
    ItemType[ItemType["MARRY_ITEM"] = 16] = "MARRY_ITEM";
    ItemType[ItemType["COOK_ITEM"] = 100] = "COOK_ITEM";
    ItemType[ItemType["FOOD_ITEM"] = 101] = "FOOD_ITEM";
    ItemType[ItemType["TREASURE_CLIP"] = 103] = "TREASURE_CLIP";
    ItemType[ItemType["TREASURE"] = 104] = "TREASURE";
    ItemType[ItemType["TRUN_TABLE"] = 105] = "TRUN_TABLE";
})(ItemType = exports.ItemType || (exports.ItemType = {}));