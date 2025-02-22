export let EItemType = cc.Enum({
    NoType:        -1,
    Gold:           1,   //元宝
    Experience:     2,   //阅历
    Silver:         3,   //银子
    Reputation:     4,   //名声
    GoldLeaf:       6,   //金叶子
    Jade:           1001,   //玉如意
    Stick:          938,   //木棒
    CrushLife:      956,//体力药水
    CrushBonus:     957,//三消勋章
    TreasureTicket: 959,//宝藏线索
    TreasureScore:  960,//宝藏积分
    MoonGold:       961,//月之尘
    MoonBoom:       962,//箭矢
    Tofu:           970,//豆腐公主-豆腐
    PvpScore:       999, //pvp积分
});

export let EFuncOpenType = cc.Enum({
    NoType:        -1,
    Null:           1,   //常驻
    Level:          2,   //等级
    Chapter:        3,   //剧情大章节
    ServantLevel:   4,   //伙伴等级
    Confidant:      5,   //知己数量（废弃）
    SonAdultNum:    6,   //徒弟成年后数量
    SonNum:         7,   //徒弟数量（暂时没用）
    ServantNum:     8,   //伙伴数量
    LittleChapter:  9,   //剧情的小章节
    MainTask:      10,   //主线任务
});

export let RankType = cc.Enum({
    Servant: 	1, //伙伴冲榜
    Cherry:     2, //樱花活动
    Fishing:    3,//钓鱼
    CrushStage: 4,//过关排行
    CrushScore: 5,//积分排行
    ToFu: 6,//豆腐公主排行
    BeachTreasureRank:7,//海滩夺宝排行
    MoonBattleDailyRank:     8,//打月亮日排行
    MoonBattleTotalRank:     9,//打月亮总排行
    QingMingRank:            10,//游山玩水
    CookingRank:             11,//厨师大赛
});

export let BonusViewType = cc.Enum({
    FishScoreBonus: 	1, //钓鱼活动积分奖励
    CrushPassBonus:     2, //过关奖励
    TofuPassBonus:      3, //豆腐奖励
    BeachTreasureTask:  4, //海滩夺宝任务奖励
    BeachTreasureAchieve:  5, //海滩夺宝成就奖励
});

export let EndViewType = cc.Enum({
    CrushEnd:   1,//三消结算
    BeachTreasureEnd: 2,//海滩夺宝结算
});

export let TofuType = cc.Enum({
    NoType:        -1,
    TofuGirl: 	1, //豆腐公主
    TofuRightBlock:  2, //豆腐块
    TofuLeftBlock:  3, //豆腐块
    TofuBlock1:  4, //豆腐块
    TofuBlock2:  5, //豆腐块
    TofuBlock3:  6, //豆腐块
    TofuBlock4:  7, //豆腐块
    TofuBlock5:  8, //豆腐块
    TofuTightBlock:9,//硬直
    TofuLeftTightBlock:10,//硬直
});

export let BeachTreasureBulltType = cc.Enum({
    NoType:         0,
    Coin:           1,//金币
    Diamond:        2,//钻石
    Bomb:           3,//炸弹
    Star:           4,//星
});

export let BeachTreasurePetType = cc.Enum({
    NoType:         0,
    Pet1:           1,//宠物1
    Pet2:           2,//宠物2
    Pet3:           3,//宠物3
    Pet4:           4,//宠物4
});

/**战斗的类型*/
export let FIGHTBATTLETYPE = cc.Enum({
    NONE: 0, //小战斗
    NORMAL: 1,  /**宫斗*/
    BOSS: 2, //大战斗
    SPECIAL_BOSS:3,//特殊的大战斗，沿用的时候小战斗的方式
    MINIGAME: 97, //小游戏 胜利失败结算用
    TANHE: 98,  //弹劾
    FUYUE: 99,  /**赴约战斗*/
    JIAOYOU: 100,
    COPY_BOSS: 101, //公会boss,
    FURNITURE:102,  //宴会材料获取打架
});

/**伙伴空间小游戏的类型*/
export let MINIGAMETYPE = cc.Enum({
    Null:    0,
    CAIMI:   1,   //猜谜
    DUISHI:  2,   //对诗
    CAIQUAN: 3,   //猜拳
    FISH:    4,   //钓鱼
    FOOD:    5,   //饮食
});

/**钓鱼的状态*/
export let FISH_STATE = cc.Enum({
    /**初始*/
    NONE:0,
    /**选择力气抛竿*/
    CAST_A_POLE:1,
    /**等待鱼上钩*/
    WAIT_FISH:2,
    /**收竿*/
    COLLECT_FISHING_ROD:3,
    /**结束*/
    END:4,
})

export let ITEM_GETTYPE = cc.Enum({
    /**玉如意解锁*/
    YURUYI:1,
    /**跳转到VIP礼包页面*/
    VIP_GIFT:2,
    /**读取text一行字*/
    READ_FIXTEXT:3,
    /**ICONOPEN解锁*/
    ICONOPEN:4,
    /**活动兑换商城解锁*/
    UNLOCK_ACTIVESHOP:5,
})

/**套装裁剪的奖励类型*/
export let USER_CUT_LEVELUP_TYPE = cc.Enum({
    /**获得特殊装扮（特效，服装）*/
    GET_SPECIAL_EFFECT:1,
    /**套装衣服属性m增加X点*/
    ADD_PROP:2,
    /**华服值增加X点*/
    ADD_CLOTHE_VALUE:3,
    /**解锁心忆槽位*/
    UNLOCK_XINYI:4,
    /**战斗时伙伴气势增加X*/
    ADD_SERVANT_PROP:5,
    /**政务次数上限增加X*/
    ADD_BANCHAI_NUM:6,
    /**每日随机问候获得奖励次数增加X*/
    ADD_RANDOM_HELLO:7,
    /**伙伴邀约次数上限增加X*/
    ADD_INVITE_SERVANT:8,
    /**政务时获得的[M资源]+X%*/
    BANCHAI_ITEM_ADDPERCENT:9,
})

/**解锁心忆大卡槽位类型*/
export let UNLOCK_CARD_BIG_SLOT_TYPE = cc.Enum({
    /**集齐套装解锁*/
    COLLECT_SUIT:1,
    /**套装等级解锁*/
    SUIT_LEVEL:2,
    /**锦衣等级解锁*/
    CLOTHE_CUT_LEVEL:3,
})

/**解锁心忆大卡里的小卡槽类型*/
export let UNLOCK_CARD_SMALL_SLOT_TYPE = cc.Enum({
    /**放置卡牌解锁*/
    PALACE_CARD:0,
    /**气势XX解锁*/
    ACHIEVE_PROP1:1,
    /**智谋XX解锁*/
    ACHIEVE_PROP2:2,
    /**政略XX解锁*/
    ACHIEVE_PROP3:3,
    /**魅力XX解锁*/
    ACHIEVE_PROP4:4,
    /**卡牌等级达到XX解锁*/
    CARD_LEVEL:5,
})


/**卡槽技能类型*/
export let CARD_SLOT_SKILL_TYPE = cc.Enum({
    /**当前放置卡牌M数值增加X%*/
    CARD_PROP_ADDPERCENT:1,
    /**政务时资源M获得增加X%*/
    BANCHAI_ITEM_ADDPERCENT:2,
    /**钓鱼积分增加X%*/
    FISH_SCORE_ADDPERCENT:3,
    /**饮食积分增加X%*/
    FOOD_SCORE_ADDPERCENT:4,
    /**伙伴m才学经验增加X%*/
    SERVANT_STUDY_REDUCE_TIME:5,
    /**徒弟活力恢复时间减少X秒*/
    CHILD_ENERGY_RECOVER_REDUCE_TIME:6,
    /**徒弟游历时间减少X秒*/
    CHILD_LILIAN_REDUCE_TIME:7,
    /**徒弟游历获取资源M增加X%*/
    CHILD_LILIAN_ITEM_ADDPERCENT:8,
    /**郊游守护产出增加X%*/
    JIAOYOU_OUTPUT_ADDPERCENT:9,
    /**弹劾m产出增加X%*/
    TANHE_ADDPERCENT:10,
})

/**卡槽属性类型*/
export let CARD_SLOT_PROP_TYPE = cc.Enum({
    /**本套装所有部件属性m增加X点*/
    SUIT_PROP_ADD:1,
    /**当前放置卡牌M属性增加X点*/
    CARD_PROP_ADD:2,
})

/**档案解锁的类型*/
export let CLOTHE_ARCHIEVE_UNLOCK_TYPE = cc.Enum({
    /**获得服装部件解锁*/
    GET_CLOTHE_PART:1,
    /**锦衣等级X解锁*/
    CLOTHE_CUT_LEVEL:2,
    /**套装等级x解锁*/
    SUIT_LEVEL:3,
})


export let BlockWidth = 81;  //三消块大小

//商场融合类型
export let Combine_Shop_TYPE = cc.Enum({
    NoType:0,
    NormalShop:1,//普通商场
    ClotheShop:2,//服装商场
    FuyueShop:3,//赴约兑换商场
    XianliShop:4,//献礼兑换商场
    TingdouShop:5,//廷斗兑换商场
    ShopHomeJF:6,//家园房间积分
})

/**帮会建筑升级*/
export let CLUP_BUILD_TYPE = cc.Enum({
    /**理事间*/
    LI_SHI_JIAN:1,
    /**商坊*/
    SHANG_FANG:2,
    /**谏言堂*/
    JIANYAN_TANG:3,
});

/**攻击者类型*/
export let BATTLE_ATTACK_OWNER = cc.Enum({
    NONE:0,
    /**女主*/
    PLAYER:1,
    /**NPC*/
    NPC:2,
})


/**战斗状态*/
export let BATTLE_STATE = cc.Enum({
    NONE:0,
    /**攻击中*/
    ATTACKING:1,
    /**攻击结束*/
    ATTACKEND:2,
})


/**战斗卡牌技能buff类型*/
export let BATTLE_CARD_BUFF_TYPE = cc.Enum({
    /**额外打出总伤害外X%的伤害*/
    ADD_DAMAGE_PERCENT:1,
    /**本回合将敌方属性转化为[M]，并降低其[X]的攻击力*/
    TRANSFER_PROP:2,
    /**本回合忽视对方[X%]的伤害*/
    MISS_PERCENT:3,
    /**使用技能后N回合内（包含本回合），若触发克制，克制额外增加X%伤害，触发一次后该效果消失 */
    RESTRAINT_ADD_DAMAGE:4,
    /**使用技能后N回合内（包含本回合），若触发连招，连招增加X%伤害，触发一次后该效果消失 */
    COMBO_ADD_DAMAGE:5,
    /**本回合必定按照克制伤害计算*/
    RESTRAINT:6,
    /**使用技能后触发连招，并清空连击点（属性按连击点属性计算，若使用技能时无连击点，则按使用技能卡牌的属性计算*/
    GET_COMBO:7,
    /**使用技能后，将当前所有连击点属性变为[M]*/
    CHANGE_PROP:8,
})

 /**服务器返回错误*/
export let SERVER_CALLBACK_ERROR_CODE = cc.Enum({
        /**普通报错，上浮提示那种*/
        NONE:0,
        /**踢出公会*/
        NO_CLUB:10001,
        /**公会战boss结束*/
        NO_CLUB_BOSS: 10002,
        /**宴会已结束*/
        UNION_PARTY_OVER:10003,
        /**在其他地方登陆*/
        LOGINE_REPEAT:30000,
})

/**宴会的状态*/
export let UNION_PARTY_STATE = cc.Enum({
    /**未开启*/
    NONE:1,
    /**开启还不能进入*/
    OPEN_BUT_NOT_ENTER:2,
    /**可以进入*/
    CAN_ENTER:3,
    /**结束*/
    END:4,
})

/**挂机状态*/
export let UNION_PARTY_HANDUP_STATE = cc.Enum({
    /**未挂机*/
    NONE:0,
    /**挂机中*/
    DOING:1,
    /**挂机结束*/
    END:2,
})

/**小游戏*/
export let MINIGAMEOWNER_TYPE = cc.Enum({
    /**普通*/
    NORMAL:0,
    /**宴会小游戏*/
    UNION_PARTY:1,
})

export let UNION_BOSS_CD = 43200;
export let DAY_SECOND = 86400;
