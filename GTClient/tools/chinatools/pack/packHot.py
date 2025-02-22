#!/usr/bin/env python
# -*- coding:UTF-8 -*-
import sys 
import os
import os.path
import shutil
import time,datetime
import stat

import utils
import log

from packEnum import SysEnum,ChannelEnum

######mypath
##targetDir = path+'/../res'
path = os.path.dirname(os.path.realpath(__file__))
nowStep = ''


################改变文件内容
def _changeAllfiles(wdPath,txtSerlist,txtPf,txtBysdk,txtApipath,txtVersion):
	# 修改Config.js中serverlist路径
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'serverList:',txtSerlist)
	log.green('修改serverlist路径--------成功！')
	# 修改Config.js中pf路径
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'pf:',txtPf)
	log.green('修改pf路径----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'login_by_sdk:',txtBysdk)
	log.green('修改login_by_sdk状态------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'version:',txtVersion)
	log.green('修改版本号----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'DEBUG:','DEBUG: 0,')
	log.green('修改DEBUG----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'apiPath:',txtApipath)
	log.green('修改apiPath----------------成功！')
	utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'newlang:','newlang: "zh-ch",')
	log.green('修改newlang----------------成功！')
#################复制需要上传cdn的资源代码到temp目录
def _copyUploadFilesToTemp(wdPath):
	print(u'开始复制文件到temp目录.....................')
	tempPath = wdPath+"/build/temp"
	utils._removeDir(tempPath)

	jsbDir = wdPath+'/build/jsb-link'
	print(u'正在复制res文件到temp目录')
	shutil.copytree(jsbDir+"/res",tempPath+"/res")
	print(u'正在复制src目录到temp目录')
	shutil.copytree(jsbDir+'/src',tempPath+'/src')

	print(u'正在复制project.manifest文件到temp目录')
	sourceFile = wdPath+'/assets/project.manifest'
	targetFile = tempPath+"/project.manifest"
	shutil.copy2( sourceFile, targetFile)

	print(u'正在复制version.manifest文件到temp目录')
	sourceFile = wdPath+'/assets/version.manifest'
	targetFile = tempPath+"/version.manifest"
	shutil.copy2( sourceFile, targetFile)

def _packIos(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion,txtCrypt,txtVerify,txtCode):
	wdPath = utils.getConfig(path+'/../')['android_path']

	_changeAllfiles(wdPath,txtServerlist,txtPf,txtBysdk,txtApipath,'version: "'+txtVersion+'",')

	# utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'CRYPTOJSKEY:',txtCrypt)
	# log.green('修改CRYPTOJSKEY----------------成功！')
	# utils._changeFile(os.path.join(wdPath+'/assets/scripts/app/','Config.js'),'isVerify:',txtVerify)
	# log.green('修改提审状态----------------成功！')

	gVersion = 'window.g_version = '+txtCode+';'
	utils._changeFile(os.path.join(wdPath+'/build-templates/jsb-link/','main.js'),'window.g_version',gVersion)

	xygVer = 'window.xygVer = '+txtCode+';'
	utils._changeFile(os.path.join(wdPath+'/build-templates/jsb-link/','main.js'),'window.xygVer',xygVer)
	
	log.green('修改apk版本(main.js)----------------成功！')

	_buildIos(wdPath)

	log.green('开始第一次生成manifest......')

	_genManifest(txtVersion,wdPath)

	########执行第二次生成manifest
	log.green('开始第二次生成manifest......')
	os.chdir(wdPath)
	cmd = 'node version_generator.js --version '+txtVersion
	os.system(cmd)


#################打包android版本
def _packHot(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion,channel,isUpload):
	config = utils.getConfig(path+'/../')
	wdPath = config['android_path']

	##存储桶数据
	bucketName = ""
	region = ""
	if channel == ChannelEnum.Develop:
		bucketName = config['cos_bucket_name_develop']
		region = config['cos_region_develop']
	elif channel == ChannelEnum.Online:
		bucketName = config['cos_bucket_name_online']
		region = config['cos_region_online']

	if utils.isVersionExist(config,wdPath+"/build/temp/",txtVersion,bucketName,region):
		return

	copyDir = wdPath+"/build-templates/android/"
	targetDir = wdPath+"/build-templates/jsb-link/"

	utils._removeDir(targetDir)

	utils._copyDirToDir(copyDir,targetDir)
	
	_packAndroid2(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion)

	_copyUploadFilesToTemp(wdPath)

	log.green('打包完成！！')

	if isUpload == 1:
		log.green('等待上传cos！！')
		utils.uploadDirToCos(config,wdPath+"/build/temp/",txtVersion,bucketName,region)

#################打包android版本
def _packIosBegin(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion,txtCode):
	wdPath = utils.getConfig(path+'/../')['android_path']

	copyDir = wdPath+"/build-templates/ios/"
	targetDir = wdPath+"/build-templates/jsb-link/"

	utils._removeDir(targetDir)

	utils._copyDirToDir(copyDir,targetDir)
	
	_packIos(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion,'CRYPTOJSKEY: "'+utils.getConfig(path+'/../')['ios_cryptkey']+'",','isVerify: true,',txtCode)

	_copyUploadFilesToTemp(wdPath)

	log.green('打包完成~~~~~~~~~~~~~~')

#######打包android原生版本
def _buildAndroid(wdPath):
	os.system(utils.getConfig(path+'/../')['create_path']+" --path "+wdPath+" --build platform=android;appABIs=['armeabi-v7a','arm64-v8a'];startScene=a7ef1d43-c5e4-4ce2-9484-2050ea1f18e9;encryptJs=true;xxteaKey="+utils.getConfig(path+'/../')['js_key'])

#######打包ios原生版本
def _buildIos(wdPath):
	os.system(utils.getConfig(path+'/../')['create_path']+" --path "+wdPath+" --build platform=ios;startScene=a7ef1d43-c5e4-4ce2-9484-2050ea1f18e9;encryptJs=true;xxteaKey="+utils.getConfig(path+'/../')['js_key'])
#################生成manifest文件
def _genManifest(txtVersion,wdPath):
	# ##修改version_generator文件中的版本号
	# utils._changeFile(os.path.join(wdPath+'/','version_generator.js'),'version:',txtVersion)
	# print(u'修改version_generator中的版本号--------成功！')
	
	os.chdir(wdPath)
	cmd = 'node version_generator.js --version '+txtVersion
	os.system(cmd)

	_copyManifest(wdPath);

#################copy manifest文件
def _copyManifest(wdPath):
	print(u'复制manifest到9e目录下的manifest文件......')
	sourceFile = wdPath+'/assets/project.manifest'
	targetFile = wdPath+"/build/jsb-link/res/raw-assets/9e/9e8775d3-725d-4872-8764-9dd9b3da40ae.manifest"
	shutil.copy2( sourceFile, targetFile)

#################打包android版本
def _packAndroid2(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion):
	wdPath = utils.getConfig(path+'/../')['android_path']

	_changeAllfiles(wdPath,txtServerlist,txtPf,txtBysdk,txtApipath,'version: "'+txtVersion+'",')

	_buildAndroid(wdPath)

	log.green('开始第一次生成manifest......')

	_genManifest(txtVersion,wdPath)

	########执行第二次生成manifest
	log.green('开始第二次生成manifest......')
	os.chdir(wdPath)
	cmd = 'node version_generator.js --version '+txtVersion
	os.system(cmd)
