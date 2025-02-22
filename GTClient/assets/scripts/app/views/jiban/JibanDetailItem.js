var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblPro: cc.Label,
        lblLv: cc.Label,
        word: cc.Label,
        nDot: cc.Node,
        bar: cc.ProgressBar,
        btn: cc.Button,
        nodeLock: cc.Node,
        lblLock: cc.Label,
        nodeNew: cc.Node,
        lblExp: cc.Label,
        yuyin: cc.Node,
        nodeIcon:cc.Node,
        cardIcon:n,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data,
        e = localcache.getItem(localdb.table_heropve, t.id);
        if (e) {
            this.yuyin.active = e.voice > 0;
            this.lblName.string = e.name;
            var o = l.jibanProxy.isOverStory(e.id),
            i = l.jibanProxy.getJibanIsOpen(e);
            this.nodeLock.active = !i;
            this.nodeIcon.active = false;
            this.lblLv.node.parent.active = i && (0 != e.star && e.star != 6 && e.star != 7);
            // this.bar.node.active = i && (0 != e.star && e.star != 6 && e.star != 7);
            // this.lblPro.node.active = i && (0 != e.star && e.star != 6 && e.star != 7);
            this.nodeNew.active = !o && i;
            this.lblLv.string = i18n.t("SERVANT_JI_BAN_ITEM_LEVEL_TXT", {
                lv: t.level
            });
            this.word.string = r.uiHelps.getJbTitleTxt(e.unlocktype);
            if (!i && e.unlocktype == 10){
                let cg = localcache.getFilter(localdb.table_card,"storynameid",e.id);
                if (cg != null){
                    this.nodeIcon.active = true;
                    this.cardIcon.url = r.uiHelps.getCardSmallFrame(cg.picture);
                    this.lblName.string = "";
                    this.nDot.active = false;
                    this.lblName.node.parent.x = 20;
                    this.word.string =cg.name;
                }
            }
            else{
                this.nDot.active = true;
                this.lblName.node.parent.x = 104;
            }


            // if (0 == e.star) this.lblLock.string = i ? "": this.getUnlockString();
            if(!i) this.lblLock.string = i ? "": this.getUnlockString();
            else {
                if(e.star != 0 && e.star != 7)
                {
                    var n = l.jibanProxy.getJbItemAddPro(t.id, t.level);
                    this.lblPro.string = i18n.t("SERVANT_JI_BAN_SHU_XING_ADD", {
                        name: i18n.t("COMMON_PROP" + n.pro),
                        value: n.value
                    });
                    var a = localcache.getItem(localdb.table_heropveJbLevel, t.level);
                    if (a) {
                        this.bar.progress = t.exp / a.story_num;
                        this.lblExp.string = i18n.t("COMMON_NUM", {
                            f: t.exp,
                            s: a.story_num
                        });
                    }
                }
            }
            
            // this.nDot.color = this.word.node.color = this.lblName.node.color = r.uiHelps.getJbColor(e.star);
            // 1 == e.star ? (this.lblPro.node.color = this.lblLv.node.color = cc.color(10, 53, 64)) : 2 == e.star ? (this.lblPro.node.color = this.lblLv.node.color = cc.color(42, 10, 64)) : 3 == e.star ? (this.lblPro.node.color = this.lblLv.node.color = cc.color(87, 9, 9)) : 4 == e.star && (this.lblPro.node.color = this.lblLv.node.color = cc.color(104, 43, 9));
        }
    },
    getUnlockString() {
        var t = this._data,
        e = localcache.getItem(localdb.table_heropve, t.id),
        o = "";
        if (e) {
            if (2 == e.type) o = l.playerProxy.getWifeName(e.roleid);
            else if (1 == e.type) {
                o = localcache.getItem(localdb.table_hero, e.roleid).name;
            }
            switch (e.unlocktype) {
            case 1:
            case 2:
                return i18n.t("SERVANT_JIBAN_NEED" + e.unlocktype, {
                    n: o,
                    c: e.unlock
                });
            case 3:
                var i = localcache.getItem(localdb.table_mainTask, e.unlock);
                return i18n.t("FIGHT_TASK_LIMIT", {
                    n: i.name
                });
            case 4:
            case 5:
            case 6:
            case 8:
                return i18n.t("UNLOCK_SERVANT");
            }
        }
        return "";
    },
});
