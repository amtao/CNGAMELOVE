#!/usr/bin/env python
# -*- coding:UTF-8 -*-
import sys 
import os
import os.path
import shutil
import time,datetime
import stat

import utils
# import log

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
	log.green('开始复制文件到temp目录.....................')
	tempPath = wdPath+"/build/temp"
	utils._removeDir(tempPath)

	jsbDir = wdPath+'/build/jsb-link'
	log.green('正在复制res文件到temp目录')
	shutil.copytree(jsbDir+"/res",tempPath+"/res")
	log.green('正在复制src目录到temp目录')
	shutil.copytree(jsbDir+'/src',tempPath+'/src')

	log.green('正在复制project.manifest文件到temp目录')
	sourceFile = wdPath+'/assets/project.manifest'
	targetFile = tempPath+"/project.manifest"
	shutil.copy2( sourceFile, targetFile)

	log.green('正在复制version.manifest文件到temp目录')
	sourceFile = wdPath+'/assets/version.manifest'
	targetFile = tempPath+"/version.manifest"
	shutil.copy2( sourceFile, targetFile)

######################移动到obb目录#######################
def _moveToObbDir(sourceDir,name):
	wdPath = utils.getConfig(path+'/../')['android_path']
	obbPath = wdPath+'/build/temp_obb/raw-assets/'

	log.green('移动['+sourceDir+']到obb目录')
	utils._copyDirToDir2(sourceDir,obbPath+name)

####################删除目录###############################
def _removeApkDir2(name,includeObj,targetDir,obb):
	flag = False
	for f in includeObj:
		if f == name:
			flag = True

	if flag == False:
		# obb需要把资源移动到另一个目录
		if obb:
			_moveToObbDir(targetDir,name)

		utils._removeDir(targetDir)
		utils._createFile(targetDir)
		shutil.rmtree(targetDir,True)
		log.green('删除['+name+']目录成功.....................')
		return True

	return False
			
####################获取目录的大小
def _getDirSize(rootDir):
	size = 0
	for name in os.listdir(rootDir):
		targetFile = os.path.join(rootDir,name)
		if os.path.isfile(targetFile):
			size = size + os.path.getsize(targetFile)

	return size;

def _removeApkDir(wdPath,channelId,obb = False):
	includeObj=[
	"02",
	"ae",
	"63",
	"cc",
	"23",
	"cd",
	"97",
	"7d",
	"31",
	"dc",
    "d0",
    "68",
    "67",
    "2a",
    "9e"
	]

	rootDir = wdPath + '/build/jsb-link/res/raw-assets/'

	totalSize = 0
	maxSize = 0
	if channelId == 1:
		maxSize = 50 * 1024 * 1024
	elif channelId == 2:
		maxSize = 300 * 1024 * 1024
	elif channelId == 3:
		maxSize = 50 *1024 * 1024

	####计算资源目录总大小
	for name in os.listdir(rootDir):
		targetDir = os.path.join(rootDir,name)
		if os.path.isdir(targetDir):
			totalSize = totalSize + _getDirSize(targetDir)
			# _removeApkDir2(name,includeObj,targetDir)
		else:
			log.red('请确认传的路径是正确的资源路径！')

	if obb:
		obbPath = wdPath+'/build/temp_obb/raw-assets/'
		utils._createFile(obbPath)
		utils._removeDir(obbPath)

		# print(u'正在复制project.manifest文件到temp目录')
		# sourceFile = wdPath+'/assets/project.manifest'
		# targetFile = obbPath+"/project.manifest"
		# shutil.copy2( sourceFile, targetFile)
	####删除目录，使得资源目录总大小小于maxSize
	for name in os.listdir(rootDir):
		if totalSize <= maxSize:
			break
		targetDir = os.path.join(rootDir,name)
		if os.path.isdir(targetDir):
			dirSize = _getDirSize(targetDir)
			if _removeApkDir2(name,includeObj,targetDir,obb):
				totalSize = totalSize - dirSize

	log.green('目录删除成功！')

##########################渠道文件修改###########################
def _changeApkChannelFile(channelId,txtCode):
	channelDir = utils.getConfig(path+'/../')['android_path']+'/build/jsb-channels'
	googleChannelDir = channelDir+'/google'
	onestoreChannelDir = channelDir+'/onestore'
	sgpChannelDir = channelDir+'/sgp'
	sourceXianyuFile = ''
	targetXianyuFile = utils.getConfig(path+'/../')['android_path']+'/build/jsb-link/frameworks/runtime-src/proj.android-studio/app/src/main/assets/developer_xianyu.properties'
	sourceGoogleFile = ''
	targetGoogleFile = utils.getConfig(path+'/../')['android_path']+'/build/jsb-link/frameworks/runtime-src/proj.android-studio/app/google-services.json'
	sourceGradleFile = ''
	targetGradleFile = utils.getConfig(path+'/../')['android_path']+'/build/jsb-link/frameworks/runtime-src/proj.android-studio/gradle.properties'
	sourceStringFile = ''
	targetStringFile = utils.getConfig(path+'/../')['android_path']+'/build/jsb-link/frameworks/runtime-src/proj.android-studio/app/res/values/strings.xml'
	###googleplay
	if channelId == 1:
		sourceXianyuFile = googleChannelDir+'/developer_xianyu.properties'
		sourceGoogleFile = googleChannelDir+'/google-services.json'
		sourceGradleFile = googleChannelDir+'/gradle.properties'
		sourceStringFile = googleChannelDir+'/strings.xml'
	elif channelId == 2:
		sourceXianyuFile = onestoreChannelDir+'/developer_xianyu.properties'
		sourceGoogleFile = onestoreChannelDir+'/google-services.json'
		sourceGradleFile = onestoreChannelDir+'/gradle.properties'
		sourceStringFile = onestoreChannelDir+'/strings.xml'
	elif channelId == 3:
		sourceXianyuFile = sgpChannelDir+'/developer_xianyu.properties'
		sourceGoogleFile = sgpChannelDir+'/google-services.json'
		sourceGradleFile = sgpChannelDir+'/gradle.properties'
		sourceStringFile = sgpChannelDir+'/strings.xml'

	shutil.copy2(sourceXianyuFile,targetXianyuFile)
	log.green('复制developer_xianyu.properties配置文件到工程目录')
	shutil.copy2(sourceGoogleFile,targetGoogleFile)
	log.green('复制google-services.json配置文件到工程目录')
	shutil.copy2(sourceGradleFile,targetGradleFile)
	log.green('复制gradle.properties配置文件到工程目录')
	###修改gradle.properties文件的版本号为配置的版本号
	utils._changeFile(targetGradleFile,'VERSION_CODE','VERSION_CODE='+txtCode)
	log.green('修改apk版本(main.js)----------------成功！')
	shutil.copy2(sourceStringFile,targetStringFile)
	log.green('复制strings.xml配置文件到工程目录')

##################打apk包
def _buildApk(wdPath,channel):
	log.green('开始打apk包')

	os.chdir(wdPath+'/build/jsb-link/frameworks/runtime-src/proj.android-studio/')

	if channel == 1:
		cmd = "./gradlew assembleAdRelease"
	elif channel == 2:
		cmd = "./gradlew assembleOneRelease"
	elif channel == 3:
		cmd = "./gradlew assembleSgpRelease"
	os.system(cmd)
#################打包android版本
def _packApk(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion,txtChannel,txtCode,txtApkType):
	wdPath = utils.getConfig(path+'/../')['android_path']

	copyDir = wdPath+"/build-templates/android/"
	targetDir = wdPath+"/build-templates/jsb-link/"

	utils._removeDir(targetDir)

	utils._copyDirToDir(copyDir,targetDir)
	
	nChannel = txtChannel

	gVersion = 'window.g_version = '+txtCode+';'

	nLevel = txtApkType

	_packAndroid2(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion)

	# _buildApk(wdPath,nChannel)
	utils._changeFile(os.path.join(wdPath+'/build/jsb-link/','main.js'),'window.g_version',gVersion)
	log.green('修改apk版本(main.js)----------------成功！')
	if nChannel == 1:
		utils._changeFile(os.path.join(wdPath+'/build/jsb-link/','main.js'),'window.g_channel_id','window.g_channel_id = 15;')
		log.green('修改渠道号(main.js)----------------成功！')
	elif nChannel == 2:
		utils._changeFile(os.path.join(wdPath+'/build/jsb-link/','main.js'),'window.g_channel_id','window.g_channel_id = 15;')
		log.green('修改渠道号(main.js)----------------成功！')
	elif nChannel == 3:
		utils._changeFile(os.path.join(wdPath+'/build/jsb-link/','main.js'),'window.g_channel_id','window.g_channel_id = 16;')
		log.green('修改渠道号(main.js)----------------成功！')

	if nLevel == 1:
		_removeApkDir(wdPath,nChannel)

		log.green('开始apk小包资源生成manifest......')
		_genManifest(txtVersion)

		_changeApkChannelFile(nChannel,txtCode)

		_buildApk(wdPath,nChannel)
	elif nLevel == 2:
		log.green('开始apk整包资源生成manifest......')
		_genManifest(txtVersion)

		_changeApkChannelFile(nChannel,txtCode)

		_buildApk(wdPath,nChannel)
	elif nLevel == 3:
		log.green('开始apk(obb)整包资源生成manifest......')
		_genManifest(txtVersion)

		_removeApkDir(wdPath,nChannel,True)

		_changeApkChannelFile(nChannel,txtCode)

		_buildApk(wdPath,nChannel)

	# _copyUploadFilesToTemp(wdPath)

	# log.green('打包完成~~~~~~~~~~~~~~')

#######打包android原生版本
def _buildAndroid(wdPath):
	os.system(utils.getConfig(path+'/../')['create_path']+" --path "+wdPath+" --build platform=android;useDebugKeystore=false;keystorePath=/Users/panyou/.jenkins/workspace/HGGT/branches/gt_love/08_proj/foreigntools/androiddis/xhhdhkymg.jks;keystorePassword=JDnsd25ds4fsSD2dsd;keystoreAlias=xianyuhkymg.jks;keystoreAliasPassword=aF5S9s4fa25DnIluOb;appABIs=['armeabi-v7a','arm64-v8a'];startScene=a7ef1d43-c5e4-4ce2-9484-2050ea1f18e9;encryptJs=true;xxteaKey="+utils.getConfig(path+'/../')['js_key'])

#################生成manifest文件
def _genManifest(txtVersion):
	wdPath = utils.getConfig(path+'/../')['android_path']
	# ##修改version_generator文件中的版本号
	# utils._changeFile(os.path.join(wdPath+'/','version_generator.js'),'version:',txtVersion)
	# log.green('修改version_generator中的版本号--------成功！')
	
	os.chdir(wdPath)
	cmd = 'node version_generator.js --version '+txtVersion
	os.system(cmd)

	_copyManifest();

#################copy manifest文件
def _copyManifest():
	wdPath = utils.getConfig(path+'/../')['android_path']
	log.green('复制manifest到9e目录下的manifest文件......')
	sourceFile = wdPath+'/assets/project.manifest'
	targetFile = wdPath+"/build/jsb-link/res/raw-assets/9e/9e8775d3-725d-4872-8764-9dd9b3da40ae.manifest"
	shutil.copy2( sourceFile, targetFile)

#################打包android版本
def _packAndroid2(txtServerlist,txtPf,txtBysdk,txtApipath,txtVersion):
	wdPath = utils.getConfig(path+'/../')['android_path']

	_changeAllfiles(wdPath,txtServerlist,txtPf,txtBysdk,txtApipath,'version: "'+txtVersion+'",')

	_buildAndroid(wdPath)

	log.green('开始第一次生成manifest......')

	_genManifest(txtVersion)

	########执行第二次生成manifest
	log.green('开始第二次生成manifest......')
	os.chdir(wdPath)
	cmd = 'node version_generator.js --version '+txtVersion
	os.system(cmd)
