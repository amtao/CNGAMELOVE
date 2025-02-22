
let renderItem = require("RenderListItem");
let Initializer = require("Initializer");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let ShaderUtils = require("ShaderUtils");

cc.Class({
    extends: renderItem,

    properties: {
        itemOne:cc.Node,

        tname:cc.Label,
        brichTxt:cc.RichText,
        nLV:cc.Label,
        imageGet:cc.Node,
        imageLost:cc.Node,

        laoutItems:cc.Node,

        titleBtn:cc.Button,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.stringMod = '<color=COLOR>STRING</color>'
        this.level = 100000000
        this.items = []
        this.hc = cc.color("#646464")
        this.ys = cc.color(182,114,53,255)
        this.stringc = {
            pt:"#B67235",
            t1:"#79a442",
            t2:"#4275a4",
            t3:"#aa639f",
            t4:"#ab783c",
        }

        this.typePercent = {
            1:0.3,
            2:0.3,
            3:0.2,
        }

        this.addcstring = "179067"
        this.reducecstring = "ea5656"

        this.nmx = ["FX" ,'N',"M","X",]
    },

    start () {
        
    },

    showData(){
        let data = this._data
        let cards = data.card
        let len = cards.length
        let itemLens = this.items.length
        let isGet = true
        let self = this
        for (let i = 0; i < 4; i++) {
            let itemsd = i<itemLens?this.items[i]:(function(){
                let itemm = cc.instantiate(self.itemOne)
                self.laoutItems.addChild(itemm)
                self.items.push(itemm)
                return itemm
            }());
            if(i<len) {
                let bos = this.renderCard(itemsd, cards[i], data.unlock)
                isGet = isGet ? (bos === true) : false
                itemsd.active = true
            }else{itemsd.active = false}
        }
        this.renderTitles(isGet,data)
        this.renderTxt(isGet,data)
    },
    renderCard(node, card, unlockType) {
        let rebool = false
        let mods = Initializer.cardProxy.cardMap[card]
        if(mods) {
            //console.log(mods)
            rebool = true
            this.level = this.level <= mods.star ? this.level:mods.star
        }
        if(this.level>=10000){
            this.level = 0
        }
        var tmod = localcache.getItem(localdb.table_card, card);
        let imagebg = node.getComponent(UrlLoad)
        imagebg.url = UIUtils.uiHelps.getQualitySpSmallNew(tmod.quality);
        let images = node.getChildByName('image').getComponent(UrlLoad)
        images.url = UIUtils.uiHelps.getCardSmallFrame(tmod.picture);

        if(unlockType == 2) { //没有上阵置灰
            ShaderUtils.shaderUtils.setImageGray(images.getComponent(cc.Sprite)
             , Initializer.cardProxy.tmpTeamList.indexOf(card) < 0);
        }
        
        let mm = node.getChildByName('mm')
        mm.active = !rebool
        let name = node.getChildByName('name').getComponent(cc.Label)
        name.string = tmod.name

        let nStar = node.getChildByName('kp_bg_star');
        nStar.active = rebool;
        if(rebool) {
            let label = nStar.getComponentInChildren(cc.Label);
            label.string = mods.star + 1;
        }
        return rebool
    },

    renderTitles(isGet,data) {
        this.titleBtn.enable = (data.unlock != 2 && isGet) || (data.unlock == 2 && data.bJiban)
        this.titleBtn.interactable = (data.unlock != 2 && isGet) || (data.unlock == 2 && data.bJiban)
        let bShowLv = isGet && data.unlock != 2;
        this.tname.string = bShowLv ? i18n.t("SERVANT_JI_BAN_TITLE_LV", {value:data.name}): data.name
        this.nLV.node.active = bShowLv
        this.nLV.string = this.level + 1
        this.tname.node.color = this.titleBtn.interactable ? this.ys : this.hc
    },

    renderTxt(isGet,data){
        let bs = this.creatStringByTempLate(this.stringc.pt,i18n.t("DALISI_OWNER_TIP"))
        let cards = data.card
        let len = cards.length
        for (let i = 0; i < len; i++) {
            let md = localcache.getItem(localdb.table_card, cards[i])
            let name = '【'+md.name+'】'
            let quality = md.quality
            bs += this.creatStringByTempLate(this.stringc['t'+quality],name)
            bs += this.creatStringByTempLate(this.stringc.pt, i===len-1?",":"、")
        }
        let txt = this.creatSkillString(data,isGet)
        if(!isGet){
            bs = i18n.t("DALISI_OWNER_TIP")
            for (let i = 0; i < len; i++) {
                let md = localcache.getItem(localdb.table_card, cards[i])
                bs += '【'+md.name+'】'
                bs += i===len-1?",":"、"
            }
            bs+=txt
        }else{
            txt = this.creatStringByTempLate(this.stringc.pt,txt)
            bs+=txt
        }
        bs = this.creatStringByTempLate("#646464",bs)
        this.imageGet.active = isGet
        this.imageLost.active = !isGet
        this.brichTxt.string = bs
    },

    creatSkillString(data,isGet){
        let bufa = data.buff
        let unlock = data.unlock
        let bufftype = data.bufftype
        let txt = i18n.t("CARD_TYPE" + unlock + "_STRING" + bufftype)
        if(unlock ===  1){
            if(bufftype === 1 || bufftype === 2 || bufftype === 3){
                txt = txt.replace("X", isGet?this.creatStringByTempLate(this.addcstring,"X"):"X")
            }
        }else if(unlock === 2){
            if(bufftype === 100000){
                txt = txt.replace("X", isGet?this.creatStringByTempLate(this.addcstring,"X"):"X")
            }else if(bufftype === 2){
                txt = txt.replace("X", isGet?this.creatStringByTempLate(this.reducecstring,"X"):"X")
            }
        }
        
        let le2 = this.nmx.length
        for (let i = 0; i < bufa.length; i++) {
            for (let j = 0; j < le2; j++) {
                let m = this.nmx[j]
                if(txt.indexOf(m)>=0){
                    if(m === "FX"){
                        txt = txt.replace(m, (bufa[i]+100) + "%")
                    }else if(m === "X"){
                        txt = txt.replace(m, (bufa[i]+this.level*(unlock === 1?this.typePercent[bufftype]:0)) + "%")
                    }else{
                        txt = txt.replace(m, m==="M"? i18n.t("COMMON_PROP"+bufa[i]):bufa[i])
                    }
                    break;
                }
            }
        }
        return txt
    },

    creatStringByTempLate(color,string){
        let temp = this.stringMod
        temp = temp.replace("STRING",string) 
        temp = temp.replace("COLOR",color) 
        return temp
    }

    // update (dt) {},
});
