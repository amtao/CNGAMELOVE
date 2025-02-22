# -*- coding:UTF-8 -*-
from enum import Enum

class SysEnum(Enum):
	#网页测试版
    web = 1
    #热更
    hot = 2
    #apk包
    pack = 3
    #ios包
    packios = 4

class ChannelEnum(Enum):
	#发布服
	Develop = 1
	#正式服/灰度服
	Online = 2
		