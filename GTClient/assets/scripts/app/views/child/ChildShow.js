var i = require("Utils");
var n = require("ChildSpine");
var l = require("Initializer");
var r = require("ServantStarShow");
let sts = require("stars")
cc.Class({
    extends: cc.Component,
    properties: {
        childSpine:n,
        childSpine2:n,
        e1:cc.Label,
        e2:cc.Label,
        e3:cc.Label,
        e4:cc.Label,
        lblDes:cc.Label,
        stars:sts,
        stars2:sts,
        spGetChild: sp.Skeleton,

        


        leftStarlist:cc.Node,
        rightStarlist:cc.Node,
        sfLabel:cc.Label,
        allcont:cc.Label,
        conts:cc.Node,
        csNode:cc.Node,
        leftName:cc.Node,
        lname:cc.Label,
        bottom:cc.Node,
        tdImage:cc.Node,
        csImage:cc.Node,

        lblfather:cc.Label,
        lpbName:cc.Label,
    },

    ctor() {
        this.cList = [];
        this.curIndex = 0;
    },
    onLoad() {
        this.isT = this.node.openParam.child||1
        this.cList = this.node.openParam.cList||[];
        this.leftStarlist.active = !(this.isT === 1) 
        this.rightStarlist.active = this.isT === 1
        this.csNode.active = !(this.isT === 1)
        this.leftName.active = !(this.isT === 1)
        this.bottom.active = this.isT === 1
        this.tdImage.active = this.isT === 1
        this.csImage.active = !(this.isT === 1)
        this.showChild();
        
        // this.spGetChild.setCompleteListener((trackEntry) => {
        //     let aniName = trackEntry.animation ? trackEntry.animation.name : "";
        //     if (aniName === 'appear5') {
        //         if(null != self.spGetChild) {
        //             self.spGetChild.setAnimation(0, 'idle5', true);
        //         }
        //     }  
        // });
    },
    showChild() {
        let t = this.node.openParam.cList||[];
        if (null != t) {
            var e = t[this.curIndex];
            if (e) {
                var o = localcache.getItem(localdb.table_hero, e.heroid);
                if(this.isT === 1){
                    this.lblDes.string = i18n.t("WIFE_CHU_YOU_CHILD_" + e.babysex, {
                        name: o.name
                    });
                }
                this.lblfather.string = l.playerProxy.getWifeName(e.heroid);
                this.lpbName.string = l.playerProxy.userData.name;
                var i = l.sonProxy.getSon(e.babyid||e.id);
                this.e1.string = i.ep.e1 + "";
                this.e2.string = i.ep.e2 + "";
                this.e3.string = i.ep.e3 + "";
                this.e4.string = i.ep.e4 + "";
                if(this.isT === 1){
                    this.childSpine.setKid(i.id, i.sex,!1);
                }else{
                    this.childSpine2.setKid(i.id, i.sex);
                }
                this.childSpine.node.active = this.isT === 1
                this.childSpine2.node.active = !(this.isT === 1)
                this.stars.setValue(i.talent);
                this.stars2.setValue(i.talent);
                if(this.isT !== 1){
                    this.conts.y = -40;
                    this.allcont.string = i.ep.e1+i.ep.e2+i.ep.e3+i.ep.e4
                    var of = localcache.getItem(localdb.table_adult, i.honor);
                    if(of){
                        this.sfLabel.string = of.name
                    }
                    this.lname.string = i.name;
                }else{
                    this.conts.y = -100;
                }
            }
        }
    },
    onClickClose() {
        if (this.cList.length - 1 <= this.curIndex) i.utils.closeView(this);
        else {
            this.curIndex++;
            this.showChild();
        }
    },
});
