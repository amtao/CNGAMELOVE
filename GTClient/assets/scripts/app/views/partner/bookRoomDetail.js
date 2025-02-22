
var i = require("Utils");
var r = require("UrlLoad");
var l = require("UIUtils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        txt_title:cc.Label,
        txt_name:cc.Label,
        txt_level:cc.Label,
        txt_jiban:cc.Label,
        txt_friendly:cc.Label,
        btn_close:cc.Button,
        bgurlLoad:r,
        nodelist:[cc.Node],
        heroIcon:r,
        scroll: cc.ScrollView,
    },

    // LIFE-CYCLE CALLBACKS:
    ctor(){
        this._curHero = null;
        this._speed = new cc.Vec2(0, 0);
        this._off = null;
        this._offMax = null;
    },

    onLoad () {
        var heroid = this.node.openParam.id;
        this._curHero = n.servantProxy.getHeroData(heroid)
        if (this._curHero == null || this._curHero.id == null) return;
        //facade.subscribe("MAIN_SET_ACTION_CHANGE", this.setActionChange, this);
        var t = localcache.getItem(localdb.table_hero, this._curHero.id + "");
        this.bgurlLoad.url = l.uiHelps.getPartnerZoneBgImg(this._curHero.id);
        this.updateHeadIcon();
        for (var g = 0;g < this.nodelist.length;g++){
            this.nodelist[g].active = (g+1) == this._curHero.id;
        }
        if (t) {
            this.txt_title.string = i18n.t("PARTNER_ROOM",{
                name:t.name
            });
            this.txt_name.string = t.name;
            this.txt_level.string = `LV${this._curHero.level}`;
            var jiban = n.jibanProxy.getHeroJB(this._curHero.id);
            this.txt_jiban.string = `${jiban}`;
            // var love = this._curHero.love ? this._curHero.love : 0;
            // this.txt_friendly.string = `${love}`;
            this.txt_friendly.string = n.jibanProxy.getHeroJbLv(this._curHero.id).level % 1000;
        }
        n.servantProxy.checkTokenRed();
        //this._off = this.scroll.getScrollOffset();
        //this._offMax = this.scroll.getMaxScrollOffset();
        //cc.sys.isMobile && this.addEvent();
    },

    onClose(){
        i.utils.closeView(this);
    },

    updateHeadIcon(){
        this.heroIcon.url = l.uiHelps.getServantHead(this._curHero.id);
    },

    setActionChange() {
        //cc.systemEvent.setAccelerometerEnabled(s.Config.main_tuoluo_action);
    },
    addEvent() {
        // cc.systemEvent.setAccelerometerEnabled(s.Config.main_tuoluo_action);
        // var t = this,
        // e = cc.EventListener.create({
        //     event: cc.EventListener.ACCELERATION,
        //     callback: function(e, o) {
        //         if (s.Config.main_tuoluo_action) {
        //             t._speed.x = e.x;
        //             t._speed.y = e.y;
        //             if (Math.abs(t._speed.x) > 0.5 || Math.abs(t._speed.y) > 0.5) {
        //                 t._speed.x = t._speed.x < -1 ? -1 : t._speed.x;
        //                 t._speed.x = t._speed.x > 1 ? 1 : t._speed.x;
        //                 t._speed.y = t._speed.y < -1 ? -1 : t._speed.y;
        //                 t._speed.y = t._speed.y > 1 ? 1 : t._speed.y;
        //                 t.updateScroll();
        //             }
        //         }
        //     }.bind(this)
        // });
        // cc.eventManager.addListener(e, this.node);
    },
    updateScroll() {
        // this._off = this.scroll.getScrollOffset();
        // this._off.x = ((this._speed.x / 50) * this._offMax.x) / 2 - this._off.x;
        // this._off.y = (( - (this._speed.y + 0.5) / 40) * this._offMax.y) / 2 + this._off.y;
        // this._off.x = this._off.x < 0 ? 0 : this._off.x;
        // this._off.y = this._off.y < 0 ? 0 : this._off.y;
        // this._off.x = this._off.x > this._offMax.x ? this._offMax.x: this._off.x;
        // this._off.y = this._off.y > this._offMax.y ? this._offMax.y: this._off.y;
        // this.scroll.scrollToOffset(this._off);
    },


    onButtonStory(){
        i.utils.openPrefabView("partner/StoryListView",null,{id:this._curHero.id});
    },

    onButtonXinWu(){
        var ls= n.servantProxy.getXinWuItemListByHeroid(this._curHero.id);
        if (ls == null){
            i.alertUtil.alert(i18n.t("HERO_HASNOTTOKEN"));
            return;
        }
        i.utils.openPrefabView("partner/TokenListView",null,this._curHero);
    },

    onClickBackToMain(){
        i.utils.closeView(this);
        i.utils.closeNameView("partner/PartnerZoneView");
        i.utils.closeNameView("servant/ServantView");
        i.utils.closeNameView("servant/ServantLobbyView");
    },

    // onDestroy(){
    //     n.servantProxy.clearCurrentHeroId();
    //     //cc.sys.isMobile && cc.systemEvent.off(cc.EventListener.ACCELERATION, this.updateScroll, this);
    // },

    // update (dt) {},
});
