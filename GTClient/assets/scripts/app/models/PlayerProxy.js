import { EFuncOpenType,SERVER_CALLBACK_ERROR_CODE } from 'GameDefine';
var i = require("Utils");
var n = require("Initializer");
var l = require("ApiUtils");
var r = require("TimeProxy");
var a = require("RedDot");
var config = require("Config");
var bagproxy = require("BagProxy");
var UIUtils = require("UIUtils");
var roleSpine = require("RoleSpine");

var PlayerProxy = function() {

    this.PLAYER_USER_UPDATE = "PLAYER_USER_UPDATE";
    this.PLAYER_FUSER_UPDATE = "PLAYER_FUSER_UPDATE";
    this.PLAYER_GUIDE_UPDATE = "PLAYER_GUIDE_UPDATE";
    this.PLAYER_EP_UPDATE = "PLAYER_EP_UPDATE";
    this.PLAYER_LEVEL_UPDATE = "PLAYER_LEVEL_UPDATE";
    this.PLAYER_PAOMA_UPDATE = "PLAYER_PAOMA_UPDATE";
    this.PLAYER_HERO_SHOW = "PLAYER_HERO_SHOW";
    this.PLAYER_CLOTH_UPDATE = "PLAYER_CLOTH_UPDATE";
    this.PLAYER_CLOTH_SUIT_LV = "PLAYER_CLOTH_SUIT_LV";
    this.PLAYER_SHOW_CHANGE_UPDATE = "PLAYER_SHOW_CHANGE_UPDATE";
    this.PLAYER_LIMIT_TIME = "PLAYER_LIMIT_TIME";
    this.PLAYER_UPDATE_HEAD = "PLAYER_UPDATE_HEAD";
    this.PLAYER_RESET_JOB = "PLAYER_RESET_JOB";
    this.PLAYER_CLOTHE_SCORE = "PLAYER_CLOTHE_SCORE";
    this.PLAYER_QIFU_UPDATE = "PLAYER_QIFU_UPDATE";
    this.PLAYER_ADDITION_UPDATE = "PLAYER_ADDITION_UPDATE";
    this.GUANG_GAO_UPDATE = "GUANG_GAO_UPDATE";
    this.PLAYER_ALLJOBS_UPDATE = "PLAYER_ALLJOBS_UPDATE";
    this.userData = null;
    this.userEp = null;
    this.fuser = null;
    this.guide = null;
    this.paoma = null;
    this.clothes = null;
    this.limitTime = null;
    this.blanks = null;
    this.headavatar = null;
    this.blankTime = null;
    this.userClothe = null;
    this.qifuData = null;
    this.addition = null;
    this.suitlv = null;
    this.percentage = null;
    this.guanggao = null;
    this.clotheScore = 0;
    this.heroShow = 1;
    this.storyIds = [];
    this.allJobs = [];
    this._maxLv = 0;
    this._allOffice = null;
    this.allEpData = null;
    this.clotheDamage = null;
    this.falg_story_id = [1, 2, 3, 4, 6, 161, 162, 31, 35, 441, 442, 67, 68];
    this.story_falg_id = {
        1: 4,
        2: 5,
        3: 6,
        4: 7,
        6: 8,
        161: 9,
        162: 9,
        31: 10,
        35: 11,
        441: 12,
        442: 12,
        67: 13,
        68: 14,
    };
    this.arrIconOpen = [];
    this.bIconOpening = false;
    this.PRE_CREATE = 1,//进入取名loading界面
    this.ENTER_CREATE_OVER = 2,//进入取名界面
    this.CREATE_SUCCESS = 3,//完成取名
    this.MAIN_SCENE_FLAG = 15,//进入主界面

    this.PLAYERCLOTHETYPE = cc.Enum({
        /**头部*/
        HEAD:1,
        /**身体*/
        BODY:2,
        /**耳部*/
        EAR:3,
        /**套装特效*/
        SUIT_EFFECT:7,
    });

    /**女主套装的类型*/
    this.PLAYERCLOTHE_SUITTYPE = cc.Enum({
        /**锦衣行*/
        JINYIXING:1,
        /**江湖游*/
        JIANGHUYOU:2,
        /**人间事*/
        RENJIANSHI:3,
        /**异域情*/
        YIYUQING:4,

        MAX:5,
    });


    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.user.ep, this.onEpData, this);
        JsonHttp.subscribe(proto_sc.user.baseep, this.onBaseEpData, this);
        JsonHttp.subscribe(proto_sc.user.addep, this.onAddEpData, this);
        JsonHttp.subscribe(proto_sc.user.sonbaseep, this.onSonBaseEpData, this);
        JsonHttp.subscribe(proto_sc.user.sonaddep, this.onSonAddEpData, this);
        JsonHttp.subscribe(proto_sc.user.herobaseep, this.onHeroBaseEpData, this);
        JsonHttp.subscribe(proto_sc.user.heroaddep, this.onHeroAddEpData, this);
        JsonHttp.subscribe(proto_sc.user.clothebaseep, this.onClotheBaseEpData, this);
        JsonHttp.subscribe(proto_sc.user.clotheaddep, this.onClotheAddEpData, this);
        JsonHttp.subscribe(proto_sc.user.cardbaseep, this.onCardBaseEpData, this);
        JsonHttp.subscribe(proto_sc.user.cardaddep, this.onCardAddEpData, this);
        JsonHttp.subscribe(proto_sc.user.cardHeroEp,this.onCardHeroEpData,this)
        JsonHttp.subscribe(proto_sc.user.baowubaseep, this.onBaowuBaseEpData, this);
        JsonHttp.subscribe(proto_sc.user.baowuaddep, this.onBaowuAddEpData, this);
        JsonHttp.subscribe(proto_sc.user.clotheDamage, this.onClotheDamage, this);
        JsonHttp.subscribe(proto_sc.user.fuser, this.onFuserData, this);
        JsonHttp.subscribe(proto_sc.user.guide, this.onGuideData, this);
        JsonHttp.subscribe(proto_sc.user.user, this.onUserData, this);
        JsonHttp.subscribe(proto_sc.user.paomadeng, this.onPaoMa, this);
        JsonHttp.subscribe(proto_sc.user.heroShow, this.onHeroShow, this);
        JsonHttp.subscribe(proto_sc.clothe.clothes, this.onClothes, this);
        JsonHttp.subscribe(proto_sc.clothe.limittime,this.onLimitTime,this);
        JsonHttp.subscribe(proto_sc.clothe.suitlv, this.onSuitLv, this);
        JsonHttp.subscribe(proto_sc.userhead.blanks, this.onBlanks, this);
        JsonHttp.subscribe(proto_sc.userhead.headavatar,this.onHeadAvatar,this);
        JsonHttp.subscribe(proto_sc.userhead.blanktime,this.onBlankTime,this);
        JsonHttp.subscribe(proto_sc.clothe.userClothe,this.onUserClothe,this);
        JsonHttp.subscribe(proto_sc.clothe.score, this.onClotheScore, this);
        JsonHttp.subscribe(proto_sc.user.qifu, this.onQifuDat, this);
        JsonHttp.subscribe(proto_sc.user.addition, this.onAddition, this);
        JsonHttp.subscribe(proto_sc.user.percentage,this.onPercentage,this);
        JsonHttp.subscribe(proto_sc.advert.cfg,this.onUpdateGuangGao,this);
        JsonHttp.subscribe(proto_sc.day.timeStamp,this.onUpdateDayZeroTimestamp,this);
        facade.subscribe("SERVER_SPECIAL_CALLBACK_ERROR",this.specialServerError,this);
        this.storyIds = [];
    };
    this.clearData = function() {
        this.userEp = null;
        this.storyIds = [];
        this.heroShow = 1;
        this.paoma = null;
        this.guide = null;
        this.fuser = null;
        this.clothes = null;
        this.limitTime = null;
        this.blanks = null;
        this.headavatar = null;
        this.blankTime = null;
        this.userClothe = null;
        this.clotheScore = 0;
        this.suitlv = null;
        this.percentage = null;
        this.guanggao = null;
        this.allEpData = null;
        this.arrIconOpen = [];
        this.bIconOpening = false;
        this.nextDayZeroTimeStamp = 0;
        this.userData = null;
        this.clotheDamage = null;
    };

    /**处理服务器的特殊error*/
    this.specialServerError = function (data) {
        switch(data.type) {
            case SERVER_CALLBACK_ERROR_CODE.NO_CLUB:{//被踢出帮会
                n.unionProxy.clubInfo = null;
                n.unionProxy.memberInfo = null;
                a.change("union_red",false);
                a.change("unionCopy",false);
                a.change("union_party_red",false);
                a.change("unionApply",false);
                a.change("union_enter",false);
                a.change("union_donate",false);  
                i.utils.closeCommonViewsByPath("union");
                i.alertUtil.alert18n("UNION_TIPS19");
            } break;
            case SERVER_CALLBACK_ERROR_CODE.NO_CLUB_BOSS: {
                i.utils.closeNameView("union/UnionBossView");
                a.change("unionCopy",false);
                i.alertUtil.alert18n("UNION_CUR_COPY_FINISH");
            } break;
            case SERVER_CALLBACK_ERROR_CODE.UNION_PARTY_OVER: {
                n.unionProxy.closeParty();
            } break;           
            case SERVER_CALLBACK_ERROR_CODE.LOGINE_REPEAT:{
                if (n.guideProxy.guideUI)
                    n.guideProxy.guideUI.loginRepeatFlag = true;
                i.utils.showSingeConfirm(i18n.t("LOGIN_YIDIDENGLU"),
                    function() {
                        n.loginProxy.loginOut();
                    },
                    null, null, i18n.t("COMMON_YES"),null);
                    }
            break;
        }
    }

    //通过其他玩家的uid 获取其他玩家的信息
    this.getUserBaseInfo = function(uid,callback){
        var proto = new proto_cs.user.getUserBaseInfo();
        proto.uid = uid
        JsonHttp.send(proto,function(data){
            callback && callback(data);
        });
    }

    this.onSuitLv = function(t) {
        this.suitlv = t;
        facade.send(this.PLAYER_CLOTH_SUIT_LV);
        for (let ii = 0;ii < 4;ii++){
            n.clotheProxy.checkClotheTypeRed(ii+1);
        }
    };
    this.onClotheScore = function(t) {
        this.clotheScore = null == t.score ? 0 : t.score;
        facade.send(this.PLAYER_CLOTHE_SCORE);
        this.canGetClotheValueReward();
    };
    this.onUserClothe = function(t) {
        //console.error("onUserClothe:",t)
        this.userClothe = t;
        facade.send(this.PLAYER_SHOW_CHANGE_UPDATE);
    };
    this.onBlankTime = function(t) {
        this.blankTime = t;
    };
    this.onBlanks = function(t) {
        this.blanks = t;
    };
    this.onHeadAvatar = function(t) {
        this.headavatar = t;
        facade.send(this.PLAYER_UPDATE_HEAD);
    };
    this.onLimitTime = function(t) {
        this.limitTime = t;
        facade.send(this.PLAYER_LIMIT_TIME);
    };

    /**返回第二天0点的时间戳*/
    this.onUpdateDayZeroTimestamp = function(data){
        this.nextDayZeroTimeStamp = data[0];
    };

    this.onUserData = function(t) {
        cc.log(t);

        null == this.userData && (this.userData = new proto_cs.user.user());
        var e = this.userData.level,
            o = this.userData.exp,
            bmap = this.userData.bmap,
            mmap = this.userData.mmap;

        if (this.userData != null && this.userData.food != null && t.food != null && this.userData.food != t.food){
            n.unionProxy.initInteriorRed();
        }
        var lastAllJobs = this.userData.allJob;

        i.utils.copyData(this.userData, t);
        facade.send(this.PLAYER_USER_UPDATE);

        if (e != this.userData.level && 0 != e && null != e) {
            facade.send(this.PLAYER_LEVEL_UPDATE);

            let openFunc = r.funUtils.getNewOpenFunc(EFuncOpenType.Level, this.userData.level);
            this.addIconOpen(openFunc);

            l.apiUtils.levelUp();
            if(config.Config.login_by_sdk)
            {
                l.apiUtils.callSMethod3("finishUpGrade");
            }
        }
        if(this.userData.bmap != bmap && 0 != bmap && null != bmap) { //查看大章节类型解锁
            let openFunc2 = r.funUtils.getNewOpenFunc(EFuncOpenType.Chapter, bmap);
            this.addIconOpen(openFunc2);
        }
        if(this.userData.mmap != mmap) {
            if(0 != mmap && null != mmap) { //查看小章节类型解锁
                let openFunc3 = r.funUtils.getNewOpenFunc(EFuncOpenType.LittleChapter, mmap);
                this.addIconOpen(openFunc3);
            }
            this.updateTeamRed();
        }
        null == o ||
            null == e ||
            (o == this.userData.exp && e == this.userData.level) ||
            this.updateRoleLvupRed();
        bmap != this.userData.bmap && 2 == bmap && l.apiUtils.completeTutorial();

        if(this.userData.allJob !== lastAllJobs) {
            // 更新allJob
            facade.send(this.PLAYER_ALLJOBS_UPDATE);
        }
    };

    //查看所有编队是否有红点可以编队
    this.updateTeamRed = function(heroId) {
        if(null != n.fightProxy && null != n.cardProxy) {
            let canLength = 0;
            let teamDataList = localcache.getList(localdb.table_team);
            for(let i = 0, len = teamDataList.length; i < len; i++) {
                if(this.userData.mmap >= teamDataList[i].unlock) {
                    canLength ++;
                }
            }
            if(null == heroId) {
                let cards = n.cardProxy.getNewCardList();
                if(null != cards && null != n.fightProxy.fTroops && n.fightProxy.fTroops.length < canLength) {
                    let remainCards = cards.filter((data) => {
                        return n.fightProxy.fTroops.indexOf(data.id) < 0;
                    });
                    let bCanTeam = null != remainCards && remainCards.length > 0;
                    a.change("team_normal", bCanTeam);
                } else {
                    a.change("team_normal", false);
                }
            } else {
                let heroCards = n.cardProxy.getNewCardList([0, heroId]);
                if(null != heroCards && null != n.fightProxy.jTroops) {
                    let array = n.fightProxy.jTroops[heroId];
                    if(null == array || array.length < canLength) {
                        let remainCards2 = null == array ? remainCards2 : heroCards.filter((data) => {
                            return array.indexOf(data.id) < 0;
                        });
                        let bCanTeam2 = null != remainCards2 && remainCards2.length > 0;
                        a.change("team_jiaoyou", bCanTeam2);
                    } else {
                        a.change("team_jiaoyou", false);
                    }
                } else {
                    a.change("team_jiaoyou", false);
                }
            }
        }
    };

    this.updateRoleLvupRed = function() {
        var t = localcache.getItem(
            localdb.table_officer,
            this.userData.level
        );
        var t2 = localcache.getItem(
            localdb.table_officer,
            this.userData.level+1
        );
        if(!t2){
            a.change("rolelvup", false);
            return
        }
        if (null != t) {
            var e = this.userData.exp >= t.need_exp;
            if (e) {
                for (
                    var o = i.stringUtil.isBlank(t.condition)
                            ? []
                            : t.condition.split("|"),
                        n = 0;
                    n < o.length;
                    n++
                ) {
                    var l = localcache.getItem(
                        localdb.table_officerType,
                        o[n]
                    );
                    if (!this.officeLvIsOver(l)) {
                        e = !1;
                        break;
                    }
                }
                a.change("rolelvup", e);
            } else a.change("rolelvup", e);
        }else{
            a.change("rolelvup", false);
        }
    };
    this.onGuideData = function(t) {
        this.guide = t;
        facade.send(this.PLAYER_GUIDE_UPDATE);
    };
    this.onFuserData = function(t) {
        this.fuser = t;
        facade.send(this.PLAYER_FUSER_UPDATE);
    };
    this.onEpData = function(t) {
        //console.error(t);
        this.userEp = t;
        facade.send(this.PLAYER_EP_UPDATE);
    };
    this.onBaseEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['baseep'] = t;
    };
    this.onAddEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['addep'] = t;
    };
    this.onSonBaseEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['sonbaseep'] = t;
    };
    this.onSonAddEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['sonaddep'] = t;
    };
    this.onHeroBaseEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['herobaseep'] = t;
    };
    this.onHeroAddEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['heroaddep'] = t;
    };
    this.onClotheBaseEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['clothebaseep'] = t;
    };
    this.onClotheAddEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['clotheaddep'] = t;
    };
    this.onCardBaseEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['cardbaseep'] = t;
    };
    this.onCardAddEpData = function(t) {
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['cardaddep'] = t;
    };

    this.onCardHeroEpData = function(t){
        if(this.allEpData == null){
            this.allEpData = {};
        }
        this.allEpData['cardHeroEp'] = t;
    }

    this.onBaowuBaseEpData = function(t) {
        if(this.allEpData == null) {
            this.allEpData = {};
        }
        this.allEpData['baowubaseep'] = t;
    };
    this.onBaowuAddEpData = function(t) {
        if(this.allEpData == null) {
            this.allEpData = {};
        }
        this.allEpData['baowuaddep'] = t;
    };
    this.onClotheDamage = function(data) {
        this.clotheDamage = data;
    };
    this.getUserEpData = function(propType) {
        switch(propType){
            case 2:{//2-百分比伙伴属性
                return this.allEpData['herobaseep'];
            }break;
            case 3:{//3-百分比服饰属性
                return this.allEpData['clothebaseep'];
            }break;
            case 4:{//4-百分比徒弟属性
                return this.allEpData['sonbaseep'];
            }break;
            case 5:{//5-百分比卡牌属性
                return this.allEpData['cardbaseep'];
            }break;
            case 6:{//6-百分比总属性
                return this.allEpData['addep'];
            }break;
        }
        return null;
    };
    this.getPropDataByIndex = function(propIndex,epData,per) {
        switch(propIndex){
            case 1:{//1-气势
                return Math.ceil(epData.e1*per);
            }break;
            case 2:{//2-政略
                return Math.ceil(epData.e2*per);
            }break;
            case 3:{//3-智谋
                return Math.ceil(epData.e3*per);
            }break;
            case 4:{//4-魅力
                return Math.ceil(epData.e4*per);
            }break;
            case 5:{//5-全属性
                return (Math.ceil(epData.e1*per)
                        +Math.ceil(epData.e2*per)
                        +Math.ceil(epData.e3*per)
                        +Math.ceil(epData.e4*per));
            }break;
        }
        return 0;
    };


    this.onPaoMa = function(t) {
        if (null != t && 0 != t.length) {
            null == this.paoma && (this.paoma = []);
            for (var e = 0; e < t.length; e++) this.paoma.push(t[e]);
            facade.send(this.PLAYER_PAOMA_UPDATE);
        }
    };
    this.onHeroShow = function(t) {
        this.heroShow = 0 == t.id ? 1 : t.id;
        facade.send(this.PLAYER_HERO_SHOW);
    };
    this.onClothes = function(t) {
        this.clothes = t;
        facade.send(this.PLAYER_CLOTH_UPDATE);
    };
    this.onPercentage = function(t) {
        this.percentage = t;
    };
    this.onUpdateGuangGao = function(t) {
        this.guanggao = t;
        facade.send(this.GUANG_GAO_UPDATE);
    };
    this.addStoryId = function(t) {
        if (-1 == this.storyIds.indexOf(t)) {
            this.storyIds.push(t);
            facade.send("SHOW_STORY");
        }
    };

    /**小战斗发起攻击*/
    this.sendUserPveRestraint = function(cardId,callback) {
        var proto = new proto_cs.user.pveRestraint();
        proto.cardId = cardId;
        JsonHttp.send(proto,function(data){
            callback && callback(data);
        });
    };

    this.sendAdok = function(t) {
        var e = new proto_cs.user.adok();
        e.label = t;
        JsonHttp.send(e);
    };

    /**重连请求登录数据*/
    this.sendOffline =function () {
        var e = new proto_cs.guide.offline();
        JsonHttp.send(e);        
    }
    this.sendUserUp = function() {
        var t = new proto_cs.user.shengguan();
        JsonHttp.send(t, function() {
            i.utils.openPrefabView("user/UserUpEffect");
        });
    };
    this.getPartId = function(t, e) {
        var o = localcache.getGroup(localdb.table_userClothe, "part", t);
        if (null == o) return 0;
        for (var i = 0; i < o.length; i++)
            for (var n = o[i].model.split("|"), l = 0; l < n.length; l++)
                if (n[l] == e) return o[i].id;
        return 0;
    };
    this.sendHeroShow = function(t) {
        var e = new proto_cs.user.serHeroShow();
        e.id = t;
        JsonHttp.send(e);
    };

    //打点 屏蔽掉上报
    this.sendFlag = function(id){
        // console.log("打点：", id);
        // let p = new proto_cs.user.recordNewBie();
        // p.newBieId = id;
        // JsonHttp.send(p);
    };

    this.getSelectId = function(id){
        if (i.stringUtil.isBlank(id)) return null;
        let selectId = 0
        function checkNextId(storyId){
            let storyData = localcache.getItem(localdb.table_story4, storyId);
            if(storyData)
            {
                let nextId = storyData.nextid;
                checkNextId(nextId);
            }else{
                selectId = storyId;
            }
        }
        checkNextId(id)
        return selectId;
    };

    this.getStoryData = function(t) {
        if (i.stringUtil.isBlank(t)) return null;
        var e = localcache.getItem(localdb.table_story2, t);
        return (
            e ||
            ((e = localcache.getItem(localdb.table_story3, t))
                ? e : (e = localcache.getItem(localdb.table_story4, t))
                ? e : (e = localcache.getItem(localdb.table_story5, t))
                ? e : (e = localcache.getItem(localdb.table_story6, t))
                ? e : (e = localcache.getItem(localdb.table_storyzw, t))
                ? e : (e = localcache.getItem(localdb.table_gushi, t)) 
                ? e : (e = localcache.getItem(localdb.table_storyactivity, t)) ||
                  null)
        );
    };
    this.getStorySelect = function(t) {
        var e;
        if (
            null ==
            (e = localcache.getGroup(
                localdb.table_storySelect2,
                "group",
                t
            ))
        )
            return null;
        for (var o = [], i = 0; i < e.length; i++) {
            var n = e[i];
            this.stroyIsCanSelect(n.chose1 + "") && o.push(n);
        }
        return o;
    };
    this.stroyIsCanSelect = function(t) {
        if (i.stringUtil.isBlank(t)) return !0;
        var e = t.split("|");
        switch (parseInt(e[0])) {
            case 1:
                return n.bagProxy.getItemCount(parseInt(e[1])) > 0;

            case 2:
                return (
                    n.jibanProxy.getWifeJB(parseInt(e[1])) >= parseInt(e[2])
                );

            case 3:
                return (
                    n.jibanProxy.getHeroJB(parseInt(e[1])) >= parseInt(e[2])
                );

            case 4:
                return this.userData.level >= parseInt(e[1]);

            case 5:
                var o = parseInt(e[1]);
                return n.jibanProxy.getHeroSW(o) >= parseInt(e[2]);

            case 6:
                o = parseInt(e[1]);
                return n.jibanProxy.getMaxSW() <= n.jibanProxy.getHeroSW(o);

            case 7:
                return null != n.servantProxy.getHeroData(parseInt(e[1]));

            case 8:
                return null != n.wifeProxy.getWifeData(parseInt(e[1]));
        }
        return !1;
    };
    this.getEmailData = function(t) {
        return localcache.getItem(localdb.table_email, t);
    };
    this.getEmailGroup = function(t, e) {
        return localcache.getGroup(localdb.table_email, e, t);
    };
    this.getReplaceName = function(t) {
        return null == t || null == this.userData
            ? ""
            : (t = (t = t.replace("#name#", this.userData.name)).replace(
                  "#chenghu#",
                  1 == this.userData.sex
                      ? i18n.t("COMMON_DIANXIA")
                      : i18n.t("COMMON_GEGE")
              ));
    };
    this.getMaxLv = function() {
        this.initOffice();
        return this._maxLv;
    };
    this.initOffice = function() {
        if (0 == this._maxLv) {
            var t = localcache.getList(localdb.table_officer);
            this._allOffice = [];
            for (var e = 0; e < t.length; e++)
                0 != t[e].id && this._allOffice.push(t[e]);
            this._maxLv = t[t.length - 1].id;
        }
    };
    this.getAllOffice = function() {
        this.initOffice();
        return this._allOffice;
    };
    this.getOfficeDes = function(t) {
        if (null == t) return "";
        var e = "",
            o = 1;
        if (0 != t.heroid) {
            var i = localcache.getItem(localdb.table_hero, t.heroid);
            null != i &&
                (e += i18n.t("COMMON_CONTEXT_NUM", {
                    c: o++,
                    n: i18n.t("USER_LOCK_HERO", {
                        n: i.name
                    })
                }));
        }
        var n = localcache.getGroup(localdb.table_jyBase, "guanid", t.id),
            l = {};
        if (n && n.length > 0) {
            for (var r = 0; r < n.length; r++) l[n[r].type] = n[r].name;
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_QINAN_GOLD", {
                    n: t.qingAn
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIP4", {
                    n: t.max_zw
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIPSP", {
                    n: l[2],
                    c: t.max_jy
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIPSP", {
                    n: l[3],
                    c: t.max_jy
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIPSP", {
                    n: l[4],
                    c: t.max_jy
                })
            });
            t.pray > 0 &&
                (e += i18n.t("COMMON_CONTEXT_NUM", {
                    c: o++,
                    n: i18n.t("USER_SP_TIP5", {
                        n: t.pray
                    })
                }));
        } else {
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_QINAN_GOLD", {
                    n: t.qingAn
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIP1", {
                    n: t.max_jy
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIP2", {
                    n: t.max_jy
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIP3", {
                    n: t.max_jy
                })
            });
            e += i18n.t("COMMON_CONTEXT_NUM", {
                c: o++,
                n: i18n.t("USER_SP_TIP4", {
                    n: t.max_zw
                })
            });
            t.pray > 0 &&
                (e += i18n.t("COMMON_CONTEXT_NUM", {
                    c: o++,
                    n: i18n.t("USER_SP_TIP5", {
                        n: t.pray
                    })
                }));
        }
        return e;
    };
    this.getWifeName = function(t) {
        var e = localcache.getItem(localdb.table_hero, t);
        return e ? e.name : "";
    };
    this.getFirstStoryId = function() {
        var t = i.utils.getParamInt(
            (2 == this.userData.sex
                ? "guide_first_femaleid"
                : "guide_first_maleid") + this.userData.job
        );
        return 0 == t ? 1 : t;
    };
    this.getKindIdName = function(t, e) {
        switch (t) {
            case 3:
                return (n = localcache.getItem(localdb.table_wife, e))
                    ? (1 == this.userData.sex ? n.wname : n.wname2) +
                          i18n.t("COMMON_QMD")
                    : i18n.t("COMMON_QMD");

            case 4:
                return (n = localcache.getItem(localdb.table_wife, e))
                    ? (1 == this.userData.sex ? n.wname : n.wname2) +
                          i18n.t("COMMON_MLZ")
                    : i18n.t("COMMON_MLZ");

            case 5:
                return (s = localcache.getItem(localdb.table_hero, e))
                    ? s.name + i18n.t("COMMON_SJJY")
                    : i18n.t("COMMON_SJJY");

            case 6:
                return (s = localcache.getItem(localdb.table_hero, e))
                    ? s.name + i18n.t("COMMON_JNJY")
                    : i18n.t("COMMON_JNJY");

            case 7:
                return (s = localcache.getItem(localdb.table_hero, e))
                    ? i18n.t("COMMON_HDMK") + s.name
                    : "";

            case 8:
                return (n = localcache.getItem(localdb.table_wife, e))
                    ? i18n.t("COMMON_HDHY") +
                          (1 == this.userData.sex ? n.wname : n.wname2)
                    : "";

            case 9:
                return (n = localcache.getItem(localdb.table_wife, e))
                    ? (1 == this.userData.sex ? n.wname : n.wname2) +
                          i18n.t("COMMON_HGD")
                    : i18n.t("COMMON_HGD");

            case 10:
                var o = localcache.getItem(localdb.table_fashion, e);
                return o ? o.name : "";

            case 2:
                var i = localcache.getItem(localdb.table_enumItem, e);
                return i ? i.title : "";

            case 12:
                return (n = localcache.getItem(localdb.table_wife, e))
                    ? (1 == this.userData.sex ? n.wname : n.wname2) +
                          i18n.t("COMMON_WIFE_EXP")
                    : i18n.t("COMMON_WIFE_EXP");

            case 90:
                return 0 == e
                    ? i18n.t("SERVANT_ROLE_SW")
                    : (s = localcache.getItem(localdb.table_hero, e))
                    ? i18n.t("SERVANT_HERO_SW", {
                          n: s.name
                      })
                    : "";

            case 91:
                return i18n.t("SERVANT_ROLE_SW");

            case 92:
                return (s = localcache.getItem(localdb.table_hero, e))
                    ? i18n.t("SERVANT_JIBAN_HERO", {
                          n: s.name
                      })
                    : "";

            case 93:
                var n = localcache.getItem(localdb.table_wife, e);
                return s
                    ? i18n.t("SERVANT_JIBAN_WIFE", {
                          n: 1 == this.userData.sex ? n.wname : n.wname2
                      })
                    : "";

            case 94:
                var l = localcache.getItem(localdb.table_userblank, e);
                return l ? l.name : "";

            case 95:
                var r = localcache.getItem(localdb.table_userClothe, e);
                return r ? r.name : "";

            case 96:
                return localcache.getItem(localdb.table_heropve, e).name;

            case 97:
                var a = localcache.getItem(localdb.table_heroClothe, e);
                return a ? a.name : "";
            case 98:
                var a = localcache.getItem(localdb.table_userjob, e);
                return a ? a.name : "";
            case bagproxy.DataType.USER_SUIT:
                var a = localcache.getItem(localdb.table_card, e);
                return a ? a.name : "";
            case 111:
                var a = localcache.getItem(localdb.table_heroDress, e);
                return a ? a.name : "";
            case bagproxy.DataType.BAOWU_ITEM:
                var a = localcache.getItem(localdb.table_baowu, e);
                return a ? a.name : "";
            case bagproxy.DataType.BUSINESS_ITEM:
                var a = localcache.getItem(localdb.table_wupin, e);
                return a ? a.name : "";
            case bagproxy.DataType.HERO_BG:
                var a = localcache.getItem(localdb.table_herobg, e);
                return a ? a.name : "";
            case bagproxy.DataType.FISHFOOD_ITEM:
                var a = localcache.getItem(localdb.table_game_item, e);
                return a ? a.name : "";
            case 999:
                var s = localcache.getItem(localdb.table_hero, e % 1e4),
                    c = Math.floor(e / 1e4);
                return s
                    ? i18n.t("COMMON_ADD_2", {
                          n: s.name,
                          c: i18n.t("COMMON_PROP" + c)
                      })
                    : "";

            default:
                var _ = localcache.getItem(localdb.table_item, e);
                return _ ? _.name : "";
        }
        return "";
    };
    this.getRwdString = function(t) {
        for (var e = "", o = 0; o < t.length; o++) {
            var n = t[o],
                l = n.hasOwnProperty("kind") ? n.kind : 1,
                r = n.hasOwnProperty("id") ? n.id : n.itemid,
                a = n.hasOwnProperty("count") ? n.count : 1;
            e += i.stringUtil.isBlank(e) ? "" : ",";
            e += i18n.t("COMMON_ADD", {
                n: this.getKindIdName(l, r),
                c: i.utils.formatMoney(a)
            });
        }
        return e;
    };
    this.officeLvIsOver = function(t) {
        if (null == t) return !1;
        var e = t.para + "";
        switch (t.condition) {
            case 1:
                return n.taskProxy.mainTask.id > parseInt(e);

            case 2:
                return null != n.servantProxy.getHeroData(parseInt(e));

            case 3:
                return null != n.servantProxy.getHeroData(parseInt(e));

            case 4:
                return this.userData.mmap > parseInt(e);

            case 5:
                return this.userData.bmap > parseInt(e);

            case 6:
                var o = localcache.getItem(localdb.table_heropve, t.para);
                if (null == o) return !0;
                var i = n.jibanProxy.getJibanData(o.type, o.roleid);
                return null != i && i.id >= parseInt(e);

            case 7:
                var l = e.split("|");
                return (
                    n.jibanProxy.getHeroJB(parseInt(l[0])) >= parseInt(l[1])
                );

            case 8:
                l = e.split("|");
                return (
                    n.jibanProxy.getWifeJB(parseInt(l[0])) >= parseInt(l[1])
                );
        }
        return !0;
    };
    this.officeOpen = function(t) {
        switch (t.condition) {
            case 1:
                r.funUtils.openView(r.funUtils.mainTask.id);
                break;

            case 4:
            case 5:
                r.funUtils.openView(r.funUtils.battleView.id);
        }
        return !0;
    };
    this.getOfficeLvError = function(t) {
        if (null == t) return "";
        var e = t.para + "";
        switch (t.condition) {
            case 1:
                var o = localcache.getItem(localdb.table_mainTask, e);
                return i18n.t("USER_UP_LV_NEED1", {
                    n: o.name
                });

            case 2:
                var i = localcache.getItem(localdb.table_hero, e);
                return i18n.t("USER_UP_LV_NEED2", {
                    n: i.name
                });

            case 3:
                return i18n.t("USER_UP_LV_NEED3", {
                    n: this.getWifeName(t.para)
                });

            case 4:
                var n = localcache.getItem(localdb.table_midPve, e);
                return i18n.t("USER_UP_LV_NEED4", {
                    n: n.mname
                });

            case 5:
                var l = localcache.getItem(localdb.table_bigPve, e);
                return i18n.t("USER_UP_LV_NEED5", {
                    n: l.name
                });

            case 6:
                var r = localcache.getItem(localdb.table_heropve, t.para);
                return i18n.t("USER_UP_LV_NEED6", {
                    n: r.name
                });

            case 7:
                var a = e.split("|");
                i = localcache.getItem(localdb.table_hero, a[0]);
                return i18n.t("USER_UP_LV_NEED7", {
                    n: i.name,
                    v: a[1]
                });

            case 8:
                a = e.split("|");
                return i18n.t("USER_UP_LV_NEED8", {
                    n: this.getWifeName(t.para),
                    v: a[1]
                });
        }
        return "";
    };
    this.getAllEp = function() {
        return this.userEp
            ? this.userEp.e1 +
                  this.userEp.e2 +
                  this.userEp.e3 +
                  this.userEp.e4
            : 0;
    };
    this.getAllJobs = function () {
        if(this.userData.allJob)
        this.allJobs = JSON.parse(this.userData.allJob);
        return this.allJobs;
    };
    this.isUnlockCloth = function(t) {
        if (0 == t || null == t) return !0;
        var e = localcache.getItem(localdb.table_userClothe, t);
        if (null == e) return !0;
        if (this.clothes && -1 != this.clothes.indexOf(t)) {
            if (0 != e.limit)
                for (
                    var o = 0;
                    this.limitTime && o < this.limitTime.length;
                    o++
                )
                    if (
                        this.limitTime[o].id == t &&
                        this.limitTime[o].time < i.timeUtil.second
                    )
                        return !1;
            return !0;
        }
        return !(!e || 1 != e.unlock) && this.userData.level >= e.para;
    };

    /**检测套装的几个部位是否都已经解锁*/
    this.isUnlockClotheArr = function(arr){
        if (arr == null) return false;
        for (let i = 0; i < arr.length; i++){
            if (!this.isUnlockCloth(arr[i])){
                return false;
            }
        }
        return true;
    };

    this.getSuitCount = function(t) {
        var e = localcache.getItem(localdb.table_usersuit, t);
        if (null == e) return "";
        for (var o = 0, i = 0; i < e.clother.length; i++)
            this.isUnlockCloth(e.clother[i]) && o++;
        return {
            str: i18n.t("COMMON_NEED", {
                f: o,
                s: e.clother.length
            }),
            myNum: o,
            totalNum: e.clother.length
        };
    };
    this.getRemainClotheTime = function(t) {
        for (var e = 0; this.limitTime && e < this.limitTime.length; e++)
            if (this.limitTime[e].id == t)
                return this.limitTime[e].time - i.timeUtil.second;
        return 0;
    };
    this.sendCloth = function(t, e, o, n, l, r, a) {
        void 0 === a && (a = !0);
        var s = new proto_cs.user.setClothe();
        s.head = t;
        s.body = e;
        s.ear = o;
        s.background = n;
        s.effect = l;
        s.animal = r;
        JsonHttp.send(s, function() {
            a && i.alertUtil.alert18n("USER_CLOTHE_SET");
        });
    };
    this.sendUnlockCloth = function(t) {
        var e = new proto_cs.user.lockClothe();
        e.id = t;
        e.id1 = 1;
        JsonHttp.send(e, function() {
            i.alertUtil.alert18n("USER_CLOTHE_UNLOCK_SUC");
        });
    };
    this.sendGetOther = function(t, e) {
        void 0 === e && (e = 0);
        var o = new proto_cs.user.getFuserMember();
        o.id = t;
        0 != e && (o.spid = e);
        JsonHttp.send(o, function() {
            i.utils.openPrefabView("user/UserInfo");
        });
    };
    this.sendHeadBlank = function(t, e) {
        let data = {};
        if(null != this.headavatar) {
            i.utils.copyData(data, this.headavatar);
        }
        var o = new proto_cs.user.setAvatar();
        o.head = t;
        o.blank = e;
        JsonHttp.send(o, function() {
            if(o.head != data.head && o.blank != data.blank) {
                i.alertUtil.alert18n("USER_HEAD_AND_BLANK_OK");
            } else if(o.blank != data.blank) {
                i.alertUtil.alert18n("USER_BLANK_OK"); 
            } else {
                i.alertUtil.alert18n("USER_HEAD_OK");
            }   
        });
    };
    this.sendResetName = function(t, e) {
        void 0 === e && (e = 1);
        var o = new proto_cs.user.resetName();
        o.name = t;
        o.type = e;
        JsonHttp.send(o, function() {
            n.playerProxy.userData.name == t &&
                i.alertUtil.alert18n("USER_RENAME_SUC");
        });
    };
    this.sendResetJob = function(t) {
        if (this.userData.job != t) {
            var e = new proto_cs.user.resetImage();
            e.job = t;
            e.sex = 2;
            JsonHttp.send(e, function() {
                if (n.playerProxy.userData.job == t) {
                    i.alertUtil.alert18n("USER_REJOB_SUC");
                    facade.send("PLAYER_RESET_JOB");
                }
            });
        }
    };

    this.sendBuyJob = function (job) {
        var e = new proto_cs.user.buyImage();
        e.job = job;
        e.sex = 2;
        JsonHttp.send(e, function() {
            // if (n.playerProxy.userData.job == t) {
            //     i.alertUtil.alert18n("USER_REJOB_SUC");
            //     facade.send("PLAYER_RESET_JOB");
            // }
        });

    };

    this.sendAddition = function() {
        JsonHttp.send(new proto_cs.user.addition());
    };
    this.isHaveBlank = function(t) {
        return (
            null != this.blanks &&
            !(!this.blanks || -1 == this.blanks.indexOf(t))
        );
    };
    this.onQifuDat = function(t) {
        this.qifuData = t;
        var e = localcache.getItem(
                localdb.table_officer,
                this.userData.level
            ),
            o =
                t.lastTime > i.timeUtil.getTodaySecond(0)
                    ? e.pray - t.free
                    : e.pray;
        a.change(
            "qifu",
            o > 0 && r.funUtils.isOpenFun(r.funUtils.qifu)
        );
        facade.send(this.PLAYER_QIFU_UPDATE);
    };
    this.onAddition = function(t) {
        this.addition = t;
        facade.send(this.PLAYER_ADDITION_UPDATE);
    };
    this.sendQifu = function(t) {
        var e = new proto_cs.user.qifu();
        e.jyid = t;
        JsonHttp.send(e,function(){
            n.timeProxy.floatReward();
        });
    };

    this.sendQifuTen = function (t) {
        var e = new proto_cs.user.qifuTen();
        e.jyid = t;
        JsonHttp.send(e,function(){
            n.timeProxy.floatReward();
        });
    };

    /**
     * 获取最大VIP等级
     */
    this.getMaxVipLv = function(){
        var list = localcache.getList(localdb.table_vip);
        return parseInt(list[list.length - 1].vip);
    }

    this.getVipValue = function(t) {
        var e = localcache.getItem(
            localdb.table_vip,
            n.playerProxy.userData.vip
        );
        return e && e[t] ? e[t] : 0;
    };
    this.getSuitLv = function(t) {
        if (null == this.suitlv) return 1;
        for (var e = 0; e < this.suitlv.length; e++)
            if (this.suitlv[e].id == t) return this.suitlv[e].lv;
        return 1;
    };
    this.sendSuitLv = function(t) {
        var e = new proto_cs.user.lvupSuit();
        e.id = t;
        JsonHttp.send(e);
    };
    this.getClotheCount = function(id) {
        for(let i = 0;i < this.clothes.length;i++)
        {
            let clothId = this.clothes[i];
            if(clothId == id)
            {
                return 1;
            }
        }
        return 0;
    };

    /**
    *加载女主
    *param nodeurl 加载的url载体
    *param customdata  自定义数据 用于处理一些特别的
    *param serverclothes 服务器返回的{head:0,body:0,ear:0}结构数据,为空取主角的数据
    */
    this.loadPlayerSpinePrefab = function(nodeurl,customdata,serverclothes){
        if (nodeurl == null) return;
        let playerSpineUrl = UIUtils.uiHelps.getPlayerSpinePrefab();
        if (nodeurl.url == playerSpineUrl){
            let spine = nodeurl.node.getComponentInChildren(roleSpine);
            this.refreshPlayerClothe(spine,customdata,serverclothes)
            return;
        }
        nodeurl.url = playerSpineUrl;
        nodeurl.loadHandle= ()=>{
            if (nodeurl == null || nodeurl.node == null || nodeurl.node.childrenCount == 0) return;
            let spine = nodeurl.node.getComponentInChildren(roleSpine);
            this.refreshPlayerClothe(spine,customdata,serverclothes)
        }
    };

    /**
    *加载女主
    *param spine RoleSpine的类
    *param customdata  自定义数据 用于处理一些特别的
    *param serverclothes 服务器返回的{head:0,body:0,ear:0}结构数据,为空取主角的数据
    */
    this.refreshPlayerClothe = function(spine, customdata, serverclothes) {
        if (spine == null) return;
        if (customdata != null) {
            if (customdata.suitId) { //显示套装
                spine.setSuitClothe(customdata.suitId)
            } else if(customdata.creatorjob) { //用于创建角色界面
                spine.setCreateClothes(customdata.creatorjob);
            } else {
                spine.setClothes(customdata.job, customdata.level, customdata.clothe,customdata.clotheSpecial);
            }                  
        }else{
            var sdata = serverclothes != null ? serverclothes : n.playerProxy.userClothe;
            var level = n.playerProxy.userData.level;
            if (level == 0) {
                level = 1;
            }
            let effectId = 0;
            if (serverclothes == null){
                effectId = n.clotheProxy.getPlayerSuitClotheEffect(sdata.body);
            }
            else{
                effectId = serverclothes.clotheSpecial;
            }
            spine.setClothes(n.playerProxy.userData.job, level, sdata,effectId);
        }
    };

    this.addIconOpen = function(arrOpenFunc) {
        if(arrOpenFunc && arrOpenFunc.length > 0) {
            this.arrIconOpen = this.arrIconOpen.concat(arrOpenFunc);
        }
    };

    this.getIconOpen = function() {
        if(this.arrIconOpen.length <= 0) {
            return null;
        }
        return this.arrIconOpen.shift();
    };

    /**获取女主的华服等级*/
    this.getClotheLevel = function(){
        let listCfg = localcache.getList(localdb.table_huafu);
        listCfg.sort((a,b)=>{
            return a.lv < b.lv ? -1 : 1;
        })
        for (let ii = listCfg.length-1; ii >= 0;ii--){
            let cg = listCfg[ii];
            if (this.clotheScore >= cg.score){
                return cg.lv;
            }
        }
        return 0;
    };

    /**判断华服值是否可领奖*/
    this.canGetClotheValueReward = function(){
        let pickLevel = n.clotheProxy.pickLv;
        let curLevel = this.getClotheLevel();
        a.change("clothe_value_reward",curLevel > pickLevel);   
    };

    this.getallClotheSuitProp = function(){
        let suitTypeDic = {};
        let listdata = localcache.getList(localdb.table_usersuit);
        for (var ii = 0;ii < listdata.length;ii++){
            let cg = listdata[ii];
            if (suitTypeDic[cg.type] == null){
                suitTypeDic[cg.type] = {ep1:0,ep2:0,ep3:0,ep4:0,curnum:0,max:0};
            }
            suitTypeDic[cg.type].max += 1;
            let flag = true;
            for (let clothid of cg.clother){
                if (!this.isUnlockCloth(clothid)){
                    flag = false;
                    break;
                }
            }
            if (flag){
                suitTypeDic[cg.type].curnum += 1;
                let level = this.getSuitLv(cg.id);
                let cfg = localcache.getItem(localdb.table_userSuitLv, 1000 * cg.lvup + level);
                for (let info of cfg.ep){
                    if (info.prop == 5){
                        suitTypeDic[cg.type]["ep1"] += info.value;
                        suitTypeDic[cg.type]["ep2"] += info.value;
                        suitTypeDic[cg.type]["ep3"] += info.value;
                        suitTypeDic[cg.type]["ep4"] += info.value;
                    }
                    else{
                        suitTypeDic[cg.type]["ep" + info.prop] += info.value;
                    }                
                }               
            }
        }
        return suitTypeDic;
    };

    this.getCanLvUpSuitArray = function(type) {
        let result = [];
        let suitCfgDataList = localcache.getList(localdb.table_usersuit);
        let count = 0;
        for (let j = 0, jLen = suitCfgDataList.length; j < jLen; j++) {
            let suitData = suitCfgDataList[j];
            if(suitData.type == type) {
                let suitCountData = this.getSuitCount(suitData.id);
                if(suitCountData.myNum == suitCountData.totalNum) {
                    let suitLv = this.getSuitLv(suitData.id),
                    nextLevelData = localcache.getItem(localdb.table_userSuitLv, 1e3 * suitData.lvup + suitLv + 1);
                    if(null != nextLevelData) {
                        suitData.index = count++;
                        suitData.bLvUp = true;
                        result.push(suitData);
                    }
                }
            }
        }
        return result;
    };

    this.getSuitByType = function(type) {
        let result = [];
        let suitCfgDataList = localcache.getList(localdb.table_usersuit);
        let count = 0;
        for (let j = 0, jLen = suitCfgDataList.length; j < jLen; j++) {
            let suitData = suitCfgDataList[j];
            if(suitData.type == type) {
                suitData.index = count++;
                result.push(suitData);
            }
        }
        return result;
    };

    /**
    *加载玩家的头像
    *@param nodeurl 传入加载头像的urlload
    *@param headData 头像数据  head 头像  blank 头像框
    *@param clotheData 服装数据
    *@param isSys 用于系统的聊天头像
    */
    this.loadUserHeadPrefab = function(nodeurl, headData, clotheData, isUser = true, isSys) {
        if (nodeurl == null){
            return;
        }
        let headUrl = UIUtils.uiHelps.getUserHeadItemPrefab();
        if (nodeurl.url == headUrl) {
            //fixed issue 【ID1021905】【换装】华服榜滑动查看时，头像不停闪动【附视频】 2020.11.16
            let script = nodeurl.getComponentInChildren("UserHeadNew");
            if(script && i.utils.isObjectValueEqual(clotheData, script.clothData)) {
                return;
            }
            this.refreshUserHead(nodeurl, headData, clotheData, isUser, isSys);
            return;
        }
        let self = this;       
        nodeurl.loadHandle = ()=>{
            self.refreshUserHead(nodeurl, headData, clotheData, isUser, isSys)
        }
        nodeurl.url = headUrl;
    };

    this.refreshUserHead = function(nodeurl,headData,clotheData,isUser,isSys){
        if (nodeurl == null || nodeurl.node == null || nodeurl.node.childrenCount == 0){
            return;
        }
        var com = nodeurl.node.getComponentInChildren("UserHeadNew");
        if (com != null){
            com.setHead(headData,clotheData,isUser,isSys);               
        }
    };

    /**判断衣服是否为套装*/
    this.isSuitPartById = function (id) {
        let listcfg = localcache.getList(localdb.table_usersuit);
        for (var ii = 0; ii < listcfg.length; ii++){
            let cg = listcfg[ii];
            if (cg.clother.indexOf(id) != -1){
                return cg.id;
            }
        }
        return -1;
    }

}
//exp  威望
exports.PlayerProxy = PlayerProxy;
var RoleData = function() {
    this.coin = 0; //阅历
    this.food = 0; //银两
    this.army = 0; //名声
    this.cash = 0; //元宝
    this.ep = 0;
};
exports.RoleData = RoleData;
