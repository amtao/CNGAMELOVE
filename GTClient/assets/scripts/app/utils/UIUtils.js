var utils = require("Utils");
var getEffectItem = require("GetEffectItem");
var config = require("Config");
var initializer = require("Initializer");
const HtmlTextParser = require("html-text-parser");
const _htmlTextParser = new HtmlTextParser();

function UIUtils() {
    //震动效果
    this.shakeArr = [[1, 1], [-1, -1], [-1, 1], [1, -1]];
    //
    //倒计时

        this.countDown = function(t, e, o, n, l, r, a) {
            void 0 === n && (n = !0);
            if (null != e && 0 != t) {
                e.unscheduleAllCallbacks();
                e.schedule(s, 1);
                s();
            }
            function s() {
                var s = t - utils.timeUtil.second;
                if (s > 0 && n)
                    if (r && "" != l) {
                        var c = {};
                        c[r] = utils.timeUtil.second2hms(s, a);
                        e.string = i18n.t(l, c);
                    } else
                        e.string =
                            (l && "" != l ? i18n.t(l) : "") +
                            utils.timeUtil.second2hms(s, a);
                else if (s <= 0) {
                    o && o();
                    e.unscheduleAllCallbacks();
                }
            }
        };
        this.scaleRepeat = function(t, e, o, i) {
            void 0 === e && (e = 0.8);
            void 0 === o && (o = 1.2);
            void 0 === i && (i = 1);
            if (null != t) {
                var n = t.scaleX,
                    l = t.scaleY;
                t.scaleX = n * e;
                t.scaleY = l * e;
                t.runAction(
                    cc.repeatForever(
                        cc.sequence(
                            cc.scaleTo(i, n * o, l * o),
                            cc.scaleTo(i, n * e, l * e)
                        )
                    )
                );
            }
        };
        this.fadeRepeat = function(t, e, o, i) {
            void 0 === e && (e = 0);
            void 0 === o && (o = 255);
            void 0 === i && (i = 1);
            if (null != t) {
                t.opacity = e;
                t.runAction(
                    cc.repeatForever(
                        cc.sequence(cc.fadeTo(i, o), cc.fadeTo(i, e))
                    )
                );
            }
        };
        this.fadeOver = function(t, e, o) {
            void 0 === e && (e = 0);
            void 0 === o && (o = 1);
            null != t && t.runAction(cc.fadeTo(o, e));
        };

        this.getRichTextOneByOne = function(content, start, end) {
            // var content = "谢大爷赏！大人您一定<size=30>大人您一定</size><b><color=#c3272b>福寿双全富贵无边家宅……</color></b>";
            var str = "";
            var count = 0;
            let newTextArray = _htmlTextParser.parse(content);
            for(var i=0; i<newTextArray.length; i++) {
                var textData = newTextArray[i];
                if(textData.style) {
                    if(textData.style.color)
                        str += "<color="+textData.style.color+">";            
                    if(textData.style.size)
                        str += "<size="+textData.style.size+">";
                    if(textData.style.bold)
                        str += "<b>";
                }
                for(var j=0; j<textData.text.length; j++) {
                    count++;
                    if(count > end) break;
                    if(start <= count) {
                        str += textData.text[j];
                    }
                }
                if(textData.style) {
                    if(textData.style.color)
                        str += "</color>";                    
                    if(textData.style.size)
                        str += "</size>";
                    if(textData.style.bold)
                        str += "</b>";
                }
            }
            return str;            
        };

        this.getRichTextLength = function(content) {
            var count = 0;
            let newTextArray = _htmlTextParser.parse(content);
            for(var i=0; i<newTextArray.length; i++) {
                var textData = newTextArray[i];
                for(var j=0; j<textData.text.length; j++) {
                    count++;
                }
            }
            return count;
        };

        this.showRichText = function(t, e, o, i) {
            void 0 === o && (o = 0.1);
            var self = this;
            if (null != t && "" != e && 0 != e.length)
                if (1 != e.length) {
                    t.string = "";
                    t.unscheduleAllCallbacks();
                    t.isRunShowText = !0;
                    t.context = e;
                    t.schedule(n, o);
                    n();
                } else {
                    t.unscheduleAllCallbacks();
                    t.isRunShowText = !1;
                    i && i();
                    t.string = e;
                }

            function n() {
                var o = self.getRichTextLength(t.string) ?self.getRichTextLength(t.string) : 0;
                if (o < self.getRichTextLength(e)) t.string = self.getRichTextOneByOne(e,0,o+1);                
                else {
                    t.unscheduleAllCallbacks();
                    t.isRunShowText = !1;
                    i && i();
                }
            }

        };
        this.showText = function(t, e, o, i) {
            void 0 === o && (o = 0.1);
            if (null != t && !!e && 0 != e.length)
                if (1 != e.length) {
                    t.string = "";
                    t.unscheduleAllCallbacks();
                    t.isRunShowText = !0;
                    t.context = e;
                    t.schedule(n, o);
                    n();
                } else {
                    t.unscheduleAllCallbacks();
                    t.isRunShowText = !1;
                    i && i();
                    t.string = e;
                }
            function n() {
                var o = t.string ? t.string.length : 0;
                if (o < e.length) t.string = e.substring(0, o + 1);
                else {
                    t.unscheduleAllCallbacks();
                    t.isRunShowText = !1;
                    i && i();
                }
            }
        };
        this.showNumChange = function(t, e, o, n, l, r, a, s) {
            void 0 === n && (n = 30);
            void 0 === s && (s = !0);
            if (null != t)
                if (e != o) {
                    t.numIndex = 1;
                    t.unscheduleAllCallbacks();
                    t.schedule(c, 0.04);
                    c();
                } else {
                    t.numIndex = n;
                    c();
                }
            function c() {
                var c = t.numIndex++,
                    _ = e + Math.floor(((o - e) / n) * c);
                _ = c >= n ? o : _;
                var d = s ? utils.utils.formatMoney(_) : _ + "";
                if (l)
                    if (r) {
                        var u = {};
                        u[r] = d;
                        d = i18n.t(l, u);
                    } else d = i18n.t(l) + " " + d;
                t.string = d;
                if (c >= n) {
                    t.unscheduleAllCallbacks();
                    a && a();
                }
            }
        };
        this.showPrgChange = function(t, e, o, i, n, l) {
            void 0 === e && (e = 0);
            void 0 === o && (o = 1);
            void 0 === i && (i = 1);
            void 0 === n && (n = 30);
            if (null != t) {
                n = n;
                i = i;
                e = e;
                o = null != o ? o : 1;
                t.progress = e;
                if (e != o) {
                    t.numIndex = 1;
                    t.unscheduleAllCallbacks();
                    t.schedule(r, 0.04);
                    r();
                }
            }
            function r() {
                var r = t.numIndex++,
                a = e + ((o - e) / n) * ((r % n) + 1);                    
                a = ((a = a < 0.05 ? 0 : a) > 1) ? 1 : a;              
                t.progress = a;
                if (r + 1 >= n * i) {
                    t.unscheduleAllCallbacks();
                    l && l();
                }
            }
        };
        this.getRwd = function(t) {
            for (
                var e = new Array(), o = t.split(","), i = 0;
                i < o.length;
                i++
            ) {
                var n = o[i].split("|"),
                    l = new s();
                l.id = n.length > 0 ? parseInt(n[0]) : 0;
                l.count = n.length > 1 ? parseInt(n[1]) : 0;
                l.kind = n.length > 2 ? parseInt(n[2]) : 0;
                e.push(l);
            }
            return e;
        };
        this.getRwdItem = function(t) {
            for (var e = new Array(), o = {}, i = 0; t && i < t.length; i++) {
                var n = t[i].itemid;
                if (1 != o[n]) {
                    o[n] = 1;
                    e.push({
                        id: n
                    });
                }
            }
            return e;
        };
        this.getItemNameCount = function(t, e) {
            var o = localcache.getItem(localdb.table_item, t);
            return i18n.t("COMMON_ADD", {
                n: o ? o.name : "",
                c: e
            });
        };

        // duration 秒数
        this.showShakeDuration = function(comp, duration, x, y, cb) {
            void 0 === x && (x = 4);
            void 0 === y && (y = 12);
            void 0 === duration && (duration = 1);
            if (null != comp) {
                var n = this;
                if (comp.orgx) {
                    comp.node.x = comp.orgx;
                    comp.node.y = comp.orgy;
                } else {
                    comp.orgx = comp.node.x;
                    comp.orgy = comp.node.y;
                }
                var count = 1;
                comp.numIndex = 0;
                comp.shakeAction = true;
                comp.unscheduleAllCallbacks();
                comp.schedule(sk, 0.04);
                comp.schedule(ht, 1);
                sk();
            }
            function sk() {
                var num = count++,
                a = num % 4;
                comp.node.x = n.shakeArr[a][0] * x + comp.orgx;
                comp.node.y = n.shakeArr[a][1] * x + comp.orgy;    
                if(!comp.shakeAction)
                    ht();
                else if((num/25.00)>duration)
                    ht();
            }

            function ht() {
                comp.numIndex++;
                if (comp.numIndex >= duration || comp.shakeAction == false) {
                    comp.node.x = comp.orgx;
                    comp.node.y = comp.orgy;
                    comp.shakeAction = false;
                    comp.unscheduleAllCallbacks();
                    cb && cb();
                }
            }
        };

        // duration 秒数
        this.showShakeNodeDuration = function(node, duration, x, y, cb) {
            void 0 === x && (x = 4);
            void 0 === y && (y = 12);
            void 0 === duration && (duration = 1);
            if (null != node) {
                var n = node.getComponent(cc.Component);
                n && this.showShakeDuration(n, duration, x, y, cb);
            }
        };
        this.moveNodeAction = function(sp, endPos, time, cb) {
            //起点
            let sp_x = sp.getPosition().x;
            let sp_y = sp.getPosition().y;
            //终点
            let node_x = endPos.x;
            let node_y = endPos.y;
            //曲线幅度
            let x_add = 50; //x_add_random;
            let y_add = 10; //y_add_random;
            let centre_x = node_x + (sp_x - node_x) / 2 + x_add;
            let centre_y = node_y + (sp_y - node_y) / 2 + y_add;
    
            let bezierArray = [];
            bezierArray.push(new cc.Vec2(sp_x, sp_y));
            bezierArray.push(new cc.Vec2(centre_x, centre_y));
            bezierArray.push(new cc.Vec2(node_x, node_y));
            let bezier = cc.bezierTo(time, bezierArray);
            let func = cc.callFunc(() => {
                cb && cb();
            });
            let seq = cc.sequence(bezier, func);
            sp.runAction(seq);
        }
        this.showShake = function(t, e, o, i) {
            void 0 === e && (e = 4);
            void 0 === o && (o = 12);
            if (null != t) {
                var n = this;
                if (t.orgx) {
                    t.node.x = t.orgx;
                    t.node.y = t.orgy;
                } else {
                    t.orgx = t.node.x;
                    t.orgy = t.node.y;
                }
                t.numIndex = 1;
                t.unscheduleAllCallbacks();
                t.schedule(l, 0.04);
                l();
            }
            function l() {
                var l = t.numIndex++,
                    r = (o - l) / o,
                    a = l % 4;
                t.node.x = n.shakeArr[a][0] * r * e + t.orgx;
                t.node.y = n.shakeArr[a][1] * r * e + t.orgy;
                if (l >= o) {
                    t.node.x = t.orgx;
                    t.node.y = t.orgy;
                    t.unscheduleAllCallbacks();
                    i && i();
                }
            }
        };
        this.showShakeNode = function(t, e, o, i) {
            void 0 === e && (e = 4);
            void 0 === o && (o = 12);
            if (null != t) {
                var n = t.getComponent(cc.Component);
                n && this.showShake(n, e, o, i);
            }
        };
        this.floatPos = function(t, e, o, i) {
            void 0 === e && (e = 0);
            void 0 === o && (o = 0);
            void 0 === i && (i = 1);
            if (null != t) {
                if (t.orgx) {
                    t.x = t.orgx;
                    t.y = t.orgy;
                } else {
                    t.orgx = t.x;
                    t.orgy = t.y;
                }
                t.x = t.orgx + e;
                t.y = t.orgy + o;
                t.runAction(
                    cc.repeatForever(
                        cc.sequence(
                            cc.moveTo(i, t.orgx - e, t.orgy - o),
                            cc.moveTo(i, t.orgx + e, t.orgy + o)
                        )
                    )
                );
            }
        };
        // 渐显渐隐
        this.fadeInOut = function(node, inOpacity, outOpacity, time) {
            void 0 === inOpacity && (inOpacity = 255);
            void 0 === outOpacity && (outOpacity = 0);
            void 0 === time && (time = 1);
            if (null != node) {                
                node.runAction(
                    cc.repeatForever(
                        cc.sequence(
                            cc.fadeTo(time, outOpacity),
                            cc.fadeTo(time, inOpacity)
                        )
                    )
                );
            }
        };

        //按比例修正icon大小
        this.resetIconSize = function(sprite, compWidth, compHeight, bForce) {
            if(null == sprite || null == sprite.node) {
                return;
            }
            sprite.sizeMode = cc.Sprite.SizeMode.TRIMMED;
            sprite.trim = true;
            sprite.sizeMode = cc.Sprite.SizeMode.CUSTOM
            if(sprite.node.width > compWidth || sprite.node.height > compHeight || bForce) {
                let scale = 1;
                if(sprite.node.width > sprite.node.height) {
                    scale = compWidth / sprite.node.width;
                } else {
                    scale = compHeight / sprite.node.height;
                }
                sprite.node.width *= scale;
                sprite.node.height *= scale;
            }
        };
    }
exports.UIUtils = UIUtils;
exports.uiUtils = new UIUtils();

var ItemSlotData = function() {
    this.count = 0;
    this.id = 0;
    this.kind = 0;
    this.itemid = 0;
}
exports.ItemSlotData = ItemSlotData;

function UIHelp() {

    this.getSuitIcon = function(name) {
        //临时瞎写的主角衣服套装icon路径，后期导入了需替换
        return config.Config.skin + "/res/clothe/" + name; 
    };
    this.getItemSlot = function(t) {
        return config.Config.skin + "/res/ico/" + t;
    };
    this.getItemColor = function(t) {
        return config.Config.skin + "/res/ico/fuyue_tk_dj_pz_" + t;
    };
    this.getStory = function(t) {
        return config.Config.skin + "/res/story/" + t;
    };
    this.getStoryBg = function(t) {
        return config.Config.skin + "/prefabs/story/" + t;
    };
    this.getServantStoryIcon = function(icon) {
        return config.Config.skin + "/res/ui/servant/" + icon;
    };
    this.getServantHead = function(t) {
        return config.Config.skin + "/res/servanthead/" + t;
    };
    this.getDiscountIcon = function(t) {
        return config.Config.skin + "/res/ui/dressshop/fzsc_sale" + t;
    };
    this.getDiscountBGIcon = function(t) {
        return config.Config.skin + "/res/ui/dressshop/fzsc_0" + t;
    };
    //获取伙伴预制体
    this.getServantSpine = function(heroID,isMyHero = true) {
        if(!isMyHero){
            return config.Config.skin + "/prefabs/servant/mk_" + heroID;
        }else{
            let heroDressID =  initializer.servantProxy.getHeroDress(heroID);
            if(0 == heroDressID){
                return config.Config.skin + "/prefabs/servant/mk_" + heroID;
            }else{
                let cfgDataArray = localcache.getFilters(localdb.table_heroDress, "id", heroDressID);
                if(cfgDataArray && cfgDataArray[0]){
                    return this.getServantSkinSpine(cfgDataArray[0].model);
                }
            }
            return config.Config.skin + "/prefabs/servant/mk_" + heroID;
        }
    };
    this.getStoryServantSpine = function(name) {
        return config.Config.skin + "/prefabs/cg/" + name;
    };
    this.getServantSkinSpine = function(dressName) {
        return config.Config.skin + "/prefabs/servant_skin/" + dressName;
    };
    //获取伙伴小预制体路径
    this.getServantSmallSpine = function(heroID) {
        let heroDressID =  initializer.servantProxy.getHeroDress(heroID);
        if(0 == heroDressID){
            return config.Config.skin + "/prefabs/servantsmall/mk_" + heroID;
        }else{
            let cfgDataArray = localcache.getFilters(localdb.table_heroDress, "id", heroDressID);
            if(cfgDataArray && cfgDataArray[0]){
                return this.getServantSkinSmallSpine(cfgDataArray[0].model);
            }
        }
        return config.Config.skin + "/prefabs/servantsmall/mk_" + heroID;
    };
    this.getServantSkinSmallSpine = function(dressName) {
        return config.Config.skin + "/prefabs/servant_skin_small/" + dressName;
    };
    this.getHead = function(t, e) {
        return (
            config.Config.skin + "/prefabs/role/head_" + (1 == t ? 1 : 0) + "_" + e
        );
    };
    this.getHeadH = function(t, e) {
        return (
            config.Config.skin + "/prefabs/role/headh_" + (1 == t ? 1 : 0) + "_" + e
        );
    };
    this.getHeadF = function(t, e) {
        return (
            config.Config.skin + "/prefabs/role/headf_" + (1 == t ? 1 : 0) + "_" + e
        );
    };
    this.getBody = function(t, e) {
        return (
            config.Config.skin + "/prefabs/role/body_" + (1 == t ? 1 : 0) + "_" + e
        );
    };
    this.getRoleSpinePart = function(t) {
        return config.Config.skin + "/prefabs/role/" + t;
    };
    this.getAnimalSpinePart = function(t) {
        return config.Config.skin + "/prefabs/animal/" + t;
    };
    this.getRolePart = function(t) {
        return config.Config.skin + "/res/clothe/" + t;
    };
    this.getHeroDressIcon = function(t) {
        return config.Config.skin + "/res/servant_skin_icon/" + t;
    };
    this.getEnemy = function(t) {
        return this.getServantSmallSpine(0 == t ? 1 : t);
    };
    this.getResIcon = function(t) {
        return config.Config.skin + "/res/resIcon/" + t;
    };
    this.getWifeHead = function(t) {
        return config.Config.skin + "/res/servanthead/" + t;
    };
    this.getWifeBody = function(t) {
        return config.Config.skin + "/prefabs/servant/mk_" + t;
    };
    this.getWifeSmallBody = function(t) {
        return config.Config.skin + "/prefabs/servantsmall/mk_" + t;
    };
    this.getSonHead = function(t, e) {
        return (
            config.Config.skin + "/res/childhead/" + (1 == e ? "boy" : "girl") + "_1"
        );
    };
    this.getSonBody = function(t, e) {
        return (
            config.Config.skin + "/prefabs/child/" + (1 == t ? "boy" : "girl") + "_1"
        );
    };
    this.getSonChengHead = function(t, e) {
        return (
            config.Config.skin +
            "/res/childhead/" +
            (1 == e ? "man" : "woman") +
            "_1"
        );
    };
    this.getSonChengBody = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == t ? "man" : "woman") +
            "_1"
        );
    };
    this.getStoryPrefab = function(t) {
        return config.Config.skin + "/prefabs/effect/story/" + t;
    };
    this.getBabyBody = function() {
        return config.Config.skin + "/prefabs/child/baby_1";
    };
    this.getKejuBody = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "man" : "woman") +
            "_1"
        );
    };
    this.getHonourIcon = function(t) {
        return config.Config.skin + "/res/honour/honour_" + t;
    };

    this.getFurnituresItem = function(t) {
        return config.Config.skin + "/res/furnitures/" + t;
    };

    this.getFurnituresItemSence = function(type,t) {
        return config.Config.skin + "/res/furniture" + type  +"/" + t;
    };

    this.getFurnituresBigImage = function(t) {
        return config.Config.skin + "/res/furniturebigs/" + t;
    };

    this.getSevenDay = function(t) {
        return config.Config.skin + "/res/active/seven/icon_day" + t;
    };
    this.getSevenDayLbl = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/active/seven/day" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/day" + t;
    };
    this.getSevenDayNum = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/active/seven/d" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/d" + t;
    };
    this.getCombineShopBtnIcon = function(iconName){
        return config.Config.skin + "/res/ui/shop/" + iconName;
    },
    this.getCombineShopTabIcon = function(iconName){
        return config.Config.skin + "/res/ui/common/" + iconName;
    },
    this.getCellBody = function(t) {
        return config.Config.skin + "/prefabs/pet/" + t;
    };
    this.getCellHeadIcon = function(t) {
        return config.Config.skin + "/res/pet/" + t;
    };
    this.getLookBuild = function(t) {
        return config.Config.skin + "/res/ui/look/" + t;
    };
    this.getDataUrl = function(t) {
        return (
            config.Config.skin +
            ("zh-ch" == config.Config.lang ? "" : "_" + config.Config.lang) +
            "/res/db/" +
            t
        );
    };
    this.getUIPrefab = function(t) {
        return config.Config.skin + "/prefabs/ui/" + t;
    };
    this.getJYPic = function(t) {
        return config.Config.skin + "/prefabs/jy/" + t;
    };
    this.getJYIcon = function(t) {
        return config.Config.skin + "/res/jingying/" + t;
    };
    this.getMatchFind = function(t) {
        return config.Config.skin + "/res/baowu/" + t;
    };
    this.getTreasureGroup = function(t) {
        return config.Config.skin + "/res/baowu/" + t;
    };
    this.getTreasure = function(t) {
        return config.Config.skin + "/res/baowu/" + t;
    };
    this.getAvatar = function(t) {
        return config.Config.skin + "/res/avatar/" + t;
    };
    this.getBlank = function(t) {
        return config.Config.skin + "/prefabs/avatar/blank" + t;
    };
    this.getChargeItem = function(t) {
        return config.Config.skin + "/res/charge/" + t;
    };
    this.getGoldLeafChargeIcon = function(iconName) {
        if(!iconName) iconName = 'fzsc_19';
        return config.Config.skin + "/res/ui/dressshop/" + iconName;
    };
    this.getStoryRoleName = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/storyname/" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/storyname/s" + t;
    };
    this.getXunfangIcon = function(t) {
        return config.Config.skin + "/res/xufang/" + t;
    };
    this.getLimitActivityBg = function(t) {
        return (
            config.Config.skin +
            ("zh-ch" == config.Config.lang ? "" : "_" + config.Config.lang) +
            "/res/limitactivity/" +
            t
        );
    };
    this.getAchieveIcon = function(t) {
        return config.Config.skin + "/res/chengjiu/" + t;
    };

    this.getAchieveImg = function(t){
        return config.Config.skin + "/res/ui/achieve/" + t;
    };

    this.getJiaoyouImg = function(t){
        return config.Config.skin + "/res/ui/jiaoyou/" + t;
    };

    /**帮会活动图片*/
    this.getUnionTaskIcon = function(t){
        return config.Config.skin + "/res/uniontask/" + t;
    };

    /**获取帮会图片*/
    this.getUnionIcon = function(t){
        return config.Config.skin + "/res/ui/union/" + t;
    };

    this.getServantSkillIcon = function(t) {
        return config.Config.skin + "/res/servantskill/" + t;
    };
    this.getKidSmallHead = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "boy_" : "girl_") + t
        );
    };
    this.getKidSmallBody = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "boy_" : "girl_") + t
        );
    };
    this.getKidChengHead = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "man_" : "woman_") + t
        );
    };
    this.getKidChengBody = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "man_" : "woman_") + t
        );
    };
    this.getKidMarryBody = function(t,e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == t ? "man_" : "woman_") + "1"
        );
    };
    this.getChuXingIcon = function(t) {
        return config.Config.skin + "/res/chuxing/" + t;
    };
    this.getXingLiIcon = function(t) {
        return config.Config.skin + "/res/ico/" + t;
    };
    this.getKidSmallHead_2 = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "boy_" : "girl_") + t
        );
    };
    this.getKidSmallBody_2 = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "boy_" : "girl_") + t
        );
    };
    this.getKidChengHead_2 = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "man_" : "woman_") + t
        );
    };
    this.getKidChengBody_2 = function(t, e) {
        return (
            config.Config.skin +
            "/prefabs/child/" +
            (1 == e ? "man_" : "woman_") + t
        );
    };
    this.getVoiceName = function(t, e) {
        return (
            config.Config.skin +
            ("zh-ch" == config.Config.lang ? "" : "_" + config.Config.lang) +
            "/res/voice/" +
            (1 == t ? "hero_" : "wife_") +
            e
        );
    };
    this.getLogo = function() {
        return config.Config.skin + "/res/logo/" + config.Config.logo;
    };
    this.getLangSprite = function(t) {
        return "zh-ch" == config.Config.lang
            ? ""
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/" + t;
    };
    this.getLangPrefab = function(t) {
        return "zh-ch" == config.Config.lang
            ? ""
            : config.Config.skin + "_" + config.Config.lang + "/prefabs/" + t;
    };
    this.getLangSp = function(t) {
        //t = 4 == t ? 1 : 1 == t ? 4 : t;
        if (t > 4){
            t = 1;
        }
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/ui/common/common_prop" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/common/common_prop" + t;
    };
    this.getCardSpecialFrame = function(quality) {
        return config.Config.skin + "/res/ui/card/kp_tsk" + quality;
    };
    this.getPinzhiStr = function(num) {
        switch(num) {
            case 1:
            case 2:
            case 3:
            case 4: 
                return i18n.t("COMMON_PROP" + num);
            case 5: return i18n.t("PINZ_QUANNENG");
            case 6: return i18n.t("PINZ_JUNHENG");
        }
    };
    this.getPinzhiPic = function(num) {
        switch(num) {
            case 1: return config.Config.skin + "/res/ui/servant/qishi";
            case 2: return config.Config.skin + "/res/ui/servant/zhimou";
            case 3: return config.Config.skin + "/res/ui/servant/zhenglve";
            case 4: return config.Config.skin + "/res/ui/servant/meili";
        }
        return config.Config.skin + "/res/ui/servant/qishi";
    }
    this.getPinzhiPicNew = function(num) {
        switch(num) {
            case 1: return config.Config.skin + "/res/ui/card/kpsj_icon_qishi_small";
            case 2: return config.Config.skin + "/res/ui/card/kpsj_icon_moulve_small";
            case 3: return config.Config.skin + "/res/ui/card/kpsj_icon_gonglve_small";
            case 4: return config.Config.skin + "/res/ui/card/kpsj_icon_meili_small";
        }
        return config.Config.skin + "/res/ui/card/kpsj_icon_qishi_small";
    }
    this.getBaowuIcon = function(url) {
        return config.Config.skin + "/res/ico/" + url;
    };
    this.getQualitySp = function (qualityId, skin) {
        var path = skin == 1 ? "/res/ui/cardQuality/" : "/res/ui/cardQuality2/";
        qualityId = qualityId ? qualityId : 1;
        return config.Config.skin + path + "quality_" + qualityId;
    };
    this.getQualitySpNew = function(qualityId, skin) {
        qualityId = qualityId ? qualityId : 1;
        let name = skin == 1 ? "kp_bg_fj_" : "kp_bg_"
        return config.Config.skin + "/res/ui/cardQuality2/" + name + qualityId;
    };

    this.getQualitySpSmallNew = function(qualityId, skin) {
        qualityId = qualityId ? qualityId : 1;
        let name = "kp_bg_fj_s"
        return config.Config.skin + "/res/ui/cardQuality2/" + name + qualityId;
    };

    /**获取战斗的卡片品质框*/
    this.getFightCardQualitySp = function(qualityId) {
        return this.getQualitySpSmallNew(qualityId);
    };

    /**获取搜集的卡牌技能图标*/
    this.getFightCardSkillIcon = function(epValue){
        switch(epValue){
            case 1:
                return config.Config.skin + "/res/ui/fightnew/kpsj_icon_qishi2";
            case 2:
                return config.Config.skin + "/res/ui/fightnew/kpsj_icon_moulve2";
            case 3:
                return config.Config.skin + "/res/ui/fightnew/kpsj_icon_gonglve2";
            case 4:
                return config.Config.skin + "/res/ui/fightnew/kpsj_icon_meili2";
            case 5:
                return config.Config.skin + "/res/ui/fightnew/kpsj_icon_quanneng";
        }
    };

    this.getQualityLbFrame = function(qualityId) {
        qualityId = qualityId ? qualityId : 1;
        return config.Config.skin + "/res/ui/cardQuality2/kp_dimian_pj_" + qualityId;
    };
    this.getCardStar = function(bShow) {
        let val = bShow ? 2 : 1;
        return config.Config.skin + "/res/ui/card/kp_bg_xingji" + val;
    };
    this.getCardYinhen = function(bShow) {
        let val = bShow ? 1 : 2;
        return config.Config.skin + "/res/ui/card/kp_dimian_ky_" + val;
    };
    this.getFlowerProp = function(bShow, prop) {
        let val = bShow ? "" : "_1";
        return config.Config.skin + "/res/ui/card/kp_dimian_sx_" + prop + val;
    };
    this.getCardQualityColor = function(qualityId) {
        switch(qualityId) {
            case 1:
                return new cc.Color(121, 164, 66, 255);
            case 2:
                return new cc.Color(66, 117, 164, 255);
            case 3:
                return new cc.Color(170, 99, 159, 255);
            case 4:
                return new cc.Color(171, 120, 60, 255);
        }

    };
    this.getQualityFrame = function (qualityId, skin) {
        var path = skin == 1 ? "/res/ui/cardFrame/" : "/res/ui/cardFrame2/";
        qualityId = qualityId ? qualityId : 1;
        return config.Config.skin + path + "frame_" + qualityId;
    };
    this.getStarFrame = function (isLight) {
        return config.Config.skin + "/res/ui/card/"+(isLight?'sx_07':'sx_08');
    };
    this.getCardFrame = function (cardName) {
        return config.Config.skin + "/res/card/"+cardName;
    };
    this.getFuyueCardFrame = function (cardName) {
        return config.Config.skin + "/res/cardfuyue/"+cardName;
    };
    this.getTianCiCardEffect = function (cardName) {
        return config.Config.skin + "/prefabs/tianCiEffect/" + cardName;
    };

    this.getCardTagFrame = function (quality) {
        return config.Config.skin + "/res/ui/cardQuality2/quality_" +  + quality;
    };
    this.getCardSmallFrame = function (cardName) {
        return config.Config.skin + "/res/cardsmall/"+cardName;
    };
    this.getCardSmallLongFrame = function (cardName) {
        return config.Config.skin + "/res/cardlong/"+cardName;
    };
    this.getCardIconFrame = function (cardName) {
        return config.Config.skin + "/res/ico/"+cardName;
    };
    this.getShuxingIcon = function(shuxing) {
        return config.Config.skin + "/res/ui/common/shuxing"+shuxing;
    };
    this.getActivityBtn = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/ui/activity/" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/" + t;
    };
    this.getSnowmanIcon = function(t) {
        return config.Config.skin + "/res/snowman/snowman_" + t;
    };
    this.getGuWuIcon = function(t) {
        return config.Config.skin + "/res/ui/zhongyuan/guwu_" + t;
    };
    this.getHedengIcon = function(t) {
        return config.Config.skin + "/res/hedeng/hedeng_" + t;
    };
    this.getChatBlank = function(t) {
        return config.Config.skin + "/res/avatar/chat/" + t + "k";
    };
    this.getPurchaseIcon = function(t) {
        return config.Config.skin + "/res/ui/purchase/" + t;
    };
    this.getActivityUrl = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/prefabs/activity/" + t
            : config.Config.skin + "_" + config.Config.lang + "/prefabs/" + t;
    };
    this.getChengHaoUrl = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/chenghao/" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/" + t;
    };
    this.getGridPointUrl = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/ui/activitygrid/xnhd_point_0" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/activitygrid/xnhd_point_0" + t;
    };
    this.getGridPointBGUrl = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/ui/activitygrid/xnhd_0" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/activitygrid/xnhd_0" + t;
    };
    this.getGridPlacardUrl = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/ui/activitygrid/xnhd_touzi_0" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/activitygrid/xnhd_touzi_0" + t;
    };
    this.getChengHaoIcon = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/chenghao/" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/" + t;
    };
    this.getRankIcon = function(t) {
        return config.Config.skin + "/res/ui/rank/" + t;
    };
    this.getRankBg = function(t) {
        switch(t) {
            case 1:
            case 2:
            case 3:
                return this.getRankIcon("jsxl_bt_dm_" + t);
            default:
                return this.getRankIcon("ph_bg_disi");
        }
    };
    this.getFlowerPlant = function(t, e) {
        if (0 == t) return "";
        switch (e) {
            case 0:
                t = 1e4;
                break;

            case 1:
                t = 2e4;
        }
        return config.Config.skin + "/prefabs/plant/" + t;
    };
    this.getChatSpine = function(t) {
        return config.Config.skin + "/prefabs/avatar/chat" + t;
    };
    this.getGuoliIcon = function(t) {
        return config.Config.skin + "/res/ui/guoli/img_qd" + t;
    };
    this.getDeatilBg = function(t) {
        return config.Config.skin + "/res/ui/servant/pro_bg" + t;
    };
    this.getSpringBz = function(t) {
        return config.Config.skin + "/res/ui/spring/baozhu_" + t;
    };
    this.getJbTitleBg = function(t) {
        return config.Config.skin + "/res/jiban/title_bg_" + t;
    };
    this.getJbTitle = function(t) {
        return config.Config.skin + "/res/jiban/jb_title_" + t;
    };
    this.getJbColor = function(t) {
        switch(t) {
            case 0: {
                return new cc.Color(59, 133, 92, 255); //绿
            } break;
            case 1: {
                return new cc.Color(80, 96, 124, 255); //蓝
            } break;
            case 2: {
                return new cc.Color(160, 117, 193, 255); //紫
            } break;
            case 3: {
                return new cc.Color(202, 90, 102, 255); //红
            } break;
            case 4: {
                return new cc.Color(246, 135, 74, 255); //橙
            } break;
            case 6: {
                return new cc.Color(255, 149, 149, 255); //粉
            } break;
            case 7: {
                return cc.Color.BLACK;
            }
        }
        return cc.Color.BLACK;
    }
    this.getJbBg = function(t) {
        switch(t) {
            case 1:
            case 4:
                t = 1;
                break;
            case 10:
                break;
            default:
                t = 2;
                break;
        }
        return config.Config.skin + "/res/jiban/story_bg_tip" + t;
    };
    this.getJbTitleWord = function(t) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/jiban/jiban_word_" + t
            : config.Config.skin + "_" + config.Config.lang + "/res/ui/jiban_word_" + t;
    };
    this.getJbTitleTxt = function(t) {
        switch(t) {
            case 1:
            case 4:
                t = 1;
                break;
            case 10:
                break;
            default:
                t = 2;
                break;
        }
        return i18n.t("JIBAN_TITLE_" + t);
    };
    this.getWorldTree = function(t) {
        t = (t = Math.ceil(t / 5)) > 3 ? 3 : t;
        return config.Config.skin + "/prefabs/ui/flower/tree" + t;
    };
    this.getServantSkinIcon = function(t) {
        return config.Config.skin + "/res/servant_skin_icon/" + t;
    };
    this.getClotheProImg = function(t, e) {
        return "zh-ch" == config.Config.lang
            ? config.Config.skin + "/res/ui/clothe/clothe_pro_" + t + "_" + e
            : config.Config.skin +
                  "_" +
                  config.Config.lang +
                  "/res/ui/clothe_pro_" +
                  t +
                  "_" +
                  e;
    };
    this.getClotheProStr = function(t, e) {
        let str = "";
        let iE = parseInt(e);
        let func = (num) => {
            switch(num) {
                case 2: 
                    str = i18n.t("PINZ_HUOBAN");
                    break;
                case 3:
                    str = i18n.t("PINZ_FUSHI");
                    break;
                case 4:
                    str = i18n.t("PINZ_TUDI");
                    break;
                case 5:
                    str = func(iE) + i18n.t("PINZ_ALL");
                    break;
            }
        }
        let iT = parseInt(t);
        func(iT);
        if(iT != 5) {
            switch(iE) {
                case 1: str += i18n.t("COMMON_PROP1");
                case 2: str += i18n.t("COMMON_PROP2");
                case 3: str += i18n.t("COMMON_PROP3");
                case 4: str += i18n.t("COMMON_PROP4");
                case 5: str += i18n.t("COMMON_PROP5");
            }
        }
        return str;
    };
    this.getChouQianImg = function(t, e) {
        return config.Config.skin + "/res/ui/qixi/qian/" + t + "_" + e;
    };
    this.getChouQianKuangImg = function(t) {
        return config.Config.skin + "/res/ui/qixi/qian/" + t;
    };
    this.getChatLocalSprite = function(t) {
        return config.Config.skin + "/res/ui/chat/" + t;
    };

    this.getPartnerZoneBgImg = function(t){
        return config.Config.skin + "/res/servantBg/partnerzone_bg" + t;
    };

    this.getSmallServantBgImg = function(t){
        return config.Config.skin + "/res/servantSmallBg/servant_bg" + t;
    };

    this.getServantJiBanRoadImg = function(t){
        return config.Config.skin + "/res/ui/partner/" + t;
    };

    this.getFuYueImg = function(t){
        return config.Config.skin + "/res/ui/fuyue/" + t;
    };

    this.getPlayerSpinePrefab = function(t){
        return config.Config.skin + "/prefabs/ui/RoleSpine";
    };

    this.getUnpackPic = function(t){
        return config.Config.skin + "/res/ui/unpack/" + t;
    };

    this.getUserclothePic = function(t){
        return config.Config.skin + "/res/ui/userclothe/" + t;
    };

    this.getCommonPic = function(t){
        return config.Config.skin + "/res/ui/common/" + t;
    };

    this.getUserHeadItemPrefab = function(t){
        return config.Config.skin + "/prefabs/ui/user/UserHeadNew";
    };
    this.getCrushIconPath = function() {
        return config.Config.skin + "/res/ui/activityCrush/";
    };
    this.getCrushComboNum = function(num) {
        return config.Config.skin + "/res/ui/activityCrush/combo_"+num;
    };

    this.getHelpPrefab = function () {
        return config.Config.skin + "/prefabs/ui/combangzhu"
    };

    this.getFuyueMainScenePrefab = function () {
        return config.Config.skin + "/prefabs/ui/main/FuYueMainScene"
    };

    this.getLoadingImg = function (t) {
        return config.Config.skin + "/res/ui/unpack/loading" + t;
    };

    this.getStoryRecordBg = function(t){
        return config.Config.skin + "/res/ui/storyrecord/juqing_bg_0" + t;
    };

    this.getUICardPic = function(t){
        return config.Config.skin + "/res/ui/card/" + t;
    };

    this.getQiFuPic = function(t){
        return config.Config.skin + "/res/ui/qifu/" + t;
    };

    this.getGiftBanner = function(index) {
        return config.Config.skin + "/res/limitactivity/gift" + index;
    };

    this.getGiftIcon = function(id) {
        return config.Config.skin + "/res/ui/activity/lb_icon_" + id;
    };

    this.getRankActIcon = function(id) {
        return config.Config.skin + "/res/ui/activity/banner_" + id;
    };

    this.getCardPveNumber = function (numChar, isRed) {
        let pngName = isRed ? "red_" : "green_";
        return config.Config.skin + "/res/ui/cardpve/pack/" + pngName + numChar;
    };

    this.getActIcon = function(path) {
        return config.Config.skin + "/res/ui/" + path;
    };

    this.getMinGamePic = function(path){
        return config.Config.skin + "/res/ui/spaceMiniGame/" + path;
    };

    /**获取水榭乐师的头像icon*/
    this.getMusicianHead = function(id){
        return config.Config.skin + "/res/ui/club/" + id;
    };

    /**获取水榭乐师的spine预制*/
    this.getMusicianSpine = function(id){
        return config.Config.skin + "/prefabs/club/" + id;
    };
}
exports.UIHelp = UIHelp;
exports.uiHelps = new UIHelp();

function GetEffect() {
    this.list = new Array();
    //获取显示效果动画
    this.prefab = null;

    this.showClick = function(t, e, o, i) {
        void 0 === i && (i = 5);
        this.show(e, t.getLocation(), o, i);
    };
    this.show = function(t, e, o, i) {
        void 0 === i && (i = 5);
        if (null == this.prefab) this.loadItem(t, e, o, i);
        else {
            t.x -= cc.winSize.width / 2;
            t.y -= cc.winSize.height / 2;
            e.x -= cc.winSize.width / 2;
            e.y -= cc.winSize.height / 2;
            for (var n = 0; n < i; n++) this.showItem(t, e, o);
        }
    };
    this.loadItem = function(t, e, o, i) {
        void 0 === i && (i = 5);
        var n = this;
        cc.resources.load(config.Config.skin + "/prefabs/ui/GetEffectItem", function(
            l,
            r
        ) {
            if (null != r) {
                MemoryMgr.saveAssets(r);
                n.prefab = r;
                n.show(t, e, o, i);
            } else cc.warn(l + " name load error!!!");
        });
    };
    this.showItem = function(t, e, o) {
        var i = cc.instantiate(this.prefab),
            l = i.getComponent(getEffectItem);
        cc.director
            .getScene()
            .getChildByName("Canvas")
            .addChild(i);
        this.list.push(l);
        l.node.x = e.x + 100 * Math.random() - 50;
        l.node.y = e.y;
        if (l) {
            l.url = o;
            l.des = t;
        }
    };
}
exports.GetEffect = GetEffect;
exports.getEffectUtils = new GetEffect();

function ClickEffect() {
    //获取显示效果动画
    this.prefab = null;
    this.curItem = null;

    this.showEffect = function(t, cb) {
        this.show(t.getLocation(), cb);
    };
    this.show = function(t, cb) {
        if (null == this.prefab) this.loadItem(t);
        else {
            t.x -= cc.winSize.width / 2;
            t.y -= cc.winSize.height / 2;
            this.showItem(t, cb);
        }
    };
    this.loadItem = function(t) {
        var e = this;
        cc.loader.loadRes(config.Config.skin + "/prefabs/effect/point", function(
            o,
            i
        ) {
            if (null != i) {
                //utils.utils.saveAssets(config.Config.skin + "/prefabs/effect/point")
                MemoryMgr.saveAssets(i)
                e.prefab = i;
                e.show(t);
            } else cc.warn(o + " name load error!!!");
        });
    };
    this.showItem = function(t, cb) {
        if (null != this.curItem && null == this.curItem.parent) {
            this.curItem.removeFromParent();
            this.curItem.destroy();
            this.curItem = null;
        }
        null == this.curItem && (this.curItem = cc.instantiate(this.prefab));
        var e = this.curItem;   
        if (e) {
            null == e.parent &&
                cc.director
                    .getScene()
                    .getChildByName("Canvas")
                    .addChild(e);
            e.x = t.x;
            e.y = t.y;
            // let particle = e.getChildByName("nParticles");
            // particle.x = particle.y = 0;
            // let array = particle.getComponentsInChildren(cc.ParticleSystem);
            // for(let i = 0, len = array.length; i < len; i++) {
            //     array[i].resetSystem();
            // }
            // particle.active = !1;
            e.active = !0;
            let urlLoad = e.getComponentInChildren("UrlLoad");
            urlLoad.reset();
            urlLoad.url = config.Config.skin + "/prefabs/effect/pointEff";
            let skeleton = e.getComponentInChildren(sp.Skeleton);
            skeleton.setAnimation(0, "idle", false);
            var o = e.getComponent(cc.Component);
            o && o.unscheduleAllCallbacks();
            o && o.scheduleOnce(() => {
                e.active = !1;
            }, 0.8);
            cb && cb(e);
        }
    };
}

exports.ClickEffect = ClickEffect;
exports.clickEffectUtils = new ClickEffect();
