var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var Utils = require("Utils");

cc.Class({

    extends: cc.Component,

    properties: {
        head: UrlLoad,
        headUrl: UrlLoad,
        headFrame: UrlLoad,
    },

    ctor() {
        this._headId = 0;
        this._headFrameId = 0;
        this._isUser = true;
    },

    onLoad() {
        facade.subscribe(Initializer.playerProxy.PLAYER_UPDATE_HEAD, this.updateUser, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_SHOW_CHANGE_UPDATE, this.updateRoleShow, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_RESET_JOB, this.updateRoleShow, this);
    },

    /**设置女主的头像*/
    setHead(headData, clothData, isuser, isSys) {    
        this._isUser = isuser;
        let blankId = (headData == null || headData.blank == null || headData.blank == 0) ? 1 : headData.blank;
        this._headFrameId = blankId + 0;
        let blankCfg = localcache.getItem(localdb.table_userblank, blankId);
        this.headFrame.url = UIUtils.uiHelps.getBlank(blankCfg ? blankCfg.blankmodel: 1);
        if (isSys) {
            this.setLocalchatImageToHead();
            return;
        }
        this.head.url = "";
        //if (headData == null || headData.head == null || headData.head == 0) {
            this._headId = 0;
            if (!isuser) {
                if (clothData == null) {
                    this.headUrl.url = "";
                    let defaultId = Utils.utils.getParamInt("morentouxiang");
                    let headCfg = localcache.getItem(localdb.table_userhead, defaultId);
                    this.head.url = UIUtils.uiHelps.getAvatar(headCfg ? headCfg.headres : 1);
                    return;
                }
            }
            this.clothData = clothData;
            Initializer.playerProxy.loadPlayerSpinePrefab(this.headUrl, clothData);
            return;
        //}
        this._headId = headData.head + 0;
        this.headUrl.url = "";
        let headCfg = localcache.getItem(localdb.table_userhead, headData.head);
        this.head.url = UIUtils.uiHelps.getAvatar(headCfg ? headCfg.headres : 1);
    },

    setLocalchatImageToHead(){
        this.headUrl.url = "";
        this.head.url = UIUtils.uiHelps.getChatLocalSprite("chat_sys");
    },

    /**头像更新晋升*/
    updateUser() {
        if (!this._isUser) return;
        this.setHead(Initializer.playerProxy.headavatar, null, true);
    },

    /**人物晋升刷新*/
    updateRoleShow(){
        if (!this._isUser || Initializer.playerProxy.headavatar.head != 0) return;
        Initializer.playerProxy.loadPlayerSpinePrefab(this.headUrl);
    },

    
});
