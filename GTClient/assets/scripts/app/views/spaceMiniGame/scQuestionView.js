let Utils = require("Utils");
let Initializer = require("Initializer");
let UIUtils = require("UIUtils");
let UrlLoad = require("UrlLoad");
let ServantSpine = require("ServantSpine");
let QuestionSelect = require("scQuestionSelect");
import { MINIGAMEOWNER_TYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {
        lbTitle: cc.Label,
        bgUrlLoad: UrlLoad,
        urlAvatar: UrlLoad,
        nodeTalk: cc.Node,
        lbName: cc.Label,
        lblDes: cc.Label,
        nQuestion: cc.Node,
        lbNumStr: cc.Label,
        lbNum: cc.Label,
        lbQuestion: cc.Label,
        arrQuestionSelect: [QuestionSelect],
        playerUrl:UrlLoad,
    },

    ctor: function() {
        this.iQuestionNum = 3;
        this.iAnswerNum = 3;
        this.mType = 0;
        this.customCount = 0;
    },

    // LIFE-CYCLE CALLBACKS:
    onLoad () {
        this.bQuestion = true;
        let rightData = Initializer.servantProxy.rightData;
        this.heroId = rightData.heroId;
        this.qaType = rightData.qaType;
        this.mType = MINIGAMEOWNER_TYPE.NORMAL;
        if (this.node.openParam && this.node.openParam.type){
            this.mType = MINIGAMEOWNER_TYPE.UNION_PARTY;
        }
        this.lbTitle.string = i18n.t(this.qaType == 1 ? "MINIGAME_CAIMI" : "MINIGAME_DUISHI");
        this.questionArr = [];
        let self = this;
        
        if (this.mType == MINIGAMEOWNER_TYPE.NORMAL){
            let bgId = Initializer.servantProxy.getServantBgId(this.heroId);
            let cfg = localcache.getItem(localdb.table_herobg, bgId);
            this.bgUrlLoad.url = UIUtils.uiHelps.getPartnerZoneBgImg(cfg.icon);
            this.urlAvatar.loadHandle = () => {
                self.urlAvatar.node.position = cc.v2(self.urlAvatar.content.x, -self.urlAvatar.content.height - 20);
                self.showTalk("start");
            }
            let heroData = localcache.getItem(localdb.table_hero, this.heroId + "");
            this.lbName.string = heroData.name;
            this.urlAvatar.url = UIUtils.uiHelps.getServantSpine(this.heroId);
        }
        else if(this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            this.bgUrlLoad.url = UIUtils.uiHelps.getUnpackPic("yh_bg");
            let playerdata = this.node.openParam.player;
            this.lbName.string = playerdata.name;
            Initializer.playerProxy.loadPlayerSpinePrefab(this.playerUrl,{job:playerdata.job,level:playerdata.level,clothe:playerdata.clothe,clotheSpecial:playerdata.clotheSpecial})
            self.scheduleOnce(()=>{
                //self.showTalk("start");
                self.showQuestion(self.customCount);
            },0.3)
        }
    },

    showTalk: function(type) {
        this.nQuestion.active = !1;
        this.nodeTalk.active = !0; 
        Utils.utils.showNodeEffect(this.nodeTalk, -1);

        let talkData = localcache.getItem(localdb.table_game_talk, this.heroId);
        let tmpStr = this.qaType == 1 ? "mi" : "poem";
        let tarArr = talkData[tmpStr + type], tmpData = null, face = null;
        tmpData = tarArr.length <= 1 ? tarArr[0] : tarArr[Utils.utils.randomNum(0, tarArr.length - 1)];
        this.str = tmpData[0];
        face = tmpData[1];

        let spine = this.urlAvatar.getComponentInChildren(ServantSpine);
        if (spine != null && face != null) {
            spine.playAni(Utils.stringUtil.isBlank(face) ? "idle1_idle" : face);
        }

        this.lblDes.unscheduleAllCallbacks();
        UIUtils.uiUtils.showText(this.lblDes, this.str, 0.05);
        this.talkType = type;
        if(type != "start") {
            this.bQuestion = false;
        }
    },

    showQuestion: function(index) {
        this.bChoiced = false;
        this.talkType = null;
        this.iQuestion = index;
        this.nodeTalk.active = !1;
        this.nQuestion.active = !0;

        let questions = localcache.getFilters(localdb.table_game_question, "type", this.qaType + "");
        questions.sort((a, b) => {
            return a.id - b.id;
        });

        let numIndex = 0;
        while(0 == numIndex || this.questionArr.indexOf(numIndex) > -1) {
            numIndex = Utils.utils.randomNum(questions[0].id, questions[questions.length - 1].id);
        }
        this.questionArr.push(numIndex);

        this.curQuestion = localcache.getItem(localdb.table_game_question, numIndex + "");
        this.lbNumStr.string = i18n.t("MINIGAME_ANSWER_NUM", {n: index + 1});
        this.lbNum.string = i18n.t("MINIGAME_ANSWER_NUM2", {f: index + 1, s: this.iQuestionNum });
        this.lbQuestion.string = this.curQuestion.question;

        let tmpIndexArr = [];
        for(let i = 0, len = this.arrQuestionSelect.length; i < len; i++) {
            let tmpIndex = -1;
            while(-1 == tmpIndex || tmpIndexArr.indexOf(tmpIndex) > -1) {
                tmpIndex = Utils.utils.randomNum(0, this.iAnswerNum - 1);
            }
            tmpIndexArr.push(tmpIndex);

            this.arrQuestionSelect[i].setAnswer(tmpIndex, this.curQuestion.answer[tmpIndex]);
        }      
    },

    onClickNext: function() {
        let self = this;
        let func = () => {
            if(self.lblDes.isRunShowText) {
                self.lblDes.unscheduleAllCallbacks();
                self.lblDes.isRunShowText = !1;
                self.lblDes.string = self.str;
                return true;
            }
            return false;
        }
        if (this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            if(this.talkType == "start") {
                if(!func()) {  
                    this.showQuestion(self.customCount);
                }
            }
            else if(null != this.talkType) {
                if(!func()) {  
                    Initializer.unionProxy.sendPickGamesAward();
                    self.onClickClose();
                }
            }
            return;
        }
        if(this.talkType == "start") {
            if(!func()) {  
                this.showQuestion(Initializer.servantProxy.rightData.anCount);
            }
        } else if(null != this.talkType) {
            if(!func()) {  
                Initializer.servantProxy.endVisitGame(() => {
                    self.onClickClose();
                });
            }
        }
    },

    onClickSelect(event) {
        if(this.bChoiced) {
            return;
        }
        let script = event.target.getComponent(QuestionSelect);
        script.nSelected.active = true;
        let self = this;
        this.bChoiced = true;
        if (this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            this.customCount++;
            self.scheduleOnce(() => {
                let bRight = script.index == self.curQuestion.idright;
                script.setRight(bRight);
                if(!bRight) {
                    for(let i = 0, len = self.arrQuestionSelect.length; i < len; i++) {
                        let scSingleSelect = self.arrQuestionSelect[i];
                        if(scSingleSelect.index == self.curQuestion.idright) {
                            scSingleSelect.setRight(true);
                        }
                    }
                }
            }, 0.4);
            self.scheduleOnce(() => {
                let count = self.customCount;
                if(count >= self.iQuestionNum) {
                    //self.showTalk("0");
                    Initializer.unionProxy.sendPickGamesAward();
                    self.onClickClose();
                } else {
                    self.showQuestion(count);
                }
            }, 1.2);
            return;
        }
        Initializer.servantProxy.chooseVisitAnswer(script.index, self.curQuestion.id, () => {
            self.scheduleOnce(() => {
                let bRight = script.index == self.curQuestion.idright;
                script.setRight(bRight);
                if(!bRight) {
                    for(let i = 0, len = self.arrQuestionSelect.length; i < len; i++) {
                        let scSingleSelect = self.arrQuestionSelect[i];
                        if(scSingleSelect.index == self.curQuestion.idright) {
                            scSingleSelect.setRight(true);
                        }
                    }
                }
            }, 0.4);

            self.scheduleOnce(() => {
                let count = Initializer.servantProxy.rightData.anCount;
                if(count >= self.iQuestionNum) {
                    self.showTalk(Initializer.servantProxy.rightData.rightCount.toString());
                } else {
                    self.showQuestion(count);
                }
            }, 1.2);
        });
    },

    //关闭界面
    onClickClose: function() {
        if (this.mType == MINIGAMEOWNER_TYPE.UNION_PARTY){
            Utils.utils.closeView(this);
            return;
        }
        if(this.bQuestion) {
            let self = this;
            Utils.utils.showConfirm(i18n.t("MINIGAME_CLOSE"), () => {
                self.bQuestion = false;
                Initializer.servantProxy.endVisitGame(() => {
                    self.onClickClose();
                });
            }); 
        } else {
            Utils.utils.closeView(this);
        }
    },
});
