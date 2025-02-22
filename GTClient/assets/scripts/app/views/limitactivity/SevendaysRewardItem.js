var i = require("RenderListItem");
let Initializer = require("Initializer");
var Utils = require("Utils");
var r = require("List");
var TimeProxy = require("TimeProxy");
var ConfirmView = require("ConfirmView");

cc.Class({

    extends: i,

    properties: {
        lblTitle: cc.RichText,
        btnYLQ: cc.Node,
        btnGet: cc.Button,
        btnGo: cc.Button,
        nodeFinish: cc.Node,
        list: r,
        itemBg: cc.Node,
        lbDesc: cc.Label,        
    },

    ctor() {},

    // 是否需要显示（当前的次数/需要完成的次数）类型
    isNeedShowHasTaskType(t) {
        return 149==t||12==t||111==t||108==t||148==t||103==t||30==t||104==t||17==t||53==t||9==t||102==t||59==t||23==t||122==t||105==t||1==t||112==t;
    },

    showData() {
        let t = this._data;
        if (t) {                 
            var data = Initializer.sevenDaysProxy.getServerSevenTaskById(t.id);
            if(data != null) {                
                if(this.isNeedShowHasTaskType(t.type)) 
                    this.lblTitle.string = i18n.t("LIMIT_NEED_VALUE_1", {
                        name: t.msg,
                        have: Utils.utils.formatMoney(data.set),
                        need: Utils.utils.formatMoney(t.set[0])
                    })
                else
                    this.lblTitle.string = t.msg;

                this.lbDesc.string = i18n.t("SEVEN_DAYS_DESC9", {num: t.score});
                this.list.data = Initializer.sevenDaysProxy.getItemDataList(t.rwd);
                this.btnGet.node.active = false;
                this.btnGo.node.active = false;
                this.nodeFinish.active = false;

                if(data.isPick) {
                    this.nodeFinish.active = true;                    
                } else {
                    if(data.type == 116) {
                      if(data.set > t.set[0]) {
                          this.btnGo.node.active = true;
                          this.buttonGray(this.btnGo.node);
                      } else {
                          this.btnGet.node.active = true;
                          this.buttonGray(this.btnGet.node);
                      }
                    } else {
                      if(data.set < t.set[0]) {
                          this.btnGo.node.active = true;
                          this.buttonGray(this.btnGo.node);
                      } else {
                          this.btnGet.node.active = true;
                          this.buttonGray(this.btnGet.node);
                      }
                    }                    
                }
            }
        }
    },

    buttonGray(btn) {
        if(Initializer.sevenDaysProxy.iSelectDay > Initializer.sevenDaysProxy.pSevenInfo.openday) {
            btn.getComponent(cc.Button).interactable = false;
            // btn.on("click", function() {
            //     Utils.alertUtil.alert18n("SEVEN_DAYS_DESC19");
            // })
        } else {
            btn.getComponent(cc.Button).interactable = true;
        }
    },

    onClickGet() {
        // unlock recharge and vip --2020.07.21
        if(Initializer.playerProxy.userData.vip == 0) {
            var r = new Utils.ConfirmData();
            r.txt = i18n.t("SEVEN_DAYS_DESC14");
            r.target = this;
            r.handler = this.onClickOne;
            // r.color = i;
            r.skip = i18n.t("SEVEN_DAYS_DESC15");
            r.left = i18n.t("SEVEN_DAYS_DESC16");
            r.right = i18n.t("SEVEN_DAYS_DESC17");
            r.cancel = this.onClickTwo;

            if(!ConfirmView.isSkip(r)) {
                Utils.utils.openPrefabView("ConfirmView", true, r);
            } else {
                Initializer.sevenDaysProxy.sendPickTask(this._data.id);
            }        
        } else {
            Initializer.sevenDaysProxy.sendPickTask(this._data.id);
        }
    },

    onClickOne() {
        Initializer.sevenDaysProxy.sendPickTask(this._data.id);     
    },

    onClickTwo() {
        Utils.utils.closeNameView("ConfirmView");
        TimeProxy.funUtils.openView(51);
    },

    onClickGo() {                        
        var t = this._data;
        t && TimeProxy.funUtils.openView(t.jumpTo);        
    },

});
