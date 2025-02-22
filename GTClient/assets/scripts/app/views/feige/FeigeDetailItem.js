var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("ChildSpine");

var FeigeDetailItem = cc.Class({
    extends: i,

    properties: {
        lblContext: [cc.Label],
        lblTitle: cc.Label,
        model: n,
        wife: n,
        childSpine: a,
        childSpineSmall: a,
        lbTitle: cc.Label,
        roleSpine:n,

        lbtext:cc.Label,
        imagehead:n
    },

    ctor() {
        // this.lblContext = [];
        // this.lblTitle = null;
        // this.model = null;
        // this.wife = null;
        // this.childSpine = null;
        // this.childSpineSmall = null;
        this.runb = false
        this.dobelArrys = {
            1:"。",
            2:"。。",
            3:"。。。",
        }
    },

    onLoad() {
        if (this.model){
            this.defaultServantY = this.model.node.position.y;
        }
        this.showData()
    },
    update(dt){
        if(!this.runb){
            return
        }
        this.indext+=dt
        this.maxTime-=dt
        if(this.maxTime<=0){
            this.ends()
        }
        if(this.indext>=0.3){
            this.indext-=0.3
            this.index++
            let string = "            " + i18n.t("FEIGE_RECYCLEING3")+this.dobelArrys[this.index]
            if(this.index>=3){
                this.index = 0
            }
            this.lbtext.string = string
        }
    },
    ends(){
        this.runb = false
        this.lbtext.string = this.txt
        if(this.func){
            this.func()
        }
    },
    cTxt2(txt,func){
        this.txt = txt;
        this.func = func
        this.indext = 0
        this.index = 0
        this.maxTime = 2.5
        this.runb = true
    },
    cTxt(string){
        this.lbtext.string = string
    },

    showData() {
        let fdata = this.node.fdata
        let isMe = this.node.isMe
        this.lbtext.string = fdata.context

        let heroid = this.node.heroid
        if(r.feigeProxy.lookSonFeige && !isMe){
            let tmode = r.sonProxy.getSon(r.feigeProxy.sonFeigeData.sid)
            tmode.state > 3 ? this.childSpine.setKid(tmode.id, tmode.sex):this.childSpineSmall.setKid(tmode.id, tmode.sex, !1)
        }else if(!r.feigeProxy.lookSonFeige && this.imagehead && !isMe){
            this.imagehead.url = l.uiHelps.getServantHead(heroid)
        }
        if(isMe && this.wife){
            r.playerProxy.loadPlayerSpinePrefab(this.wife);
        }
        isMe?this.runRight():this.runLeft()
        return
        var t = this._data;
        if (t) {
            var e = r.playerProxy.getEmailData(t.id);
            if (e) {
                let name = "";
                for (var o = localcache.getItem(localdb.table_emailgroup, e.group), i = t.select, n = 0 == i ? e.context: i == e.award1 ? e.text1: e.text2, a = this.getLabels(n), s = 0; s < this.lblContext.length; s++) {
                    this.lblContext[s].string = a.length > s ? a[s] : "";
                }
                let select = t.select; 
                switch (o.fromtype) {
                    case 1:
                        let cfgName1 = localcache.getItem(localdb.table_hero, o.heroid).name;
                        this.wife && (this.wife.node.active = !1);
                        if (this.model) {
                            this.model.node.active = !0;                            
                            this.model.loadHandle = () => {
                                this.servantAnchorYPos(this.model);              
                            };
                            this.model.url = l.uiHelps.getServantSmallSpine(o.heroid);
                        }
                        for(let i = 0, len = cfgName1.length; i < len; i++) {
                            name += cfgName1[i] + "\n";
                        }
                        null != this.lbTitle && (this.lbTitle.string = select == 0
                         ? i18n.t("FEIGE_TITLE") : i18n.t("FEIGE_TITLE_CUSTOM", { value: name }));
                        break;
                    // case 2:
                    //     var c = localcache.getItem(localdb.table_wife, o.heroid);
                    //     let cfgName2 = r.playerProxy.getWifeName(o.heroid);
                    //     this.model && (this.model.node.active = !1);
                    //     if (this.wife) {
                    //         this.wife.node.active = !0;
                    //         //this.wife.url = l.uiHelps.getWifeSmallBody(c.res);
                    //         this.wife.url = l.uiHelps.getServantSmallSpine(o.heroid);
                    //     }
                    //     for(let i = 0, len = cfgName2.length; i < len; i++) {
                    //         name += cfgName2[i] + "\n";
                    //     }
                    //     break;
                    case 3:
                        var _ = r.sonProxy.getSon(r.feigeProxy.sonFeigeData.sid);
                        _.state > 3 ? (this.childSpine && this.childSpine.setKid(_.id, _.sex))
                         : (this.childSpineSmall && this.childSpineSmall.setKid(_.id, _.sex, !1));
                        let cfgName3 = i18n.t("SON_TIP2");  
                        for(let i = 0, len = cfgName3.length; i < len; i++) {
                            name += cfgName3[i] + "\n";
                        }
                        null != this.lbTitle && (this.lbTitle.string = select == 0
                         ? i18n.t("FEIGE_TITLE_TEACHER") : i18n.t("FEIGE_TITLE_CUSTOM", { value: name }));
                        break;
                }
                if (this.roleSpine){
                    r.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
                }
            }
        }
    },

    runLeft(){
        this.node.opacity = 0
        let  action = cc.fadeIn(0.6);
        this.node.x -=800
        let move = cc.moveBy(0.3, 800, 0)
        var spawn = cc.spawn(action, move);
        this.node.runAction(spawn);
    },
    runRight(){
        this.node.opacity = 0
        let  action = cc.fadeIn(0.6);
        this.node.x +=800
        let move = cc.moveBy(0.3, -800, 0)
        var spawn = cc.spawn(action, move);
        this.node.runAction(spawn);
    },

    getLabels(t) {
        for (var e = [], i = (t = r.playerProxy.getReplaceName(t)).split("\n"), n = 0; n < i.length; n++) {
            var l = 0,
            a = i[n].length;
            if (a < FeigeDetailItem.countMax) e.push(i[n]);
            else for (; l < a;) {
                e.push(i[n].substr(l, a - l > FeigeDetailItem.countMax ? FeigeDetailItem.countMax: a - l));
                l += FeigeDetailItem.countMax;
            }
        }
        return e;
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

});

FeigeDetailItem.countMax = 13;