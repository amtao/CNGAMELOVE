var i = require("List");
var n = require("Initializer");
var l = require("Utils");
//var r = require("./ChatItem");
var r = require("ChatLabaItem");
var a = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        btnList:[cc.Button],
        lblCount:cc.Label,
        lblFontSize:cc.Label,
        editContext:cc.EditBox,
        nodeTab:cc.Node,
        nodeContext:cc.Node,
        chatItem:cc.Node,
        scroll:cc.ScrollView,
        nodeUnsend:cc.Node,
        ndoeSend:cc.Node,
        list:i,
        nodeUnread:cc.Node,
        lblUnread:cc.Label,
        labaItem:r,
        lblLBCount:cc.Label,
        labaSprite:cc.Sprite,
        labaCheck:cc.Toggle,
        nodeLabaTip:cc.Node,
        seColor: cc.Color,
        norColor: cc.Color,
        buttonclose:cc.Button,
    },

    ctor() {
        this.curIndex = 0;
        this._renders = [];
        this._chatitemId = 0;
        this._unreadCount = 0;
        this._isSend = !1;
    },
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            this.curIndex = parseInt(t.type) - 1;
            this.onClickBtn(null, t.type);
        }
        this._chatitemId = l.utils.getParamInt("chat_item");
        this.chatItem.active = !1;
        this.btnList[2].node.active = !1;
        this.btnList[1].node.active = null != n.unionProxy.clubInfo;
        this.btnList[0].interactable = !1;
        this.btnList[4].interactable = !0;
        this.upItemList();
        this.UPDATE_SCROLL_TO_BOT();
        this.updateLabaMsg();
        this.updateLabaCount();
        facade.subscribe(n.chatProxy.UPDATE_BLACK_MSG, this.UPDATE_BLACK_MSG, this);
        facade.subscribe(n.chatProxy.UPDATE_CLUB_MSG, this.UPDATE_CLUB_MSG, this);
        facade.subscribe(n.chatProxy.UPDATE_KUAFU_MSG, this.UPDATE_KUAFU_MSG, this);
        facade.subscribe(n.chatProxy.UPDATE_NOR_MSG, this.UPDATE_NOR_MSG, this);
        facade.subscribe(n.chatProxy.UPDATE_SYS_MSG, this.UPDATE_SYS_MSG, this);
        facade.subscribe(n.chatProxy.UPDATE_LABA_MSG, this.updateLabaMsg, this);
        facade.subscribe(n.chatProxy.UPDATE_SCROLL_TO_BOT, this.UPDATE_SCROLL_TO_BOT, this);
        facade.subscribe(n.chatProxy.UPDATE_SCROLL_TO_TOP, this.UPDATE_SCROLL_TO_TOP, this);
        facade.subscribe(n.bagProxy.UPDATE_BAG_ITEM, this.updateLabaCount, this);
        this.editContext.placeholder = i18n.t("COMMON_INPUT_TXT");
        this.schedule(this.onSendAdok, 5);
        this.onSelect(this.curIndex);
    },
    updateLabaCount() {
        var t = l.utils.getParamInt("speaker_itemid");
        this.lblLBCount.string = n.bagProxy.getItemCount(t) + "";
    },
    onSendAdok() {
        n.chatProxy.sendChatAdok();
    },
    delayCreate() {
        for (var t = n.chatProxy.getMsg(this.getMsgType(this.curIndex)).length, e = [], o = 0; o < t - this._renders.length; o++) {
            var i = cc.instantiate(this.chatItem),
            l = i.getComponent(r);
            i.active = !0;
            this.nodeContext.addChild(i);
            i.setSiblingIndex(0);
            e.push(l);
        }
        for (o = 0; o < this._renders.length; o++) e.push(this._renders[o]);
        this._renders = e;
        this.renderMsg();
    },
    upItemList() {
        this.lblCount.string = n.bagProxy.getItemCount(this._chatitemId) + "";
    },
    onClickBlack() {
        l.utils.openPrefabView("chat/ChatBlackView");
    },
    onClickSend() {
        var t = this.editContext.string;
        t = this.editContext.string.trim();
        t = this.editContext.string.replace(/[\n\r]/g, "");
        if (a.funUtils.isOpenFun(a.funUtils.chatView)) if (l.stringUtil.isBlank(t)) l.alertUtil.alert18n("chat_EMPTY");
        else {
            if (this.labaCheck.interactable && this.labaCheck.isChecked) {
                var e = l.utils.getParamInt("speaker_itemid");
                if (n.bagProxy.getItemCount(e) < 1) {
                    l.alertUtil.alertItemLimit(e);
                    return;
                }
            }
            this._isSend = !0;
            n.chatProxy.sendChat(t.substr(0, 60), this.curIndex, this.labaCheck.interactable && this.labaCheck.isChecked ? 1 : 0);
            this.editContext.string = "";
            this.onEditChange(null, null);
        } else {
            var o = localcache.getItem(localdb.table_iconOpen, a.funUtils.chatView.id);
            l.alertUtil.alert(o.errmsg);
        }
    },
    onClickBtn(t, e) {
        this.onSelect(parseInt(e) - 1);
    },
    onEditChange(t, e) { - 1 == this.editContext.string.indexOf("\n") ? (this.lblFontSize.string = this.editContext.string.length + "/60") : this.onClickSend();
    },
    onSelect(t, e) {
        void 0 === e && (e = !0);
        this.nodeUnsend.active = 3 == t || 4 == t;
        this.ndoeSend.active = 3 != t && 4 != t;
        this.curIndex = t;
        var temparr = []
        let chooseidx = 0;
        for (var o = 0; o < this.btnList.length; o++)
        {
            this.btnList[o].interactable = o != t;
            if (this.btnList[o].node.active){
                temparr.push(o);
                if (o == t){
                    chooseidx = temparr.length -1
                }
            }
        }
        for (var o = 0;o < temparr.length;o++){
            this.btnList[temparr[o]].node.getChildByName("btn_bac").active = (temparr[o]==t);
            if (o == temparr.length - 1){
                this.btnList[temparr[o]].node.getChildByName("img_line").active = false;
            }
            else{
                this.btnList[temparr[o]].node.getChildByName("img_line").active = (temparr[o] != t && chooseidx-1 != o);
            }
            this.btnList[temparr[o]].node.getChildByName("btnName").color = temparr[o] == t ? this.seColor : this.norColor;
        }
        this.labaCheck.interactable = 0 == t;
        this.updateLaba();
        var i = !e && this.scroll.getScrollOffset().y + this.scroll.node.height < this.list.node.height - this.list.item.node.height;
        this.renderMsg(i); (i && !this._isSend) || this.scheduleOnce(this.UPDATE_SCROLL_TO_BOT, 0.1);
    },
    updateLaba() {
        this.nodeLabaTip.active = this.labaCheck.isChecked && this.labaCheck.interactable;
    },
    renderMsg(t) {
        void 0 === t && (t = !1);
        var e = n.chatProxy.getMsg(this.getMsgType(this.curIndex));
        this.nodeUnread.active = !1;
        if (t) {
            var o = this.list.data ? this.list.data.length: 0;
            this.nodeUnread.active = e.length > o;
            this._unreadCount = this._unreadCount + e.length - o;
            this.lblUnread.string = i18n.t("CAHT_UNREAD", {
                d: this._unreadCount
            });
        }
        this.list.data = e;
    },
    getMsgType(t) {
        switch (t) {
        case 0:
            return n.chatProxy.norMsg;
        case 1:
            return n.chatProxy.clubMsg;
        case 2:
            return n.chatProxy.kuafuMsg;
        case 3:
            return n.chatProxy.sysMsg;
        case 4:
            return n.chatProxy.blackMsg;
        }
        return null;
    },
    updateScollChange() {
        if (this.nodeUnread.active && this.scroll.getScrollOffset().y + this.scroll.node.height > this.list.node.height - this.list.item.node.height) {
            this.nodeUnread.active = !1;
            this._unreadCount = 0;
            this._isSend = !1;
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    updateLabaMsg() {
        this.labaItem.node.active = n.chatProxy.laba && n.chatProxy.laba.length > 0 && n.chatProxy.laba[0].time + 3600 > l.timeUtil.second;
        this.labaItem.node.active && (this.labaItem.addLabaData(n.chatProxy.laba[0]));
        //this.labaItem.node.active && (this.labaItem.data = n.chatProxy.laba[0]);
    },
    UPDATE_BLACK_MSG() {
        this.updateShow(4);
    },
    UPDATE_CLUB_MSG() {
        this.updateShow(1);
    },
    UPDATE_KUAFU_MSG() {
        this.updateShow(2);
    },
    UPDATE_NOR_MSG() {
        this.updateShow(0);
    },
    UPDATE_SYS_MSG() {
        this.updateShow(3);
    },
    updateShow(t) {
        if (t == this.curIndex) {
            this.onSelect(this.curIndex, !1);
            this.updateLabaMsg();
        }
    },
    UPDATE_SCROLL_TO_BOT() {
        this.nodeUnread.active = !1;
        this._unreadCount = 0;
        this._isSend = !1;
        this.scroll.scrollToBottom();
    },
    UPDATE_SCROLL_TO_TOP() {
        this.scroll.scrollToTop();
    },
});
