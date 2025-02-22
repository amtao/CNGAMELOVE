#!/usr/bin/env python
# -*- coding:UTF-8 -*-
import sys 
import os
import os.path
import shutil
import time,datetime
import stat
import subprocess
import zipfile
import filecmp
import paramiko

import utils
######mypath
##targetDir = path+'/../res'
path = os.path.dirname(os.path.realpath(__file__))


################改变文件内容
def _changeAllfiles(wdPath,txtSerlist,txtPf,txtBysdk,txtApipath,txtVersion):
	# 修改Config.js中serverlist路径
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'serverList:',txtSerlist)
	print(u'修改serverlist路径--------成功！')
	# 修改Config.js中pf路径
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'pf:',txtPf)
	print(u'修改pf路径----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'login_by_sdk:',txtBysdk)
	print(u'修改login_by_sdk状态------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'version:',txtVersion)
	print(u'修改版本号----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'DEBUG:','DEBUG: 0,')
	print(u'修改DEBUG----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'apiPath:',txtApipath)
	print(u'修改apiPath----------------成功！')

#######打包web-mobile版本
def _packNewWebMobile(wdPath):
	os.system(utils.getConfig(path+'/../')['create_path']+" --path "+wdPath+" --build platform=web-mobile")

##############0u打包宫廷秘传H5测试无sdk版
def _packNewWeiDuan(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion,serverPath):
	wdPath = utils.getConfig(path+'/../')['trunk_path']
	utils._updateSvn(wdPath+'/assets/')

	_changeAllfiles(wdPath,txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion)

	_packNewWebMobile(wdPath)

	buildPath = wdPath+"/build/web-mobile/"

	print(u'发布成功，等待打包...')

	if serverPath:
		utils._zipDir(buildPath,buildPath+"/web-mobile.zip")
		print(u'打包成功！')
		print(u'正在上传压缩包，可能用时长，请耐心等待。。。。。。')

		utils._uploadFileToServer(buildPath+"/web-mobile.zip",serverPath)