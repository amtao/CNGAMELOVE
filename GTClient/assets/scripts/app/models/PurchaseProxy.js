let initializer = require("Initializer");
let scRedDot = require("RedDot");
var scUtils = require("Utils");

var PurchaseProxy = function() {

    this.gift = null;
    this.PURCHASE_DATA_UPDATA = "PURCHASE_DATA_UPDATA";
    this.PURCHASE_IS_BUY = "PURCHASE_IS_BUY";
    this.UPDATE_BANK_INFO = "UPDATE_BANK_INFO";
    this.limitBuy = !1;
    this.limitGiftBuy = !1;
    this._lastid = 0;
    this.bRed = !1;
    this.boughtInfo = null;
    this.bankInfo = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.zchuodong.Gift, this.onUpGift, this);
        JsonHttp.subscribe(proto_sc.bank.bankInfo, this.onBankInfo, this);
        JsonHttp.subscribe(proto_sc.giftBag.buy, this.onShowLimitGift, this);
        facade.subscribe(initializer.playerProxy.PLAYER_LEVEL_UPDATE, this.updateBank, this);
    };

    this.clearData = function() {
        this.gift = null;
        this.limitBuy = !1;
        this.limitGiftBuy = !1;
        this._lastid = 0;
        this.bRed = !1;
        this.boughtInfo = null;
        this.bankInfo = null;
    };

    this.onUpGift = function(t) {
        if(null != t.cfg) {
            this.gift = t.cfg;
        }
        this.bRed = !t.clickInfo;
        this.boughtInfo = t.shop;
        scRedDot.change("giftLimit", this.bRed);
        facade.send(this.PURCHASE_DATA_UPDATA);
        this.limitBuy = !1;
    };

    this.onBankInfo = function(val) {
        this.bankInfo = val;
        this.updateBank();
    };

    this.updateBank = function() {
        scRedDot.change("bankReward", this.hasBankReward());
        facade.send(this.UPDATE_BANK_INFO);   
    };

    this.getGifts = function(index) {
        if(null == this.gift) {
            return null;
        }
        let array = [];
        let gifts = initializer.purchaseProxy.gift;
        for (let k = 0; k < gifts.length; k++) {
            let data = gifts[k];
            let bAdd = null == data.type && data.acttype == (index + 1);
            if(bAdd) {
                if(index + 1 == 2) { //新手
                    if(null == this.boughtInfo[data.id]
                     || data.limit > this.boughtInfo[data.id]) {
                        data.count = null == this.boughtInfo[data.id]
                         ? 0 : this.boughtInfo[data.id];
                        array.push(data); 
                    }
                } else {
                    data.count = null == this.boughtInfo[data.id]
                     ? 0 : this.boughtInfo[data.id];
                    array.push(data);
                }
            }
        }
        let self = this;
        array.sort((a, b) => {
            let i = a.limit - self.boughtInfo[a.id] > 0;
            if (i != b.limit - self.boughtInfo[b.id] > 0) return i ? -1 : 1;
            let j = a.end - scUtils.timeUtil.second <= 31536e3;
            return j != b.end - scUtils.timeUtil.second <= 31536e3 ? j ? -1 : 1 : a.id - b.id;
        });
        return array;
    };

    this.getBoughtNum = function(id) {
        if(this.boughtInfo[id]) {
            return this.boughtInfo[id];
        } else {
            return 0;
        }
    };

    this.setGiftNum = function(t, e) {
        t = 0 == t ? this._lastid : t;
        this._lastid = t;
        if(this.boughtInfo[t] == null);
            this.boughtInfo[t] -= e;
        if(this.boughtInfo[t] <= 0)
            this.boughtInfo[t] = e;
        for(let i = 0, len = this.gift.length; i < len; i++) {
            if(this.gift[i].id == t) {
                this.gift[i].count = null == this.boughtInfo[t] ? 0 : this.boughtInfo[t];
                break;
            }
        }
        e > 0 && (this._lastid = 0);
        facade.send(this.PURCHASE_DATA_UPDATA);
    };

    this.sendOpenPrince = function() {
        JsonHttp.send(new proto_cs.huodong.hd6180Info());
    };

    this.sendBuy = function() {
        if (0 != this._lastid) {
            var t = new proto_cs.huodong.hd6180buy();
            t.id = this._lastid;
            JsonHttp.send(t);
            this._lastid = 0;
        }
    };

    this.hasRed = function() {
        var t = !1,
            e = this.gift.length;
        if (this.gift)
            for (var o = 0; o < e; o++) this.gift[o].limit > 0 && (t = !0);
        return t;
    };

    this.reqBankRwd = function(id) {
        let req = new proto_cs.fuli.pickBankAward();
        req.id = id;
        JsonHttp.send(req, () => {
            initializer.timeProxy.floatReward();
        });
    };

    this.isCanShowBank = function() {
        let result = false;
        let bankInfo = this.bankInfo;
        if(null != bankInfo && null != bankInfo.pickInfo) {
            let list = localcache.getFilters(localdb.table_giftpack, 'type', 3);
            for(let i = 0, len = list.length; i < len; i++) {
                if(null == bankInfo.pickInfo[list[i].id]) {
                    result = true;
                    break;
                }
            }
        } else {
            result = true;
        }
        return result;
    };

    this.hasBankReward = function() {
        let val = false;
        let listData = localcache.getFilters(localdb.table_giftpack, 'type', 3);
        let pickInfo = this.bankInfo.pickInfo;
        let level = initializer.playerProxy.userData.level;
        for(let i = 0, len = listData.length; i < len; i++) {
            let data = listData[i];
            let bGot = null != pickInfo && pickInfo[data.id];
            if(!bGot && level >= data.set) {
                val = true;
                break;
            }
        }
        return val && null != this.bankInfo && null != this.bankInfo.buyTime;
    };

    //弹出礼包
    this.onShowLimitGift = function(data) {
        this.LimitGiftData = data;
        this.limitGiftBuy = !1;
        this.handleLimitData();
        // if(data.isPop == 1) {
        //     scUtils.utils.openPrefabView("purchase/LimitPurchaseView");
        // }
    };

    this.handleLimitData = function() {
        let limitData = this.LimitGiftData;
        this.limitArray = [];
        let index = 0;
        this.LimitCountDown = null;
        for(let i in limitData.pop) {
            let cfgData = localcache.getItem(localdb.table_gift_bag, i);
            if(cfgData) {
                let data = {};
                scUtils.utils.copyData(data, limitData.pop[i]);
                data.cfgData = cfgData;
                data.id = Number(i);
                data.buyData = null != limitData.buy[i] ? limitData.buy[i] : 0;            
                data.bShow = this.getLimitCanBuy(data, cfgData);
                if(data.bShow) {
                    data.index = index;
                    this.limitArray.push(data);
                    if(null == this.LimitCountDown || this.LimitCountDown > (data.popTime + cfgData.duration)) {
                        this.LimitCountDown = data.popTime + cfgData.duration;
                    } else if(!scUtils.stringUtil.isBlank(cfgData.actid) && initializer.limitActivityProxy.isHaveIdActive(cfgData.actid)
                     && initializer.limitActivityProxy.getActivityData(cfgData.actid).eTime > scUtils.timeUtil.second
                     && this.LimitCountDown > initializer.limitActivityProxy.getActivityData(cfgData.actid).eTime) {
                        this.LimitCountDown = initializer.limitActivityProxy.getActivityData(cfgData.actid).eTime;
                    }
                    index++;
                }
            }
        }    
        if(null != this.LimitCountDown) {
            let self = this;
            scUtils.timeUtil.addCountEvent(true, this.LimitCountDown, "pop_gift", () => {
                initializer.playerProxy.sendAdok("pop_gift");
                self.handleLimitData();
            });
        }
        facade.send("UPDATE_LIMIT_GIFT");
    };

    //获取是否可以购买
    this.getLimitCanBuy = function(data, cfgData) {
        let limitData = this.LimitGiftData;
        if(null == limitData.pop[data.id]) {
            return false;
        }
        //礼包本身时间未过期并且限购次数没用完并且如果是活动礼包活动时间没结束或者不是活动礼包
        let bAct = !scUtils.stringUtil.isBlank(cfgData.actid);
        return (data.popTime + cfgData.duration) > scUtils.timeUtil.second
         && cfgData.limit > data.buyData && (!bAct || (bAct && initializer.limitActivityProxy.isHaveIdActive(cfgData.actid)
         && initializer.limitActivityProxy.getActivityData(cfgData.actid).eTime > scUtils.timeUtil.second));
    };
}
exports.PurchaseProxy = PurchaseProxy;
