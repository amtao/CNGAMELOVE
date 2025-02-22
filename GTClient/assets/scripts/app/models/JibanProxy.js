var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("RedDot");
let timeProxy = require("TimeProxy");

var JibanProxy = function() { 

        this.UPDATE_JIBAN = "UPDATE_JIBAN";
        this.UPDATE_BELIEF = "UPDATE_BELIEF";
        this.UPDATE_HERO_JB = "UPDATE_HERO_JB";
        this.UPDATE_WIFE_JB = "UPDATE_WIFE_JB";
        this.UPDATE_HERO_SW = "UPDATE_HERO_SW";
        this.UPDATE_JIBAN_LV_UP = "UPDATE_JIBAN_LV_UP";
        this.UPDATE_SELECT_GROUP = "UPDATE_SELECT_GROUP";
        this.UPDATE_JI_BAN_ITEM = "UPDATE_JI_BAN_ITEM";
        this.UPDATE_WISHING_COUNT = "UPDATE_WISHING_COUNT";
        this.jibanList = null;
        this.heroJb = null;
        this.wifeJb = null;
        this.heroSw = null;
        this.jbItem = null;
        this.wishing = null;
        this.belief = -1;
        this.selectGroup = -1;
        this.delayShow = !1;
        this.delayList = [];

        this.ctor = function() {
            JsonHttp.subscribe(proto_sc.scpoint.list, this.onJibanList, this);
            JsonHttp.subscribe(proto_sc.scpoint.heroJB, this.onHeroJB, this);
            JsonHttp.subscribe(proto_sc.scpoint.wifeJB, this.onWifeJB, this);
            JsonHttp.subscribe(proto_sc.scpoint.heroSW, this.onHeroSW, this);
            JsonHttp.subscribe(proto_sc.scpoint.belief, this.onBelief, this);
            JsonHttp.subscribe( proto_sc.scpoint.selectGroup, this.onSelectGroup, this);
            JsonHttp.subscribe(proto_sc.scpoint.jbItem, this.onJbItem, this);
            JsonHttp.subscribe(proto_sc.user.wishTree, this.onWishingCount, this);
            JsonHttp.subscribe(proto_sc.user.plotFragments, this.onShowJibanItem, this);
            JsonHttp.subscribe(proto_sc.scpoint.cardFetter, this.onCardFetter, this);
            
        };
        this.clearData = function() {
            this.jibanList = null;
            this.heroJb = null;
            this.wifeJb = null;
            this.heroSw = null;
            this.belief = -1;
            this.selectGroup = -1;
            this.jbItem = null;
        };
        this.updateRed = function() {
            for (
                var t = localcache.getList(localdb.table_heropve),
                    e = !1,
                    o = !1,
                    i = 0;
                i < t.length;
                i++
            ) {
                var n = t[i];
                if (
                    (!o || 1 != n.type) &&
                    ((!e || 2 != n.type) &&
                        this.getJibanIsOpen(n) &&
                        !this.isOverStory(n.id))
                ) {
                    o =
                        4 == n.unlocktype ||
                        5 == n.unlocktype ||
                        6 == n.unlocktype ||
                        8 == n.unlocktype
                            ? this.hasNewStory(n.roleid)
                            : 1 == n.type || o;
                    e = 2 == n.type || e;
                }
            }
            r.change("jibanwife", e);
            r.change("jibanhero", o && timeProxy.funUtils.isOpenFun(timeProxy.funUtils.jibanView));
        };
        this.onHeroJB = function(t) {
            this.alertChange(this.heroJb, t, 1);
            null == this.heroJb
                ? (this.heroJb = t)
                : i.utils.copyList(this.heroJb, t);
            n.servantProxy.updateServantJiBanRoadRed();
            facade.send(this.UPDATE_HERO_JB);
        };
        this.onWifeJB = function(t) {
            this.alertChange(this.wifeJb, t, 2);
            null == this.wifeJb
                ? (this.wifeJb = t)
                : i.utils.copyList(this.wifeJb, t);
            facade.send(this.UPDATE_WIFE_JB);
        };
        this.onHeroSW = function(t) {
            this.alertChange(this.heroSw, t, 3);
            null == this.heroSw
                ? (this.heroSw = t)
                : i.utils.copyList(this.heroSw, t);
            facade.send(this.UPDATE_HERO_SW);
        };
        this.onSelectGroup = function(t) {
            this.selectGroup = t.id;
            facade.send(this.UPDATE_SELECT_GROUP);
        };
        this.onJbItem = function(t) {
            this.jbItem = t;
            facade.send(this.UPDATE_JI_BAN_ITEM);
        };
        this.onCardFetter = function(val) {
            this.cardFetter = val.cardFetter;
            this.updateRed();
        }
        this.onWishingCount = function(t) {
            this.wishing = t;
            facade.send(this.UPDATE_WISHING_COUNT);
        };
        this.onShowJibanItem = function(t) {
            i.utils.openPrefabView("jiban/JibanItemShow", null, {
                list: t
            });
        };
        this.alertChange = function(t, e, o) {
            if (null != e && null != t) {
                for (var r = e.length < 5, a = 0; a < e.length; a++) {
                    for (
                        var s = e[a], c = 0, _ = 0;
                        null != t && _ < t.length;
                        _++
                    )
                        t[_].id == s.id && (c = t[_].num);
                    if (c != s.num)
                        switch (o) {
                            case 1:
                                null ==
                                    (y = localcache.getItem(
                                        localdb.table_hero,
                                        s.id
                                    )) && cc.log(s.id + " is not find");
                                if (
                                    0 !=
                                    this.isJibanUp(c, s.num, y ? y.star : null)
                                ) {
                                    var d =
                                            this.getJibanLv(
                                                s.num,
                                                y ? y.star : null
                                            ).level % 1e3,
                                        u =
                                            this.getJibanLv(
                                                c,
                                                y ? y.star : null
                                            ).level % 1e3;
                                    facade.send("UNLOCK_AUTO_LOOK");
                                    i.utils.openPrefabView(
                                        "servant/ServantJibanUp",
                                        null,
                                        {
                                            type: 1,
                                            id: s.id,
                                            lv: d,
                                            orgLv: u
                                        }
                                    );
                                }
                                var p = {
                                    id: s.id,
                                    name: y ? y.name : "",
                                    org: c,
                                    add: s.num,
                                    url: l.uiHelps.getServantSpine(s.id),
                                    sp: i18n.t("SERVANT_JIBAN_TIP")
                                };
                                r &&
                                    (this.delayShow
                                        ? this.delayList.push(p)
                                        : i.utils.popView("AlertHeroView", p));
                                break;

                            case 2:
                                if (0 != this.isJibanUp(c, s.num, 99)) {
                                    (d =
                                        this.getJibanLv(s.num, 99).level % 1e3),
                                        (u =
                                            this.getJibanLv(c, 99).level % 1e3);
                                    facade.send("UNLOCK_AUTO_LOOK");
                                    i.utils.openPrefabView(
                                        "servant/ServantJibanUp",
                                        null,
                                        {
                                            type: 2,
                                            id: s.id,
                                            lv: d,
                                            orgLv: u
                                        }
                                    );
                                }
                                var h = localcache.getItem(
                                    localdb.table_wife,
                                    s.id
                                );
                                p = {
                                    id: s.id,
                                    name: n.playerProxy.getWifeName(s.id),
                                    org: c,
                                    add: s.num,
                                    url: l.uiHelps.getWifeBody(h.res),
                                    sp: i18n.t("WIFE_JIBAN_TIP")
                                };
                                r &&
                                    (this.delayShow
                                        ? this.delayList.push(p)
                                        : i.utils.popView("AlertHeroView", p));
                                break;

                            case 3:
                                var y;
                                p = {
                                    id: s.id,
                                    name: (y = localcache.getItem(
                                        localdb.table_hero,
                                        s.id
                                    )).name,
                                    org: c,
                                    add: s.num,
                                    url: l.uiHelps.getServantSpine(s.id),
                                    sp: i18n.t("SERVANT_ROLE_SW")
                                };
                                r &&
                                    (this.delayShow
                                        ? this.delayList.push(p)
                                        : i.utils.popView("AlertHeroView", p));
                        }
                }
                i.utils.popNext(!0);
            }
        };
        this.getHeroNextJb = function(t, e) {
            for (
                var o = localcache.getItem(localdb.table_hero, t),
                    i = localcache.getGroup(localdb.table_yoke, "star", o.star),
                    n = null,
                    l = 0;
                l < i.length;
                l++
            ) {
                var r = i[l].level % 1e3;
                if (e + 1 <= 30) {
                    if (r == e + 1) {
                        n = i[l];
                        break;
                    }
                } else n = i[i.length - 1];
            }
            return n;
        };
        this.getWifeNextJb = function(t) {
            for (
                var e = localcache.getGroup(localdb.table_yoke, "star", 99),
                    o = null,
                    i = 0;
                i < e.length;
                i++
            ) {
                if (e[i].level % 1e3 == t + 1) {
                    o = e[i];
                    break;
                }
            }
            return o;
        };
        this.setDelayShow = function(t) {
            this.delayShow = t;
            if (0 == this.delayShow && this.delayList.length > 0) {
                for (var e = 0; e < this.delayList.length; e++)
                    i.utils.popView("AlertHeroView", this.delayList[e]);
                this.delayList = [];
                i.utils.popNext(!0);
            }
        };
        this.getJibanLv = function(t, e) {
            var o = localcache.getGroup(localdb.table_yoke, "star", e);
            if (null == o) return 1;
            for (var i = null, n = 0; n < o.length; n++)
                t >= o[n].yoke && (i = o[n]);
            return i;
        };
        this.isJibanUp = function(t, e, o) {
            var i = localcache.getGroup(localdb.table_yoke, "star", o);
            if (null == i) return 0;
            for (var n = 0; n < i.length; n++)
                if (i[n].yoke > t && i[n].yoke <= e) return i[n].level;
            return 0;
        };
        this.onBelief = function(t) {
            if (-1 != this.belief && t.id != this.belief) {
                i.utils.popView("AlertHeroView", {
                    name: n.playerProxy.userData.name,
                    org: this.belief,
                    add: t.id,
                    url: "",
                    role: 1,
                    sp: i18n.t("SERVANT_ROLE_SW")
                });
                i.utils.popNext(!0);
            }
            this.belief = t.id;
            facade.send(this.UPDATE_BELIEF);
        };
        this.onJibanList = function(t) {
            this.jibanList = t;
            facade.send(this.UPDATE_JIBAN);
            this.updateRed();
        };
        this.getJibanType = function(t, e) {
            var o = [],
                i = localcache.getGroup(localdb.table_heropve, "roleid", t);
            if (null == i) return o;
            for (var n = 0; i && n < i.length; n++)
                i[n].type == e && o.push(i[n]);
            return o;
        };
        this.getJibanData = function(t, e) {
            if (null == this.jibanList) return null;
            for (var o = 0; o < this.jibanList.length; o++) {
                var i = this.jibanList[o];
                if (i.roleid == e && i.type == t) return i;
            }
            return null;
        };
        this.getHeroJB = function(t) {
            if (null == this.heroJb) return 0;
            for (var e = 0; e < this.heroJb.length; e++)
                if (this.heroJb[e].id == t) return this.heroJb[e].num;
            return 0;
        };

        /**获取所有伙伴的羁绊等级和羁绊值*/
        this.getAllHeroJBValueAndJBLevel = function(){
            let jibanlevel = 0;
            let jibanvalue = 0;
            if (n.servantProxy.servantList != null){
                for (let ii = 0; ii < n.servantProxy.servantList.length;ii++){
                    jibanlevel += (this.getHeroJbLv(n.servantProxy.servantList[ii].id).level % 1000);
                }
                if (this.heroJb != null){
                    for (let ii = 0; ii < this.heroJb.length;ii++){
                        jibanvalue += this.heroJb[ii].num;
                    }
                }
            }
            return [jibanlevel,jibanvalue];
        };

        this.getHeroJbLv = function(t) {
            var e = this.getHeroJB(t),
                o = localcache.getItem(localdb.table_hero, t),
                i = o ? o.star : 4;
            return this.getJibanLv(e, i);
        };


        this.getWifeJbLv = function(t) {
            var e = this.getWifeJB(t);
            return this.getJibanLv(e, 99);
        };
        this.getWifeJB = function(t) {
            if (null == this.wifeJb) return 0;
            for (var e = 0; e < this.wifeJb.length; e++)
                if (this.wifeJb[e].id == t) return this.wifeJb[e].num;
            return 0;
        };
        this.getHeroSW = function(t) {
            if (0 == t) return this.belief;
            if (null == this.heroSw) return 0;
            for (var e = 0; e < this.heroSw.length; e++)
                if (this.heroSw[e].id == t) return this.heroSw[e].num;
            return 0;
        };
        this.getMaxSW = function() {
            var t = this.belief;
            if (null == this.heroSw) return t;
            for (var e = 0; e < this.heroSw.length; e++)
                t = this.heroSw[e].num > t ? this.heroSw[e].num : t;
            return t;
        };
        this.isOverStory = function(t) {
            var e = localcache.getItem(localdb.table_heropve, t);
            if (e) {
                var o = this.getJibanExactData(t);
                if (o)
                    return -1 != t.toString().indexOf("jiban")
                        ? o.jbs && -1 != o.jbs.indexOf(t)
                        : o.id >= t;
            }
            return !1;
        };
        this.getJibanExactData = function(id) {
            if (null == this.jibanList) return null;
            for (var o = 0; o < this.jibanList.length; o++) {
                var i = this.jibanList[o];
                if (i.id == id) return i;
            }
            return null;
        };
        this.saveHeroStory = function(t) {
            var e = new proto_cs.scpoint.recored();
            e.id = t;
            JsonHttp.send(e);
        };
        this.sendGetAward = function(t, e) {
            void 0 === e && (e = !0);
            var o = new proto_cs.scpoint.story();
            o.id = t;
            this.isDuplicat(t) && i.alertUtil.alert18n("STORY_SELECT_DUPLICAT");
            JsonHttp.send(o, function() {
                e && n.timeProxy.floatReward();
            });
        };
        this.isDuplicat = function(t) {
            var e = localcache.getItem(localdb.table_storySelect2, t),
                o = e.group ? e.group.split("_") : "0";
            return !(o.length > 1) && parseInt(o[0]) <= this.selectGroup;
        };
        this.sendGetJYAward = function(t) {
            var e = new proto_cs.scpoint.jyStory();
            e.id = t;
            JsonHttp.send(e, function() {
                n.timeProxy.floatReward();
            });
        };
        this.sendGetZWAward = function(t) {
            var e = new proto_cs.scpoint.zwStory();
            e.id = t;
            JsonHttp.send(e, function() {
                n.timeProxy.floatReward();
            });
        };
        this.sendWishing = function(t, e) {
            var o = new proto_cs.user.wishPlay();
            o.num = e;
            o.id = t;
            JsonHttp.send(o, function(gg) {
                n.timeProxy.floatReward();
            });
        };
        this.sendWishInfo = function() {
            var t = new proto_cs.user.wishInfo();
            JsonHttp.send(t);
        };
        this.getJibanFirst = function(t) {
            for (
                var e = [],
                    o = localcache.getGroup(localdb.table_heropve, "type", t),
                    i = {},
                    l = {},
                    a = 0;
                a < o.length;
                a++
            ) {
                if (!i[(p = o[a]).roleid]) {
                    if (null == r[p.roleid]) {
                        for (
                            var s = this.getJibanType(p.roleid, t),
                                c = 0,
                                _ = 0;
                            _ < s.length;
                            _++
                        )
                            this.isOverStory(s[_].id) && c++;
                        r[p.roleid] = c >= s.length ? 0 : 1;
                    }
                    if (
                        (null == (u = this.getJibanData(p.type, p.roleid)) &&
                            0 == p.unlocktype) ||
                        (u &&
                            -1 == p.id.indexOf("jiban") &&
                            u.id >= parseInt(p.id))
                    ) {
                        i[p.roleid] = 1;
                        e.push(p);
                    } else if (this.getJibanIsOpen(p)) {
                        i[p.roleid] = 1;
                        e.push(p);
                    } else if (
                        1 == p.type &&
                        null != n.servantProxy.getHeroData(p.roleid) &&
                        null == u
                    ) {
                        i[p.roleid] = 1;
                        e.push(p);
                    } else if (
                        2 == p.type &&
                        null != n.wifeProxy.getWifeData(p.roleid) &&
                        null == u
                    ) {
                        i[p.roleid] = 1;
                        e.push(p);
                    } else if (3 == p.unlocktype) {
                        if (
                            (n.taskProxy.mainTask.id < p.unlock &&
                                (null != u ||
                                    null !=
                                        n.wifeProxy.getWifeData(p.roleid))) ||
                            n.taskProxy.mainTask.id > p.unlock
                        ) {
                            i[p.roleid] = 1;
                            e.push(p);
                        }
                    } else
                        (null == l[p.roleid] || l[p.roleid].id < p.id) &&
                            (l[p.roleid] = p);
                }
            }
            for (var d in l)
                if (1 != i[d]) {
                    var u;
                    (u = this.getJibanData(l[d].type, l[d].roleid)) && e.push(l[d]);
                }
            if (1 == t)
                for (a = 0; this.jbItem && a < this.jbItem.length; a++)
                    if (1 != i[this.jbItem[a].id] && this.jbItem[a].jibans) {
                        var p;
                        p = localcache.getItem(localdb.table_heropve,this.jbItem[a].jibans[0].id)
                        if(p)
                        {
                            let isHas = false;
                            for(let id in e)
                            {
                                let data = e[id];
                                if(data.roleid == p.roleid)
                                {
                                    isHas = true;
                                }
                            }
                            if(!isHas)
                            {
                                e.push(p);
                            }
                        }
                        // (p = localcache.getItem(
                        //     localdb.table_heropve,
                        //     this.jbItem[a].jibans[0].id
                        // )) && e.push(p);
                    }
            let self = this;
            let result = e.filter((data) => {
                return self.hasNewStory(data.roleid);
            });
            let nohas = e.filter((data) => {
                return !self.hasNewStory(data.roleid);
            });
            let sortFunc = (a, b) => {
                return a.roleid - b.roleid;
            };
            result.sort(sortFunc);
            let countFunc = (roleId) => {
                let tmpArray = [];
                for (var n = 0; n <= 7; n++) {
                    let count = this.getJbItemCount(roleId, n);
                    count > 0 && tmpArray.push({ star: n, num: count});
                }
                return tmpArray.length;
            }
            let has = nohas.filter((data) => {
                return countFunc(data.roleid) > 0;
            });
            let no = nohas.filter((data) => {
                return countFunc(data.roleid) <= 0;
            });
            has.sort(sortFunc);
            result = result.concat(has);
            no.sort(sortFunc);
            result = result.concat(no);
            return result;
        };
        this.getJibanIsOpen = function(t) {
            if (null == t) return !1;
            switch (t.unlocktype) {
                case 1:
                    var e = this.getHeroJB(t.roleid);
                    2 == t.type && (e = this.getWifeJB(t.roleid));
                    return e >= t.unlock;

                case 2:
                    break;

                case 3:
                    return n.taskProxy.mainTask.id > t.unlock;

                case 4:
                case 5:
                case 6:
                    // return null != this.getJbItemData(t.roleid) && n.servantProxy.getHeroData(t.roleid);
                case 8:
                    // return this.getJbItemData(t.roleid) != null && n.servantProxy.getHeroData(t.roleid);
                    return this.getJbItemData(t.roleid) != null
                    
                case 7: return false;
                case 10:
                    let bUnlock = false;
                    for(let i in this.cardFetter) {
                        if(i == t.id) {
                            bUnlock = true;
                            break;
                        }
                    } 
                 return bUnlock;
            }
            return !0;
        };

        // unlocktype: 1.4=羁绊, 10=卡牌, 其他=主线 现在只显示1.3.4.10 其他不显示
        this.getJbItemCount = function(t, unlocktype, o) {
            // if(!n.servantProxy.getHeroData(t)) return 0;
            void 0 === o && (o = 1);
            var i = 0;
            if (1 == unlocktype || 4 == unlocktype)
                for (
                    var l = localcache.getList(localdb.table_heropve), r = 0;
                    r < l.length;
                    r++
                ) {
                    (1 == l[r].unlocktype || 4 == l[r].unlocktype) &&
                     t == l[r].roleid &&
                     l[r].type == o &&
                     n.jibanProxy.getJibanIsOpen(l[r]) &&
                     i++;
                }
            else if(10 == unlocktype) {
                if(null != this.cardFetter) {
                    for(let id in this.cardFetter) {
                        let jbData = localcache.getItem(localdb.table_heropve, id);
                        if(jbData && jbData.roleid == t) {
                            i++;
                        }
                    }
                }
            } else {
                for (
                    var l = localcache.getList(localdb.table_heropve), r = 0;
                    r < l.length;
                    r++
                ) {
                        3 == l[r].unlocktype &&
                         t == l[r].roleid &&
                         l[r].type == o &&
                         n.jibanProxy.getJibanIsOpen(l[r]) &&
                         i++;
                }
            }
            //  else {
            //     if (null == this.jbItem) return 0;
            //     var a = this.getJbItemData(t);
            //     if (null == a) return 0;
            //     for (var s = 0; s < a.jibans.length; s++) {
            //         var c = localcache.getItem(
            //             localdb.table_heropve,
            //             a.jibans[s].id
            //         );
            //         c && c.star == e && i++;
            //     }
            // }
            return i;
        };

        this.getJbItemList = function(t, e) {
            var o = this;
            void 0 === e && (e = 1);
            var i = [],
                n = localcache.getList(localdb.table_heropve),
                l = this.getJbItemData(t);
            l && (i = i.concat(l.jibans));
            for (var r = 0; r < n.length; r++) {
                if (n[r].roleid == t && (1 == n[r].unlocktype || 3 == n[r].unlocktype) && n[r].type == e) {
                    var a = {};
                    a.id = n[r].id;
                    a.level = 1;
                    a.exp = 1;
                    i.push(a);
                } else if(n[r].roleid == t && 4 == n[r].unlocktype && o.getJibanIsOpen(n[r])) {
                    var a = {};
                    a.id = n[r].id;
                    a.level = 1;
                    a.exp = 1;
                    i.push(a);
                }
            }
            if(null != this.cardFetter) {
                for(let item in this.cardFetter) {
                    let tmpData = localcache.getItem(localdb.table_heropve, item);
                    if(tmpData.roleid == t) {
                        i.push({id: item});
                    }
                }
            } 
            i.sort(function(e, i) {
                var n = localcache.getItem(localdb.table_heropve, e.id),
                    l = localcache.getItem(localdb.table_heropve, i.id);
                if (null == n || null == l) {
                    cc.log(" roleId = ", t);
                    return 1;
                }
                var r = o.getJibanIsOpen(n) ? 0 : 1,
                    a = o.getJibanIsOpen(l) ? 0 : 1,
                    s = o.isOverStory(e.id) ? 0 : 1,
                    c = o.isOverStory(i.id) ? 0 : 1,
                    _ = 0 == r && 1 == s ? 0 : 1,
                    d = 0 == a && 1 == c ? 0 : 1;
                return _ != d
                    ? _ - d
                    : s != c
                    ? s - c
                    : r != a
                    ? r - a
                    : l.star - n.star;
            });
            return i;
        };

        this.getJbItemAddPro = function(t, e) {
            var o = {
                    pro: 0,
                    value: 0
                },
                i = localcache.getItem(localdb.table_heropve, t);
            if (i) {
                o.pro = i.specMsg;
                var n = localcache.getItem(localdb.table_heropveJbProp, e);
                if (n)
                    for (var l = 0; l < n.count.length; l++)
                        if (n.count[l].star == i.star) {
                            o.value = n.count[l].count;
                            break;
                        }
            }
            return o;
        };
        this.getJbItemLvData = function(t, e) {
            var o = this.getJbItemData(t);
            if (o)
                for (var i = 0; i < o.jibans.length; i++)
                    if (o.jibans[i].id == e) return o.jibans[i];
            return null;
        };
        this.getJbItemLv = function(t, e) {
            var o = this.getJbItemData(t);
            if (o)
                for (var i = 0; i < o.jibans.length; i++)
                    if (o.jibans[i].id == e) return o.jibans[i].level;
            return 1;
        };
        this.getJbItemData = function(t) {
            if (null == this.jbItem) return null;
            for (var e = 0; e < this.jbItem.length; e++)
                if (this.jbItem[e].id == t) return this.jbItem[e];
            return null;
        };
        this.hasNewStory = function(t) {
            for (var e = this.getJbItemList(t), o = 0; o < e.length; o++) {
                var i = localcache.getItem(localdb.table_heropve, e[o].id);
                if (
                    !n.jibanProxy.isOverStory(e[o].id) &&
                    n.jibanProxy.getJibanIsOpen(i)
                )
                    return !0;
            }
            return !1;
        };
        this.getJiBan = function(t) {
            var e = n.jibanProxy.wishing.have;
            if (null == e) return !1;
            for (var o = 0; o < e.length; o++) if (e[o] == t) return !0;
            return !1;
        };
        this.getTreeTypeCount = function(t) {
            var e = 0;
            if (this.wishing.have)
                for (var o = 0; o < this.wishing.have.length; o++) {
                    var i = localcache.getItem(
                        localdb.table_heropve,
                        this.wishing.have[o]
                    );
                    i && i.tree == t && e++;
                }
            return e;
        };

        /**
        *@param t hero_yoke_unlock表
        * t.type 1:解锁背景购买权限，set:编号，显示背景icon 2:解锁时装购买权限，set：时装编号，显示时装icon 3:获得奖励，set：道具种类，道具编号，道具数量 4:解锁表情包购买权限，set：编号 5:解锁对话及表情，set：编号 6:解锁培养药品使用上限 set：物品类型：物品数量
        */
        this.getServantJiBanDes = function(t){
            switch(t.type){
                case 1:{
                    let cg = localcache.getItem(localdb.table_herobg,t.set[0]);
                    return i18n.t("PARYNER_ROOMTIPS15",{v1:cg.name});
                }
                case 2:{
                    let cg = localcache.getItem(localdb.table_heroDress,t.set[0]);
                    return i18n.t("PARYNER_ROOMTIPS16",{v1:cg.name});
                }
                case 3:{
                    let str = ""
                    for (var ii = 0; ii < t.jiangli.length;ii++){
                        let cg = localcache.getItem(localdb.table_item,t.jiangli[ii].id)
                        str += cg.name + "、";
                    }
                    str = str.substring(0, str.length - 1);
                    return i18n.t("PARYNER_ROOMTIPS20",{v1:str});                    
                }
                case 4:{
                    let cg = localcache.getItem(localdb.table_hero_emojis,t.set[0]);
                    return i18n.t("PARYNER_ROOMTIPS17",{v1:cg.name});
                }
                case 5:
                    return i18n.t("PARYNER_ROOMTIPS18");
                case 6:{
                    // let str = ""
                    // for (var ii = 0; ii < t.set.length;ii++){
                    //     let cg = localcache.getItem(localdb.table_item,t.set[ii].itemid)
                    //     str += cg.name + "、";
                    // }
                    // str = str.substring(0, str.length - 1);
                    // return i18n.t("PARYNER_ROOMTIPS19",{v1:str});
                    return i18n.t("PARYNER_ROOMTIPS19")
                }
                case 7:{
                    let cg = localcache.getItem(localdb.table_item,t.set[0]);
                    return i18n.t("PARYNER_ROOMTIPS36",{v1:cg.name});
                }
            }
        };
    }
exports.JibanProxy = JibanProxy;
var StorySelectData = function() {
    this.id = "";
    this.context = "";
    this.nextid = "";
    this.tiaojian = 0;
    this.para = "";
}
exports.StorySelectData = StorySelectData;
