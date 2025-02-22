var i = require("Utils");
var n = require("Initializer");
var l = require("RenderListItem");
cc.Class({
    extends: cc.Component,
    properties: {
        itemPrefab: cc.Node,
        itemPrefabLeft: cc.Node,
        contextNode: cc.Node,
        recyclyNode: cc.Node,
        waitNode: cc.Node,
        lblContext1: cc.Label,
        lblContext2: cc.Label,
        scrollView: cc.ScrollView,
        nodeUnhave: cc.Node,
        nodeBtn: cc.Node,
        btnNext: cc.Node,

        lname:cc.Label,
        btnUp:cc.Label,
        btnDown:cc.Label,
        downLines:cc.Node,
        Btn1:cc.Node,
        Btn2:cc.Node,
    },
    ctor() {
        this.curEmailGroup = null;
        this.curSelect = 0;
        this.curItem = null;
        this.isWait = !1;
        this.items = [];
        this.isRev = !1;
        this.heroid = 0
    },
    onLoad() {
        this.indexItem = null
        this.leftIndexItem = null
        this.nowItem = null
        this.itemPrefabLeft.active = false
        this.itemPrefab.active = false
        var t = this.node.openParam;
        if (null == t) {
            var e = this.node.openParam;
            t = n.feigeProxy.getSonfeigeData(e.id);
        }
        this.curEmailGroup = t;
        facade.subscribe(n.feigeProxy.UPDATE_READ, this.updateShowData, this);
        facade.subscribe("UPDATE_READ_SON", this.updateShowData, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickBack, this);
        this.updateShowData();
    },
    updateShowData() {
        let name = ''
        let t = this.curEmailGroup
        let selectList = []
        let edata = null
        let e2 = null
        if (n.feigeProxy.lookSonFeige) {
            selectList = n.feigeProxy.getSonFeigeItem(n.feigeProxy.sonFeigeData.id, n.feigeProxy.sonFeigeData.sid, n.feigeProxy.sonFeigeData.time).select;
            e2 = n.sonProxy.getSon(n.feigeProxy.sonFeigeData.sid);
            name = e2?e2.name:''
        }else{
            this.heroid = t.heroid
            edata = localcache.getItem('hero',t.heroid)
            name = edata.name?edata.name:''
            selectList = n.feigeProxy.getFeigeData(t.id).select;
            selectList = selectList?selectList:[]
        }
        this.selectList = selectList
        this.lname.string = name
        let grolist = n.playerProxy.getEmailGroup(t.id, "group");
        //i18n.t("MONTH_CARD_PRICE",{value:pData.rmb})
        let len = grolist.length
        let slen = selectList.length
        let pushs = []
        for (let i = 0; i < len; i++) {
            let tmod = grolist[i]
            if(slen>i){
                let select = selectList[i]
                let txts = tmod.award1 === select?tmod.text1:tmod.text2
                pushs.push(tmod)
                pushs.push({context:txts}) 
            }else{
                pushs.push(tmod)
                break;
            }
        }
        let len2 = pushs.length
        for (let i = 0; i < len2; i++) {
            this.createItemTxt(pushs[i],i+1)
        }
        this.nowItem = slen<len?grolist[slen]:null
        if(this.leftIndexItem){
            this.runGetMessages(this.leftIndexItem,grolist[slen].context,()=>{
                this.leftIndexItem = null
                this.showBtnAndGro(slen)
            })
        }else{
            this.showBtnAndGro(slen)
        }
        return
        //  t = this.curEmailGroup;
        // var e = n.feigeProxy.getFeigeData(t.id),
        // o = e ? e.select: [];
        // r = n.playerProxy.getEmailGroup(t.id, "group");
        // if (n.feigeProxy.lookSonFeige) {
        //     o = n.feigeProxy.getSonFeigeItem(n.feigeProxy.sonFeigeData.id, n.feigeProxy.sonFeigeData.sid, n.feigeProxy.sonFeigeData.time).select;
        // }
        // var l = [],
        // r = n.playerProxy.getEmailGroup(t.id, "group");

        // this.recyclyNode.active = !1;
        // this.itemPrefab.active = !1;
        // this.itemPrefabLeft.active = !1;
        // this.waitNode.active = !1;
        // this.nodeUnhave.active = !1;
        // r.sort(function(t, e) {
        //     return t.index - e.index;
        // });
        // for (var a = 0; a < r.length; a++) {
        //     this.curItem = r[a];
        //     var s = {};
        //     s.id = this.curItem.id;
        //     s.select = 0;
        //     l.push(s);
        //     if (! (o && o.length > a)) break; (s = {}).id = this.curItem.id;
        //     s.select = o && o.length > a ? o[a] : 0;
        //     l.push(s);
        // }
        // for (a = 0; a < l.length; a++) this.createAddItem(a, l[a]);
        // this.recyclyNode.active = l.length % 2 == 1 && !i.stringUtil.isBlank(this.curItem.select1);
        // this.isRev = 10 * Math.random() < 5;
        // this.lblContext1.string = this.isRev ? this.curItem.select2: this.curItem.select1;
        // this.lblContext2.string = this.isRev ? this.curItem.select1: this.curItem.select2;
        // this.nodeUnhave.active = !this.recyclyNode.active;
        // i.utils.showNodeEffect(this.nodeBtn, 0);
        // this.scrollView.scrollToBottom();
        // this.btnNext.active = !this.recyclyNode.active && n.feigeProxy.lookSonFeige && n.feigeProxy.hasSonFeige();
    },

    showBtnAndGro(slen){
        this.showBtnItem()
        if(this.nowItem){
            this.indexItem = this.createItemTxt({context:i18n.t("FEIGE_RECYCLEING2")},slen*2+2)
        }
        this.scheduleOnce(()=>{
            this.scrollView.scrollToBottom(0.1);
        },0.2)
    },

    runGetMessages(node,txt,funs){
        node.getComponent(l).cTxt2(txt,funs)
    },

    showBtnItem(){
        this.downLines.active = this.nowItem?true:false
        if(!this.nowItem){
            return
        }
        this.isRev = 10 * Math.random() < 5;
        this.btnUp.string = this.isRev ? this.nowItem.select2: this.nowItem.select1;
        this.btnDown.string = this.isRev ? this.nowItem.select1: this.nowItem.select2;

        this.Btn1.opacity = 0
        let  action = cc.fadeIn(1.3);
        this.Btn1.x +=800
        let move = cc.moveBy(0.5, -800, 0)
        var spawn = cc.spawn(action, move);
        this.Btn1.runAction(spawn);

        this.Btn2.opacity = 0
        let  action2 = cc.fadeIn(1.3);
        this.Btn2.x -=800
        let move2 = cc.moveBy(0.5, 800, 0)
        var spawn2 = cc.spawn(action2, move2);
        this.Btn2.runAction(spawn2);
    },


    createItemTxt(tmod,ind){
        if(this.items.length > ind-1){
            return
        }
        let index = ind
        let itemp = index%2 === 0?this.itemPrefabLeft:this.itemPrefab
        let item = cc.instantiate(itemp)
        item.active = true
        item.fdata = tmod
        item.isMe = index%2 === 0?true:false
        item.heroid = this.heroid
        this.items.push(item)
        this.contextNode.addChild(item);
        return item
    },

    btnCloseAn(){
        this.downLines.active = false
    },

    onClickText(t, e) {
        var o = parseInt(e);
        this.isWait = !0;
        this.curSelect = this.isRev ? (1 == o ? 2 : 1) : o;
        if(this.nowItem){
            this.runGetMessages(this.indexItem,1 == this.curSelect ? this.nowItem.text1: this.nowItem.text2,()=>{
                //this.indexItem.getComponent(l).cTxt(1 == this.curSelect ? this.nowItem.text1: this.nowItem.text2)
                if(this.hasNext()){
                    this.leftIndexItem = this.createItemTxt({context:i18n.t("FEIGE_RECYCLEING2")},this.selectList.length*2+3)
                }
                this.saveSelect()
            })
            this.btnCloseAn()
        }
        this.scheduleOnce(()=>{
            this.scrollView.scrollToBottom(0.1);
        },0.2)
        return 
        this.waitNode.active = !0;
        this.recyclyNode.active = !1;
        if (this.hasNext()) {
            this.createAddItem(1, {
                id: this.curItem.id,
                select: 1 == this.curSelect ? this.curItem.award1: this.curItem.award2
            });
            this.scheduleOnce(this.saveSelect, 2 * Math.random() + 2);
        } else this.saveSelect();
        this.scrollView.scrollToBottom();
    },
    hasNext() {
        var t = this.curEmailGroup,
        e = n.playerProxy.getEmailGroup(t.id, "group");
        return e[e.length - 1] != this.nowItem;
    },
    createAddItem(t, e) {
        var o = this.items.length > t ? this.items[t].data: null;
        if (!o || o.id != e.id || o.select != e.select) {
            var i = t % 2 == 0 ? cc.instantiate(this.itemPrefab) : cc.instantiate(this.itemPrefabLeft);
            i.active = !0;
            var n = i.getComponent(l);
            n.data = e;
            this.items.push(n);
            this.contextNode.addChild(i);
        }
    },
    saveSelect() {
        if (0 != this.curSelect) {
            this.isWait = !1;
            //this.waitNode.active = !1;``
            n.feigeProxy.lookSonFeige ? n.feigeProxy.sendReadSonFeige(n.feigeProxy.sonFeigeData.sid, 1 == this.curSelect ? this.nowItem.award1: this.nowItem.award2, n.feigeProxy.sonFeigeData.time) : n.feigeProxy.sendReadFeige(1 == this.curSelect ? this.nowItem.award1: this.nowItem.award2);
        }
    },
    onClickClost() {
        this.isWait && this.saveSelect();
        i.utils.closeView(this);
        i.utils.closeNameView("feige/FeigeView", !0);
    },
    onClickBack() {
        this.isWait && this.saveSelect();
        i.utils.closeView(this);
    },
    onClickNext() {
        this.contextNode.removeAllChildren();
        this.items = [];
        this.scrollView.scrollToTop();
        if (n.feigeProxy.hasSonFeige()) {
            var t = n.feigeProxy.getUnReadSonMail();
            n.feigeProxy.sonFeigeData = t;
            var e = n.feigeProxy.getSonfeigeData(t.id);
            this.curEmailGroup = e;
            this.updateShowData();
            this.scrollView.scrollToTop();
        }
    },
});
