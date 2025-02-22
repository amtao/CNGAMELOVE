var e = module,
    o = exports;
function i(t) {
    var e = function() {
        this.getJson = function() {
            var e = t.split(".");
            return (
                '{"' + e[1] + '":{"' + e[2] + '":' + JSON.stringify(this) + "}}"
            );
        };
    };
    e.key = t;
    e.class = t;
    return e;
}
window.proto_sc = {
    loginMod: {
        loginAccount: i("proto_sc.loginMod.loginAccount")
    },


    furniture:{
        intergral: i("proto_sc.furniture.intergral"),       //login 返回数据家园相关
        open: i("proto_sc.furniture.open"),       // 已经通过的关卡
        hook: i("proto_sc.furniture.hook"),       // 挂机信息
        copy: i("proto_sc.furniture.copy"),       // 战斗信息
        warehouse: i("proto_sc.furniture.warehouse"),       // 家具信息
        msgwin: i("proto_sc.furniture.msgwin"),       // 家具item
        buyScore: i("proto_sc.furniture.buyScore"),       // 花费元宝购买次数buyCount
        shop: i("proto_sc.furniture.shop"),       //玉牌商店信息
        display: i("proto_sc.furniture.display"),       //布局信息
    },

    user: {
        user: i("proto_sc.user.user"),
        fuserStatus: i("proto_sc.user.fuserStatus"),
        guide: i("proto_sc.user.guide"),
        pvb: i("proto_sc.user.pvb"),
        pvb2: i("proto_sc.user.pvb2"),
        ep: i("proto_sc.user.ep"),
        baseep: i("proto_sc.user.baseep"),
        addep: i("proto_sc.user.addep"),
        sonbaseep: i("proto_sc.user.sonbaseep"),
        sonaddep: i("proto_sc.user.sonaddep"),
        herobaseep: i("proto_sc.user.herobaseep"),
        heroaddep: i("proto_sc.user.heroaddep"),
        clothebaseep: i("proto_sc.user.clothebaseep"),
        clotheaddep: i("proto_sc.user.clotheaddep"),
        cardbaseep: i("proto_sc.user.cardbaseep"),
        cardaddep: i("proto_sc.user.cardaddep"),
        baowubaseep: i("proto_sc.user.baowubaseep"),
        baowuaddep: i("proto_sc.user.baowuaddep"),
        addition: i("proto_sc.user.addition"),
        percentage: i("proto_sc.user.percentage"),
        fuser: i("proto_sc.user.fuser"),
        fuserhw: i("proto_sc.user.fuserhw"),
        win: i("proto_sc.user.win"),
        changjing: i("proto_sc.user.changjing"),
        banben: i("proto_sc.user.banben"),
        paomadeng: i("proto_sc.user.paomadeng"),
        heroShow: i("proto_sc.user.heroShow"),
        qifu: i("proto_sc.user.qifu"),
        wishTree: i("proto_sc.user.wishTree"),
        plotFragments: i("proto_sc.user.plotFragments"),
        cardHeroEp: i("proto_sc.user.cardHeroEp"),
        pvewin: i("proto_sc.user.pvewin"),
        clotheDamage: i("proto_sc.user.clotheDamage"),
    },
    card: {
        drawCard: i("proto_sc.card.drawCard"),
        cardList: i("proto_sc.card.cardList"),
        cardsys: i("proto_sc.card.cardsys"),
        addcard: i("proto_sc.card.addcard"),
        cardstory: i("proto_sc.card.cardstory"),
        cfg: i("proto_sc.card.cfg"),
        act: i("proto_sc.card.act"),
        updatecard: i("proto_sc.card.updatecard"),
        equipCard: i("proto_sc.card.equipCard"),
        fight: i("proto_sc.card.fight"),
    },
    baowu:{
        drawBaowu:i("proto_sc.baowu.drawBaowu"),
        updatebaowu:i("proto_sc.baowu.updatebaowu"),
        baowusys:i("proto_sc.baowu.baowusys"),
        baowuList: i("proto_sc.baowu.baowuList"),
        addbaowu: i("proto_sc.baowu.addbaowu"),
        drawBaowu: i("proto_sc.baowu.drawBaowu"),
    },
    recommend: {
        list: i("proto_sc.recommend.list"),
    },
    search:{
        list: i("proto_sc.search.list"),
    }, 
    hero: {
        heroList: i("proto_sc.hero.heroList"),
        heroChat: i("proto_sc.hero.heroChat"),
        skin: i("proto_sc.hero.skin"),
        heroDress: i('proto_sc.hero.heroDress'),
        setClothe: i('proto_sc.hero.setClothe'),
        jingLi: i("proto_sc.hero.jingLi"),
        jiaQi: i("proto_sc.hero.jiaQi"),
        travel: i("proto_sc.hero.travel"),
        hello: i("proto_sc.hero.hello"),
        activationMail: i("proto_sc.hero.activationMail"),
        tokenFetters:i("proto_sc.hero.tokenFetters"),
        useItem: i("proto_sc.hero.useItem"),
        jibanAward: i("proto_sc.hero.jibanAward"),
        heroshop: i("proto_sc.hero.heroshop"),
        heroBlank: i("proto_sc.hero.heroBlank"),
        heroEmoji: i("proto_sc.hero.heroEmoji"),        
        visit: i("proto_sc.hero.visit"), //问候拜访
        right: i("proto_sc.hero.right"), //答题 对诗 猜拳
        invite: i("proto_sc.hero.invite"), //获取邀约事件
        inviteInfo: i("proto_sc.hero.inviteInfo"), //获取邀约信息
        food: i("proto_sc.hero.food"), //翻牌子信息
        fish: i("proto_sc.hero.fish"), //钓鱼的信息
        collect: i("proto_sc.hero.collect"), //风物志收集的信息
        buy: i("proto_sc.hero.buy"), //邀约购买的次数
    },
    xinchun:{
        xinchungame:i('proto_sc.xinchun.xinchungame'),
        shop:i('proto_sc.xinchun.shop'),
        exchange:i('proto_sc.xinchun.exchange'),
        myQxRid: i("proto_sc.xinchun.myQxRid"),
        qxRank: i("proto_sc.xinchun.qxRank"),
    },
    wife: {
        wifeList: i("proto_sc.wife.wifeList"),
        base: i("proto_sc.wife.base"),
        win: i("proto_sc.wife.win"),
        firstborn: i("proto_sc.wife.firstborn"),
        wifeChat: i("proto_sc.wife.wifeChat")
    },
    son: {
        sonList: i("proto_sc.son.sonList"),
        base: i("proto_sc.son.base"),
        qList: i("proto_sc.son.qList"),
        cList: i("proto_sc.son.cList"),
        win: i("proto_sc.son.win"),
        lilianSeatNum: i("proto_sc.son.lilianSeatNum"),
        lilianList: i("proto_sc.son.lilianList"),
        firstkeju: i("proto_sc.son.firstkeju"),
        tdDrec: i("proto_sc.son.tdDrec")
    },
    item: {
        itemList: i("proto_sc.item.itemList"),
        hecheng: i("proto_sc.item.hecheng")
    },
    jingYing: {
        coin: i("proto_sc.jingYing.coin"),
        food: i("proto_sc.jingYing.food"),
        army: i("proto_sc.jingYing.army"),
        exp: i("proto_sc.jingYing.exp"),
        qzam: i("proto_sc.jingYing.qzam"),
        win: i("proto_sc.jingYing.win"),
        weipai: i("proto_sc.jingYing.weipai")
    },
    bag: {
        bag_list: i("proto_sc.bag.bag_list"),
        bagList1: i("proto_sc.bag.bagList1"),
        bagList2: i("proto_sc.bag.bagList2")
    },
    school: {
        base: i("proto_sc.school.base"),
        list: i("proto_sc.school.list"),
        level: i("proto_sc.school.level")
    },
    system: {
        server_list: i("proto_sc.system.server_list"),
        errror: i("proto_sc.system.errror"),
        sys: i("proto_sc.system.sys"),
        randname: i("proto_sc.system.randname"),
        kefu: i("proto_sc.system.kefu"),
        unopen_notice: i("proto_sc.system.unopen_notice"),
        unconn_notice: i("proto_sc.system.unconn_notice"),
        server_info: i("proto_sc.system.server_info")
    },
    role: {
        rolelist: i("proto_sc.role.rolelist")
    },
    ranking: {
        shili: i("proto_sc.ranking.shili"),
        guanka: i("proto_sc.ranking.guanka"),
        love: i("proto_sc.ranking.love"),
        shiliKua: i("proto_sc.ranking.shiliKua"),
        clubKua: i("proto_sc.ranking.clubKua"),
        myclubkuaRid: i("proto_sc.ranking.myclubkuaRid"),
        mobai: i("proto_sc.ranking.mobai"),
        selfRid: i("proto_sc.ranking.selfRid"),
        win: i("proto_sc.ranking.win")
    },
    laofang: {
        laofang: i("proto_sc.laofang.laofang"),
        pets: i("proto_sc.laofang.pets"),
        mingwang: i("proto_sc.laofang.mingwang"),
        win: i("proto_sc.laofang.win")
    },
    wordboss: {
        menggu: i("proto_sc.wordboss.menggu"),
        ge2dan: i("proto_sc.wordboss.ge2dan"),
        ge2danMyDmg: i("proto_sc.wordboss.ge2danMyDmg"),
        shop: i("proto_sc.wordboss.shop"),
        scoreRank: i("proto_sc.wordboss.scoreRank"),
        myScore: i("proto_sc.wordboss.myScore"),
        hurtRank: i("proto_sc.wordboss.hurtRank"),
        rwdLog: i("proto_sc.wordboss.rwdLog"),
        g2dkill: i("proto_sc.wordboss.g2dkill"),
        mgft: i("proto_sc.wordboss.mgft"),
        g2dft: i("proto_sc.wordboss.g2dft"),
        win: i("proto_sc.wordboss.win")
    },
    fengxiandian: {
        info: i("proto_sc.fengxiandian.info"),
        qingAn: i("proto_sc.fengxiandian.qingAn")
    },
    chenghao: {
        chInfo: i("proto_sc.chenghao.chInfo"),
        wyrwd: i("proto_sc.chenghao.wyrwd")
    },
    xunfang: {
        xfInfo: i("proto_sc.xunfang.xfInfo"),
        recover: i("proto_sc.xunfang.recover"),
        zhenZai: i("proto_sc.xunfang.zhenZai"),
        win: i("proto_sc.xunfang.win")
    },
    msgwin: {
        items: i("proto_sc.msgwin.items"),
        newnpc: i("proto_sc.msgwin.newnpc"),
        fight: i("proto_sc.msgwin.fight")
    },
    fuyue: {
        fuyueInfo: i("proto_sc.fuyue.fuyueInfo"),
        memory: i("proto_sc.fuyue.memory"),
        fight: i("proto_sc.fuyue.fight"),
        exchange: i("proto_sc.fuyue.exchange"),
    },
    mail: {
        mailList: i("proto_sc.mail.mailList")
    },
    club: {
        clubList: i("proto_sc.club.clubList"),
        myClubRid: i("proto_sc.club.myClubRid"),
        clubInfo: i("proto_sc.club.clubInfo"),
        memberInfo: i("proto_sc.club.memberInfo"),
        shopList: i("proto_sc.club.shopList"),
        applyList: i("proto_sc.club.applyList"),
        bossft: i("proto_sc.club.bossft"),
        bossInfo: i("proto_sc.club.bossInfo"),
        bossInfoList: i("proto_sc.club.bossInfoList"),
        heroLog: i("proto_sc.club.heroLog"),
        uidLog: i("proto_sc.club.uidLog"),
        clubLog: i("proto_sc.club.clubLog"),
        win: i("proto_sc.club.win"),
        transInfo: i("proto_sc.club.transInfo"),
        clubKuaInfo: i("proto_sc.club.clubKuaInfo"),
        clubKuaMsg: i("proto_sc.club.clubKuaMsg"),
        clubKuaCszr: i("proto_sc.club.clubKuaCszr"),
        clubKuapkzr: i("proto_sc.club.clubKuapkzr"),
        clubKuaWin: i("proto_sc.club.clubKuaWin"),
        clubKuahit: i("proto_sc.club.clubKuahit"),
        clubKuapklog: i("proto_sc.club.clubKuapklog"),
        clubKuapkrwd: i("proto_sc.club.clubKuapkrwd"),
        clubKualooklog: i("proto_sc.club.clubKualooklog"),
        kuaHeroList: i("proto_sc.club.kuaHeroList"),
        clubTask: i("proto_sc.club.clubTask"),
        active: i("proto_sc.club.active"),
        partyResource: i("proto_sc.club.partyResource"),
        party: i("proto_sc.club.party"),
        redBag: i("proto_sc.club.redBag"),
        throwPot: i("proto_sc.club.throwPot"),
        randUser: i("proto_sc.club.randUser"),
        myCid: i("proto_sc.club.myCid"),
    },
    xianshi: {
        usecash: i("proto_sc.xianshi.usecash"),
        usebook: i("proto_sc.xianshi.usebook"),
        shiliup: i("proto_sc.xianshi.shiliup"),
        loginday: i("proto_sc.xianshi.loginday"),
        killg2d: i("proto_sc.xianshi.killg2d"),
        goeat: i("proto_sc.xianshi.goeat"),
        clubbossdmg: i("proto_sc.xianshi.clubbossdmg"),
        clubbosskill: i("proto_sc.xianshi.clubbosskill")
    },
    daily: {
        tasks: i("proto_sc.daily.tasks"),
        score: i("proto_sc.daily.score"),
        rwds: i("proto_sc.daily.rwds"),
        base: i("proto_sc.daily.base"),
        level: i("proto_sc.daily.level")
    },
    chengjiu: {
        cjlist: i("proto_sc.chengjiu.cjlist")
    },
    fuli: {
        qiandao: i("proto_sc.fuli.qiandao"),
        mooncard: i("proto_sc.fuli.mooncard"),
        shenji: i("proto_sc.fuli.shenji"),
        guanqun: i("proto_sc.fuli.guanqun"),
        fchofuli: i("proto_sc.fuli.fchofuli"),
        fexchofuli: i("proto_sc.fuli.fexchofuli"),
        vipfuli: i("proto_sc.fuli.vipfuli"),
        win: i("proto_sc.fuli.win"),
        wxqq: i("proto_sc.fuli.wxqq"),
        share: i("proto_sc.fuli.share"),
        mGift: i("proto_sc.fuli.mGift"),
        actqd: i("proto_sc.fuli.actqd"),
        jxh: i("proto_sc.fuli.jxh"),
        zeroGift: i("proto_sc.fuli.zeroGift"),
        money: i("proto_sc.fuli.money"),
    },
    boite: {
        yhInfo: i("proto_sc.boite.yhInfo"),
        yhType: i("proto_sc.boite.yhType"),
        yhshow: i("proto_sc.boite.yhshow"),
        yhBaseInfo: i("proto_sc.boite.yhBaseInfo"),
        yhOld: i("proto_sc.boite.yhOld"),
        yhbad: i("proto_sc.boite.yhbad"),
        lbList: i("proto_sc.boite.lbList"),
        jlShop: i("proto_sc.boite.jlShop"),
        yhList: i("proto_sc.boite.yhList"),
        heroList: i("proto_sc.boite.heroList"),
        myYhRid: i("proto_sc.boite.myYhRid"),
        win: i("proto_sc.boite.win")
    },
    shop: {
        list: i("proto_sc.shop.list"),
        giftlist: i("proto_sc.shop.giftlist")
    },
    yamen: {
        info: i("proto_sc.yamen.info"),
        fight: i("proto_sc.yamen.fight"),
        cslist: i("proto_sc.yamen.cslist"),
        fclist: i("proto_sc.yamen.fclist"),
        rank: i("proto_sc.yamen.rank"),
        deflog: i("proto_sc.yamen.deflog"),
        enymsg: i("proto_sc.yamen.enymsg"),
        myrank: i("proto_sc.yamen.myrank"),
        kill20log: i("proto_sc.yamen.kill20log"),
        win: i("proto_sc.yamen.win"),
        zhuisha: i("proto_sc.yamen.zhuisha")
    },
    task: {
        tmain: i("proto_sc.task.tmain")
    },
    order: {
        back: i("proto_sc.order.back"),
        rorder: i("proto_sc.order.rorder"),
        rshop: i("proto_sc.order.rshop"),
        vipexp: i("proto_sc.order.vipexp")
    },
    bank: {
        bankInfo: i("proto_sc.bank.bankInfo")
    },
    huodonglist: {
        all: i("proto_sc.huodonglist.all")
    },
    newPeopleBuy: {
        buyinfo: i("proto_sc.newPeopleBuy.buyinfo")
    },
    sevenCelebration: {
        seveninfo: i("proto_sc.sevenCelebration.seveninfo")
    },
    sanxiao:{
        sanxiaohuodong:i('proto_sc.sanxiao.sanxiaohuodong'),
        shop:i('proto_sc.sanxiao.shop'),
        exchange:i('proto_sc.sanxiao.exchange'),
        qxRank:i('proto_sc.sanxiao.qxRank'),
        myQxRid:i('proto_sc.sanxiao.myQxRid'),
    },
    xshuodong: {
        cash: i("proto_sc.xshuodong.cash"),
        amy: i("proto_sc.xshuodong.amy"),
        coin: i("proto_sc.xshuodong.coin"),
        food: i("proto_sc.xshuodong.food"),
        juanzhou: i("proto_sc.xshuodong.juanzhou"),
        qinmi: i("proto_sc.xshuodong.qinmi"),
        shili: i("proto_sc.xshuodong.shili"),
        zhengwu: i("proto_sc.xshuodong.zhengwu"),
        login: i("proto_sc.xshuodong.login"),
        yamen: i("proto_sc.xshuodong.yamen"),
        lianyin: i("proto_sc.xshuodong.lianyin"),
        school: i("proto_sc.xshuodong.school"),
        jingshang: i("proto_sc.xshuodong.jingshang"),
        nongchan: i("proto_sc.xshuodong.nongchan"),
        zhaomu: i("proto_sc.xshuodong.zhaomu"),
        jishag2d: i("proto_sc.xshuodong.jishag2d"),
        cjfanren: i("proto_sc.xshuodong.cjfanren"),
        tiaozhanshu: i("proto_sc.xshuodong.tiaozhanshu"),
        zhenzai: i("proto_sc.xshuodong.zhenzai"),
        tilidan: i("proto_sc.xshuodong.tilidan"),
        huolidan: i("proto_sc.xshuodong.huolidan"),
        meilizhi: i("proto_sc.xshuodong.meilizhi"),
        fuyanhui: i("proto_sc.xshuodong.fuyanhui"),
        clubbosshit: i("proto_sc.xshuodong.clubbosshit"),
        clubbossjs: i("proto_sc.xshuodong.clubbossjs"),
        jiulouzf: i("proto_sc.xshuodong.jiulouzf"),
        treasure: i("proto_sc.xshuodong.treasure"),
        qifu: i("proto_sc.xshuodong.qifu"),
        jinglidan: i("proto_sc.xshuodong.jinglidan"),
        chuyou: i("proto_sc.xshuodong.chuyou"),
        wenhou: i("proto_sc.xshuodong.wenhou"),
        jiaoji: i("proto_sc.xshuodong.jiaoji"),
        yingyuan: i("proto_sc.xshuodong.yingyuan"),
        xufang: i("proto_sc.xshuodong.xufang"),
        lilian: i("proto_sc.xshuodong.lilian"),
        pengren: i("proto_sc.xshuodong.pengren"),
        xsRank: i("proto_sc.xshuodong.xsRank"),
        dzlogin: i("proto_sc.xshuodong.dzlogin"),
        stealdew: i("proto_sc.xshuodong.stealdew"),
        plant: i("proto_sc.xshuodong.plant")
    },
    cbhuodong: {
        club: i("proto_sc.cbhuodong.club"),
        clublist: i("proto_sc.cbhuodong.clublist"),
        myclubRid: i("proto_sc.cbhuodong.myclubRid"),
        guanqia: i("proto_sc.cbhuodong.guanqia"),
        guanqialist: i("proto_sc.cbhuodong.guanqialist"),
        myguanqiaRid: i("proto_sc.cbhuodong.myguanqiaRid"),
        shili: i("proto_sc.cbhuodong.shili"),       // 252 势力涨幅榜
        shililist: i("proto_sc.cbhuodong.shililist"),   
        myshiliRid: i("proto_sc.cbhuodong.myshiliRid"),
        love: i("proto_sc.cbhuodong.love"),     // 253
        lovelist: i("proto_sc.cbhuodong.lovelist"),
        myloveRid: i("proto_sc.cbhuodong.myloveRid"),
        yamen: i("proto_sc.cbhuodong.yamen"),   // 254
        yamenlist: i("proto_sc.cbhuodong.yamenlist"),
        myyamenRid: i("proto_sc.cbhuodong.myyamenRid"),
        yinliang: i("proto_sc.cbhuodong.yinliang"),     // 255
        yinlianglist: i("proto_sc.cbhuodong.yinlianglist"),
        myYinLiangRid: i("proto_sc.cbhuodong.myYinLiangRid"),
        jiulou: i("proto_sc.cbhuodong.jiulou"),     // 256
        jiuloulist: i("proto_sc.cbhuodong.jiuloulist"),
        myJiuLouRid: i("proto_sc.cbhuodong.myJiuLouRid"),
        shibing: i("proto_sc.cbhuodong.shibing"),   // 257
        shibinglist: i("proto_sc.cbhuodong.shibinglist"),
        myShiBingRid: i("proto_sc.cbhuodong.myShiBingRid"),
        liangshi: i("proto_sc.cbhuodong.liangshi"),     // 259
        liangshilist: i("proto_sc.cbhuodong.liangshilist"),
        myLiangShiRid: i("proto_sc.cbhuodong.myLiangShiRid"),
        treasure: i("proto_sc.cbhuodong.treasure"),     // 6135
        treasurelist: i("proto_sc.cbhuodong.treasurelist"),
        myTreaRid: i("proto_sc.cbhuodong.myTreaRid"),
        herojb: i("proto_sc.cbhuodong.herojb"),         // 6166 伙伴羁绊涨幅
        herojblist: i("proto_sc.cbhuodong.herojblist"),
        myHerojbRid: i("proto_sc.cbhuodong.myHerojbRid"),
        herozz: i("proto_sc.cbhuodong.herozz"), // 6167 
        herozzlist: i("proto_sc.cbhuodong.herozzlist"),
        myHerozzRid: i("proto_sc.cbhuodong.myHerozzRid"),
        meili: i("proto_sc.cbhuodong.meili"),   // 258
        meililist: i("proto_sc.cbhuodong.meililist"),
        myMeiLiRid: i("proto_sc.cbhuodong.myMeiLiRid"),
        stealcl: i("proto_sc.cbhuodong.stealcl"),
        stealcllist: i("proto_sc.cbhuodong.stealcllist"),
        myStealclRid: i("proto_sc.cbhuodong.myStealclRid"),
        plants: i("proto_sc.cbhuodong.plants"), // 6216
        plantslist: i("proto_sc.cbhuodong.plantslist"),
        myPlantsRid: i("proto_sc.cbhuodong.myPlantsRid"),
        wifeskillexp: i("proto_sc.cbhuodong.wifeskillexp"), // 6217
        wifeskillexplist: i("proto_sc.cbhuodong.wifeskillexplist"),
        myWifeskillexpRid: i("proto_sc.cbhuodong.myWifeskillexpRid"),
        sonshili: i("proto_sc.cbhuodong.sonshili"),     // 6218
        sonshililist: i("proto_sc.cbhuodong.sonshililist"),
        mySonshiliRid: i("proto_sc.cbhuodong.mySonshiliRid"),
        clubyamen: i("proto_sc.cbhuodong.clubyamen"),
        clubyamenlist: i("proto_sc.cbhuodong.clubyamenlist"),
        myclubyamen: i("proto_sc.cbhuodong.myclubyamen"),        
        guanqiaexchange: i("proto_sc.cbhuodong.guanqiaexchange"),
        shiliexchange: i("proto_sc.cbhuodong.shiliexchange"),
        loveexchange: i("proto_sc.cbhuodong.loveexchange"),
        treasureexchange: i("proto_sc.cbhuodong.treasureexchange"),
        liangshiexchange: i("proto_sc.cbhuodong.liangshiexchange"),
        yinliangexchange: i("proto_sc.cbhuodong.yinliangexchange"),
        jiulouexchange: i("proto_sc.cbhuodong.jiulouexchange"),
        shibingexchange: i("proto_sc.cbhuodong.shibingexchange"),
        herojbexchange: i("proto_sc.cbhuodong.herojbexchange"),
        herozzexchange: i("proto_sc.cbhuodong.herozzexchange"),
        meiliexchange: i("proto_sc.cbhuodong.meiliexchange"),
        yamenexchange: i("proto_sc.cbhuodong.yamenexchange"),
        clubyamenexchange: i("proto_sc.cbhuodong.clubyamenexchange"),
        stealclexchange: i("proto_sc.cbhuodong.stealclexchange"),
        plantsexchange: i("proto_sc.cbhuodong.plantsexchange"),
        sonshiliexpexchange: i("proto_sc.cbhuodong.sonshiliexpexchange"),
    },
    czhuodong: {
        day: i("proto_sc.czhuodong.day"),
        total: i("proto_sc.czhuodong.total"),
        leitian: i("proto_sc.czhuodong.leitian"),
        onceRecharge: i("proto_sc.czhuodong.onceRecharge")
    },
    edczhuodong: {
        everyday: i("proto_sc.edczhuodong.everyday")
    },
    zchuodong: {
        Gift: i("proto_sc.zchuodong.Gift")
    },
    jchuodong: {
        jianchen: i("proto_sc.jchuodong.jianchen")
    },
    jghuodong: {
        jinguo: i("proto_sc.jghuodong.jinguo")
    },
    njhuodong: {
        nvjiang: i("proto_sc.njhuodong.nvjiang")
    },
    xghuodong: {
        shop: i("proto_sc.xghuodong.shop"),
        boss: i("proto_sc.xghuodong.boss"),
        exchange: i("proto_sc.xghuodong.exchange"),
        bag: i("proto_sc.xghuodong.bag"),
        scoreRank: i("proto_sc.xghuodong.scoreRank"),
        clublist: i("proto_sc.xghuodong.clublist"),
        cfg: i("proto_sc.xghuodong.cfg"),
        win: i("proto_sc.xghuodong.win"),
        rwdLog: i("proto_sc.xghuodong.rwdLog"),
        myScore: i("proto_sc.xghuodong.myScore"),
        myclubRid: i("proto_sc.xghuodong.myclubRid"),
        score: i("proto_sc.xghuodong.score")
    },
    yyhuodong: {
        shop: i("proto_sc.yyhuodong.shop"),
        exchange: i("proto_sc.yyhuodong.exchange"),
        bag: i("proto_sc.yyhuodong.bag"),
        small_list: i("proto_sc.yyhuodong.small_list"),
        big_list: i("proto_sc.yyhuodong.big_list"),
        VictoryOrDefeat: i("proto_sc.yyhuodong.VictoryOrDefeat"),
        TotalRank_list: i("proto_sc.yyhuodong.TotalRank_list"),
        myYyRid: i("proto_sc.yyhuodong.myYyRid"),
        cfg: i("proto_sc.yyhuodong.cfg"),
        records: i("proto_sc.yyhuodong.records"),
        score: i("proto_sc.yyhuodong.score"),
        contribution: i("proto_sc.yyhuodong.contribution"),
        leiji: i("proto_sc.yyhuodong.leiji")
    },
    penalize: {
        shop: i("proto_sc.penalize.shop"),
        boss: i("proto_sc.penalize.boss"),
        exchange: i("proto_sc.penalize.exchange"),
        bag: i("proto_sc.penalize.bag"),
        scoreRank: i("proto_sc.penalize.scoreRank"),
        clublist: i("proto_sc.penalize.clublist"),
        cfg: i("proto_sc.penalize.cfg"),
        win: i("proto_sc.penalize.win"),
        rwdLog: i("proto_sc.penalize.rwdLog"),
        myScore: i("proto_sc.penalize.myScore"),
        myclubRid: i("proto_sc.penalize.myclubRid"),
        score: i("proto_sc.penalize.score")
    },
    nationalDay: {
        shop: i("proto_sc.nationalDay.shop"),
        boss: i("proto_sc.nationalDay.boss"),
        exchange: i("proto_sc.nationalDay.exchange"),
        bag: i("proto_sc.nationalDay.bag"),
        scoreRank: i("proto_sc.nationalDay.scoreRank"),
        clublist: i("proto_sc.nationalDay.clublist"),
        cfg: i("proto_sc.nationalDay.cfg"),
        win: i("proto_sc.nationalDay.win"),
        rwdLog: i("proto_sc.nationalDay.rwdLog"),
        myScore: i("proto_sc.nationalDay.myScore"),
        myclubRid: i("proto_sc.nationalDay.myclubRid"),
        score: i("proto_sc.nationalDay.score")
    },
    doubleNinth: {
        shop: i("proto_sc.doubleNinth.shop"),
        boss: i("proto_sc.doubleNinth.boss"),
        exchange: i("proto_sc.doubleNinth.exchange"),
        bag: i("proto_sc.doubleNinth.bag"),
        scoreRank: i("proto_sc.doubleNinth.scoreRank"),
        clublist: i("proto_sc.doubleNinth.clublist"),
        cfg: i("proto_sc.doubleNinth.cfg"),
        win: i("proto_sc.doubleNinth.win"),
        rwdLog: i("proto_sc.doubleNinth.rwdLog"),
        myScore: i("proto_sc.doubleNinth.myScore"),
        myclubRid: i("proto_sc.doubleNinth.myclubRid"),
        score: i("proto_sc.doubleNinth.score"),
        leiji: i("proto_sc.doubleNinth.leiji")
    },
    doubleEleven: {
        list: i("proto_sc.doubleEleven.list"),
        giftlist: i("proto_sc.doubleEleven.giftlist"),
        cfg: i("proto_sc.doubleEleven.cfg"),
        win: i("proto_sc.doubleEleven.win"),
        leiji: i("proto_sc.doubleEleven.leiji")
    },
    ThanksDay: {
        shop: i("proto_sc.ThanksDay.shop"),
        boss: i("proto_sc.ThanksDay.boss"),
        exchange: i("proto_sc.ThanksDay.exchange"),
        bag: i("proto_sc.ThanksDay.bag"),
        scoreRank: i("proto_sc.ThanksDay.scoreRank"),
        clublist: i("proto_sc.ThanksDay.clublist"),
        cfg: i("proto_sc.ThanksDay.cfg"),
        win: i("proto_sc.ThanksDay.win"),
        rwdLog: i("proto_sc.ThanksDay.rwdLog"),
        myScore: i("proto_sc.ThanksDay.myScore"),
        myclubRid: i("proto_sc.ThanksDay.myclubRid"),
        score: i("proto_sc.ThanksDay.score"),
        leiji: i("proto_sc.ThanksDay.leiji")
    },
    zphuodong: {
        zhuanpan: i("proto_sc.zphuodong.zhuanpan"),
        total: i("proto_sc.zphuodong.total"),
        zpzmd: i("proto_sc.zphuodong.zpzmd"),
        zplog: i("proto_sc.zphuodong.zplog"),
        win: i("proto_sc.zphuodong.win"),
        xsRank: i("proto_sc.zphuodong.xsRank")
    },
    sdhuodong: {
        zadan: i("proto_sc.sdhuodong.zadan"),
        sdzmd: i("proto_sc.sdhuodong.sdzmd"),
        win: i("proto_sc.sdhuodong.win"),
        souji: i("proto_sc.sdhuodong.souji")
    },
    dzphuodong: {
        cfg: i("proto_sc.dzphuodong.cfg")
    },
    luckydraw: {
        turntable: i("proto_sc.luckydraw.turntable"),
        rwdLog: i("proto_sc.luckydraw.rwdLog"),
        shop: i("proto_sc.luckydraw.shop"),
        scoreExchange: i("proto_sc.luckydraw.scoreExchange"),
        dayRank: i("proto_sc.luckydraw.dayRank"),
        mydayRank: i("proto_sc.luckydraw.mydayRank"),
        totalRank: i("proto_sc.luckydraw.totalRank"),
        myTotalRank: i("proto_sc.luckydraw.myTotalRank")
    },
    xbhuodong: {
        xunbao: i("proto_sc.xbhuodong.xunbao")
    },
    sevenSign: {
        cfg: i("proto_sc.sevenSign.cfg")
    },
    duihuodong: {
        duihuan: i("proto_sc.duihuodong.duihuan")
    },
    sidafanwanghd: {
        fanwang: i("proto_sc.sidafanwanghd.fanwang")
    },
    daydaybuy: {
        dayday: i("proto_sc.daydaybuy.dayday")
    },
    duihuanshop: {
        shop: i("proto_sc.duihuanshop.shop")
    },
    jshuodong: {
        unlock: i("proto_sc.jshuodong.unlock")
    },
    sfhuodong: {
        sfGift: i("proto_sc.sfhuodong.sfGift")
    },
    dxrhuodong: {
        snowman: i("proto_sc.dxrhuodong.snowman"),
        records: i("proto_sc.dxrhuodong.records"),
        myQxRid: i("proto_sc.dxrhuodong.myQxRid"),
        qxRank: i("proto_sc.dxrhuodong.qxRank"),
        exchange: i("proto_sc.dxrhuodong.exchange"),
        shop: i("proto_sc.dxrhuodong.shop"),

    },
    lxczhuodong: {
        continuity: i("proto_sc.lxczhuodong.continuity")
    },
    glqdhuodong: {
        celebration: i("proto_sc.glqdhuodong.celebration"),
        rule: i("proto_sc.glqdhuodong.rule"),
        cbrwd: i("proto_sc.glqdhuodong.cbrwd"),
        paihangZl: i("proto_sc.glqdhuodong.paihangZl"),
        exchange: i("proto_sc.glqdhuodong.exchange"),
        shop:i("proto_sc.glqdhuodong.shop"),
        cashlist: i("proto_sc.glqdhuodong.cashlist"),
        myCashRid: i("proto_sc.glqdhuodong.myCashRid"),
        yuelilist: i("proto_sc.glqdhuodong.yuelilist"),
        myYueLiRid: i("proto_sc.glqdhuodong.myYueLiRid"),
        yinlianglist: i("proto_sc.glqdhuodong.yinlianglist"),
        myYinLiangRid: i("proto_sc.glqdhuodong.myYinLiangRid"),
        mingshenglist: i("proto_sc.glqdhuodong.mingshenglist"),
        myMingShengRid: i("proto_sc.glqdhuodong.myMingShengRid"),
        treasurelist: i("proto_sc.glqdhuodong.treasurelist"),
        myTreaRid: i("proto_sc.glqdhuodong.myTreaRid"),
        banchailist: i("proto_sc.glqdhuodong.banchailist"),
        myBanChaiRid: i("proto_sc.glqdhuodong.myBanChaiRid"),
        xunfanglist: i("proto_sc.glqdhuodong.xunfanglist"),
        myXunFangRid: i("proto_sc.glqdhuodong.myXunFangRid"),
        lianyinlist: i("proto_sc.glqdhuodong.lianyinlist"),
        myLianYinRid: i("proto_sc.glqdhuodong.myLianYinRid"),
        dayRankList: i("proto_sc.glqdhuodong.dayRankList"),
        mydayRankRid: i("proto_sc.glqdhuodong.mydayRankRid"),
        totalRankList: i("proto_sc.glqdhuodong.totalRankList"),
        mytotalRankRid: i("proto_sc.glqdhuodong.mytotalRankRid")
    },
    fphuodong: {
        flop: i("proto_sc.fphuodong.flop"),
        records: i("proto_sc.fphuodong.records"),
        showeff: i("proto_sc.fphuodong.showeff")
    },
    ddhuodong: {
        lantern: i("proto_sc.ddhuodong.lantern"),
        records: i("proto_sc.ddhuodong.records")
    },
    solarterms: {
        purchase: i("proto_sc.solarterms.purchase"),
        cfg: i("proto_sc.solarterms.cfg")
    },
    luckyCharm: {
        share: i("proto_sc.luckyCharm.share")
    },
    girlsday: {
        mirror: i("proto_sc.girlsday.mirror"),
        res: i("proto_sc.girlsday.res"),
        rwd: i("proto_sc.girlsday.rwd"),
        allrwd: i("proto_sc.girlsday.allrwd"),
        records: i("proto_sc.girlsday.records"),
        shop: i("proto_sc.girlsday.shop"),
        exchange: i("proto_sc.girlsday.exchange")
    },
    arborday: {
        cfg: i("proto_sc.arborday.cfg"),
        rwdLog: i("proto_sc.arborday.rwdLog"),
        finalRank: i("proto_sc.arborday.finalRank"),
        myYyRid: i("proto_sc.arborday.myYyRid")
    },
    qingming: {
        cfg: i("proto_sc.qingming.cfg"),
        act: i("proto_sc.qingming.act"),
        shop: i("proto_sc.qingming.shop"),
        exchange: i("proto_sc.qingming.exchange"),
        rwdLog: i("proto_sc.qingming.rwdLog"),
        qmRank: i("proto_sc.qingming.qmRank"),
        myQmRid: i("proto_sc.qingming.myQmRid")
    },
    studyday: {
        mirror: i("proto_sc.studyday.mirror"),
        shop: i("proto_sc.studyday.shop"),
        res: i("proto_sc.studyday.res"),
        records: i("proto_sc.studyday.records")
    },
    laborDay: {
        cfg: i("proto_sc.laborDay.cfg"),
        exchange: i("proto_sc.laborDay.exchange"),
        shop: i("proto_sc.laborDay.shop"),
        rwdLog: i("proto_sc.laborDay.rwdLog"),
        finalRank: i("proto_sc.laborDay.finalRank"),
        myLdRid: i("proto_sc.laborDay.myLdRid")
    },
    dragonBoat: {
        cfg: i("proto_sc.dragonBoat.cfg"),
        act: i("proto_sc.dragonBoat.act"),
        shop: i("proto_sc.dragonBoat.shop"),
        exchange: i("proto_sc.dragonBoat.exchange"),
        rwdLog: i("proto_sc.dragonBoat.rwdLog"),
        dwRank: i("proto_sc.dragonBoat.dwRank"),
        myDwRid: i("proto_sc.dragonBoat.myDwRid")
    },
    Balloon: {
        cfg: i("proto_sc.Balloon.cfg"),
        act: i("proto_sc.Balloon.act"),
        shop: i("proto_sc.Balloon.shop"),
        exchange: i("proto_sc.Balloon.exchange"),
        rwdLog: i("proto_sc.Balloon.rwdLog"),
        QqRank: i("proto_sc.Balloon.QqRank"),
        myQqRid: i("proto_sc.Balloon.myQqRid")
    },
    Professional: {
        cfg: i("proto_sc.Professional.cfg"),
        act: i("proto_sc.Professional.act"),
        shop: i("proto_sc.Professional.shop"),
        exchange: i("proto_sc.Professional.exchange"),
        rwdLog: i("proto_sc.Professional.rwdLog"),
        QqRank: i("proto_sc.Professional.QqRank"),
        myQqRid: i("proto_sc.Professional.myQqRid")
    },
    wishingWell:{
        cfg: i("proto_sc.wishingWell.cfg"),
        act: i("proto_sc.wishingWell.act"),
        shop: i("proto_sc.wishingWell.shop"),
        exchange: i("proto_sc.wishingWell.exchange"),
        rwdLog: i("proto_sc.wishingWell.rwdLog"),
        qxRank: i("proto_sc.wishingWell.qxRank"),
        myQxRid: i("proto_sc.wishingWell.myQxRid"),
        well: i("proto_sc.wishingWell.well"),
    },
    sdjhuodong: {
        christmas: i("proto_sc.sdjhuodong.christmas"),
        records: i("proto_sc.sdjhuodong.records"),
        myQxRid: i("proto_sc.sdjhuodong.myQxRid"),
        qxRank: i("proto_sc.sdjhuodong.qxRank"),
        exchange: i("proto_sc.sdjhuodong.exchange"),
        shop: i("proto_sc.sdjhuodong.shop"),

    },
    christmas:{
        cfg: i("proto_sc.christmas.cfg"),
        act: i("proto_sc.christmas.act"),
        shop: i("proto_sc.christmas.shop"),
        exchange: i("proto_sc.christmas.exchange"),
        rwdLog: i("proto_sc.christmas.rwdLog"),
        qxRank: i("proto_sc.christmas.qxRank"),
        myQxRid: i("proto_sc.christmas.myQxRid"),
    },
    jigsaw: {
        cfg: i("proto_sc.jigsaw.cfg"),
        rwdLog: i("proto_sc.jigsaw.rwdLog")
    },
    liondance: {
        cfg: i("proto_sc.liondance.cfg")
    },
    cjttczhuodong: {
        cjttcz: i("proto_sc.cjttczhuodong.cjttcz")
    },
    zhenxiufang: {
        zhenxiushizhuang: i("proto_sc.zhenxiufang.zhenxiushizhuang")
    },
    dblchuodong: {
        cfg: i("proto_sc.dblchuodong.cfg")
    },
    clothepve: {
        info: i("proto_sc.clothepve.info"),
        ranklist: i("proto_sc.clothepve.ranklist"),
        myScore: i("proto_sc.clothepve.myScore"),
        base: i("proto_sc.clothepve.base"),
        scores: i("proto_sc.clothepve.scores"),
        win: i("proto_sc.clothepve.win"),
        logs: i("proto_sc.clothepve.logs"),
        referr: i("proto_sc.clothepve.referr")
    },
    clothepvp: {
        info: i("proto_sc.clothepvp.info"),
        ranklist: i("proto_sc.clothepvp.ranklist"),
        myScore: i("proto_sc.clothepvp.myScore"),
        base: i("proto_sc.clothepvp.base"),
        clothe: i("proto_sc.clothepvp.clothe"),
        math: i("proto_sc.clothepvp.math")
    },
    chat: {
        sev: i("proto_sc.chat.sev"),
        sys: i("proto_sc.chat.sys"),
        club: i("proto_sc.chat.club"),
        kuafu: i("proto_sc.chat.kuafu"),
        pao: i("proto_sc.chat.pao"),
        blacklist: i("proto_sc.chat.blacklist"),
        laba: i("proto_sc.chat.laba")
    },
    recode: {
        exchange: i("proto_sc.recode.exchange")
    },
    notice: {
        list: i("proto_sc.notice.list"),
        listNew: i("proto_sc.notice.listNew"),
        activity: i("proto_sc.notice.activity")
    },
    derail: {
        list: i("proto_sc.derail.list")
    },
    hunt: {
        hdInfo: i("proto_sc.hunt.hdInfo"),
        hunt: i("proto_sc.hunt.hunt"),
        rwdLog: i("proto_sc.hunt.rwdLog"),
        firstScore: i("proto_sc.hunt.firstScore"),
        allScore: i("proto_sc.hunt.allScore"),
        scoreRank: i("proto_sc.hunt.scoreRank"),
        rwd: i("proto_sc.hunt.rwd"),
        fail: i("proto_sc.hunt.fail"),
        win: i("proto_sc.hunt.win"),
        myScore: i("proto_sc.hunt.myScore"),
        huntFinish: i("proto_sc.hunt.huntFinish")
    },
    taofa: {
        playInfo: i("proto_sc.taofa.playInfo"),
        scoreRank: i("proto_sc.taofa.scoreRank"),
        myRand: i("proto_sc.taofa.myRand"),
        rootInfo: i("proto_sc.taofa.rootInfo"),
        win: i("proto_sc.taofa.win"),
        fail: i("proto_sc.taofa.fail")
    },
    hanlin: {
        ting: i("proto_sc.hanlin.ting"),
        info: i("proto_sc.hanlin.info"),
        desk: i("proto_sc.hanlin.desk"),
        win: i("proto_sc.hanlin.win")
    },
    trade: {
        Info: i("proto_sc.trade.Info"),
        scoreRank: i("proto_sc.trade.scoreRank"),
        myRand: i("proto_sc.trade.myRand"),
        win: i("proto_sc.trade.win"),
        fail: i("proto_sc.trade.fail"),
        fight: i("proto_sc.trade.fight")
    },
    kuayamen: {
        hdinfo: i("proto_sc.kuayamen.hdinfo"),
        info: i("proto_sc.kuayamen.info"),
        fight: i("proto_sc.kuayamen.fight"),
        cslist: i("proto_sc.kuayamen.cslist"),
        fclist: i("proto_sc.kuayamen.fclist"),
        scoreRank: i("proto_sc.kuayamen.scoreRank"),
        severRank: i("proto_sc.kuayamen.severRank"),
        deflog: i("proto_sc.kuayamen.deflog"),
        enymsg: i("proto_sc.kuayamen.enymsg"),
        myScore: i("proto_sc.kuayamen.myScore"),
        severScore: i("proto_sc.kuayamen.severScore"),
        kill20log: i("proto_sc.kuayamen.kill20log"),
        win: i("proto_sc.kuayamen.win"),
        zhuisha: i("proto_sc.kuayamen.zhuisha"),
        yuxuan: i("proto_sc.kuayamen.yuxuan"),
        lingqu: i("proto_sc.kuayamen.lingqu"),
        chat: i("proto_sc.kuayamen.chat"),
        firstScoreRank: i("proto_sc.kuayamen.firstScoreRank")
    },
    kuaguo: {
        baseinfo: i("proto_sc.kuaguo.baseinfo")
    },
    kuacbhuodong: {
        kuashili: i("proto_sc.kuacbhuodong.kuashili"),
        userlist: i("proto_sc.kuacbhuodong.userlist"),
        mykuashiliRid: i("proto_sc.kuacbhuodong.mykuashiliRid"),
        qufulist: i("proto_sc.kuacbhuodong.qufulist"),
        mykuaquRid: i("proto_sc.kuacbhuodong.mykuaquRid"),
        chat: i("proto_sc.kuacbhuodong.chat"),
        kualove: i("proto_sc.kuacbhuodong.kualove"),
        userlovelist: i("proto_sc.kuacbhuodong.userlovelist"),
        mykualoveRid: i("proto_sc.kuacbhuodong.mykualoveRid"),
        qufulovelist: i("proto_sc.kuacbhuodong.qufulovelist"),
        mykuaquloveRid: i("proto_sc.kuacbhuodong.mykuaquloveRid"),
        lovechat: i("proto_sc.kuacbhuodong.lovechat"),
        fengxiandian: i("proto_sc.kuacbhuodong.fengxiandian")
    },
    gongdou: {
        baseInfo: i("proto_sc.gongdou.baseInfo"),
        totalInfo: i("proto_sc.gongdou.totalInfo"),
        winInfo: i("proto_sc.gongdou.winInfo"),
        batInfo: i("proto_sc.gongdou.batInfo"),
        cardInfo: i("proto_sc.gongdou.cardInfo"),
        showCard: i("proto_sc.gongdou.showCard"),
        result: i("proto_sc.gongdou.result"),
        scoreRank: i("proto_sc.gongdou.scoreRank"),
        myRand: i("proto_sc.gongdou.myRand"),
        rank: i("proto_sc.gongdou.rank"),
        shop: i("proto_sc.gongdou.shop"),
        batCard: i("proto_sc.gongdou.batCard"),
        spec: i("proto_sc.gongdou.spec")
    },
    naqie: {
        naqieList: i("proto_sc.naqie.naqieList")
    },
    friends: {
        flist: i("proto_sc.friends.flist"),
        news: i("proto_sc.friends.news"),
        tips: i("proto_sc.friends.tips"),
        fapplylist: i("proto_sc.friends.fapplylist"),
        fvow: i("proto_sc.friends.fvow"),
        affection: i("proto_sc.friends.affection"),
        qjlist: i("proto_sc.friends.qjlist"),    

        //flist: i("proto_sc.friends.flist"),
        //fapplylist: i("proto_sc.friends.fapplylist"),
		sltip: i("proto_sc.friends.sltip"),
		fllist: i("proto_sc.friends.fllist"),
        sonshili: i("proto_sc.friends.sonshili"),
        blist: i("proto_sc.friends.blist"),
        applyList: i("proto_sc.friends.applyList"),
        redPoint: i("proto_sc.friends.redPoint"),
        flove: i("proto_sc.friends.flove"),
    },
    gzj: {
        base: i("proto_sc.gzj.base"),
        list: i("proto_sc.gzj.list"),
        graduation: i("proto_sc.gzj.graduation"),
        dgift: i("proto_sc.gzj.dgift"),
        reward: i("proto_sc.gzj.reward"),
        gifts: i("proto_sc.gzj.gifts")
    },
    banish: {
        base: i("proto_sc.banish.base"),
        deskCashList: i("proto_sc.banish.deskCashList"),
        list: i("proto_sc.banish.list"),
        herolist: i("proto_sc.banish.herolist"),
        days: i("proto_sc.banish.days"),
        recall: i("proto_sc.banish.recall")
    },
    scpoint: {
        list: i("proto_sc.scpoint.list"),
        heroJB: i("proto_sc.scpoint.heroJB"),
        wifeJB: i("proto_sc.scpoint.wifeJB"),
        heroSW: i("proto_sc.scpoint.heroSW"),
        belief: i("proto_sc.scpoint.belief"),
        selectGroup: i("proto_sc.scpoint.selectGroup"),
        jbItem: i("proto_sc.scpoint.jbItem"),
        cardFetter: i("proto_sc.scpoint.cardFetter")
    },
    kitchen: {
        base: i("proto_sc.kitchen.base"),
        list: i("proto_sc.kitchen.list"),
        record: i("proto_sc.kitchen.record"),
        foods: i("proto_sc.kitchen.foods"),
        level: i("proto_sc.kitchen.level")
    },
    treasure: {
        base: i("proto_sc.treasure.base"),
        groups: i("proto_sc.treasure.groups"),
        treasure: i("proto_sc.treasure.treasure"),
        rankList: i("proto_sc.treasure.rankList"),
        myTreaRank: i("proto_sc.treasure.myTreaRank"),
        treatidy: i("proto_sc.treasure.treatidy"),
        tidyList: i("proto_sc.treasure.tidyList"),
        myTidyRank: i("proto_sc.treasure.myTidyRank")
    },
    feige: {
        feige: i("proto_sc.feige.feige"),
        sonFeige: i("proto_sc.feige.sonFeige"),
        friendFeige: i("proto_sc.feige.friendFeige")
    },
    clothe: {
        clothes: i("proto_sc.clothe.clothes"),
        userClothe: i("proto_sc.clothe.userClothe"),
        limittime: i("proto_sc.clothe.limittime"),
        rankList: i("proto_sc.clothe.rankList"),
        myClotheRank: i("proto_sc.clothe.myClotheRank"),
        suitlv: i("proto_sc.clothe.suitlv"),
        score: i("proto_sc.clothe.score"),
        pickAward: i("proto_sc.clothe.pickAward"),
        brocade: i("proto_sc.clothe.brocade"),
        equipCard: i("proto_sc.clothe.equipCard"),
        sepcial: i("proto_sc.clothe.sepcial"),
    },
    userhead: {
        blanks: i("proto_sc.userhead.blanks"),
        blanktime: i("proto_sc.userhead.blanktime"),
        headavatar: i("proto_sc.userhead.headavatar")
    },
    voice: {
        voices: i("proto_sc.voice.voices")
    },
    flower: {
        base: i("proto_sc.flower.base"),
        chenlu: i("proto_sc.flower.chenlu"),
        level: i("proto_sc.flower.level"),
        feild: i("proto_sc.flower.feild"),
        rank: i("proto_sc.flower.rank"),
        myRank: i("proto_sc.flower.myRank"),
        steal: i("proto_sc.flower.steal"),
        logs: i("proto_sc.flower.logs"),
        cd: i("proto_sc.flower.cd"),
        autoshou: i("proto_sc.flower.autoshou"),
        worldtree: i("proto_sc.flower.worldtree"),
        treerank: i("proto_sc.flower.treerank"),
        myTreeRank: i("proto_sc.flower.myTreeRank"),
        protect: i("proto_sc.flower.protect")
    },
    keju: {},
    actboss: {
        info: i("proto_sc.actboss.info"),
        flist: i("proto_sc.actboss.flist"),
        myDmg: i("proto_sc.actboss.myDmg"),
        rankList: i("proto_sc.actboss.rankList"),
        hit: i("proto_sc.actboss.hit")
    },
    tangyuan: {
        info: i("proto_sc.tangyuan.info"),
        rank: i("proto_sc.tangyuan.rank"),
        myRank: i("proto_sc.tangyuan.myRank"),
        base: i("proto_sc.tangyuan.base"),
        exchange: i("proto_sc.tangyuan.exchange")
    },
    gaodian: {
        info: i("proto_sc.gaodian.info"),
        rank: i("proto_sc.gaodian.rank"),
        myRank: i("proto_sc.gaodian.myRank"),
        base: i("proto_sc.gaodian.base"),
        exchange: i("proto_sc.gaodian.exchange")
    },
    fanghedeng: {
        hedenginfo: i("proto_sc.fanghedeng.hedenginfo"),
        fhdRank: i("proto_sc.fanghedeng.fhdRank"),
        myfhdRid: i("proto_sc.fanghedeng.myfhdRid"),
        shop: i("proto_sc.fanghedeng.shop"),
        exchange: i("proto_sc.fanghedeng.exchange")
    },
    thirtyCheck: {
        hdQianDaoConfig: i("proto_sc.fottyFiveSign.cfg")
    },
    sevenDays: {
        cfg: i("proto_sc.sevenDays.cfg"),
        act: i("proto_sc.sevenDays.act"),
        shop: i("proto_sc.sevenDays.shop"),
        exchange: i("proto_sc.sevenDays.exchange"),
        qxRank: i("proto_sc.sevenDays.qxRank"),
        myQxRid: i("proto_sc.sevenDays.myQxRid")
    },
    advert: {
        cfg: i("proto_sc.advert.cfg")
    },
    zhongyuan: {
        cfg: i("proto_sc.zhongyuan.cfg"),
        win: i("proto_sc.zhongyuan.win"),
        rwdLog: i("proto_sc.zhongyuan.rwdLog"),
        shop: i("proto_sc.zhongyuan.shop"),
        exchange: i("proto_sc.zhongyuan.exchange"),
        zyRank: i("proto_sc.zhongyuan.zyRank"),
        myZyRid: i("proto_sc.zhongyuan.myZyRid")
    },
    shopping: {
        exchange: i("proto_sc.shopping.exchange"),
        shoppingSpree: i("proto_sc.shopping.shoppingSpree")
    },
    chuyigame: {
        chuyidasai: i("proto_sc.chuyigame.chuyidasai"),
        exchange: i("proto_sc.chuyigame.exchange"),
        shop: i("proto_sc.chuyigame.shop"),
        myQxRid: i("proto_sc.chuyigame.myQxRid"),
        qxRank: i("proto_sc.chuyigame.qxRank"),
    },
    qingrenjie: {
        qingrenjiehuodong: i("proto_sc.qingrenjie.qingrenjiehuodong"),
        exchange: i("proto_sc.qingrenjie.exchange"),
        shop: i("proto_sc.qingrenjie.shop"),
        myQxRid: i("proto_sc.qingrenjie.myQxRid"),
        qxRank: i("proto_sc.qingrenjie.qxRank"),
    },
    business: {
        info: i("proto_sc.business.info"), //返回行商的数据
        buyInfo: i("proto_sc.business.buyInfo"), //返回购买数据
        bagInfo: i("proto_sc.business.bagInfo"),
        startinfo: i("proto_sc.business.startinfo"),
        randPrice: i("proto_sc.business.randPrice"),         
    },
    day: {
        timeStamp: i("proto_sc.day.timeStamp"),//返回第二天0点的时间戳
    },
    office: {
        work: i("proto_sc.office.work"), 
        story: i("proto_sc.office.story"),
        award: i("proto_sc.office.award"),
        buy: i("proto_sc.office.buy"),
        recover: i("proto_sc.office.recover"),
    },
    playmoon: {
        playmoonhuodong: i("proto_sc.playmoon.playmoonhuodong"),
        rwdData: i("proto_sc.playmoon.rwdData"),
        myQxRid: i("proto_sc.playmoon.myQxRid"),
        qxRank: i("proto_sc.playmoon.qxRank"),
        exchange: i("proto_sc.playmoon.exchange"),
    },
    doufu:{
        doufuhuodong:i('proto_sc.doufu.doufuhuodong'),
        shop:i('proto_sc.doufu.shop'),
        exchange:i('proto_sc.doufu.exchange'),
        qxRank:i('proto_sc.doufu.qxRank'),
        myQxRid:i('proto_sc.doufu.myQxRid'),
    },
	tanhe: {
        outside: i("proto_sc.tanhe.outside"),
        free: i("proto_sc.tanhe.free"),
        info: i("proto_sc.tanhe.info"),
    },
    guirenling: {
        guirenlinghuodong: i("proto_sc.guirenling.guirenlinghuodong"),
        qxRank: i("proto_sc.guirenling.qxRank"),
        myQxRid: i("proto_sc.guirenling.myQxRid"),
    },
    newguirenling: {
        newguirenlinghuodong: i("proto_sc.newguirenling.newguirenlinghuodong"),
        qxRank: i("proto_sc.newguirenling.qxRank"),
        myQxRid: i("proto_sc.newguirenling.myQxRid"),
    },
    invite: {
        achieve: i("proto_sc.invite.achieve"),
    },
    jiaoyou:{
        jiaoyou: i("proto_sc.jiaoyou.jiaoyou"),
        list: i("proto_sc.jiaoyou.list"),
        fightInfo: i("proto_sc.jiaoyou.fightInfo"),
    },
    giftBag: {
        buy: i("proto_sc.giftBag.buy") //弹出礼包
    },
    fight: {
        team: i("proto_sc.fight.team") //编队信息
    },
    ErrorCode: {
        noNum: 0,
        noFood: 1
    },
    ServerState: {
        no: 0,
        normal: 1,
        crowded: 2,
        full: 3,
        line: 4,
        new: 5,
        bloken: 6
    },
    SomState: {
        tName: 0,
        baby: 1,
        Child: 2,
        Student: 3,
        loser: 4,
        request: 5,
        pass: 6,
        timeout: 7,
        ok: 8,
        huen: 9,
        requestAll: 10
    },
};
window.proto_cs = {
    login: {
        loginAccount: i("proto_cs.login.loginAccount"),
        getNotice: i("proto_cs.login.getNotice"),
    },
    guide: {
        login: i("proto_cs.guide.login"),
        randName: i("proto_cs.guide.randName"),
        setUinfo: i("proto_cs.guide.setUinfo"),
        guide: i("proto_cs.guide.guide"),
        guideUpguan: i("proto_cs.guide.guideUpguan"),
        kefu: i("proto_cs.guide.kefu"),
        flushZero: i("proto_cs.guide.flushZero"),
        offline: i("proto_cs.guide.offline"),
    },
    user: {
        user: i("proto_cs.user.user"),
        recordNewBie: i("proto_cs.user.recordNewBie"),
        jingYing: i("proto_cs.user.jingYing"),
        jingYingLing: i("proto_cs.user.jingYingLing"),
        jingYingAll: i("proto_cs.user.jingYingAll"),
        qzam: i("proto_cs.user.qzam"),
        zhengWu: i("proto_cs.user.zhengWu"),
        zhengWuLing: i("proto_cs.user.zhengWuLing"),
        pve: i("proto_cs.user.pve"),
        pvenew: i("proto_cs.user.pvenew"),
        pveRestraint: i("proto_cs.user.pveRestraint"),
        pvb: i("proto_cs.user.pvb"),
        pvb2: i("proto_cs.user.pvb2"),
        comeback: i("proto_cs.user.comeback"),
        adok: i("proto_cs.user.adok"),
        randName: i("proto_cs.user.randName"),
        getFuserMember: i("proto_cs.user.getFuserMember"),
        shengguan: i("proto_cs.user.shengguan"),
        resetName: i("proto_cs.user.resetName"),
        resetImage: i("proto_cs.user.resetImage"),
        buyImage: i("proto_cs.user.buyImage"),
        refjingying: i("proto_cs.user.refjingying"),
        refwife: i("proto_cs.user.refwife"),
        refxunfang: i("proto_cs.user.refxunfang"),
        refson: i("proto_cs.user.refson"),
        getuback: i("proto_cs.user.getuback"),
        setuback: i("proto_cs.user.setuback"),
        serHeroShow: i("proto_cs.user.serHeroShow"),
        setClothe: i("proto_cs.user.setClothe"),
        lockClothe: i("proto_cs.user.lockClothe"),
        lvupSuit: i("proto_cs.user.lvupSuit"),
        weipai: i("proto_cs.user.weipai"),
        setAvatar: i("proto_cs.user.setAvatar"),
        clotheRank: i("proto_cs.user.clotheRank"),
        qifu: i("proto_cs.user.qifu"),
        qifuTen: i("proto_cs.user.qifuTen"),
        addition: i("proto_cs.user.addition"),
        wishInfo: i("proto_cs.user.wishInfo"),
        wishPlay: i("proto_cs.user.wishPlay"),
        recordSteps: i("proto_cs.user.recordSteps"),
        comebackall: i("proto_cs.user.comebackall"),
        getUserBaseInfo: i("proto_cs.user.getUserBaseInfo"),  //传入uid  获取其他玩家的信息
        battleInit: i("proto_cs.user.battleInit"),
        battleRes: i("proto_cs.user.battleRes"),
    },
    friends: {
		flist: i("proto_cs.friends.flist"), //好友列表
		fapply: i("proto_cs.friends.fapply"), //申请好友
		fapplylist: i("proto_cs.friends.fapplylist"), //申请好友列表
		applyList: i("proto_cs.friends.applyList"),
		fno: i("proto_cs.friends.fno"), //好友拒绝
		fok: i("proto_cs.friends.fok"), //同意好友
		fsub: i("proto_cs.friends.fsub"), //好友删除
		ffchat: i("proto_cs.friends.ffchat"), //检测聊天对象
		fschat: i("proto_cs.friends.fschat"), //私聊
		frchat: i("proto_cs.friends.frchat"), //刷新私聊
		rlist: i("proto_cs.friends.rlist"), //推荐列表
		search: i("proto_cs.friends.search"), //查找玩家
		blacklist: i("proto_cs.friends.blacklist"),
		subblacklist: i("proto_cs.friends.subblacklist"),
		addblacklist: i("proto_cs.friends.addblacklist"),

		blist: i("proto_cs.chat.blacklist"),
		redPoint: i("proto_cs.friends.redPoint"),
        sendGift: i("proto_cs.friends.sendGift"),

        getNew: i("proto_cs.friends.getNew"),
        assist: i("proto_cs.friends.assist"),
        getVow: i("proto_cs.friends.getVow"),
        changeVow: i("proto_cs.friends.changeVow"),
        fsend: i("proto_cs.friends.fsend"),
        fred: i("proto_cs.friends.fred")
    },
    card: {
        drawCard: i("proto_cs.card.drawCard"),
        quick_buy: i("proto_cs.card.quick_buy"),
        cardsys: i("proto_cs.card.cardsys"),
        read_story: i("proto_cs.card.read_story"),
        upCardStar: i("proto_cs.card.upCardStar"),
        unlock_cloth: i("proto_cs.card.unlock_cloth"),
        upgradeCard: i("proto_cs.card.upgradeCard"),
        upgradeCardFive: i("proto_cs.card.upgradeCardFive"),
        cardDecompose: i("proto_cs.card.cardDecompose"),//卡牌分解
        cardImprintUpLv: i("proto_cs.card.cardImprintUpLv"), //印痕升级
        cardFlowerPoint: i("proto_cs.card.cardFlowerPoint"), //卡牌开花
    },
    baowu:{
        drawbaowu: i("proto_cs.baowu.drawbaowu"),
        upBaowuStar: i("proto_cs.baowu.upBaowuStar"),
        quick_buy: i("proto_cs.baowu.quick_buy"),
    },
    cardsys: {
        poolstate: i("proto_cs.cardsys.poolstate"),
    },
    hero: {
        upgrade: i("proto_cs.hero.upgrade"),
        upgradeTen: i("proto_cs.hero.upgradeTen"),
        upsenior: i("proto_cs.hero.upsenior"),
        upzzskill: i("proto_cs.hero.upzzskill"),
        uppkskill: i("proto_cs.hero.uppkskill"),
        giveGift: i("proto_cs.hero.giveGift"),
        upghskill: i("proto_cs.hero.upghskill"),
        upcharisma: i("proto_cs.hero.upcharisma"),
        hchat: i("proto_cs.hero.hchat"),
        heroDress: i("proto_cs.hero.heroDress"),        
        weige: i('proto_cs.hero.weige'),
        hfjiaqi: i('proto_cs.hero.hfjiaqi'),
        xxoo: i("proto_cs.hero.xxoo"),
        xxoonobaby: i("proto_cs.hero.xxoonobaby"),
        sjcy: i("proto_cs.hero.sjcy"),
        sjxo: i("proto_cs.hero.sjxo"),
        tokenUpLv:i("proto_cs.hero.tokenUpLv"),
        fetterActivation:i("proto_cs.hero.fetterActivation"),
        tokenActivation:i("proto_cs.hero.tokenActivation"),
        upStar: i("proto_cs.hero.upStar"),
        setClothe: i('proto_cs.hero.setClothe'),
        pickJibanAward: i('proto_cs.hero.pickJibanAward'),
        buyShopItem: i('proto_cs.hero.buyShopItem'),
        setBlanks: i('proto_cs.hero.setBlanks'),
        randVisit: i('proto_cs.hero.randVisit'), //随机拜访
        visit: i('proto_cs.hero.visit'), //定向拜访
        chooseAnswer: i('proto_cs.hero.chooseAnswer'), //选择答案
        endGame: i("proto_cs.hero.endGame"), //结束游戏
    },
    item: {
        useitem: i("proto_cs.item.useitem"),
        useforhero: i("proto_cs.item.useforhero"),
        hecheng: i("proto_cs.item.hecheng"),
        yjHecheng: i("proto_cs.item.yjHecheng"),
        itemlist: i("proto_cs.item.itemlist")
    },
    wife: {
        xxoo: i("proto_cs.wife.xxoo"),
        xxoonobaby: i("proto_cs.wife.xxoonobaby"),
        xxoogetbaby: i("proto_cs.wife.xxoogetbaby"),
        sjcy: i("proto_cs.wife.sjcy"),
        sjxo: i("proto_cs.wife.sjxo"),
        yjxo: i("proto_cs.wife.yjxo"),
        yjxxoogetbaby: i("proto_cs.wife.yjxxoogetbaby"),
        reward: i("proto_cs.wife.reward"),
        upskill: i("proto_cs.wife.upskill"),
        weige: i("proto_cs.wife.weige"),
        hfjiaqi: i("proto_cs.wife.hfjiaqi"),
        giveGift: i("proto_cs.wife.giveGift"),
        wchat: i("proto_cs.wife.wchat")
    },
    school: {
        buydesk: i("proto_cs.school.buydesk"),
        start: i("proto_cs.school.start"),
        yjStart: i("proto_cs.school.yjStart"),
        over: i("proto_cs.school.over"),
        allover: i("proto_cs.school.allover"),
        speedFinish: i("proto_cs.school.speedFinish"),
    },
    son: {
        buyseat: i("proto_cs.son.buyseat"),
        sonname: i("proto_cs.son.sonname"),
        rname: i("proto_cs.son.rname"),
        play: i("proto_cs.son.play"),
        onfood: i("proto_cs.son.onfood"),
        allplay: i("proto_cs.son.allplay"),
        allfood: i("proto_cs.son.allfood"),
        keju: i("proto_cs.son.keju"),
        meipo: i("proto_cs.son.meipo"),
        tiqin: i("proto_cs.son.tiqin"),
        getTiqin: i("proto_cs.son.getTiqin"),
        zhaoqin: i("proto_cs.son.zhaoqin"),
        jiehun: i("proto_cs.son.jiehun"),
        agree: i("proto_cs.son.agree"),
        pass: i("proto_cs.son.pass"),
        allpass: i("proto_cs.son.allpass"),
        cancel: i("proto_cs.son.cancel"),
        rstzhaoqin: i("proto_cs.son.rstzhaoqin"),
        freshTime: i("proto_cs.son.freshTime"),
        shitu: i("proto_cs.son.shitu"),
        allshitu: i("proto_cs.son.allshitu"),
        allrecycleShitu: i("proto_cs.son.allrecycleShitu"),
        recycleShitu: i("proto_cs.son.recycleShitu"),
        naqie: i("proto_cs.son.naqie"),
        freshshitu: i("proto_cs.son.freshshitu"),
        intoLilian: i("proto_cs.son.intoLilian"),
        buyLilianSeat: i("proto_cs.son.buyLilianSeat"),
        delReadMail: i("proto_cs.son.delReadMail"),
        liLianSon: i("proto_cs.son.liLianSon"),
        yjLiLianSon: i("proto_cs.son.yjLiLianSon"),
        liLianReward: i("proto_cs.son.liLianReward"),
        yjLiLianReward: i("proto_cs.son.yjLiLianReward"),
        liLianMail: i("proto_cs.son.liLianMail"),
        randSonName:i("proto_cs.son.randSonName"),
        speedFinish:i("proto_cs.son.speedFinish"),
    },
    ranking: {
        paihang: i("proto_cs.ranking.paihang"),
        flush: i("proto_cs.ranking.flush"),
        mobai: i("proto_cs.ranking.mobai"),
        selfRid: i("proto_cs.ranking.selfRid"),
    },
    laofang: {
        bianDa: i("proto_cs.laofang.bianDa")
    },
    wordboss: {
        wordboss: i("proto_cs.wordboss.wordboss"),
        hitmenggu: i("proto_cs.wordboss.hitmenggu"),
        hitgeerdan: i("proto_cs.wordboss.hitgeerdan"),
        goFightmg: i("proto_cs.wordboss.goFightmg"),
        goFightg2d: i("proto_cs.wordboss.goFightg2d"),
        scoreRank: i("proto_cs.wordboss.scoreRank"),
        g2dHitRank: i("proto_cs.wordboss.g2dHitRank"),
        shopBuy: i("proto_cs.wordboss.shopBuy"),
        comebackmg: i("proto_cs.wordboss.comebackmg"),
        comebackg2d: i("proto_cs.wordboss.comebackg2d")
    },
    fengxiandian: {
        getInfo: i("proto_cs.fengxiandian.getInfo"),
        qingAn: i("proto_cs.fengxiandian.qingAn")
    },
    chenghao: {
        setChengHao: i("proto_cs.chenghao.setChengHao"),
        offChengHao: i("proto_cs.chenghao.offChengHao"),
        wyrwd: i("proto_cs.chenghao.wyrwd"),
        chInfo: i("proto_cs.chenghao.chInfo"),
    },
    xunfang: {
        recover: i("proto_cs.xunfang.recover"),
        zzHand: i("proto_cs.xunfang.zzHand"),
        xunfan: i("proto_cs.xunfang.xunfan"),
        yunshi: i("proto_cs.xunfang.yunshi")
    },
    mail: {
        getMail: i("proto_cs.mail.getMail"),
        redMails: i("proto_cs.mail.redMails"),
        delMail: i("proto_cs.mail.delMail"),
        delMails: i("proto_cs.mail.delMails"),
        openMails: i("proto_cs.mail.openMails"),
        oneKeyPickMails: i("proto_cs.mail.oneKeyPickMails"),
    },
    club: {
        clubCreate: i("proto_cs.club.clubCreate"),
        clubRand: i("proto_cs.club.clubRand"),
        clubFind: i("proto_cs.club.clubFind"),
        clubApply: i("proto_cs.club.clubApply"),
        clubList: i("proto_cs.club.clubList"),
        clubInfo: i("proto_cs.club.clubInfo"),
        clubName: i("proto_cs.club.clubName"),
        clubPwd: i("proto_cs.club.clubPwd"),
        clubInfoSave: i("proto_cs.club.clubInfoSave"),
        isJoin: i("proto_cs.club.isJoin"),
        applyList: i("proto_cs.club.applyList"),
        noJoin: i("proto_cs.club.noJoin"),
        yesJoin: i("proto_cs.club.yesJoin"),
        outClub: i("proto_cs.club.outClub"),
        delClub: i("proto_cs.club.delClub"),
        pre_delClub:i("proto_cs.club.pre_delClub"),
        cancel_delClub:i("proto_cs.club.cancel_delClub"),
        dayGongXian: i("proto_cs.club.dayGongXian"),
        memberPost: i("proto_cs.club.memberPost"),
        shopList: i("proto_cs.club.shopList"),
        shopBuy: i("proto_cs.club.shopBuy"),
        clubBossInfo: i("proto_cs.club.clubBossInfo"),
        clubBossOpen: i("proto_cs.club.clubBossOpen"),
        clubBossPK: i("proto_cs.club.clubBossPK"),
        clubHeroCone: i("proto_cs.club.clubHeroCone"),
        clubBosslog: i("proto_cs.club.clubBosslog"),
        clubBossHitList: i("proto_cs.club.clubBossHitList"),
        transList: i("proto_cs.club.transList"),
        transWang: i("proto_cs.club.transWang"),
        clubMemberInfo: i("proto_cs.club.clubMemberInfo"),
        clubBossPKLog: i("proto_cs.club.clubBossPKLog"),
        kuaPKinfo: i("proto_cs.club.kuaPKinfo"),
        kuaPKCszr: i("proto_cs.club.kuaPKCszr"),
        kuaPKAdd: i("proto_cs.club.kuaPKAdd"),
        kuaPKBack: i("proto_cs.club.kuaPKBack"),
        kuaPKzr: i("proto_cs.club.kuaPKzr"),
        kuaPKusejn: i("proto_cs.club.kuaPKusejn"),
        kuaLookWin: i("proto_cs.club.kuaLookWin"),
        kuaPKbflog: i("proto_cs.club.kuaPKbflog"),
        kuaLookHit: i("proto_cs.club.kuaLookHit"),
        kuaPKrwdinfo: i("proto_cs.club.kuaPKrwdinfo"),
        kuaPKrwdget: i("proto_cs.club.kuaPKrwdget"),
        getUserClubInfo: i("proto_cs.club.getUserClubInfo"),
        pickActiveAward: i("proto_cs.club.pickActiveAward"),
        cluBuildingUp: i("proto_cs.club.cluBuildingUp"),
        getTaskRwd: i("proto_cs.club.getTaskRwd"),
        getResourceBaseInfo: i("proto_cs.club.getResourceBaseInfo"),//获取资源的基础信息
        getPartyBaseInfo: i("proto_cs.club.getPartyBaseInfo"),  //获取宴会基础信息
        submitResource: i("proto_cs.club.submitResource"),  //提交资源
        buyCount: i("proto_cs.club.buyCount"),  //购买提交资源的次数
        refreshList: i("proto_cs.club.refreshList"),  //刷新资提交资源列表
        openParty: i("proto_cs.club.openParty"),  //开启宴会
        joinParty: i("proto_cs.club.joinParty"),  //参加宴会
        changeMusician: i("proto_cs.club.changeMusician"),  //更换乐师
        buyBuff: i("proto_cs.club.buyBuff"),  //购买特效
        robRedBag: i("proto_cs.club.robRedBag"),  //抢红包
        startHook: i("proto_cs.club.startHook"),  //开始挂机
        pickHookAward: i("proto_cs.club.pickHookAward"),  //领取挂机奖励
        throwPot: i("proto_cs.club.throwPot"),  //投壶
        pickAward: i("proto_cs.club.pickAward"),  //领取投壶奖励
        randGameUser: i("proto_cs.club.randGameUser"),  //随机玩家
        pickGamesAward: i("proto_cs.club.pickGamesAward"),  //领取游戏奖励
        getThrowInfo: i("proto_cs.club.getThrowInfo"),  //获取投壶奖励信息(是否已经领取奖励)
        updateClubInfo: i("proto_cs.club.updateClubInfo"),//宴会更新消息
        getMyCid: i("proto_cs.club.getMyCid"),//获取是否有公会       
    },
    daily: {
        getrwd: i("proto_cs.daily.getrwd"),
        gettask: i("proto_cs.daily.gettask"),
        answer: i("proto_cs.daily.answer")
    },
    chengjiu: {
        rwd: i("proto_cs.chengjiu.rwd")
    },
    qiandao: {
        rwd: i("proto_cs.qiandao.rwd")
    },
    fuli: {
        qiandao: i("proto_cs.fuli.qiandao"),
        fcho: i("proto_cs.fuli.fcho"),
        fcho_ex: i("proto_cs.fuli.fcho_ex"),
        vip: i("proto_cs.fuli.vip"),
        buy: i("proto_cs.fuli.buy"),
        mooncard: i("proto_cs.fuli.mooncard"),
        share: i("proto_cs.fuli.share"),
        monday: i("proto_cs.fuli.monday"),
        pickBankAward: i("proto_cs.fuli.pickBankAward"),
        buyZeroGift: i("proto_cs.fuli.buyZeroGift"),
        pickZeroRebate: i("proto_cs.fuli.pickZeroRebate")
    },
    boite: {
        jlInfo: i("proto_cs.boite.jlInfo"),
        yhFind: i("proto_cs.boite.yhFind"),
        yhGo: i("proto_cs.boite.yhGo"),
        yhChi: i("proto_cs.boite.yhChi"),
        shopChange: i("proto_cs.boite.shopChange"),
        yhHold: i("proto_cs.boite.yhHold"),
        jlRanking: i("proto_cs.boite.jlRanking")
    },
    shop: {
        shoplist: i("proto_cs.shop.shoplist"),
        shopLimit: i("proto_cs.shop.shopLimit"),
        shopGift: i("proto_cs.shop.shopGift")
    },
    task: {
        taskdo: i("proto_cs.task.taskdo")
    },
    //双旦-家园家具材料等相关
    furniture:{
        
        firstJoin: i("proto_cs.furniture.firstJoin"),   //第一次进入
        getScores: i("proto_cs.furniture.getScores"),   //收取积分，根据时间获得
        createFurniture: i("proto_cs.furniture.createFurniture"),   //根据图纸打造家具,可以多个打造
        decomposeFurniture: i("proto_cs.furniture.decomposeFurniture"),   //分解家具，同一类型可以分解多个
        putFurniture: i("proto_cs.furniture.putFurniture"),   //保存摆放的家具
        getFightBaseInfo: i("proto_cs.furniture.getFightBaseInfo"),   //获取城池相关信息
        getFightInfo: i("proto_cs.furniture.getFightInfo"),   //获取材料副本的信息
        fight: i("proto_cs.furniture.fight"),   //进入材料副本
        onHook: i("proto_cs.furniture.onHook"),   //材料副本挂机
        clearHookTime: i("proto_cs.furniture.clearHookTime"),   //清楚补货时间
        openFamilyFeast: i("proto_cs.furniture.openFamilyFeast"),   //开启家宴 需要消耗材料
        endFeast: i("proto_cs.furniture.endFeast"),   //结束家宴
        getShopList: i("proto_cs.furniture.getShopList"),   //获取兑换商店的列表
        exchangeShop: i("proto_cs.furniture.exchangeShop"),   //兑换商品
        buyScoreByGold: i("proto_cs.furniture.buyScoreByGold"),   //话费元宝买积分
        getFurnitureInfo: i("proto_cs.furniture.getFurnitureInfo"),   //家具信息
        getFeastInfo: i("proto_cs.furniture.getFeastInfo"),   //获取良辰家宴的信息
        
    },
    yamen: {
        yamen: i("proto_cs.yamen.yamen"),
        chushi: i("proto_cs.yamen.chushi"),
        tiaozhan: i("proto_cs.yamen.tiaozhan"),
        fuchou: i("proto_cs.yamen.fuchou"),
        zhuisha: i("proto_cs.yamen.zhuisha"),
        findzhuisha: i("proto_cs.yamen.findzhuisha"),
        pizun: i("proto_cs.yamen.pizun"),
        clearCD: i("proto_cs.yamen.clearCD"),
        fight: i("proto_cs.yamen.fight"),
        seladd: i("proto_cs.yamen.seladd"),
        getrwd: i("proto_cs.yamen.getrwd"),
        getrank: i("proto_cs.yamen.getrank"),
        yamenhistory: i("proto_cs.yamen.yamenhistory"),
        getHistory: i("proto_cs.yamen.getHistory"),
        exchange: i("proto_cs.yamen.exchange"),
    },
    order: {
        getOrderId: i("proto_cs.order.getOrderId"),
        orderBack: i("proto_cs.order.orderBack"),
        AppFailCallback: i("proto_cs.order.AppFailCallback")
    },
    huodong: {
        hdList: i("proto_cs.huodong.hdList"),
        hd201Info: i("proto_cs.huodong.hd201Info"),
        hd201Rwd: i("proto_cs.huodong.hd201Rwd"),
        hd202Info: i("proto_cs.huodong.hd202Info"),
        hd202Rwd: i("proto_cs.huodong.hd202Rwd"),
        hd203Info: i("proto_cs.huodong.hd203Info"),
        hd203Rwd: i("proto_cs.huodong.hd203Rwd"),
        hd204Info: i("proto_cs.huodong.hd204Info"),
        hd204Rwd: i("proto_cs.huodong.hd204Rwd"),
        hd205Info: i("proto_cs.huodong.hd205Info"),
        hd205Rwd: i("proto_cs.huodong.hd205Rwd"),
        hd206Info: i("proto_cs.huodong.hd206Info"),
        hd206Rwd: i("proto_cs.huodong.hd206Rwd"),
        hd207Info: i("proto_cs.huodong.hd207Info"),
        hd207Rwd: i("proto_cs.huodong.hd207Rwd"),
        hd208Info: i("proto_cs.huodong.hd208Info"),
        hd208Rwd: i("proto_cs.huodong.hd208Rwd"),
        hd209Info: i("proto_cs.huodong.hd209Info"),
        hd209Rwd: i("proto_cs.huodong.hd209Rwd"),
        hd210Info: i("proto_cs.huodong.hd210Info"),
        hd210Rwd: i("proto_cs.huodong.hd210Rwd"),
        hd211Info: i("proto_cs.huodong.hd211Info"),
        hd211Rwd: i("proto_cs.huodong.hd211Rwd"),
        hd212Info: i("proto_cs.huodong.hd212Info"),
        hd212Rwd: i("proto_cs.huodong.hd212Rwd"),
        hd213Info: i("proto_cs.huodong.hd213Info"),
        hd213Rwd: i("proto_cs.huodong.hd213Rwd"),
        hd214Info: i("proto_cs.huodong.hd214Info"),
        hd214Rwd: i("proto_cs.huodong.hd214Rwd"),
        hd215Info: i("proto_cs.huodong.hd215Info"),
        hd215Rwd: i("proto_cs.huodong.hd215Rwd"),
        hd216Info: i("proto_cs.huodong.hd216Info"),
        hd216Rwd: i("proto_cs.huodong.hd216Rwd"),
        hd217Info: i("proto_cs.huodong.hd217Info"),
        hd217Rwd: i("proto_cs.huodong.hd217Rwd"),
        hd218Info: i("proto_cs.huodong.hd218Info"),
        hd218Rwd: i("proto_cs.huodong.hd218Rwd"),
        hd219Info: i("proto_cs.huodong.hd219Info"),
        hd219Rwd: i("proto_cs.huodong.hd219Rwd"),
        hd220Info: i("proto_cs.huodong.hd220Info"),
        hd220Rwd: i("proto_cs.huodong.hd220Rwd"),
        hd221Info: i("proto_cs.huodong.hd221Info"),
        hd221Rwd: i("proto_cs.huodong.hd221Rwd"),
        hd222Info: i("proto_cs.huodong.hd222Info"),
        hd222Rwd: i("proto_cs.huodong.hd222Rwd"),
        hd223Info: i("proto_cs.huodong.hd223Info"),
        hd223Rwd: i("proto_cs.huodong.hd223Rwd"),
        hd224Info: i("proto_cs.huodong.hd224Info"),
        hd224Rwd: i("proto_cs.huodong.hd224Rwd"),
        hd225Info: i("proto_cs.huodong.hd225Info"),
        hd225Rwd: i("proto_cs.huodong.hd225Rwd"),
        hd226Info: i("proto_cs.huodong.hd226Info"),
        hd226Rwd: i("proto_cs.huodong.hd226Rwd"),
        hd313Info: i("proto_cs.huodong.hd313Info"),
        hd313Get: i("proto_cs.huodong.hd313Get"),
        hd313YXRank: i("proto_cs.huodong.hd313YXRank"),
        hd313UserRank: i("proto_cs.huodong.hd313UserRank"),
        hd313QuRank: i("proto_cs.huodong.hd313QuRank"),
        hd313Chat: i("proto_cs.huodong.hd313Chat"),
        hd313Check: i("proto_cs.huodong.hd313Check"),
        hd313Log: i("proto_cs.huodong.hd313Log"),
        hd314Info: i("proto_cs.huodong.hd314Info"),
        hd314Get: i("proto_cs.huodong.hd314Get"),
        hd314YXRank: i("proto_cs.huodong.hd314YXRank"),
        hd314UserRank: i("proto_cs.huodong.hd314UserRank"),
        hd314QuRank: i("proto_cs.huodong.hd314QuRank"),
        hd314Chat: i("proto_cs.huodong.hd314Chat"),
        hd314Check: i("proto_cs.huodong.hd314Check"),
        hd314Log: i("proto_cs.huodong.hd314Log"),
        hd315Info: i("proto_cs.huodong.hd315Info"),
        hd315Rank: i("proto_cs.huodong.hd315Rank"),
        hd6183paihang: i("proto_cs.huodong.hd6183paihang"),
        hd6183buy: i("proto_cs.huodong.hd6183buy"),
        hd6187buy: i("proto_cs.huodong.hd6187buy"),
        hd6139Info: i("proto_cs.huodong.hd6139Info"),
        hd6139Rwd: i("proto_cs.huodong.hd6139Rwd"),
        hd6170Info: i("proto_cs.huodong.hd6170Info"),
        hd6170Rwd: i("proto_cs.huodong.hd6170Rwd"),
        hd6171Info: i("proto_cs.huodong.hd6171Info"),
        hd6171Rwd: i("proto_cs.huodong.hd6171Rwd"),
        hd6212Info: i("proto_cs.huodong.hd6212Info"),
        hd6212Rwd: i("proto_cs.huodong.hd6212Rwd"),
        hd6213Info: i("proto_cs.huodong.hd6213Info"),
        hd6213Rwd: i("proto_cs.huodong.hd6213Rwd"),
        hd6172Info: i("proto_cs.huodong.hd6172Info"),
        hd6172Rwd: i("proto_cs.huodong.hd6172Rwd"),
        hd6173Info: i("proto_cs.huodong.hd6173Info"),
        hd6173Rwd: i("proto_cs.huodong.hd6173Rwd"),
        hd6174Info: i("proto_cs.huodong.hd6174Info"),
        hd6174Rwd: i("proto_cs.huodong.hd6174Rwd"),
        hd6175Info: i("proto_cs.huodong.hd6175Info"),
        hd6175Rwd: i("proto_cs.huodong.hd6175Rwd"),
        hd6176Info: i("proto_cs.huodong.hd6176Info"),
        hd6176Rwd: i("proto_cs.huodong.hd6176Rwd"),
        hd6177Info: i("proto_cs.huodong.hd6177Info"),
        hd6177Rwd: i("proto_cs.huodong.hd6177Rwd"),
        hd6178Info: i("proto_cs.huodong.hd6178Info"),
        hd6178Rwd: i("proto_cs.huodong.hd6178Rwd"),
        hd6179Info: i("proto_cs.huodong.hd6179Info"),
        hd6179Rwd: i("proto_cs.huodong.hd6179Rwd"),
        hd6180Info: i("proto_cs.huodong.hd6180Info"),
        hd6180buy: i("proto_cs.huodong.hd6180buy"),
        hd6181Info: i("proto_cs.huodong.hd6181Info"),
        hd6181Rwd: i("proto_cs.huodong.hd6181Rwd"),
        hd6182Info: i("proto_cs.huodong.hd6182Info"),
        hd6182Rwd: i("proto_cs.huodong.hd6182Rwd"),
        hd6182RwdCharge: i("proto_cs.huodong.hd6182RwdCharge"),
        hd6183exchange: i("proto_cs.huodong.hd6183exchange"),
        hd6186Info: i("proto_cs.huodong.hd6186Info"),
        hd6186Rwd: i("proto_cs.huodong.hd6186Rwd"),
        hd6187Info: i("proto_cs.huodong.hd6187Info"),
        hd6187Rwd: i("proto_cs.huodong.hd6187Rwd"),
        hd6187dayPaihang: i("proto_cs.huodong.hd6187dayPaihang"),
        hd6187Paihang: i("proto_cs.huodong.hd6187Paihang"),
        hd6187flush: i("proto_cs.huodong.hd6187flush"),
        hd6187exchange: i("proto_cs.huodong.hd6187exchange"),
        hd6188Info: i("proto_cs.huodong.hd6188Info"),
        hd6188Rwd: i("proto_cs.huodong.hd6188Rwd"),
        hd6188Journal: i("proto_cs.huodong.hd6188Journal"),
        hd6189Info: i("proto_cs.huodong.hd6189Info"),
        hd6189Rwd: i("proto_cs.huodong.hd6189Rwd"),
        hd250Info: i("proto_cs.huodong.hd250Info"),
        hd251Info: i("proto_cs.huodong.hd251Info"),
        hd252Info: i("proto_cs.huodong.hd252Info"),
        hd253Info: i("proto_cs.huodong.hd253Info"),
        hd254Info: i("proto_cs.huodong.hd254Info"),
        hd255Info: i("proto_cs.huodong.hd255Info"),
        hd256Info: i("proto_cs.huodong.hd256Info"),
        hd257Info: i("proto_cs.huodong.hd257Info"),
        hd258Info: i("proto_cs.huodong.hd258Info"),
        hd259Info: i("proto_cs.huodong.hd259Info"),
        hd6135Info: i("proto_cs.huodong.hd6135Info"),
        hd6166Info: i("proto_cs.huodong.hd6166Info"),
        hd6167Info: i("proto_cs.huodong.hd6167Info"),
        hd6215Info: i("proto_cs.huodong.hd6215Info"),
        hd6216Info: i("proto_cs.huodong.hd6216Info"),
        hd6217Info: i("proto_cs.huodong.hd6217Info"),
        hd6218Info: i("proto_cs.huodong.hd6218Info"),
        hd260Info: i("proto_cs.huodong.hd260Info"),
        hd260Rwd: i("proto_cs.huodong.hd260Rwd"),
        hd261Info: i("proto_cs.huodong.hd261Info"),
        hd261Rwd: i("proto_cs.huodong.hd261Rwd"),
        hd262Info: i("proto_cs.huodong.hd262Info"),
        hd262Rwd: i("proto_cs.huodong.hd262Rwd"),
        hd6168Info: i("proto_cs.huodong.hd6168Info"),
        hd6168Rwd: i("proto_cs.huodong.hd6168Rwd"),
        hd6168TotalRwd: i("proto_cs.huodong.hd6168TotalRwd"),
        hd6184Info: i("proto_cs.huodong.hd6184Info"),
        hd6184Rwd: i("proto_cs.huodong.hd6184Rwd"),
        hd6184TotalRwd: i("proto_cs.huodong.hd6184TotalRwd"),
        hd270Info: i("proto_cs.huodong.hd270Info"),
        hd270Rwd: i("proto_cs.huodong.hd270Rwd"),
        hd271Info: i("proto_cs.huodong.hd271Info"),
        hd271Rwd: i("proto_cs.huodong.hd271Rwd"),
        hd272Info: i("proto_cs.huodong.hd272Info"),
        hd272Rwd: i("proto_cs.huodong.hd272Rwd"),
        hd280Info: i("proto_cs.huodong.hd280Info"),
        hd280buy: i("proto_cs.huodong.hd280buy"),
        hd280exchange: i("proto_cs.huodong.hd280exchange"),
        hd280play: i("proto_cs.huodong.hd280play"),
        hd280paihang: i("proto_cs.huodong.hd280paihang"),
        hd280Rwd: i("proto_cs.huodong.hd280Rwd"),
        hd281Info: i("proto_cs.huodong.hd281Info"),
        hd281buy: i("proto_cs.huodong.hd281buy"),
        hd281exchange: i("proto_cs.huodong.hd281exchange"),
        hd281play: i("proto_cs.huodong.hd281play"),
        hd281paihang: i("proto_cs.huodong.hd281paihang"),
        hd281Rwd: i("proto_cs.huodong.hd281Rwd"),
        hd281getRwd: i("proto_cs.huodong.hd281getRwd"),
        hd282Info: i("proto_cs.huodong.hd282Info"),
        hd282buy: i("proto_cs.huodong.hd282buy"),
        hd282exchange: i("proto_cs.huodong.hd282exchange"),
        hd282play: i("proto_cs.huodong.hd282play"),
        hd282paihang: i("proto_cs.huodong.hd282paihang"),
        hd282Rwd: i("proto_cs.huodong.hd282Rwd"),
        hd283Info: i("proto_cs.huodong.hd283Info"),
        hd283buy: i("proto_cs.huodong.hd283buy"),
        hd283exchange: i("proto_cs.huodong.hd283exchange"),
        hd283play: i("proto_cs.huodong.hd283play"),
        hd283paihang: i("proto_cs.huodong.hd283paihang"),
        hd283Rwd: i("proto_cs.huodong.hd283Rwd"),
        hd284Info: i("proto_cs.huodong.hd284Info"),
        hd284buy: i("proto_cs.huodong.hd284buy"),
        hd284exchange: i("proto_cs.huodong.hd284exchange"),
        hd284play: i("proto_cs.huodong.hd284play"),
        hd284paihang: i("proto_cs.huodong.hd284paihang"),
        hd284Rwd: i("proto_cs.huodong.hd284Rwd"),
        hd284getRwd: i("proto_cs.huodong.hd284getRwd"),
        hd6136Info: i("proto_cs.huodong.hd6136Info"),
        hd6136buy: i("proto_cs.huodong.hd6136buy"),
        hd6136exchange: i("proto_cs.huodong.hd6136exchange"),
        hd6136play: i("proto_cs.huodong.hd6136play"),
        hd6136paihang: i("proto_cs.huodong.hd6136paihang"),
        hd6136getRwd: i("proto_cs.huodong.hd6136getRwd"),
        hd6136Rewards: i("proto_cs.huodong.hd6136Rewards"),
        hd6136Journal: i("proto_cs.huodong.hd6136Journal"),
        hd6137Info: i("proto_cs.huodong.hd6137Info"),
        hd6137Rwd: i("proto_cs.huodong.hd6137Rwd"),
        hd6152Info: i("proto_cs.huodong.hd6152Info"),
        hd6152Rwd: i("proto_cs.huodong.hd6152Rwd"),
        hd6121Info: i("proto_cs.huodong.hd6121Info"),
        hd6121Rwd: i("proto_cs.huodong.hd6121Rwd"),
        hd6122Info: i("proto_cs.huodong.hd6122Info"),
        hd6122Rwd: i("proto_cs.huodong.hd6122Rwd"),
        hd285Info: i("proto_cs.huodong.hd285Info"),
        hd285buy: i("proto_cs.huodong.hd285buy"),
        hd285buyGift: i("proto_cs.huodong.hd285buyGift"),
        hd285getRwd: i("proto_cs.huodong.hd285getRwd"),
        hd290Info: i("proto_cs.huodong.hd290Info"),
        hd290Yao: i("proto_cs.huodong.hd290Yao"),
        hd290log: i("proto_cs.huodong.hd290log"),
        hd290exchange: i("proto_cs.huodong.hd290exchange"),
        hd291Info: i("proto_cs.huodong.hd291Info"),
        hd291Zadan: i("proto_cs.huodong.hd291Zadan"),
        hd291Set: i("proto_cs.huodong.hd291Set"),
        hd292exchange: i("proto_cs.huodong.hd292exchange"),
        hd293Rwd: i("proto_cs.huodong.hd293Rwd"),
        hd293Task: i("proto_cs.huodong.hd293Task"),
        hd293Run: i("proto_cs.huodong.hd293Run"),
        hd287Info: i("proto_cs.huodong.hd287Info"),
        hd287Get: i("proto_cs.huodong.hd287Get"),
        hdGetXSRank: i("proto_cs.huodong.hdGetXSRank"),
        hd6169Info: i("proto_cs.huodong.hd6169Info"),
        hd6169Yao: i("proto_cs.huodong.hd6169Yao"),
        hd6123Fight: i("proto_cs.huodong.hd6123Fight"),
        hd6123Clear: i("proto_cs.huodong.hd6123Clear"),
        hd6123Add: i("proto_cs.huodong.hd6123Add"),
        hd6123Info: i("proto_cs.huodong.hd6123Info"),
        hd6123Rank: i("proto_cs.huodong.hd6123Rank"),
        hd6123Rwd: i("proto_cs.huodong.hd6123Rwd"),
        hd6123Referr: i("proto_cs.huodong.hd6123Referr"),
        hd6183Info: i("proto_cs.huodong.hd6183Info"),
        hd6183Paly: i("proto_cs.huodong.hd6183Paly"),
        hd6183PalyTen: i("proto_cs.huodong.hd6183PalyTen"),
        hd6183Rwd: i("proto_cs.huodong.hd6183Rwd"),
        hd6142Rwd: i("proto_cs.huodong.hd6142Rwd"),
        hd6142Info: i("proto_cs.huodong.hd6142Info"),
        hd6142Rank: i("proto_cs.huodong.hd6142Rank"),
        hd6142Zan: i("proto_cs.huodong.hd6142Zan"),
        hd6142Math: i("proto_cs.huodong.hd6142Math"),
        hd6142Fight: i("proto_cs.huodong.hd6142Fight"),
        hd6010Info: i("proto_cs.huodong.hd6010Info"),
        hd6010Rank: i("proto_cs.huodong.hd6010Rank"),
        hd6010Fight: i("proto_cs.huodong.hd6010Fight"),
        hd6010Add: i("proto_cs.huodong.hd6010Add"),
        hd6211Info: i("proto_cs.huodong.hd6211Info"),
        hd6211free: i("proto_cs.huodong.hd6211free"),
        hd6211cash: i("proto_cs.huodong.hd6211cash"),
        hd6214Info: i("proto_cs.huodong.hd6214Info"),
        hd6015Rank: i("proto_cs.huodong.hd6015Rank"),
        hd6015Info: i("proto_cs.huodong.hd6015Info"),
        hd6015buy: i("proto_cs.huodong.hd6015buy"),
        hd6015exchange: i("proto_cs.huodong.hd6015exchange"),
        hd6015Rwd: i("proto_cs.huodong.hd6015Rwd"),
        hd6220Info: i("proto_cs.huodong.hd6220Info"),
        hd6220Rwd: i("proto_cs.huodong.hd6220Rwd"),
        hd6220exchange: i("proto_cs.huodong.hd6220exchange"),
        hd6220buy: i("proto_cs.huodong.hd6220buy"),
        hd6221Info: i("proto_cs.huodong.hd6221Info"),
        hd6221play: i("proto_cs.huodong.hd6221play"),
        hd6221paihang: i("proto_cs.huodong.hd6221paihang"),
        hd6221Rwd: i("proto_cs.huodong.hd6221Rwd"),
        hd6221Select: i("proto_cs.huodong.hd6221Select"),
        hd6222Info: i("proto_cs.huodong.hd6222Info"),
        hd6222buy: i("proto_cs.huodong.hd6222buy"),
        hd6222play: i("proto_cs.huodong.hd6222play"),
        hd6222paihang: i("proto_cs.huodong.hd6222paihang"),
        hd6222exchange: i("proto_cs.huodong.hd6222exchange"),
        hd6223Info: i("proto_cs.huodong.hd6223Info"),
        hd6223give: i("proto_cs.huodong.hd6223give"),
        hd6223Rwd: i("proto_cs.huodong.hd6223Rwd"),
        hd6224Info: i("proto_cs.huodong.hd6224Info"),
        hd6224buy: i("proto_cs.huodong.hd6224buy"),
        hd6224Rwd: i("proto_cs.huodong.hd6224Rwd"),
        hd6224change: i("proto_cs.huodong.hd6224change"),
        hd6224task: i("proto_cs.huodong.hd6224task"),
        hd6224exchange: i("proto_cs.huodong.hd6224exchange"),
        hd6225Info: i("proto_cs.huodong.hd6225Info"),
        hd6225Rwd: i("proto_cs.huodong.hd6225Rwd"),
        hd6225TotalRwd: i("proto_cs.huodong.hd6225TotalRwd"),
        hd6226Info: i("proto_cs.huodong.hd6226Info"),
        hd6226Rwd: i("proto_cs.huodong.hd6226Rwd"),
        hd6227Info: i("proto_cs.huodong.hd6227Info"),
        hd6227Yao: i("proto_cs.huodong.hd6227Yao"),
        hd6227buy: i("proto_cs.huodong.hd6227buy"),
        hd6227Paihang: i("proto_cs.huodong.hd6227Paihang"),
        hd6227duihuan: i("proto_cs.huodong.hd6227duihuan"),
        hd6228Info: i("proto_cs.huodong.hd6228Info"),
        hd6228buy: i("proto_cs.huodong.hd6228buy"),
        hd6228Rwd: i("proto_cs.huodong.hd6228Rwd"),
        hd6229Info: i("proto_cs.huodong.hd6229Info"),
        hd6229play: i("proto_cs.huodong.hd6229play"),
        hd6229paihang: i("proto_cs.huodong.hd6229paihang"),
        hd6229Rwd: i("proto_cs.huodong.hd6229Rwd"),
        hd6229Select: i("proto_cs.huodong.hd6229Select"),
        hd6229buy: i("proto_cs.huodong.hd6229buy"),
        hd6229exchange: i("proto_cs.huodong.hd6229exchange"),
        hd6230Info: i("proto_cs.huodong.hd6230Info"),
        hd6230buy: i("proto_cs.huodong.hd6230buy"),
        hd6230play: i("proto_cs.huodong.hd6230play"),
        hd6230paihang: i("proto_cs.huodong.hd6230paihang"),
        hd6230exchange: i("proto_cs.huodong.hd6230exchange"),
        hd6231Rwd: i("proto_cs.huodong.hd6231Rwd"),
        hd6231Info: i("proto_cs.huodong.hd6231Info"),
        hd6231Rank: i("proto_cs.huodong.hd6231Rank"),
        hd6231buy: i("proto_cs.huodong.hd6231buy"),
        hd6231exchange: i("proto_cs.huodong.hd6231exchange"),
        hd6232Info: i("proto_cs.huodong.hd6232Info"),
        hd6232buy: i("proto_cs.huodong.hd6232buy"),
        hd6232play: i("proto_cs.huodong.hd6232play"),
        hd6232paihang: i("proto_cs.huodong.hd6232paihang"),
        hd6232exchange: i("proto_cs.huodong.hd6232exchange"),
        hd6233Info: i("proto_cs.huodong.hd6233Info"),
        hd6233Rwd: i("proto_cs.huodong.hd6233Rwd"),
        hd6234Info: i("proto_cs.huodong.hd6234Info"),
        hd6234Paly: i("proto_cs.huodong.hd6234Paly"),
        hd6234PalyTen: i("proto_cs.huodong.hd6234PalyTen"),
        hd6234Rwd: i("proto_cs.huodong.hd6234Rwd"),
        hd6234buy: i("proto_cs.huodong.hd6234buy"),
        hd6234exchange: i("proto_cs.huodong.hd6234exchange"),
        hd6234paihang: i("proto_cs.huodong.hd6234paihang"),
        hd6240Info: i("proto_cs.huodong.hd6240Info"),
        hd6240exchange: i("proto_cs.huodong.hd6240exchange"),
        hd6241Info: i("proto_cs.huodong.hd6241Info"),
        hd6241paihang: i("proto_cs.huodong.hd6241paihang"),
        hd6241Paly: i("proto_cs.huodong.hd6241Paly"),
        hd6241Rwd: i("proto_cs.huodong.hd6241Rwd"),
        hd6241buy: i("proto_cs.huodong.hd6241buy"),
        hd6241exchange: i("proto_cs.huodong.hd6241exchange"),
        hd6244Info: i("proto_cs.huodong.hd6244Info"),
        hd6244Paly: i("proto_cs.huodong.hd6244Paly"),
        hd6244Give: i("proto_cs.huodong.hd6244Give"),
        hd6244Rwd: i("proto_cs.huodong.hd6244Rwd"),
        hd6244Paihang: i("proto_cs.huodong.hd6244Paihang"),
        hd6244buy: i("proto_cs.huodong.hd6244buy"),
        hd6244exchange: i("proto_cs.huodong.hd6244exchange"),
        hd6500Info: i("proto_cs.huodong.hd6500Info"),
        hd6500Get: i("proto_cs.huodong.hd6500Get"),
        hd7001List: i("proto_cs.huodong.hd7001List"),
        hd8002Info: i("proto_cs.huodong.hd8002Info"),
        hd8002buy: i("proto_cs.huodong.hd8002buy"),
        hd8002play: i("proto_cs.huodong.hd8002play"),
        hd8002paihang: i("proto_cs.huodong.hd8002paihang"),
        hd8002exchange: i("proto_cs.huodong.hd8002exchange"),
        hd8003Info: i("proto_cs.huodong.hd8003Info"),
        hd8003buy: i("proto_cs.huodong.hd8003buy"),
        hd8003Play: i("proto_cs.huodong.hd8003Play"),
        hd8003paihang: i("proto_cs.huodong.hd8003paihang"),
        hd8003exchange: i("proto_cs.huodong.hd8003exchange"),
        hd8003Rwd: i("proto_cs.huodong.hd8003Rwd"),
        hd8003log: i("proto_cs.huodong.hd8003log"),
        hd8004Info: i("proto_cs.huodong.hd8004Info"),
        hd8005paihang: i("proto_cs.huodong.hd8005paihang"),
        hd8005buy: i("proto_cs.huodong.hd8005buy"),
        hd8005exchange: i("proto_cs.huodong.hd8005exchange"),
        hd8005Info: i("proto_cs.huodong.hd8005Info"),
        hd8005Paly: i("proto_cs.huodong.hd8005Paly"),
        hd8005PalyTen: i("proto_cs.huodong.hd8005PalyTen"),
        hd8005Rwd: i("proto_cs.huodong.hd8005Rwd"),
        hd8004Rwd: i("proto_cs.huodong.hd8004Rwd"),
        hd8007Info: i("proto_cs.huodong.hd8007Info"),
        hd8007exchange: i("proto_cs.huodong.hd8007exchange"),
        hd8007Refresh: i("proto_cs.huodong.hd8007Refresh"),
        hd8008Info: i("proto_cs.huodong.hd8008Info"),
        hd8008Play: i("proto_cs.huodong.hd8008Play"),
        hd8008Move: i("proto_cs.huodong.hd8008Move"),
        hd8008Rwd: i("proto_cs.huodong.hd8008Rwd"),
        hd8008paihang: i("proto_cs.huodong.hd8008paihang"),
        hd8008buy: i("proto_cs.huodong.hd8008buy"),
        hd8008exchange: i("proto_cs.huodong.hd8008exchange"),
        hd8004Rwd: i("proto_cs.huodong.hd8004Rwd"),
        hd8006Info: i("proto_cs.huodong.hd8006Info"),
        hd8006Play: i("proto_cs.huodong.hd8006Play"),
        hd8006PlayTen: i("proto_cs.huodong.hd8006PlayTen"),
        hd8006Rwd: i("proto_cs.huodong.hd8006Rwd"),
        hd8006paihang: i("proto_cs.huodong.hd8006paihang"),
        hd6242Info: i("proto_cs.huodong.hd6242Info"),
        hd8006buy: i("proto_cs.huodong.hd8006buy"),
        hd8006exchange: i("proto_cs.huodong.hd8006exchange"),
        hd8009Info: i("proto_cs.huodong.hd8009Info"),
        hd8009Play: i("proto_cs.huodong.hd8009Play"),
        hd8009PlayTen: i("proto_cs.huodong.hd8009PlayTen"),
        hd8009Rwd: i("proto_cs.huodong.hd8009Rwd"),
        hd8009paihang: i("proto_cs.huodong.hd8009paihang"),
        hd8009buy: i("proto_cs.huodong.hd8009buy"),
        hd8009exchange: i("proto_cs.huodong.hd8009exchange"),
        hd7010Info: i("proto_cs.huodong.hd7010Info"),
        hd7010Rwd: i("proto_cs.huodong.hd7010Rwd"),
        hd252buy: i("proto_cs.huodong.hd252buy"),
        hd251buy: i("proto_cs.huodong.hd251buy"),
        hd6166buy: i("proto_cs.huodong.hd6166buy"),
        hd6167buy: i("proto_cs.huodong.hd6167buy"),
        hd255buy: i("proto_cs.huodong.hd255buy"),
        hd259buy: i("proto_cs.huodong.hd259buy"),
        hd257buy: i("proto_cs.huodong.hd257buy"),
        hd254buy: i("proto_cs.huodong.hd254buy"),
        hd6218buy: i("proto_cs.huodong.hd6218buy"),
        hd6900buy: i("proto_cs.huodong.hd6900buy"),
        hd8018Info: i("proto_cs.huodong.hd8018Info"),
        hd8018Select: i("proto_cs.huodong.hd8018Select"),
        hd8018Play: i("proto_cs.huodong.hd8018Play"),
        hd8018Next: i("proto_cs.huodong.hd8018Next"),
        hd8018Reset: i("proto_cs.huodong.hd8018Reset"),
        hd8018Rwd: i("proto_cs.huodong.hd8018Rwd"),
        hd8018PveRwd: i("proto_cs.huodong.hd8018PveRwd"),
        hd8018paihang: i("proto_cs.huodong.hd8018paihang"),
        hd8018Pvepaihang: i("proto_cs.huodong.hd8018Pvepaihang"),
        hd8018buy: i("proto_cs.huodong.hd8018buy"),
        hd8018exchange: i("proto_cs.huodong.hd8018exchange"),
        hd8018Recovery: i("proto_cs.huodong.hd8018Recovery"),
        hd8018Fail:i("proto_cs.huodong.hd8018Fail"),
        hd8018Save:i("proto_cs.huodong.hd8018Save"),

        hd8016Info: i("proto_cs.huodong.hd8016Info"),
        hd8016paihang: i("proto_cs.huodong.hd8016paihang"),
        hd8016UpLevel: i("proto_cs.huodong.hd8016UpLevel"),
        hd8016Rwd: i("proto_cs.huodong.hd8016Rwd"),
        hd8016UpElite: i("proto_cs.huodong.hd8016UpElite"),

        hd8011Info: i("proto_cs.huodong.hd8011Info"),
        hd8011paihang: i("proto_cs.huodong.hd8011paihang"),
        hd8011UpLevel: i("proto_cs.huodong.hd8011UpLevel"),
        hd8011Rwd: i("proto_cs.huodong.hd8011Rwd"),
        hd8011UpElite: i("proto_cs.huodong.hd8011UpElite"),

        hd8022Info: i("proto_cs.huodong.hd8022Info"),
        hd8022Play: i("proto_cs.huodong.hd8022Play"),
        hd8022Rwd: i("proto_cs.huodong.hd8022Rwd"),
        hd8022paihang: i("proto_cs.huodong.hd8022paihang"),
        hd8022Recovery: i("proto_cs.huodong.hd8022Recovery"),
        hd8022exchange: i("proto_cs.huodong.hd8022exchange"),
        hd8022buy: i("proto_cs.huodong.hd8022buy"),

        hd8029Info:i("proto_cs.huodong.hd8029Info"),//打月亮
        hd8029OpenMoon:i("proto_cs.huodong.hd8029OpenMoon"),//打月亮
        hd8029Play:i("proto_cs.huodong.hd8029Play"),//打月亮
        hd8029Rwd:i("proto_cs.huodong.hd8029Rwd"),//打月亮
        hd8029paihang:i("proto_cs.huodong.hd8029paihang"),//打月亮
        hd8029AllPaihang:i("proto_cs.huodong.hd8029AllPaihang"),//打月亮
        hd8029buy:i("proto_cs.huodong.hd8029buy"),//打月亮
        hd8029exchange:i("proto_cs.huodong.hd8029exchange"),//打月亮
        hd8029exchange:i("proto_cs.huodong.hd8029exchange"),//打月亮
        hd8029ShellRwd:i("proto_cs.huodong.hd8029ShellRwd"),//领取好友赠送月亮礼花
        hd8029GetShell: i("proto_cs.huodong.hd8029GetShell"),//领取每日赠送
        hd8029PlayTen: i("proto_cs.huodong.hd8029PlayTen"),//多领十份
    },
    sevendays: {
        sevenSign: i("proto_cs.sevendays.sevenSign"),
        sevenSupplySign: i("proto_cs.sevendays.sevenSupplySign"),
        buyValueGift: i("proto_cs.sevendays.buyValueGift"),
        pickFinalAward: i("proto_cs.sevendays.pickFinalAward"),
        pickScoreAward: i("proto_cs.sevendays.pickScoreAward"),
        pickTaskAward: i("proto_cs.sevendays.pickTaskAward"),
    },
    fuyue: {
        getFuyueInfo: i("proto_cs.fuyue.getFuyueInfo"), // 获取赴约信息
        startStory: i("proto_cs.fuyue.startStory"),     // 开始故事
        getFuser: i("proto_cs.fuyue.getFuser"),         // 获取对手id
        startFight: i("proto_cs.fuyue.startFight"),     // 开始战斗
        saveStory: i("proto_cs.fuyue.saveStory"),       // 保存故事
        noSaveStory: i("proto_cs.fuyue.noSaveStory"),   // 取消保存
        exchange: i("proto_cs.fuyue.exchange"),         // 兑换商城 id(配置表唯一id)
        delStory: i("proto_cs.fuyue.delStory"),         // 删除故事 id(唯一id)
        pickClearanceAward: i("proto_cs.fuyue.pickClearanceAward"),// 领取剧情完结奖励
        buyCount: i("proto_cs.fuyue.buyCount"),// 购买次数
    },
    chat: {
        sev: i("proto_cs.chat.sev"),
        sevhistory: i("proto_cs.chat.sevhistory"),
        club: i("proto_cs.chat.club"),
        kuafu: i("proto_cs.chat.kuafu"),
        clubhistory: i("proto_cs.chat.clubhistory"),
        kuafuhistory: i("proto_cs.chat.kuafuhistory"),
        addblacklist: i("proto_cs.chat.addblacklist"),
        subblacklist: i("proto_cs.chat.subblacklist")
    },
    recode: {
        exchange: i("proto_cs.recode.exchange")
    },
    hunt: {
        hunt: i("proto_cs.hunt.hunt"),
        play: i("proto_cs.hunt.play"),
        jf_rwd: i("proto_cs.hunt.jf_rwd"),
        rankRwd: i("proto_cs.hunt.rankRwd"),
        allDressRwd: i("proto_cs.hunt.allDressRwd"),
        paihang: i("proto_cs.hunt.paihang"),
        isOpen: i("proto_cs.hunt.isOpen")
    },
    taofa: {
        taofa: i("proto_cs.taofa.taofa"),
        play: i("proto_cs.taofa.play"),
        paihang: i("proto_cs.taofa.paihang"),
        rootPlay: i("proto_cs.taofa.rootPlay"),
        rootInfo: i("proto_cs.taofa.rootInfo")
    },
    hanlin: {
        listinfo: i("proto_cs.hanlin.listinfo"),
        opendesk: i("proto_cs.hanlin.opendesk"),
        comein: i("proto_cs.hanlin.comein"),
        sitdown: i("proto_cs.hanlin.sitdown"),
        ti: i("proto_cs.hanlin.ti"),
        find: i("proto_cs.hanlin.find"),
        upskill: i("proto_cs.hanlin.upskill"),
        suoding: i("proto_cs.hanlin.suoding")
    },
    silkroad: {
        trade: i("proto_cs.silkroad.trade"),
        play: i("proto_cs.silkroad.play"),
        rootPlay: i("proto_cs.silkroad.rootPlay"),
        paihang: i("proto_cs.silkroad.paihang")
    },
    gongdou: {
        gongdou: i("proto_cs.gongdou.gongdou"),
        fight: i("proto_cs.gongdou.fight"),
        showCard: i("proto_cs.gongdou.showCard"),
        spec: i("proto_cs.gongdou.spec"),
        paihang: i("proto_cs.gongdou.paihang"),
        duihuan: i("proto_cs.gongdou.duihuan"),
        giveup: i("proto_cs.gongdou.giveup"),
        shopBuy: i("proto_cs.gongdou.shopBuy"),
        recycle: i("proto_cs.gongdou.recycle"),
        downcard: i("proto_cs.gongdou.downcard"),
        battlecard: i("proto_cs.gongdou.battlecard")
    },
    kuayamen: {
        comehd: i("proto_cs.kuayamen.comehd"),
        yamen: i("proto_cs.kuayamen.yamen"),
        chushi: i("proto_cs.kuayamen.chushi"),
        tiaozhan: i("proto_cs.kuayamen.tiaozhan"),
        fuchou: i("proto_cs.kuayamen.fuchou"),
        zhuisha: i("proto_cs.kuayamen.zhuisha"),
        findzhuisha: i("proto_cs.kuayamen.findzhuisha"),
        pizun: i("proto_cs.kuayamen.pizun"),
        getSevRwd: i("proto_cs.kuayamen.getSevRwd"),
        fight: i("proto_cs.kuayamen.fight"),
        seladd: i("proto_cs.kuayamen.seladd"),
        getrwd: i("proto_cs.kuayamen.getrwd"),
        getRank: i("proto_cs.kuayamen.getRank"),
        yamenhistory: i("proto_cs.kuayamen.yamenhistory"),
        kuafu: i("proto_cs.kuayamen.kuafu"),
        kuafuhistory: i("proto_cs.kuayamen.kuafuhistory"),
        getYxRank: i("proto_cs.kuayamen.getYxRank"),
        getMyRank: i("proto_cs.kuayamen.getMyRank")
    },
    kuaguo: {
        kuaguo: i("proto_cs.kuaguo.kuaguo"),
        batHero: i("proto_cs.kuaguo.batHero"),
        healHero: i("proto_cs.kuaguo.healHero"),
        supSoldier: i("proto_cs.kuaguo.supSoldier"),
        move: i("proto_cs.kuaguo.move"),
        action: i("proto_cs.kuaguo.action")
    },
    guozijian: {
        gzj: i("proto_cs.guozijian.gzj"),
        addDesk: i("proto_cs.guozijian.addDesk"),
        startStudy: i("proto_cs.guozijian.startStudy"),
        bribery: i("proto_cs.guozijian.bribery"),
        overWork: i("proto_cs.guozijian.overWork"),
        alloverWork: i("proto_cs.guozijian.alloverWork"),
        getdayreward: i("proto_cs.guozijian.getdayreward"),
        alldayreward: i("proto_cs.guozijian.alldayreward")
    },
    scpoint: {
        recored: i("proto_cs.scpoint.recored"),
        story: i("proto_cs.scpoint.story"),
        zwStory: i("proto_cs.scpoint.zwStory"),
        jyStory: i("proto_cs.scpoint.jyStory"),
        emailStory: i("proto_cs.scpoint.emailStory"),
        emailSonStory: i("proto_cs.scpoint.emailSonStory"),
        yjEmailSonStory: i("proto_cs.scpoint.yjEmailSonStory"),
        heroOrwifeStory: i("proto_cs.scpoint.heroOrwifeStory")
    },
    kitchen: {
        buyStove: i("proto_cs.kitchen.buyStove"),
        food: i("proto_cs.kitchen.food"),
        over: i("proto_cs.kitchen.over"),
        allover: i("proto_cs.kitchen.allover"),
        set: i("proto_cs.kitchen.set"),
        setinfo: i("proto_cs.kitchen.setinfo"),
        allstart: i("proto_cs.kitchen.allstart"),
        fast: i("proto_cs.kitchen.fast"),
        buyFood: i("proto_cs.kitchen.buyFood")
    },
    treasure: {
        reward: i("proto_cs.treasure.reward"),
        treasure: i("proto_cs.treasure.treasure"),
        clipTrea: i("proto_cs.treasure.clipTrea"),
        clear: i("proto_cs.treasure.clear"),
        rank: i("proto_cs.treasure.rank"),
        trun: i("proto_cs.treasure.trun"),
        reset: i("proto_cs.treasure.reset"),
        win: i("proto_cs.treasure.win"),
        info: i("proto_cs.treasure.info"),
        tidyRank: i("proto_cs.treasure.tidyRank"),
        addCount: i("proto_cs.treasure.addCount")
    },
    voice: {},
    flower: {
        rwd: i("proto_cs.flower.rwd"),
        yjRwd: i("proto_cs.flower.yjRwd"),
        steal: i("proto_cs.flower.steal"),
        plant: i("proto_cs.flower.plant"),
        yjPlant: i("proto_cs.flower.yjPlant"),
        open: i("proto_cs.flower.open"),
        plantRwd: i("proto_cs.flower.plantRwd"),
        yjPlantRwd: i("proto_cs.flower.yjPlantRwd"),
        rank: i("proto_cs.flower.rank"),
        flush: i("proto_cs.flower.flush"),
        info: i("proto_cs.flower.info"),
        wordlTree: i("proto_cs.flower.wordlTree"),
        treeRank: i("proto_cs.flower.treeRank"),
        protectCover: i("proto_cs.flower.protectCover")
    },
    keju: {},
    fapei: {
        info: i("proto_cs.fapei.info"),
        addDesk: i("proto_cs.fapei.addDesk"),
        banish: i("proto_cs.fapei.banish"),
        recall: i("proto_cs.fapei.recall")
    },
    business: {
        startBusiness: i("proto_cs.business.startBusiness"),
        nextTravel: i("proto_cs.business.nextTravel"),
        buyItem: i("proto_cs.business.buyItem"),
        saleItem: i("proto_cs.business.saleItem"),
        pickFinalAward: i("proto_cs.business.pickFinalAward"),
        getInfo: i("proto_cs.business.getInfo"),
        buyCount: i("proto_cs.business.buyCount"),
    },
    banchai: {
        getInfo: i("proto_cs.banchai.getInfo"),
        startBanchai: i("proto_cs.banchai.startBanchai"),
        chooseAnswer: i("proto_cs.banchai.chooseAnswer"),
        abandonRevive: i("proto_cs.banchai.abandonRevive"),
        buyCount: i("proto_cs.banchai.buyCount"),
        revive: i("proto_cs.banchai.revive"),
        pickFinalAward: i("proto_cs.banchai.pickFinalAward"),
        useBanchaiLing: i("proto_cs.banchai.useBanchaiLing"),
    },
    tanhe: {
        getBaseInfo: i("proto_cs.tanhe.getBaseInfo"),
        getTanheInfo: i("proto_cs.tanhe.getTanheInfo"),
        wipeOut: i("proto_cs.tanhe.wipeOut"),
        weekWipeOut: i("proto_cs.tanhe.weekWipeOut"),
        fight: i("proto_cs.tanhe.fight"),
    },
    invite: {
        getBaseInfo: i("proto_cs.invite.getBaseInfo"), //获取邀约的基础信息
        startInvite: i("proto_cs.invite.startInvite"), //开始邀约
        pickCollectAward: i("proto_cs.invite.pickCollectAward"), //领取收集奖励
        pickMaxAward: i("proto_cs.invite.pickMaxAward"), //领取最大赏味值奖励
        turnFood: i("proto_cs.invite.turnFood"), //翻牌子 
        pickEndAward: i("proto_cs.invite.pickEndAward"), //领取游戏结束奖励
        getFakeFish: i("proto_cs.invite.getFakeFish"), //根据水区获取鱼
        buyBait: i("proto_cs.invite.buyBait"), //购买鱼饵
        consumeBait: i("proto_cs.invite.consumeBait"), //消耗鱼饵
        buyCount: i("proto_cs.invite.buyCount"), //购买次数
        getRandYur: i("proto_cs.invite.getRandYur"), //获取随机鱼饵
        pickRandYur: i("proto_cs.invite.pickRandYur"), //领取鱼饵
        goFishing: i("proto_cs.invite.goFishing"), //钓鱼的结果
        getCollectInfo: i("proto_cs.invite.getCollectInfo"), //请求风物志信息
        pickTaskAward: i("proto_cs.invite.pickTaskAward"), //领取成就奖励
    },
    jiaoyou:{
        getBaseInfo: i("proto_cs.jiaoyou.getBaseInfo"), //获取基础信息
        getFightInfo: i("proto_cs.jiaoyou.getFightInfo"), //获取战斗信息
        fight: i("proto_cs.jiaoyou.fight"),  //epId 选择的属性id
        cashBuyCount: i("proto_cs.jiaoyou.cashBuyCount"), //花费元宝购买次数
        startGuard: i("proto_cs.jiaoyou.startGuard"),  //开始守护
        refreshGuardList: i("proto_cs.jiaoyou.refreshGuardList"), //刷新守护列表
        pickGuardAward: i("proto_cs.jiaoyou.pickGuardAward"), //领取守护奖励
        pickGuardWeekAward: i("proto_cs.jiaoyou.pickGuardWeekAward") //领取每周守护次数奖励
    },
    clothe:{
        pickHuaFuAward: i("proto_cs.clothe.pickHuaFuAward"), //领取华服奖励
        jyUpLv: i("proto_cs.clothe.jyUpLv"), //锦衣华服升级(裁剪)
        putCard: i("proto_cs.clothe.putCard"),  //放置卡牌套装id  大槽位  卡牌
        getUnlockInfo: i("proto_cs.clothe.getUnlockInfo"), //获取槽位解锁信息
        refresh: i("proto_cs.clothe.refresh"),  //开始守护
        equipSpecial: i("proto_cs.clothe.equipSpecial"),  //套装使用特效
    },
    teams:{
        setTeams: i("proto_cs.teams.setTeams"), //编队
    },


};
