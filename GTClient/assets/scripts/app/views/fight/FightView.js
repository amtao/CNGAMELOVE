var i = require("Utils");
var n = require("Initializer");
var l = require("FightMidItem");
var r = require("Config");
var a = require("TimeProxy");
var UIUtils = require("UIUtils");
var PlayerProxy = require("PlayerProxy");
//var ShaderUtils = require("ShaderUtils");
var UrlLoad = require("UrlLoad");
import { FIGHTBATTLETYPE } from "GameDefine";
// var guideItem = require("../../guide/GuideItem");

cc.Class({
    extends: cc.Component,

    properties: {
        lblBigName: cc.Label,
        lblDes: cc.Label,
        nodeMid: cc.Node,
        boss: l,
        lblTask: cc.Label,
        scroll: cc.ScrollView,
        //effect:cc.Node,
        btnRecord:cc.Node,
        //nodeSound:cc.Node,
        lb_army: cc.Label,
        lbChapterNum: cc.Label,
        lbChapterTitle: cc.Label,
        bg: UrlLoad,
        arrNodes: [cc.Node],
        nTaskFinished: cc.Node,
    },

    ctor(){
        this.iCurrentMmap = 0;
        this.listMid = new Array();
        this.itemWidth = 0;
        this.isStroy = false;
        this._lastMap = -1;
        this.curStorySelect = null;
        //this.lastData = new PlayerProxy.RoleData();
    },

    onLoad() {
        facade.subscribe("STORY_END", this.storyEnd, this);
        facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.showData, this);
        facade.subscribe("FIGHT_CLOES_VIEW", this.closeBtn, this);
        facade.subscribe("STORY_SELECT", this.onStorySelect, this);
        facade.subscribe("MAIN_TASK_OPEN", this.closeBtn, this);
        facade.subscribe("FIGHT_SHOW_GUIDE", this.showGuide, this);
        facade.subscribe("BATTLE_ENEMY_OVER", this.showData, this);        
        facade.subscribe("STORY_VIEW_CLOSE", this.storyviewClose, this);
        facade.subscribe(n.taskProxy.MAIN_TASK_REFESH, this.updateMainTask, this);
        facade.subscribe(n.playerProxy.PLAYER_USER_UPDATE, this.onUpdateArmy, this);
        facade.subscribe("SOUND_DOWN_LOAD_OVER", this.updateSound, this);
        n.taskProxy.setDelayShow(!0);
        this.updateMainTask();
        this.itemWidth = this.boss.node.width;
        this.showData();
        this.showGuide(!0);
        this.updateSound();
        //this.update_UserData();
        this.showBg();

        this.node.getComponent(cc.Animation).on("stop", () => {
            facade.send("GUIDE_ANI_FINISHED");
        });

        let storyIndex = i.utils.getViewIndex("StoryView");
        if(storyIndex > -1 && storyIndex < i.utils.getViewIndex("battle/FightView")) {
            i.utils.setViewIndex(r.Config.skin + "/prefabs/ui/battle/FightView", storyIndex);
        }
        n.playerProxy.updateTeamRed();
    },

    showBg() {          
        // if(n.playerProxy.userData.lastStoryId != null) {            
        //     var storyInfo = n.playerProxy.getStoryData(n.playerProxy.userData.lastStoryId);
        //     if(storyInfo != null)
        //         this.setBg(storyInfo.bg);
        // } else {
        //     var storyId = n.fightProxy.getCurrentStoryId();
        //     if (0 != storyId) {
        //         var storyInfo = n.playerProxy.getStoryData(storyId);
        //         if(storyInfo.bg != null)
        //             this.setBg(storyInfo.bg);
        //     }
        // } 

        this.setBg(n.fightProxy.getPveStoryBg());
    },

    setBg: function(bg) {
        this.bg.url = UIUtils.uiHelps.getStory(bg);      
    },

    initImage () {
        let data = this.texture.readPixels();
        this._width = this.texture.width;
        this._height = this.texture.height;
        let picData = this.gsBlure(data, this._width, this._height);
        return picData;
    },

    gsBlure(data, width, height) {
        let picData = new Uint8Array(width * height * 4);
        let rowBytes = width * 4;
        for (let row = 0; row < height; row++) {
            let srow = height - 1 - row;
            let start = srow * width * 4;
            let reStart = row * width * 4;
            // save the piexls data
            for (let i = 0; i < rowBytes; i++) {
                picData[reStart + i] = data[start + i];
            }
        }
        return picData;
    },

    getBlurColor (pos, data) {
        var RADIUS = 16;
        var size = cc.v2(500, 500);
        var color = cc.v4(0); // 初始颜色
        var sum = 0.0; // 总权重
        // 卷积过程
        for (var r = -RADIUS; r <= RADIUS; r++) { // 水平方向
          for (var c = -RADIUS; c <= RADIUS; c++) { // 垂直方向
            var target = pos + vec2(r / size.x, c / size.y); // 目标像素位置
            var weight = (RADIUS - abs(r)) * (RADIUS - abs(c)); // 计算权重
            color += data[target.x][target.y] * weight; // 累加颜色
            sum += weight; // 累加权重
          }
        }
        color /= sum; // 求出平均值
        return color;
    },

    // update_UserData() {
    //     UIUtils.uiUtils.showNumChange(this.lb_army, this.lastData.army, n.playerProxy.userData.army);
    
    //     this.lastData.army = n.playerProxy.userData.army;        
    // },

    updateSound() {
        var t = n.playerProxy.userData;
        // this.nodeSound.active = i.audioManager.isNeedDown() && t.bmap > 2 && t.bmap <= i.utils.getParamInt("main_sound_id");
        //this.nodeSound.active = false;
        var e = n.timeProxy.getLoacalValue("MAIN_SOUND_ID");
        // if (this.nodeSound.active && ((e && parseInt(e) < t.bmap) || null == e)) {
        //     n.timeProxy.saveLocalValue("MAIN_SOUND_ID", t.bmap + "");
        //     this.onClickSound();
        // }
        if (t.bmap == i.utils.getParamInt("main_sound_id") + 1) { (null == (e = n.timeProxy.getLoacalValue("MAIN_SOUND_NOT")) || parseInt(e) < t.bmap);
            n.timeProxy.saveLocalValue("MAIN_SOUND_NOT", t.bmap + "");
        }
    },

    showData() {
        var t = n.playerProxy.userData;
        this.btnRecord.active = t.bmap > 1;
        var e = localcache.getItem(localdb.table_midPve, t.mmap),
            o = localcache.getItem(localdb.table_bigPve, t.bmap),
            chapters = localcache.getGroup(localdb.table_midPve, "bmap", t.bmap);
        if (e) {
            this.lblDes.unscheduleAllCallbacks();
            this.lblBigName.unscheduleAllCallbacks();

            if(!n.fightProxy.checkIsBoss() && parseInt(e.mdtext) == 1) {
                let self = this;
                let str2 = i18n.t("FIGHT_BIG_TIP", {
                    s: o.id
                }) + " " + o.name;
                UIUtils.uiUtils.showRichText(self.lblBigName, str2, 0.005 * str2.length, () => {
                    let str1 = o.msg;
                    UIUtils.uiUtils.showRichText(this.lblDes, str1, 0.0015 * str1.length);
                });
            } else {
                this.lblDes.string = o.msg;
                this.lblBigName.string = i18n.t("FIGHT_BIG_TIP", {
                    s: o.id
                }) + " " + o.name;
            }

            if(n.fightProxy.checkIsBoss()) {
                this.lbChapterNum.string = i18n.t("FIGHT_MID_TIP", { s: chapters.length + 1 });
                this.lbChapterTitle.string = o.bossname;
            } else {
                this.lbChapterNum.string = i18n.t("FIGHT_MID_TIP", { s: e.mdtext });
                this.lbChapterTitle.string = e.mname;
            }
            this.showMidItem();
        }        
    },

    showMidItem() {
        var t = n.playerProxy.userData,
        e = localcache.getGroup(localdb.table_midPve, "bmap", t.bmap),
        o = localcache.getItem(localdb.table_bigPve, t.bmap);
        e.sort(function(t, e) {
            return t.id - e.id;
        });
        this.boss.node.active = false;
        //this.boss.node.active && (this.boss.data = o);
        for (var i = 0; i < this.listMid.length; i++) this.listMid[i].node.active = !1;
        for (i = 0; i <= e.length; i++) {
            var r = this.listMid.length > i ? this.listMid[i] : null;
            if (null == r) {
                var a = cc.instantiate(this.boss.node);
                r = a.getComponent(l);
                var child = a.getChildByName("item")
                var guideItem = child.getComponent("GuideItem")
                this.listMid.push(r);
                this.nodeMid.addChild(a);
                // if (i < e.length && n.guideProxy.guideUI && e[i].id == t.mmap && t.mmap > 1) {
                //     guideItem.btnUI = "FightView";
                //     guideItem.btnName = "item2";
                //     guideItem.key = t.mmap;
                //     // guideItem.start();
                //     n.guideProxy.guideUI.setItem("FightView" + "-" + "item" + t.mmap, guideItem);
                // }
            }
            r.node.active = !0;
            r.data = i == e.length ? o : e[i];
        }
        this.updateItemPos();
    },

    updateItemPos() {
        if(n.fightProxy.checkIsBoss()) {
            this.scroll.scrollToRight();
        } else {
            let userData = n.playerProxy.userData,
                chapters = localcache.getGroup(localdb.table_midPve, "bmap", userData.bmap),
                curMap = localcache.getItem(localdb.table_midPve, userData.mmap);
            this.scroll.scrollToPercentHorizontal(parseInt(curMap.mdtext) / (chapters.length + 1), 0.2);
        }
    },

    alertItemLimit2(t, e) {
        var msg = i18n.t("COMMON_LIMIT2_GO", {
            a: t,
            b: e
        });
        i.utils.showConfirm(msg, function() {
            a.funUtils.openView(16);
        });
    },

    onClickFight(t, e) {
        // var o = e.data;
        // if (o && o.bmap && o.id < n.playerProxy.userData.mmap) {
        //     i.alertUtil.alert18n("FIGHT_STORY_OVER");
        // } else if((o && o.bmap && o.id > n.playerProxy.userData.mmap) || (o && o.bossname && !n.fightProxy.checkIsBoss())) {
        //     i.alertUtil.alert18n("KITCHEN_UNLOCK_UNLOOK");
        // } else {
            var pveInfo = localcache.getItem(localdb.table_smallPve, n.playerProxy.userData.mmap);  
            var p = n.fightProxy.findRestrainProperty(Number(pveInfo.jisuan_number[0]))
            let epData = n.playerProxy.getUserEpData(6);
            var value = Number(epData['e'+p]) * i.utils.getParamInt("pve_restraint") / 10;
            var minus = value-Number(pveInfo.jisuan_number[1]);
            // 先判断属性克制值是否大于npc的属性
            if(minus < 0) {
                this.alertItemLimit2(i18n.t('FIGHT_DESC'+p), Math.floor(-minus));
            // 其次判断名声是否满足
            } else if (!n.fightProxy.isEnoughArmy()) {                                                                   
                i.alertUtil.alertItemLimit(4, n.fightProxy.needArmy());               
            // 满足才可进入战斗
            } else if(n.playerProxy.userData.mmap >= i.utils.getParamInt("battle_team") && !n.fightProxy.checkTeamCanFight(FIGHTBATTLETYPE.NORMAL)) {
                this.onClickTeam();
                return;
            } else {
                var l = n.fightProxy.checkIsBoss();
                if (!l && n.playerProxy.userData.army <= 0) i.alertUtil.alert18n("FIGHT_MINGSHENG_EMPTY");
                else {
                    // l && n.fightProxy.sendBossFight(0);
                    this.isStroy = n.fightProxy.checkStory();
                    if (!this.isStroy) {
                        this.isStroy = !0;
                        this.storyEnd();
                    }
                }
            }       
            
        //}
    },

    onEnable() {
        this.onBGM();
    },

    storyviewClose() {
        this.onBGM();
    },

    onBGM() {
        var flag = i.utils.findMiddleLayerName("StoryView");
        if(!flag)
            this.node.getComponent("SoundItem").enabled = true;
    },

    showGuide(t) {
        void 0 === t && (t = !1);
        var e = n.playerProxy.userData;
        if (this._lastMap != e.mmap && !this.isStroy) {
            // change new guide --2020.08.11
            // facade.send(n.guideProxy.UPDATE_TRIGGER_GUIDE, {
            //     type: 2,
            //     value: e.mmap
            // });
            this._lastMap = e.mmap;
        } !n.fightProxy.isFirstBMap() || t || n.fightProxy.checkIsBoss() || this.isStroy; //|| i.utils.openPrefabView("renmai/RenMaiView");
    },

    storyEnd() {
        this.showBg();
        //if (null == this.curStorySelect) {
            this.showData();
            this.showGuide();
            if (this.isStroy) {
                this.isStroy = !1;
                n.fightProxy.checkFight();
            }
        //} else this.storyEndSelect();
    },

    storyEndSelect() {
        if (null != this.curStorySelect) {
            var t = n.fightProxy.checkIsBoss();
            this.isStroy = !1;
            switch (this.curStorySelect.battle1) {
            case 2:
                if (t) {
                    n.fightProxy.initBMapBossData();
                    if (null == n.fightProxy.bossData) {
                        i.alertUtil.alert(i18n.t("FIGHT_NOT_FIND_BOSS"));
                        return;
                    }
                    i.utils.openPrefabView("battle/FightBossSay");
                }
                this.curStorySelect = null;
                break;
            case 3:
                var e = parseInt(n.playerProxy.userData.smap + "") + 1,
                o = localcache.getGroup(localdb.table_lunZhanSingle, "groupid", e);
                if (o && o.length > 0) {
                    n.fightProxy.initSmapData();
                    if (null == n.fightProxy.battleData) {
                        i.alertUtil.alert(i18n.t("FIGHT_NOT_FIND"));
                        return;
                    }
                    i.utils.openPrefabView("battle/FightSay", !1, {
                        type: 0,
                        id: e
                    });
                }
                this.curStorySelect = null;
                break;
            default:
                this.isStroy = !0;
            }
        }
    },

    onStorySelect(t) {
        var e = localcache.getItem(localdb.table_storySelect2, t.id);
        if (e) {
            if (2 == e.battle1 || 3 == e.battle1) {
                this.curStorySelect = e;
                return;
            }
            var o = n.fightProxy.checkIsBoss();
            this.isStroy = !1;
            switch (e.battle1) {
            case 1:
                if (!o && e.isjump != 1) {
                    for (var i = localcache.getItem(localdb.table_smallPve, parseInt(n.playerProxy.userData.smap + "") + 1), l = localcache.getGroup(localdb.table_smallPve, "mmap", i.mmap), r = 0; r < l.length; r++) 
                        n.fightProxy.sendEnemyFight(!0);
                }
                break;
            default:
                this.isStroy = !0;
            }
        }
    },   

    helpHd() {},

    closeBtn() {
        i.utils.closeView(this, !0);
        n.taskProxy.setDelayShow(!1, !1);
    },

    onClickMainTask() {
        a.funUtils.openView(a.funUtils.mainTask.id);
    },

    onClickOpen(t, e) {
        i.utils.openPrefabView(e + "", !1, {
            isTrigg: !1
        });
    },

    updateMainTask() {
        var t = n.taskProxy.mainTask,
        e = localcache.getItem(localdb.table_mainTask, t.id + "");
        e && n.taskProxy.isFiltTaskType(e.type) ? (this.lblTask.string = e ? i18n.t(r.Config.DEBUG ? "MAIN_TASK_SHOW": "MAIN_TASK_UNID_SHOW", {
            id: t.id,
            t: e.name,
            c: t.num < t.max || t.num <= 0 ? 0 : 1,
            m: 1
        }) : i18n.t("MAIN_TASK_OVER")) : (this.lblTask.string = e ? i18n.t(r.Config.DEBUG ? "MAIN_TASK_SHOW": "MAIN_TASK_UNID_SHOW", {
            id: t.id,
            t: e.name,
            c: t.num,
            m: t.max
        }) : i18n.t("MAIN_TASK_OVER"));
        this.nTaskFinished.active = t.num >= t.max;
        this.lblTask.color = t.num < t.max || t.num <= 0 ? i.utils.WHITE: cc.Color.WHITE.fromHEX("#e4fba4");
    },

    onClickTeam: function() {
        i.utils.openPrefabView("battle/BattleTeamView", null, { type: FIGHTBATTLETYPE.NORMAL });
    },

    onClickStory() {
        i.utils.openPrefabView("battle/StoryRecord");
    },

    onClickSound() {
        facade.send("DOWNLOAD_SOUND");
    },
});
