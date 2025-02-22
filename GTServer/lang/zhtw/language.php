<?php

//幫會
define('CLUB_HAVE_JOIN', '已擁有幫會');
define('CLUB_NAME_NOT_NULL', '幫會名稱不能為空');
define('CLUB_PWD_NOT_NULL', '幫會密碼不能為空');
define('CLUB_PWD_ERROR', '密碼錯誤');
define('CLUB_IS_NULL', '幫會不存在');
define('CLUB_IS_EMPTY', '無幫會可加入');
define('CLUB_QUIT_TIME_TIPS', '退幫未達24小時');
define('CLUB_SUCCESS_APPLICATION', '申請成功');
define('CLUB_NO_HAVE_JOIN', '未加入幫會');
define('CLUB_MODIFY_ONLY_LEADER', '只有幫主才能修改');
define('CLUB_PERMISSION_DENIED_MODIFY', '無權限修改');
define('CLUB_PERMISSION_DENIED_OPERATE', '無權限操作');
define('CLUB_PERSON_IS_FULL', '幫會人員已滿');
define('CLUB_LEADER_NOT_QUIT', '幫主不能退出幫會');
define('CLUB_TODAY_BUILDED', '今日已建設');
define('CLUB_CONTRIBUTION_SHORT', '貢獻值不足');
define('CLUB_EXCHANGE_GOODS_UNLOCK', '兌換的物品未解鎖');
define('CLUB_EXCHANGE_GOODS_MAX', '已無可兌換次數');
define('CLUB_MONEY_SHORT', '財富值不足');
define('CLUB_EXP_SHORT', '經驗不足');
define('CLUB_PERSON_FULL', '幫會人員已滿');
define('CLUB_NAME_USERED', '該名字已被占用');
define('CLUB_NO_ALL_NUMBER', '非純數字');
define('CLUB_BUMBER_TO_LONG', '長度不能超過8個字符');
define('CLUB_BUILD_PERSON_TO_MAX', '建設人數已達上限');
define('CLUB_PERSON_TO_MAX', '該幫會人數已達上限');
define('CLUB_COPY_IS_OPEN', '副本已開啟');
define('CLUB_COPY_NEED_LEVEL_SHORT', '幫會等級不足');
define('CLUB_COPY_UNOPEN', '副本未開啟');
define('CLUB_COPY_KILLED_BOSS', 'boss已被擊殺');
define('CLUB_COPY_RESTING_BOSS', 'boss休息中');
define('CLUB_COPY_ESCAPE_BOSS', 'boss已逃跑');
define('CLUB_APPLYED', '已申請');
define('CLUB_OPEN_FUBEN_ERROR', '精英成員只能使用元寶開啟副本');
define('CLUB_OPEN_BAOMING', '不在報名期間');
//新
define('CLUB_NO_FUBANG', '沒有可以轉讓的副幫主');
define('CLUB_NO_ZHUANGLANG', '該成員不在轉讓列表中');
define('CLUB_CAIFU_BUZU', '聯盟財富不足');
define('CLUB_ZHUAN_FUBANG', '只能轉讓給副幫主');
define('CLUB_NO_BANGZHU', '你不是幫主');
define('CLUB_YITUIBANG', '該大人已退幫');

//門客
define('HERO_LEVEL_CAP','已達到等級上限');
define('HERO_COIN_SHORT','銀兩不足');
define('HERO_LEVEL_SHORT','等級不足');
define('HERO_LEVEL_FULL','已達到等級上限');
define('HERO_SKILL_FULL','等級達到滿級,請提拔門客');
define('HERO_BOOK_LEVEL_SHORT','書籍經驗不足');
define('HERO_SKILL_LEVEL_SHORT','技能經驗不足');
define('HERO_RESTING','門客休息中');
define('HERO_CAN_PLAY','門客已經可以戰鬥');
define('HERO_TODAY_NOT_PLAY','門客出戰次數已用完');
define('HERO_HAVEED','已擁有該門客');
define('HERO_SKILL_LEVEL_FULL','技能已達到最高級');


//道具
define('ITEMS_ERROR', '道具錯誤');
define('ITEMS_USE_ERROR', '道具使用錯誤');
define('ITEMS_USE_HERO_ERROR', '門客道具使用錯誤');
define('ITEMS_NUMBER_SHORT', '道具不足');
define('ITEMS_TYPE_ERROR', '道具類型錯誤');

//酒樓
define('BOITE_NO_OPEN','官品達到從七品開啟');
define('BOITE_NO_FEAST','無人擺設宴席');
define('BOITE_FEAST_END','宴會已結束');
define('BOITE_FEAST_HAVE_ATTEND','已參加過該宴會');
define('BOITE_SEATE_USERED','該席位已被占用');
define('BOITE_FEAST_PLAYING','宴會未結束');
define('BOITE_FEAST_PARAM_ERROR','宴會參數錯誤');
define('BOITE_EXCHANGE_SCORE_SHORT','積分不足');
define('BOITE_EXCHANGE_HAVE_GOODS','該物品已兌換');
define('BOITE_GOODS_REFRESH_NUM_SHORT','次數不足');
define('BOITE_ATTEND_NUM_SHORT','次數不足');
define('BOITE_ATTEND_NO_FIND_OWNER','查無此人');


//訂單
define('ORDER_CREATE_ABNORMAL','無法生成訂單');

//排行榜
define('REANK_WORSHIP_COMPLETE','今日膜拜次數已用完');
define('REANK_XUANYUN', '大家同朝為官，以後還需多多關照~');

//子嗣
define('SON_HAVE_NAME', '不能重復取名');
define('SON_NOT_CULTIVATE', '子嗣不可培養');
define('SON_POWER_SHORT', '活力不足');
define('SON_CULTIVATE_IS_EMPTY', '無可培養子嗣');
define('SON_TIME_IS_UP', '不能一鍵恢復');
define('SON_LEVER_SHORT', '不能科舉');
define('SON_MARRIAGE_SISTER_ERROR', '自家子嗣不可聯姻');
define('SON_MARRIAGE_OBJECT_ERROR', '對方狀態錯誤');
define('SON_MARRIAGE_SEX_ERROR', '同性不可聯姻');
define('SON_MARRIAGE_HONOR_ERROR', '子嗣身份不匹配');
define('SON_NOTIN_JUST_LIST', '不在招親列表中');
define('SON_NOTIN_SUCCESS_UP', '恢復活力成功');
define('SON_NOTIN_NO_JUST', '該子嗣已經不在招親');

//經營
define('OPERATE_NUM_SHORT', '經營次數不足');
define('OPERATE_PROVISION_SHORT', '糧草不足');
define('OPERATE_POWER_GT_MAX', '次數超出');
define('OPERATE_TIME_IS_NOT', '還不能征收');
define('OPERATE_TODAY_NO_CASE', '今日無案可審');

//刷圖
define('GAME_LEVER_GT_BMAP', '現在該打boss了');
define('GAME_LEVER_NO_SOLDIER', '士兵不足，無法開戰！');
define('GAME_LEVER_LT_BMAP', '現在該打小怪');
define('GAME_LEVER_PLAY_END','献礼已结束');
define('GAME_LEVER_HERO_PLAYING','門客正在戰鬥中,請稍等');
define('GAME_LEVER_UNOPENED', '活動未開啟');

//紅顏
define('WIFE_USE_ITEMS_ERROR', '道具錯誤');
define('WIFE_SKILL_LEVEL_SHORT', '經驗值不足');
define('WIFE_POWER_EMPTY', '精力不足');
define('WIFE_WEIKAIFANG', '該紅顏未開放');

//尋訪
define('LOOK_FOR_VIP_LEVEL_SHORT', 'VIP等級不足');
define('LOOK_FOR_POWER_SHORT', '體力不足');
define('LOOK_FOR_FATE_FULL', '運勢已滿,無需轉運');
define('LOOK_FOR_FATE_SHORT', '運勢不足');
define('LOOK_FOR_FATE_GT_MAX', '運勢大於90,無法賑災');
define('LOOK_FOR_GOODS_SHORT', '賑災物品不足');

//衙門
define('YAMUN_NOT_OPEN', '尚未开启');
define('YAMUN_CHALLENGE_YOURSELF', '不能挑戰自己');
define('YAMUN_PLAYER_UNCOMMIT', '玩家未參與衙門戰');
define('YAMUN_UNFUND_ENEMY', '找不到敵人');
define('YAMUN_NO_PLAY_HERO', '沒有可以出戰的門客');
define('YAMUN_HAVE_PLAYING_HERO', '有門客出戰中');
define('YAMUN_BONUS_STATUS_ERROR', '狀態錯誤,沒有加成項目');
define('YAMUN_BONUS_SHOP_BUYED', '已購買完');
define('YAMUN_PLAY_GIFT', '請先選擇獎勵');
define('YAMUN_PLAY_END', '戰鬥結束');
define('YAMUN_CHALLENGING', '請進行挑戰');
define('YAMUN_BONUS_SCORE_SHORT', '點數不足');

//學院學習
define('COLLEGE_HERO_LEARNING', '已在學習中');
define('COLLEGE_SEATE_IS_TAKEN', '席位被佔用');
define('COLLEGE_SEATE_UN_TAKEN', '席位沒人');
define('COLLEGE_NO_TIME_YET', '學習還未結束');
define('COLLEGE_ALL_HERO_NO_TIME_YET', '學習還未結束！');

//皇宮
define('PALACE_RESPECT_COMPLETE', '今日請安次數已用完');


//監獄
define('JAIL_NO_PRISONER', '當前沒有犯人');
define('JAIL_RENOWN_FULL', '名望達到上限');
define('JAIL_RENOWN_SHORT', '名望不足');

//世界boss
define('ACT23_CREDITS_EXCHANGE_MAX', '已達到購買上限');
define('ACT23_INTEGRAL_SHORT', '積分不足');
define('ACT13_CD_FLIGHT', '戰鬥冷卻中');

//稱號
define('TITLE_IS_ERROR', '稱號錯誤');
define('TITLE_NO_GET', '未獲得稱號');
define('TITLE_IS_OVERDUE', '稱號錯誤');


//政務
define('GOVERNMENT_NUM_ENPTY', '沒有政務待處理');
define('GOVERNMENT_NUM_FULL', '政務次數已滿');

//簽到
define('SIGN_IN_COMPLETE', '今日簽到次數已用完');

//充值禮包
define('ACT66_UNRECHARGE', '未儲值');
define('ACT66_HAVE_RECEIVE', '已領取');

//vip獎勵
define('ACT67_HAVE_RECEIVE', '已領取');

//月卡\年卡
define('ACT68_UNBUY','未購買');
define('ACT68_OVERDUE','已過期');
define('ACT68_HAVE_RECEIVE','已領取');
define('MONTH_UNBUY','未購買月卡');
define('YEAR_UNBUY','未購買年卡');


//商城
define('SHOP_VIP_LEVEL_SHORT','vip等級不足');
define('SHOP_BUY_NUM_GT_MAX','購買次數超過限制次數');
define('SHOP_ACTIVITY_UNOPEN','活動未進行');

//限時活動
define('ACTHD_OVERDUE', '活動已結束');
define('ACTHD_NO_REWARD', '無獎勵可領取');
define('ACTHD_NO_RECEIVE', '不能領取');
define('ACTHD_ACTIVITY_UNOPEN.__LINE__', '活動未開啟');
define('ACTHD_ACTIVITY_ERROR', '任務發生錯誤');

//日常任務
define('DAILY_UN_COMPLETE', '任務未完成');
define('DAILY_IS_RECEIVE', '已經領取');
define('DAILY_NO_RECEIVE', '分值不夠');

//成就
define('ACHIEVEMENT_UN_TO_ACHIEVE','成就未達成');

//稱號
define('DESIGN_ERROR', '稱號錯誤');
define('DESIGN_MISS', '還未得到該稱號');
define('DESIGN_EXPIRE', '你的稱號已過期');

//兌換碼
define('ACODE_DEVELOPMENT', '該功能正在開發中');
define('ACODE_HAS_THE_FAILURE', '無效兌換碼');
define('ACODE_HAS_LINGQU', '兌換碼已被使用過');
define('ACODE_CFG_INGO_ERROR', '配置文件出錯啦');
define('ACODE_OVERDUE', '兌換碼已過期');
define('ACODE_HASPROBLEM', '兌換碼有問題');
define('ACODE_HAVE_RECEIVE', '您已領取過該活動的禮品');
define('ACODE_EXCHANGE_FAILURE', '兌換失敗');


//郵件
define('MAIL_DELETED', '郵件已刪除');
define('MAIL_MEIYOUJIAN', '找不到該郵件');
define('MAIL_IS_RECEIVE', '郵件已領取');
define('MAIL_NO_REWARD', '無獎勵道具');
define('MAIL_UNION_LIST', '幫會衝榜');
define('MAIL_UNION_LIST_CONTENT_HEAD', '恭喜你在幫會衝榜中獲得第');
define('MAIL_UNION_LIST_CONTENT_FOOT', '名,請收下幫會衝榜獎勵');
define('MAIL_CHECKPOINT_LIST', '關卡衝榜');
define('MAIL_CHECKPOINT_LIST_CONTENT_HEAD', '恭喜你在關卡衝榜中獲得第');
define('MAIL_CHECKPOINT_LIST_CONTENT_FOOT', '名,請收下關卡衝榜獎勵');
define('MAIL_FORCES_LIST', '勢力衝榜');
define('MAIL_FORCES_LIST_CONTENT_HEAD', '恭喜你在勢力衝榜中獲得第');
define('MAIL_FORCES_LIST_CONTENT_FOOT', '名,請收下勢力衝榜獎勵');
define('MAIL_CLOSE_LIST', '親密衝榜');
define('MAIL_CLOSE_LIST_CONTENT_HEAD', '恭喜你在親密衝榜中獲得第');
define('MAIL_CLOSE_LIST_CONTENT_FOOT', '名,請收下親密衝榜獎勵');
define('MAIL_GOVERN_LIST', '衙門衝榜');
define('MAIL_GOVERN_LIST_CONTENT_HEAD', '恭喜你在衙門衝榜中獲得第');
define('MAIL_GOVERN_LIST_CONTENT_FOOT', '名,請收下衙門衝榜獎勵');
define('MAIL_RECHANGE', '儲值到賬通知');//儲值通知
define('MAIL_RECHANGE_CONTENT_HEAD', '您儲值的');
define('MAIL_RECHANGE_YUEKA', '月卡');//儲值通知
define('MAIL_RECHANGE_NIANKA', '年卡');//儲值通知
define('MAIL_RECHANGE_CONTENT_YUEKA', '您購買的月卡和');
define('MAIL_RECHANGE_CONTENT_NIANKA', '您購買的年卡和');
define('MAIL_RECHANGE_CONTENT_FOOT', '元寶已到賬');
define('MAIL_RECHANGE_EXTRA', '儲值額外獎勵');//儲值額外獎勵通知
define('MAIL_RECHANGE_EXTRA_CONTENT', '以下是你單檔首次儲值獲得的獎勵');
define('MAIL_YINLIANG_LIST', '銀兩衝榜');
define('MAIL_YINLIANG_LIST_CONTENT_HEAD', '恭喜你在銀兩衝榜中獲得第');
define('MAIL_YINLIANG_LIST_CONTENT_FOOT', '名,請收下銀兩衝榜獎勵');
define('MAIL_JIULOU_LIST', '酒樓衝榜');
define('MAIL_JIULOU_LIST_CONTENT_HEAD', '恭喜你在酒樓衝榜中獲得第');
define('MAIL_JIULOU_LIST_CONTENT_FOOT', '名,請收下酒樓衝榜獎勵');
define('MAIL_SHIBING_LIST', '士兵衝榜');
define('MAIL_SHIBING_LIST_CONTENT_HEAD', '恭喜你在士兵衝榜中獲得第');
define('MAIL_SHIBING_LIST_CONTENT_FOOT', '名,請收下士兵衝榜獎勵');


//用戶
define('USER_CASH_SHORT', '元寶不足');
define('USER_ITEMS_SHORT', '道具不足');
define('USER_ITEMS_NUM_ERROR', '道具數量有誤');
define('USER_GOVERNMENT_SHORT', '政績不足');
define('USER_POSITION_UP', '引導出錯');//升官時報錯
define('USER_ACCOUNT_NO_EXIT', '玩家編號不存在');
define('USER_COUNT_SHORT_NAME', '長度要在2~8個字符之間');
define('USER_CREATE_SUCCESS', '創建成功');
define('USER_ID_ERROR', '玩家ID錯誤');

//通用
define('PARAMS_ERROR', '參數錯誤');
define('STATUS_ERROR', '狀態錯誤');

//系統
define('SYSTEM_VERSION_LOWER', '版本過期sevid_null');
define('SYSTEM_FREEZE_UID', '您已被封號了');
define('SYSTEM_FREEZE_OPENID', '您的設備號已被查封');
define('SYSTEM_NO_KEFU', '暫時沒有客服');

//系統
define('ACT_HD_RANK_NO_EXISTS', '榜單不存在');

//翰林院--新
define('LEVEL_LIMIT_SIX', '官品達到從六品開啟');
define('LEVEL_LIMIT_FIVE', '官品達到從五品開啟');
define('DESK_HAVE_PEOPEL', '座位有人了');
define('DESK_WRONG', "位置錯誤");
define('HANLIN_XIUXI', "休息中");
define('HANLIN_BUCUNZAI', "不存在");
define('HANLIN_PROTECT', "被保護中,不能挑戰");
define('HANLIN_DESK_CHANGE', "位置信息已經改變");
define('HANLIN_NO_PEOPEL', "位置上沒人");
define('HANLIN_JIAOLIANG', "剛剛跟這個人較量過");
define('HANLIN_SELF', "不能T自己");

define('RANKING_WRONG', "榜單異常");
//活動
define('ACT_HD_RANK_NO_EXISTS', '榜單不存在');
define('ACT_HD_ADD_SCORE_NO_INT', '添加的積分不是整數');
define('ACT_HD_NO_ACT_ITEM', '不是活動道具');
define('ACT_HD_INFO_ERROR', '活動信息有誤');
define('ACT_HD_MISS_CONFIG_FILE', '缺少配置文件');
define('ACT_HD_PLAY_CD', '戰鬥冷卻中');
define('ACT_HD_ADD_ATTRIBUTE_NO_FUND', '加成的檔次不存在');
define('ACT_HD_ALL_KILL', '已全部獵殺');
define('ACT_HD_GIVE_ATTRIBUTE_ERROR', '領取的檔次有問題');
define('ACT_HD_GIVE_MAX', '已領取到最高檔次了');
define('ACT_HD_TOTAL_SCORE_IS_SHORT', '總積分還不夠呀');
define('ACT_HD_REWARD_CONFIG_ERROR', '獎勵配置不能為空');
define('ACT_HD_LEAST_ONE_HERO', '至少擁有一個門客');
define('ACT_HD_LEAST_ONE_WIFE', '至少擁有一個紅顏');
define('ACT_HD_RAND_CHARM_ERROR', '隨機魅力有問題');
define('ACT_HD_CUSTOM_ERROR', '要打的關卡數有誤');
define('ACT_HD_PLAY_CUSTOM_ERROR', '已打的關卡數大於要打的關卡數');
define('ACT_HD_NO_FUND_OPPONENT', '沒有找到要打的對手');
define('ACT_HD_ALL_KILL_OPPONENT', '你已經把敵軍圍剿完畢');

//絲綢之路
define('TRADE_OPEN_LIMIT', '官品達到亲王十级開啟');
define('TRADE_NOT_OPEN_ROOT_PLAY', '您還未開啟一鍵絲綢之路');
define('TRADE_NOT_REACH_PLACE_ERROR', '要到達的目標有誤');
define('TRADE_NOT_EXCEED_REACH_PLACE', '已超過你要到達的目標地點');
define('TRADE_NOT_ROUND_WORLD_WEEK', '您已繞世界一周啦');

//亂黨
define('TAOFA_OPEN_LIMIT', '官品達到從五品開啟');

//聊天
define('CHAT_OPEN_LIMIT', '發言條件：官品達到');
define('CHAT_BLACKLIST_ADD_USER_ERROR', '要加入的黑名單的人物有問題');
define('CHAT_BLACKLIST_IN_UID', '他已經在你的黑名單內');
define('CHAT_BLACKLIST_IS_EMPTY', '你的黑名單為空');
define('CHAT_BLACKLIST_NOFUND_UID', '他不在你的黑名單內');
define('CHAT_SPACE_TIMES_LIMIT', '您的手速太快啦');

//官品
define('USER_LEVEL_0_NAME', '貧民');
define('USER_LEVEL_1_NAME', '從九品');
define('USER_LEVEL_2_NAME', '亲王二级');
define('USER_LEVEL_3_NAME', '從八品');
define('USER_LEVEL_4_NAME', '亲王四级');
define('USER_LEVEL_5_NAME', '從七品');
define('USER_LEVEL_6_NAME', '亲王六级');
define('USER_LEVEL_7_NAME', '從六品');
define('USER_LEVEL_8_NAME', '亲王八级');
define('USER_LEVEL_9_NAME', '從五品');
define('USER_LEVEL_10_NAME', '亲王十级');
define('USER_LEVEL_11_NAME', '從四品');
define('USER_LEVEL_12_NAME', '亲王十二级');
define('USER_LEVEL_13_NAME', '從三品');
define('USER_LEVEL_14_NAME', '亲王十四级');
define('USER_LEVEL_15_NAME', '從二品');
define('USER_LEVEL_16_NAME', '亲王十六级');
define('USER_LEVEL_17_NAME', '從一品');
define('USER_LEVEL_18_NAME', '亲王十八级');

//嚴刑逼供
define('HD_TYPE8_TIME_LIMIT', '這段時間不能打哦');
define('HD_TYPE8_USE_ITEM_ERROR', '使用的道具有誤');
define('HD_TYPE8_KILL_END', '已被打死');
define('HD_TYPE8_DONT_SHOPING', '商品不能購買');
define('HD_TYPE8_SHOP_NO_FUND', '購買的商品不存在');
define('HD_TYPE8_EXCEED_LIMIT', '超過限購次數');
define('HD_TYPE8_EXCHANGE_NO_FUND', '兌換的商品不存在');
define('HD_TYPE8_DONT_LINGQU', 'boss未死不能領取');
define('HD_TYPE8_HAVE_LINGQU', '您已領取過獎勵');


//ACTMODEL
define('ACT_112_MUBIAOWRONG', '要到達的目標地點有誤');
define('ACT_112_MUBIAOCHAOGUO', '已超過你要到達的目標地點');
define('ACT_14_CONFIGWRONG', '配置錯誤');
define('ACT_1_HASCOUNT', '還有次數');
define('ACT_20_LIMITVALUE', '名望值已達到上限!');
define('ACT_2_BUZU', '處理政務次數不足!');
define('ACT_30_BUKELING', '獎勵不可領取!');
define('ACT_30_YILING', '獎勵已領取!');
define('ACT_34_MINGAN', '存在敏感字段!');
define('ACT_34_BUKESHEZHI', '不能設置當前王爺!');
define('ACT_36_LINGWAN', '已經領完');
define('ACT_39_FAILUER', '請求失敗');
define('ACT_58_XIWU', '已經在習武中');
define('ACT_58_NOROOM', '不在房間裡');
define('ACT_58_BUZU', '經驗不足');
define('ACT_61_JIACHENG', '加成類型異常');
define('ACT_61_IDWRONG', 'ID錯誤');
define('ACT_61_WEIZHIDAOJU', '未知道具');
define('ACT_61_WEIZHILEIXING', '未知類型');
define('ACT_96_DUIHUAN', '你已使用過該類型兌換碼');
define('ACT_98_JINYAN', '禁言內容為空');
define('ACT_9_STATUS', '狀態錯誤');

define('DASUANMOUFAN', '你打算謀反?');

define('SEV_23_UIDNONULL', 'uid不能為空');
define('SEV_26_IDNONULL', '用戶id不能為空');
define('SEV_51_MENKECHUZHAN', '請先派遣門客出戰!');
define('SEV_51_JINNANG', '錦囊只能使用一個');
define('SEV_51_DAOJUCUO', '道具使用錯誤!');
define('SEV_54_XITONGMANG', '系統繁忙!');
define('SEV_9_ROOMOPEN', '房間已開啟');
define('SEV_9_ROOMDOWN', '房間已關閉');

define('SERVER_NO_OPEN', '服務器未開啟!');
define('SERVER_WEIHU', '服務器維護中');
define('JSON_CANSHU_CUOWU', '參數_JSON_錯誤_');
define('LOGIN_GUOQI', '登入過期，請重新登入');
define('LOGIN_YIDIDENGLU', '在其他地方登入了');
define('GONGNENG_NO_OPEN', '功能未開啟');
