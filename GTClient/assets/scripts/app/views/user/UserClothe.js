var i = require("List");
var l = require("Utils");
var r = require("Initializer");
var a = require("ConfirmView");
var s = require("ApiUtils");
var c = require("UrlLoad");
var _ = require("UIUtils");
var d = require("Config");
var u = require("TimeProxy");

cc.Class({
    extends: cc.Component,

    properties: {
        list: i,
        roleSpine: c,
        nodeTab: cc.Node,
        nodeList: cc.Node,
        btns: [cc.Button],
        btnSave: cc.Button,
        //bgUrl: c,
        nodeBot: cc.Node,
        nodeTop: cc.Node,
        nodeRight: cc.Node,
        nodeShare: cc.Node,
        lblScore: cc.Label,
        btnShare: cc.Node,
        nodeRole: cc.Node,
        nodeNothing: cc.Node,
        nodeShareLogo:cc.Node,
        nodeQRCode:cc.Node,
        nodeTop2:cc.Node,
        spine:sp.Skeleton,
        spineHou:sp.Skeleton,
        nMenu: cc.Node,
        nFuyueRate: cc.Node,
        nFuyueTip: cc.Node,
        lbFuyueTip: cc.Label,
        nodeGet:cc.Node,
    },

    ctor() {
        this.typeStrs = [
                "",
                "USER_CLOTHE_HEAD",
                "USER_CLOTHE_DRESS",
                "USER_CLOTHE_EAR",
                "USER_CLOTHE_BG",
                "USER_CLOTHE_EFF",
                "USER_CLOTHE_ANIMAL",                
            ];
        this._orgBody = 0;
        this._orgHead = 0;
        this._orgEar = 0;
        this._orgBg = 0;
        this._orgEff = 0;
        this._orgAnimal = 0;
        this._body = 0;
        this._head = 0;
        this._ear = 0;
        this._bg = 0;
        this._eff = 0;
        this._animal = 0;
        this._curIndex = 1;
        this._curType = 0;//1.晋升，2.玉如意，3.活动
        this._curUnlockState = 1;//1.拥有，2.未拥有
        this._curData = null;
        this._orgNodeRoleX = 0;
        this._model = 1;//传入的类型 1为女主换装 2为赴约的换装
    },

    onLoad() {
        this.btnShare && (this.btnShare.active = d.Config.isShowShare);
        // if (this.btnSuit) {
        //     var t = localcache.getList(localdb.table_usersuit);
        //     this.btnSuit.active = t && t.length > 0;
        // }
        this.nodeRole && (this._orgNodeRoleX = this.nodeRole.x);
        this.nodeGet.active = false;
        this.updateCurClothe(r.playerProxy.userClothe);
        // this.setRoleShow();
        this.onClickBack();
        this.updateScore();
        facade.subscribe(r.playerProxy.PLAYER_CLOTH_UPDATE, this.updateShow, this);
        facade.subscribe(r.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.updateShowCloth, this);
        facade.subscribe(r.playerProxy.PLAYER_RESET_JOB, this.setRoleShow, this);
        facade.subscribe(r.playerProxy.PLAYER_CLOTHE_SCORE, this.updateScore, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClost, this);
        facade.subscribe("SHARE_SUCCESS", this.onShareShow, this);
        facade.subscribe("SHOP_BUY_ITEM_ID", this.onShopBuy, this);
        facade.subscribe("UPDATE_CLOTHE_SPECIALINFO",this.updateShow,this);
        var e = this.node.openParam;
        e && e.tab && this.onClickItem(null, e.tab);

        if (e && e.model != null) { 
            this._model = e.model;
        }
        let bFuyue = this._model == r.fuyueProxy.USERCLOTH_MODEL.FUYUE;
        this.nMenu.active = !bFuyue;
        this.nFuyueRate.active = bFuyue;
        if(bFuyue) {
            this.updateCurClothe(r.fuyueProxy.pSelectUserClothe);
            this.setRoleShow();
            this.checkCondition(r.fuyueProxy.pSelectUserClothe);
            facade.subscribe(r.fuyueProxy.REFRESH_USERCLOTH, this.checkCondition, this);
            facade.subscribe("TEMP_REFRESH_SELECT", this.checkSingleCondition, this);
        }

        if (e && e.hideSpine) {
            this.spine.setAnimation(0, "animation3", true);
        } else {
            this.initSpineEvent();
            this.playSpineAni("animation1");
        }
    },

    //保存整套的时候显示提示
    checkCondition: function(data) {
        if(null == data) {
            return;
        }
        let fuyueProxy = r.fuyueProxy;
        let conditionData = fuyueProxy.checkSuitCondition(data),
            clotheCondition = conditionData.clotheCondition,
            suitCondition = conditionData.suitCondition,
            tips = (clotheCondition && clotheCondition >= suitCondition) //有clothe的提示优先显示clothe的
             ? fuyueProxy.getConditionStr(fuyueProxy.conditionType.usercloth, clotheCondition)
             : fuyueProxy.getConditionStr(fuyueProxy.conditionType.suit, suitCondition);
        let bHasTip = tips != null;
        this.nFuyueTip && (this.nFuyueTip.active = bHasTip);
        if(bHasTip) {
            let ani = this.nFuyueTip.getComponent(cc.Animation);
            ani && ani.play("fuyue_tip_ani");
        }
        this.lbFuyueTip && (this.lbFuyueTip.string = bHasTip ? tips : " ");
    },

    //每件衣服单独换的时候的情况
    checkSingleCondition: function(data) {
        let fuyueProxy = r.fuyueProxy;
        if(data.set != fuyueProxy.conditionType.usercloth) {
            return;
        }

        let value = data.val;
        let suitCondition = null;
        let clotheCondition = null;
        if(value.clother) { //套装
            suitCondition = fuyueProxy.checkCondition(fuyueProxy.conditionType.suit, value.type);
            for(let i = 0, len = value.clother.length; i < len; i++) {
                let clotheData = localcache.getItem(localdb.table_userClothe, value.clother[i]);
                if(clotheData && clotheData.part == r.playerProxy.PLAYERCLOTHETYPE.BODY) {
                    clotheCondition = fuyueProxy.checkCondition(fuyueProxy.conditionType.usercloth, clotheData.id);
                    break;
                }
            }
        } else {
            let clotheId = value.id;
            if(value.part == r.playerProxy.PLAYERCLOTHETYPE.BODY) {
                clotheCondition = fuyueProxy.checkCondition(fuyueProxy.conditionType.usercloth, clotheId);
            }
            suitCondition = fuyueProxy.getSuitCondition(clotheId);
            if(null == suitCondition) {
                suitCondition = fuyueProxy.checkCondition(fuyueProxy.conditionType.suit, 0);
            }
        }
        let tips = (clotheCondition && clotheCondition >= suitCondition) //有clothe的提示优先显示clothe的
         ? fuyueProxy.getConditionStr(fuyueProxy.conditionType.usercloth, clotheCondition)
         : fuyueProxy.getConditionStr(fuyueProxy.conditionType.suit, suitCondition);
        let bHasTip = tips != null;
        this.nFuyueTip && (this.nFuyueTip.active = bHasTip);
        if(bHasTip) {
            let ani = this.nFuyueTip.getComponent(cc.Animation);
            ani && ani.play("fuyue_tip_ani");
        }
        this.lbFuyueTip && (this.lbFuyueTip.string = bHasTip ? tips : " ");
    },



    playSpineAni(aniname){
        if (this.spine.animation == aniname) return;
        if (this.spineHou.animation == aniname) return;
        this.nodeRole.active = false;
        this.nodeTop.active = false;
        this.nodeRight.active = false;
        this.nodeBot.active = false;
        this.nodeTop2.active = false;
        //this.bgUrl.node.active = false;
        this.spine.animation = aniname;
        this.spineHou.animation = aniname;
    },

    initSpineEvent(){
        let self = this;
        this.spine.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            if (self.spine == null) return;
            switch(aniName){
                case 'animation1':{
                    self.nodeRole.active = true;
                    self.nodeTop.active = true;
                    self.nodeRight.active = true;
                    self.nodeBot.active = true;
                    self.nodeTop2.active = true;
                    self.setRoleShow();
                    facade.send("GUIDE_ANI_FINISHED");
                    //self.bgUrl.node.active = true;
                }
                break;
                case 'animation2':{
                    l.utils.closeView(this, !0);
                }
                break;
            }
        });
    },

    updateShowCloth: function() {
        this.updateCurClothe(r.playerProxy.userClothe);
        this.setRoleShow();
    },

    updateCurClothe(t) {
        this._body = t ? t.body: 0;
        this._head = t ? t.head: 0;
        this._ear = t ? t.ear: 0;
        this._bg = t ? t.background: 0;
        this._eff = t ? t.effect: 0;
        this._animal = t ? t.animal: 0;
        if (0 == this._body || 0 == this._head) {
            var e = localcache.getItem(localdb.table_officer, r.playerProxy.userData.level),
            o = localcache.getItem(localdb.table_roleSkin, e.shizhuang);
            let clothArr = o.clotheid.split("|");
            for (var ii = 0; ii < clothArr.length;ii++){
                let cg = localcache.getItem(localdb.table_userClothe,clothArr[ii]);
                if (cg){
                    switch(cg.part){
                        case r.playerProxy.PLAYERCLOTHETYPE.HEAD:{
                            if (this._head == 0){
                                this._head = clothArr[ii];
                            }
                        }
                        break;
                        case r.playerProxy.PLAYERCLOTHETYPE.BODY:{
                            if (this._body == 0){
                                this._body = clothArr[ii];
                            }
                        }
                        break;
                        case r.playerProxy.PLAYERCLOTHETYPE.EAR:{
                            if (this._ear == 0){
                                this._ear = clothArr[ii];
                            }
                        }
                        break;
                    }
                }
            }
            0 == this._body && (this._body = r.playerProxy.getPartId(2, "body_0_" + o.body));
            0 == this._head && (this._head = r.playerProxy.getPartId(1, "headf_0_" + o.headf));
            0 == this._head && (this._head = r.playerProxy.getPartId(1, "headh_0_" + o.headh));
        }
        this._orgBody = this._body;
        this._orgHead = this._head;
        this._orgEar = this._ear;
        this._orgAnimal = this._animal;
        this._orgBg = this._bg;
        this._orgEff = this._eff;
    },
    updateScore() {
        this.lblScore && (this.lblScore.string = l.utils.formatMoney(r.playerProxy.clotheScore));
    },
    setRoleShow() {
        // var t = r.playerProxy.userData,
        var e = {};
        e.body = this._body;
        e.ear = this._ear;
        e.head = this._head;
        // e.animal = this._animal;
        // e.effect = this._eff;
        //this.bgUrl.node.active = 0 != this._bg;
        // if (0 != this._bg) {
        //     var o = localcache.getItem(localdb.table_userClothe, this._bg);
        //     o && (this.bgUrl.url = _.uiHelps.getStoryBg(o.model));
        // }
        // else{
        //     this.bgUrl.url = "";
        // }
        //this.roleSpine.setClothes(t.job, t.level, e);
        //console.error("e:",e)
        e.clotheSpecial = r.clotheProxy.getPlayerSuitClotheEffect(this._body);
        r.playerProxy.loadPlayerSpinePrefab(this.roleSpine,null,e)
    },
    onClickLockState(t,index)
    {
        let num = parseInt(index);
        this._curUnlockState = num;
        this.onClickItem(null, this._curIndex);
    },
    onclickTypeItem(t,index)
    {
        let num = parseInt(index);
        this._curType = num;
        this.onClickItem(null, this._curIndex);
    },
    onClickItem(t, e) {
        this.nodeList.active = !0;
        this.nodeTab.active = !1;
        this._curData = null;
        this.nodeGet.active = false;
        var o = parseInt(e);
        //this.lblType.string = i18n.t(this.typeStrs[o]);
        this._curIndex = o;
        this.updateShow();
        //for (var i = 0; i < this.btns.length; i++) this.btns[i].interactable = this._curType - 1 != i;
        this.onClickRole(null, !0);
    },
    updateShow() {
        console.error("this._curIndex:",this._curIndex)
        var t = localcache.getGroup(localdb.table_userClothe, "part", this._curIndex),
        e = [];
        if (this._curIndex == 6){
            t = localcache.getList(localdb.table_usersuit);
        }
        this._curIndex > 2 && this._curIndex < 6 && this._curUnlockState == 2 && e.push({
            id: 0,
            name: i18n.t("USER_CLOTHE_DELECT"),
            part: this._curIndex
        });
        for (var o = 0; o < t.length; o++) {
            if (t[o].show_time && "0" != t[o].show_time)
            {
                if (l.timeUtil.str2Second(t[o].show_time) > l.timeUtil.second && !r.limitActivityProxy.isHaveTypeActive(t[o].show_avid)) continue;
            }
            else if(!r.limitActivityProxy.isHaveTypeActive(t[o].show_avid))
            {
                continue;
            }
            if(t[o].display != null && 0 != t[o].display.length && -1 == t[o].display.indexOf(d.Config.pf))
            {

            } else{
                if(this._curType == 0 || t[o].unlock == this._curType){
                    let isUnlock = r.playerProxy.isUnlockCloth(t[o].id)
                    if((this._curUnlockState == 1 && isUnlock) || (this._curUnlockState == 2 && !isUnlock))
                    {
                        e.push(t[o]);
                    }
                }

            }
        }
        if (e.length == 1 && this._curIndex > 2 && this._curIndex < 6 && this._curUnlockState == 2){
            e.length = 0;
        }
        var i = {};
        if (e.length > 0){
            e.sort(function(t, e) {
                null == i[t.id] && (i[t.id] = r.playerProxy.isUnlockCloth(t.id) ? 1 : 0);
                null == i[e.id] && (i[e.id] = r.playerProxy.isUnlockCloth(e.id) ? 1 : 0);
                var o = i[t.id],
                n = i[e.id];
                return o != n ? o - n: t.id - e.id;
            });
        }
        
        var n = -1;
        if (this._curIndex != 6)
            for (o = 0; o < e.length; o++) 0 != e[o].id && ((e[o].id != this._body && e[o].id != this._ear && e[o].id != this._head && e[o].id != this._bg && e[o].id != this._eff && e[o].id != this._animal) || (n = o));
        this.list.data = e;
        this.list.selectIndex = n;
        this._curData = e[n];
        this.nodeNothing.active = e.length == 0 && this.nodeList.active == true;
    },
    clearData(){
        this._head = 0;
        this._body = 0;
        this._ear = 0;
        this._bg = 0;
        this._eff = 0;
        this._animal = 0;
    },
    onClickClothe(t, e) {
        var o = e.data;
        if (o) {
            if (this._curIndex == 6){
                this.clearData();
                for (var i = 0; i < o.clother.length; i++) {
                    var a = localcache.getItem(localdb.table_userClothe, o.clother[i]);
                    switch (a.part) {
                    case 1:
                        this._head = a.id;
                        break;
                    case 2:
                        this._body = a.id;
                        break;
                    case 3:
                        this._ear = a.id;
                        break;
                    case 4:
                        this._bg = a.id;
                        break;
                    case 5:
                        this._eff = a.id;
                        break;
                    case 6:
                        this._animal = a.id;
                    }
                }
                this.setRoleShow();
                this.nodeGet.active = false;
                return;
            }
            //var i = r.playerProxy.isUnlockCloth(o.id);
            this._curData = o;
            switch (o.part) {
            case 1:
                this._head = o.id;
                break;
            case 2:
                this._body = o.id;
                break;
            case 3:
                this._ear = o.id;
                break;
            case 4:
                this._bg = o.id;
                break;
            case 5:
                this._eff = o.id;
                break;
            case 6:
                this._animal = o.id;
            }
            if (this._head == 0) this._head = this._orgHead;
            if (this._body == 0) this._head = this._orgBody;
            // if (this._ear == 0) this._head = this._orgEar;
            this.setRoleShow();
            if(this._model == r.fuyueProxy.USERCLOTH_MODEL.FUYUE) {
                facade.send("TEMP_REFRESH_SELECT", { set: r.fuyueProxy.conditionType.usercloth, val: o });
            }
        }
        if (this._curUnlockState == 2){
            this.nodeGet.active = o != null;

        }
    },
    isCanSave() {
        return ( !! r.playerProxy.isUnlockCloth(this._body) && ( !! r.playerProxy.isUnlockCloth(this._ear) && ( !! r.playerProxy.isUnlockCloth(this._head) && ( !! r.playerProxy.isUnlockCloth(this._bg) && ( !! r.playerProxy.isUnlockCloth(this._eff) && !!r.playerProxy.isUnlockCloth(this._animal))))));
    },
    checkBuyItem() {
        if (!this.isCanSave()) {
            if (this._curIndex == 6){
                l.alertUtil.alert18n("USERCLOTHE_TIPS1");
                return;
            }
            l.alertUtil.alert18n("USER_SAVE_LOST");
            var t = [],
            e = !1;
            if (!this.checkAddClothe(this._body, t)) {
                this._body = this._orgBody;
                e = !0;
            }
            if (!this.checkAddClothe(this._head, t)) {
                this._head = this._orgHead;
                e = !0;
            }
            if (!this.checkAddClothe(this._ear, t)) {
                this._ear = this._orgEar;
                e = !0;
            }
            if (!this.checkAddClothe(this._animal, t)) {
                this._animal = this._orgAnimal;
                e = !0;
            }
            if (!this.checkAddClothe(this._bg, t)) {
                this._bg = this._orgBg;
                e = !0;
            }
            if (!this.checkAddClothe(this._eff, t)) {
                this._eff = this._orgEff;
                e = !0;
            }
            if (e) {
                l.alertUtil.alert18n("USER_CLOTHE_SAVE_NOT_BUY");
                this.setRoleShow();
                if (0 == t.length && (this._eff != this._orgEff || this._ear != this._orgEar || this._body != this._orgBody || this._bg != this._orgBg || this._animal != this._orgAnimal || this._head != this._orgHead)) return ! 0;
            }
            t.length > 0 && l.utils.openPrefabView("user/UserBuyMore", !1, t);
            return ! 1;
        }
        return ! 0;
    },
    checkAddClothe(t, e) {
        if (!r.playerProxy.isUnlockCloth(t)) {
            var o = localcache.getItem(localdb.table_userClothe, t);
            if (!o || (1 != o.unlock && 2 != o.unlock)) return ! 1;
            e.push(o);
            return ! 0;
        }
        return ! 0;
    },
    onClickSave() {
        if (this.checkBuyItem()) {
            this._orgBody = this._body;
            this._orgEar = this._ear;
            this._orgHead = this._head;
            this._orgAnimal = this._animal;
            this._orgBg = this._bg;
            this._orgEff = this._eff;
            if (this._model == r.fuyueProxy.USERCLOTH_MODEL.FUYUE){//如果是赴约换装
                let e = {};
                e.body = parseInt(this._body);
                e.ear = parseInt(this._ear);
                e.head = parseInt(this._head);
                e.animal = parseInt(this._animal);
                e.effect = parseInt(this._eff);
                e.background = parseInt(this._bg);
                e.clotheSpecial = r.clotheProxy.getPlayerSuitClotheEffect(this._body);
                r.fuyueProxy.pSelectUserClothe = e;
                l.utils.closeView(this, !0);
                facade.send(r.fuyueProxy.REFRESH_USERCLOTH, e);
                return;
            }
            r.playerProxy.sendCloth(this._head, this._body, this._ear, this._bg, this._eff, this._animal);
        }
    },
    onClickRecy() {
        this._body = this._orgBody;
        this._head = this._orgHead;
        this._ear = this._orgEar;
        this._animal = this._orgAnimal;
        this._bg = this._orgBg;
        this._eff = this._orgEff;
        this.setRoleShow();
    },
    onClickBack() {
        this.nodeList.active = !1;
        this.nodeTab.active = !0;
        this.nodeGet.active = false;
        if (null != this._curData) {
            var t = r.playerProxy.isUnlockCloth(this._curData.id);
            this._curData = null;
        }
    },
    onClickClost() {
        var t = this;
        // if (this._orgBody == this._body && this._orgEar == this._ear && this._orgHead == this._head && this._orgBg == this._bg && this._orgEff == this._eff && this._orgAnimal == this._animal) l.utils.closeView(this, !0);
        if (this._orgBody == this._body && this._orgEar == this._ear && this._orgHead == this._head && this._orgBg == this._bg && this._orgEff == this._eff && this._orgAnimal == this._animal) this.onCloseView();
        else {
            if (!this.isCanSave()) {
                //l.utils.closeView(this, !0);
                this.onCloseView();
                return;
            }
            t = this;
            l.utils.showConfirm(i18n.t("USER_CLOTHE_SAVE_CONFIRM"),
            function(e) {
                e != a.NO && r.playerProxy.sendCloth(t._head, t._body, t._ear, t._bg, t._eff, t._animal);
                // l.utils.isOpenView("user/UserClothe") && l.utils.closeView(t, !0);
                l.utils.isOpenView("user/UserClothe") && t.onCloseView();
            },
            null, null, i18n.t("USER_CLOTHE_SAVE"), i18n.t("COMMON_NO"));
        }
    },

    onCloseView(){
        if (this.node.openParam && this.node.openParam.hideSpine){
            l.utils.closeView(this, !0);
        }
        else{
            this.playSpineAni("animation2");
        }
    },
    onClickUnlock(t) {
        void 0 === t && (t = !0);
        var e = this._curData,
        o = e.money ? e.money.itemid: 0,
        i = e.money ? e.money.count: 0,
        n = r.bagProxy.getItemCount(o);
        let unlock = this._curData.unlock
        if(unlock == 4)
        {
            u.funUtils.openView(u.funUtils.clotheshop.id);
            return;
        }
        if (!t && n >= i && !r.playerProxy.isUnlockCloth(e.id)) r.playerProxy.sendUnlockCloth(e.id);
        else {
            if (1 == e.unlock && 0 != i) {
                var a = localcache.getItem(localdb.table_officer, e.para);
                l.utils.showConfirmItem(i18n.t("USER_CLOTHE_BUY_MAIN", {
                    n: a.name,
                    v: i,
                    iname: r.playerProxy.getKindIdName(1, o)
                }), o, n,
                function() {
                    n < i ? l.alertUtil.alertItemLimit(o, i - n) : r.playerProxy.sendUnlockCloth(e.id);
                },
                "USER_CLOTHE_BUY_MAIN");
            }
            2 == e.unlock && 0 != i && l.utils.showConfirmItem(i18n.t("USER_CLOTHE_BUY", {
                v: i,
                n: r.playerProxy.getKindIdName(1, o)
            }), o, n,
            function() {
                n < i ? l.alertUtil.alertItemLimit(o, i - n) : r.playerProxy.sendUnlockCloth(e.id);
            },
            "USER_CLOTHE_BUY");
        }
    },
    onClickRank() {
        // r.rankProxy.sendClotheRank();
        r.shopProxy.sendShopListMsg(2);
    },
    onClickResetJob() {
        l.utils.openPrefabView("user/UserJob");
    },
    onClickShare() {
        this.nodeTop.active = this.nodeRight.active = this.nodeBot.active = this.nodeTop2.active = !1;
        this.nodeShare && (this.nodeShare.active = !0);
        this.nodeShareLogo && (this.nodeShareLogo.active = !0);
        this.nodeQRCode && (this.nodeQRCode.active = !0);
        this.scheduleOnce(this.delayShare, 0.1);
    },
    onClickGo() {
        var t = this._curData;
        0 != t.iconopen && u.funUtils.openView(t.iconopen);
    },
    delayShare() {
        s.apiUtils.share_game("clothe");
    },
    onShareShow() {
        this.nodeTop.active = this.nodeRight.active = this.nodeBot.active = this.nodeTop2.active = !0;
        this.nodeShare && (this.nodeShare.active = !1);
        this.nodeShareLogo && (this.nodeShareLogo.active = !1);
        this.nodeQRCode && (this.nodeQRCode.active = !1);
        if (0 == this.nodeRole.x){
            l.utils.showNodeEffect(this.nodeRole, 0);
        }
    },
    onClickSuit() {
        //l.utils.openPrefabView("user/UserSuit");

    },
    onShopBuy(t) {
        this._curData && this.onClickUnlock(!1);
    },
    onClickRole(t, e) {
        // if (null != this.nodeRole && (null == e || 1 != e || 0 == this.nodeRole.x)) if (0 == this.nodeRole.x) {
        //     l.utils.showNodeEffect(this.nodeRight, 0);
        //     l.utils.showNodeEffect(this.nodeRole, 0);
        // } else if (this.nodeRole.x == this._orgNodeRoleX) {
        //     l.utils.showNodeEffect(this.nodeRight, 1);
        //     l.utils.showNodeEffect(this.nodeRole, 1);
        // }
    },

    /**点击未央阁*/
    onClickWeiYangGe(){
        l.utils.openPrefabView("user/UIWeiYangGeView");
    },

    /**点击预览*/
    onClickYuRan(){
        this.nodeTop.active = this.nodeRight.active = this.nodeBot.active = !1;
        this.nodeShare && (this.nodeShare.active = !0);
        this.nodeShareLogo && (this.nodeShareLogo.active = !1);
        this.nodeQRCode && (this.nodeQRCode.active = !1);
        l.utils.showNodeEffect(this.nodeRole, 1);
    },

    /**打开物品详情*/
    onClickGet(){
        let data = this._curData;
        let t = {id:data.id,count:1,kind:95,showSuit:true};
        l.utils.openPrefabView("ItemInfo", !1, t);
    },

    /**打开锦衣阁*/
    onClickJinYiGe(){

    },
});
