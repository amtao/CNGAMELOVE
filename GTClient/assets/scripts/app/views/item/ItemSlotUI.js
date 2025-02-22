var i = require("RenderListItem");
var n = require("UIUtils");
var l = require("UrlLoad");
var r = require("Utils");
var a = require("Initializer");
var s = require("BagProxy");
var c = require("TimeProxy");
var myShaderUtils = require("ShaderUtils");
var ItemSlotUI = cc.Class({
    extends: i,
    properties: {
        imgSlot: l,
        colorFrame: l,
        lblcount: cc.Label,
        lblNameCount: cc.Label,
        lblName: cc.Label,
        nodeCount: cc.Node,
        effect: cc.Node,
        propNode: cc.Node,
        nodeProp: cc.Node,
        urlProp: l,
        lblProp: cc.Label,
        lbPropName: cc.Label,
        bgSp: cc.Sprite,
        userJob: l,
        sp_lightbac:cc.Node,
        height: 200,
        nCountBg: cc.Node,
        nodeExtra:cc.Node,

        lock:cc.Node,
    },
    ctor() {
        this._icosp = null;
        this._slotsp = null;
    },

    setGray(t) {
        void 0 === t && (t = !1);
        if (this.imgSlot) {
            null == this._icosp && (this._icosp = this.imgSlot.node.getComponent(cc.Sprite));
            this._icosp && myShaderUtils.shaderUtils.setImageGray(this._icosp, t);
        }
        // if (this.colorFrame) {
        //     null == this._slotsp && (this._slotsp = this.colorFrame.node.getComponent(cc.Sprite));
        //     this._slotsp && myShaderUtils.shaderUtils.setImageGray(this._slotsp, t);
        // }
        this.bgSp && myShaderUtils.shaderUtils.setImageGray(this.bgSp, t);
    },
    onClickShowInfo() {
        var t = this._data;
        if (t) switch (t.kind) {
        case s.DataType.WIFE:
            r.utils.openPrefabView("wife/WifeInfo", !1, localcache.getItem(localdb.table_wife, t.id));
            break;
        // case s.DataType.CLOTHE:
        //     r.utils.openPrefabView("itemClothesInfo", false, t);
        //     break;
        case s.DataType.USER_JOB:
            r.utils.openPrefabView("itemClothesInfo", false, t);
            break;
        case s.DataType.HERO:
        case s.DataType.HERO_JB:
        case s.DataType.HERO_SW:
        case s.DataType.BOOK_EXP:
        case s.DataType.SKILL_EXT:
        case s.DataType.WIFE_EXP:
        case s.DataType.WIFE_FLOWER:
        case s.DataType.WIFE_HAOGAN:
        case s.DataType.WIFE_JB:
        case s.DataType.WIFE_LOVE:
            break;
        case s.DataType.HERO_DRESS:
            r.utils.openPrefabView("servant/HeroClothesInfo", false, t);
            break;
        case s.DataType.USER_SUIT:
            if (null == localcache.getItem(localdb.table_card, t.id) && (t.kind == s.DataType.USER_SUIT || 0 == t.kind)) return;
            r.utils.openPrefabView("ItemInfo", !1, t);
            break;
        default:
            if (null == localcache.getItem(localdb.table_item, t.id) && (t.kind == s.DataType.ITEM || 0 == t.kind)) return;
            r.utils.openPrefabView("ItemInfo", !1, t);
        }
    },
    getImgSlotUrl() {
        var t = this._data;
        if (t) switch (t.kind) {
        case s.DataType.HERO:
        case s.DataType.HERO_JB:
        case s.DataType.HERO_SW:
        case s.DataType.BOOK_EXP:
        case s.DataType.SKILL_EXT:
            return n.uiHelps.getServantHead(t.id);
        case s.DataType.WIFE:
        case s.DataType.WIFE_EXP:
        case s.DataType.WIFE_FLOWER:
        case s.DataType.WIFE_HAOGAN:
        case s.DataType.WIFE_JB:
        case s.DataType.WIFE_LOVE:
            var e = localcache.getItem(localdb.table_wife, t.id);
            return n.uiHelps.getWifeHead(e.res);
        case s.DataType.HEAD_BLANK:
            var o = localcache.getItem(localdb.table_userblank, t.id);
            return o ? n.uiHelps.getBlank(o.blankmodel) : "";
        case s.DataType.CLOTHE:
            var cg = localcache.getItem(localdb.table_userClothe, t.id)
            if (cg  && cg.model){
                var i =cg.model.split("|");
                return n.uiHelps.getRolePart(i[0]);
            }
            return "";
        case s.DataType.JB_ITEM:
            var l = localcache.getItem(localdb.table_heropve, t.id);
            return n.uiHelps.getServantHead(l.roleid);
        case s.DataType.HERO_CLOTHE:
            var r = localcache.getItem(localdb.table_heroClothe, t.id);
            return n.uiHelps.getServantSkinIcon(r.model);
        case s.DataType.CHENGHAO:
            var a = localcache.getItem(localdb.table_fashion, t.id);
            return a ? n.uiHelps.getChengHaoIcon(a.simg) : "";
        case s.DataType.USER_JOB:
            return n.uiHelps.getHead(0, t.id);
        case s.DataType.HERO_DRESS:{
            if(null != t.id){
                let heroDresssArray = localcache.getFilters(localdb.table_heroDress, "id", t.id);
                if(heroDresssArray && heroDresssArray.length > 0){
                    return n.uiHelps.getHeroDressIcon(heroDresssArray[0].model);
                }
            }
        }break;
        case s.DataType.BAOWU_ITEM:
        case s.DataType.BAOWU_SUIPIAN: {
            let itemBaowu = localcache.getItem(localdb.table_item, t.id);
            let baowuData = localcache.getItem(localdb.table_baowu, itemBaowu.icon);
            return n.uiHelps.getBaowuIcon(baowuData.picture);
        } break;
        case s.DataType.BUSINESS_ITEM: {
            let baowuData = localcache.getItem(localdb.table_wupin, t.id);
            return n.uiHelps.getItemSlot(baowuData.picture);
        } break;
        case s.DataType.HERO_BG: {
            let cfg = localcache.getItem(localdb.table_herobg, t.id);
            return n.uiHelps.getSmallServantBgImg(cfg.icon);
        }
        break;
        case s.DataType.FISHFOOD_ITEM: {
            return n.uiHelps.getItemSlot(t.id);
        }
        break;
        
        // case s.DataType.HERO_EMOJI: {
            
        // }
        // break;
        default:
            var c = localcache.getItem(localdb.table_item, t.id + "");
            return n.uiHelps.getItemSlot(c ? c.icon: t.id);
        }
    },
    isSpKind(t) {
        return (t == s.DataType.HEAD_BLANK || t == s.DataType.HERO || t == s.DataType.WIFE || t == s.DataType.CLOTHE || t == s.DataType.WIFE_EXP || t == s.DataType.WIFE_FLOWER || t == s.DataType.WIFE_HAOGAN || t == s.DataType.WIFE_JB || t == s.DataType.WIFE_LOVE || t == s.DataType.HERO_JB || t == s.DataType.HERO_SW || t == s.DataType.BOOK_EXP || t == s.DataType.SKILL_EXT || t == s.DataType.HERO_CLOTHE);
    },
    isSpKindName(t) {
        return (t == s.DataType.HEAD_BLANK || t == s.DataType.HERO || t == s.DataType.WIFE || t == s.DataType.CLOTHE || t == s.DataType.JB_ITEM || t == s.DataType.HERO_CLOTHE);
    },
    showData() {
        var t = this._data;
        if (t) {
            0 == ItemSlotUI._clothe_item_id && (ItemSlotUI._clothe_item_id = r.utils.getParamInt("clother_item"));
            t.id = t.id ? t.id: t.itemid;
            t.count = t.num ? t.num: t.count;
            var e = localcache.getItem(localdb.table_item, t.id + ""),
            i = this.isSpKind(t.kind);
            if(t.kind === s.DataType.USER_JOB && this.userJob) {
                this.userJob.url = this.getImgSlotUrl();
                this.userJob.loadHandle = () => {
                    var skeletons = this.userJob.node.getComponentsInChildren(sp.Skeleton);
                    for (var k = 0; k < skeletons.length; k++) {
                        if(skeletons[k]) {
                            skeletons[k].animation = "";
                        }
                    }
                }
            }
            
            this.effect && (i || 1 == t.id || 1200 == t.id || t.id == ItemSlotUI._clothe_item_id || t.kind == s.DataType.CHENGHAO || (e && e.kind == s.ItemType.PROP_ADD) ? (this.effect.active = !0) : (this.effect.active = !1));
            this.nodeCount && (this.nodeCount.active = t.count && t.count > 1);
            this.nCountBg && (this.nCountBg.active = t.count && t.count > 1);
            var _ = this.isSpKindName(t.kind); //a.playerProxy.getKindIdName(t.kind, t.id)
            this.lblcount && (this.lblcount.string = _ ? " " : r.utils.formatMoney(t.count && t.count > 0 ? t.count: 0));
            if (this.lblNameCount && t.count) if (_) this.lblNameCount.string = i18n.t("COMMON_ADD", {
                n: a.playerProxy.getKindIdName(t.kind, t.id),
                c: 1
            });
            else if (t.kind == s.DataType.CHENGHAO) {
                var d = localcache.getItem(localdb.table_fashion, t.id + "");
                this.lblNameCount.string = i18n.t("COMMON_ADD", {
                    n: d ? d.name: "",
                    c: r.utils.formatMoney(t.count)
                });
            }else if (t.kind == s.DataType.HERO_DRESS) {
                this.lblNameCount && (this.lblNameCount.string = a.playerProxy.getKindIdName(t.kind, i && !_ ? 0 : t.id));
            }  
            else if(t.kind == s.DataType.BUSINESS_ITEM){
                let itemcfg = localcache.getItem(localdb.table_wupin, t.id);
                this.lblNameCount.string = i18n.t("COMMON_ADD", {
                    n: itemcfg ? itemcfg.name: "",
                    c: r.utils.formatMoney(t.count)
                });
            }         
            else {
                var u = localcache.getItem(localdb.table_item, t.id + "");
                this.lblNameCount.string = i18n.t("COMMON_ADD", {
                    n: u ? u.name: "",
                    c: r.utils.formatMoney(t.count)
                });
            }
            if (null != this.nodeProp) {
                this.nodeProp.active = (t.kind == s.DataType.CLOTHE || t.kind == s.DataType.USER_JOB);
                if (this.nodeProp.active) {
                    var p = null;
                    if (t.kind == s.DataType.CLOTHE) {
                        p = localcache.getItem(localdb.table_userClothe, t.id);
                        this.nodeProp.active = p.prop && p.prop.length > 0;
                        if (this.nodeProp.active){
                            if (1 == p.prop_type) {//prop_type-->1-基础数值非加成
                                this.lblProp.string = "+" + p.prop[0].value;
                                this.urlProp.url = n.uiHelps.getLangSp(p.prop[0].prop);
                            } else {
                                this.urlProp.url = n.uiHelps.getClotheProImg(p.prop_type, p.prop[0].prop);
                                let epData = a.playerProxy.getUserEpData(p.prop_type);
                                let addPerInfo = Math.floor(p.prop[0].value / 100);
                                let propEpData = a.playerProxy.getPropDataByIndex(p.prop[0].prop,epData,addPerInfo*0.01);
                                this.lblProp.string = "+" + addPerInfo + "%" +"("+propEpData+")";
                            }
                            this.lbPropName && (this.lbPropName.string = n.uiHelps.getPinzhiStr(p.prop[0].prop));
                        }
                    } else {
                        p = localcache.getItem(localdb.table_userjob, t.id);
                        if (p.prop) {
                            this.lblProp.string = "+" + p.prop.value;
                            this.urlProp.url = n.uiHelps.getLangSp(p.prop.prop);
                            this.lbPropName && (this.lbPropName.string = n.uiHelps.getPinzhiStr(p.prop.prop));
                        } else {
                            this.nodeProp.active = false;
                        }
                    }
                }
            }
            this.lblName && (this.lblName.string = a.playerProxy.getKindIdName(t.kind, i && !_ ? 0 : t.id));
            this.propNode && (this.propNode.active = null != t.prop);
            this.sp_lightbac && (this.sp_lightbac.active = null != t.innerlight)
            this.nodeExtra && (this.nodeExtra.active = t.extra != null);
            if (this.lblcount && t.showStr != null){
                this.lblcount.node.active = true;
                this.lblcount.string = t.showStr;
            }


            //------------------add lock and others -----------------------
            this.lock && (this.lock.active = t.lock === 1? true:false)
            let mod = t.kind === 205?localcache.getItem(localdb.table_furniture, t.id):t.kind === 206?localcache.getItem(localdb.table_furniture_drawing, t.id):{}
            if(t.kind === 206 || t.kind === 205){
                this.lblName && (this.lblName.string = mod.name)
                this.lblcount && (this.lblcount.string =  t.count)
                let quality = mod.quality?mod.quality+1:2
                this.colorFrame && (this.colorFrame.url = n.uiHelps.getItemColor(quality))
                this.imgSlot && (this.imgSlot.url = n.uiHelps.getFurnituresItem(mod.picture))
                if (this.lblNameCount && t.count){
                    this.lblNameCount.string = i18n.t("COMMON_ADD", {
                        n: mod.name,
                        c: t.count
                    });  
                }
                return
            }
            if(t.kind === 207 || t.kind === 208){
                let itemd = localcache.getItem(localdb.table_item, t.id)
                let picture = itemd.icon
                let quality = itemd.color + 1;
                this.imgSlot && (this.imgSlot.url = n.uiHelps.getFurnituresItem(picture))  
                this.colorFrame && (this.colorFrame.url = n.uiHelps.getItemColor(quality))
                return 
            }
            //------------------add lock and others end ----------------------

            //换了个位置其他没有改过

            if (this.colorFrame) {
                this.colorFrame.node.active = t.kind != s.DataType.HEAD_BLANK;
                if (i || t.kind == s.DataType.CHENGHAO) this.colorFrame.url = n.uiHelps.getItemColor(5);
                else if (t.kind == s.DataType.CLOTHE){
                    this.colorFrame.url = n.uiHelps.getItemColor(5);
                }
                else if (t.kind == s.DataType.JB_ITEM) {
                    var l = localcache.getItem(localdb.table_heropve, t.id);
                    this.colorFrame.url = n.uiHelps.getItemColor(l.color);
                }
                else if(t.kind == s.DataType.BAOWU_ITEM || t.kind == s.DataType.BAOWU_SUIPIAN){
                    let itemcfg = localcache.getItem(localdb.table_baowu, e.id + "");
                    if (itemcfg != null)
                        this.colorFrame.url = n.uiHelps.getItemColor(itemcfg.quality + 1);
                }
                else if(t.kind == s.DataType.BUSINESS_ITEM){
                    let itemcfg = localcache.getItem(localdb.table_wupin, t.id);
                    if (itemcfg != null)
                        this.colorFrame.url = n.uiHelps.getItemColor(itemcfg.quality+1);
                }
                else if(t.kind == s.DataType.FISHFOOD_ITEM){
                    let itemcfg = localcache.getItem(localdb.table_game_item, t.id);
                    if (itemcfg != null)
                        this.colorFrame.url = n.uiHelps.getItemColor(itemcfg.star+1);
                }
                else {
                    var c = e && e.color ? e.color: 2;
                    c = c < 2 ? 2 : c;
                    this.colorFrame.url = n.uiHelps.getItemColor(c);
                }
            }

            if (this.imgSlot) {
                let self = this;
                this.imgSlot.node.scaleX = this.imgSlot.node.scaleY = i ? 0.85 : 1;
                this.imgSlot.loadHandle = () => {
                    let sp = self.imgSlot.node.getComponent(cc.Sprite);
                    if(sp && self.node && self.node.isValid) {
                        let bCard = t.kind == s.DataType.CARD_SUIPIAN || t.kind == s.DataType.USER_SUIT;
                        let height = null;
                        if(self.colorFrame) {
                            height = bCard ? (self.colorFrame.node.height - 4) : (self.colorFrame.node.height * 0.9);
                        } else if(self.height < 200) {
                            height = self.height;
                        } else {
                            height = self.node.height > self.height ? self.height : self.node.height; //有些大小有问题的用参数修改
                        }
                        if(null != height) {
                            n.uiUtils.resetIconSize(sp, height, height, bCard); 
                        }
                    }
                }
                this.imgSlot.url = this.getImgSlotUrl();
            }
        }
    },
});

ItemSlotUI._clothe_item_id = 0;
