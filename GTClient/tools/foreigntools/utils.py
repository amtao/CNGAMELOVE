#!/usr/bin/env python
# -*- coding:UTF-8 -*-
import json
import sys 
import os
import os.path
import shutil
import time,datetime
import stat
import subprocess
import zipfile
import filecmp
# import paramiko

import log
######mypath
##targetDir = path+'/../res'
path = os.path.dirname(os.path.realpath(__file__))
print(u"我的路径是:%s" %path )
nowStep = ''

#####u压缩文件
def _zipDir(dirname,zipfilename):
    filelist = []
    if os.path.isfile(dirname):
        filelist.append(dirname)
    else :
        for root, dirs, files in os.walk(dirname):
            for name in files:
                filelist.append(os.path.join(root, name))
         
    zf = zipfile.ZipFile(zipfilename, "w", zipfile.zlib.DEFLATED)
    for tar in filelist:
        arcname = tar[len(dirname):]
        #print arcname
        zf.write(tar,arcname)
    zf.close()

def _createFile(targetDir):
	if os.path.exists(targetDir) == False:
		os.makedirs(targetDir)
def rmtree(top):
    for root, dirs, files in os.walk(top, topdown=False):
        for name in files:
            filename = os.path.join(root, name)
            os.chmod(filename, stat.S_IWUSR)
            os.remove(filename)
        for name in dirs:
            os.rmdir(os.path.join(root, name))
    os.rmdir(top)
def _removeDir(targetDir):
	_createFile(targetDir)
	for file in os.listdir(targetDir):
		targetFile = os.path.join(targetDir,  file)
		if os.path.isfile(targetFile):
			os.remove(targetFile)
		elif os.path.isdir(targetFile):
			rmtree(targetFile)

def _copyDirToDir(respath,dispath):
	_createFile(dispath)
	for file in os.listdir(respath):
		sourceFile = os.path.join(respath,  file)
		targetFile = os.path.join(dispath,  file)
		if os.path.isfile(sourceFile):
			shutil.copy2( sourceFile, targetFile)
		elif os.path.isdir(sourceFile):
			shutil.copytree(sourceFile, targetFile)
#######更新svn
def _updateSvn(uppath):
	#svn update命令
	svnPath = getConfig(path)['tortoise_path']
	cmd = svnPath+'/TortoiseProc.exe /command:update /path:'+uppath+' /closeonend:3'
	result = os.system(cmd)

	if result == 0:
		print(u'svn更新------------------成功！')
	else:
		print(u'svn更新------------------失败！')

##############修改地址
def _changeFile(fpath,a,b):
	lines = []
	with open(fpath,'r',encoding='UTF-8') as f:
		for line in f.readlines():
			if line != '\n':
				lines.append(line)
		f.close()

	with open(fpath,'w',encoding='UTF-8') as f:
		for line in lines:
			newline = line.replace("\t","").lstrip()
			if a in newline and newline[0] != "/":
				newline = "\t"+b
				f.write('%s\n' %newline)
			else:
				f.write('%s' %line)

##############解压缩文件
def _unzipServerFile(serverPath):
	client = paramiko.SSHClient()
	client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
	client.connect(hostname='118.89.163.69', port=22, username='root', password='jB7r2HEh%TUa%qWZ%h#!')
	stdin, stdout, stderr = client.exec_command('unzip -o -d '+serverPath+' '+serverPath+'web-mobile.zip')
	print(stdout.read().decode('utf-8'))
	print(u'解压缩成功')
	client.close()


##############上传文件到指定目录
def _uploadFileToServer(file,serverPath):
	transport = paramiko.Transport(('118.89.163.69', 22))
	transport.connect(username='root', password='jB7r2HEh%TUa%qWZ%h#!')
	sftp = paramiko.SFTPClient.from_transport(transport)
	sftp.put(file, serverPath+'web-mobile.zip')
	transport.close()
	print(u'上传成功！')
	_unzipServerFile(serverPath)

#############移动资源到tempres目录
def _movResToTempRes(buildPath):
	tempPath = buildPath+"/../tempPath"
	_removeDir(tempPath)

	print(u'正在移动res文件夹到tempPath目录，请稍后.......')
	shutil.copytree(buildPath+"/res",tempPath+"/res")

	_removeDir(buildPath+"/res")

	_createFile(buildPath+"/res")
	shutil.rmtree(buildPath+"/res",True)
	print(u'删除res文件夹成功！！！')

def _copyDirToDir2(respath,dispath):
	_createFile(dispath)
	for file in os.listdir(respath):
		sourceFile = os.path.join(respath,  file)
		targetFile = os.path.join(dispath,  file)
		if os.path.isfile(sourceFile):
			shutil.copy2( sourceFile, targetFile)
		elif os.path.isdir(sourceFile):
			_copyDirToDir2(sourceFile,targetFile)

#########获取配置中的工程路径
def getConfig(path):
	f=open(path+'/config.json','r',encoding='utf-8');
	config=json.load(f);
	f.close
	return config

#####################上传文件到存储桶start#####################
# print("\033[31m资源版本["+version+"]已经在cos上，请检查打包版本号！！！\033[0m") 红色
# 前景色 30:黑色 31:红色 32:绿色 33:黄色 34:蓝色 35:紫红色 36:青蓝色 37:白色
# 背景色 40:黑色 41:红色 42:绿色 43:黄色 44:蓝色 45:紫红色 46:青蓝色 47:白色
def isVersionExist(config,path,version,bucketName,region):
	##设置存储桶区域
	cmd = 'coscmd config -a '+config['cos_secret_id']+' -s '+config['cos_secret_key']+' -b '+bucketName+' -r '+region;
	result = os.system(cmd)
	if result == 0:
		print(u'存储桶区域设置------------------成功！')
	else:
		print(u'存储桶区域设置------------------失败！')
		return;

	##检查当前版本是否存在
	cmd = 'coscmd info '+version+'/project.manifest'
	result = os.popen(cmd)
	info = result.readlines()
	lines = []
	for line in info:
		line = line.strip('\r\n')
		lines.append(line)

	##当前版本已存在，不能上传
	if len(lines) > 0:
		log.red("资源版本["+version+"]已经在cos上，请检查打包版本号！！！")
		return True

	return False

def uploadDirToCos(config,path,version,bucketName,region):
	##设置存储桶区域
	cmd = 'coscmd config -a '+config['cos_secret_id']+' -s '+config['cos_secret_key']+' -b '+bucketName+' -r '+region;
	result = os.system(cmd)
	if result == 0:
		log.green('存储桶区域设置------------------成功！')
	else:
		log.red('存储桶区域设置------------------失败！')
		return;

	##检查当前版本是否存在
	cmd = 'coscmd info '+version+'/project.manifest'
	result = os.popen(cmd)
	info = result.readlines()
	lines = []
	for line in info:
		line = line.strip('\r\n')
		lines.append(line)

	##当前版本已存在，不能上传
	if len(lines) > 0:
		log.red("资源版本["+version+"]已经在cos上，请检查打包版本号！！！")
	else:
		cmd = 'coscmd upload -r '+path+' '+version+'/'
		result = os.system(cmd)
		if result == 0:
			log.green('资源版本'+version+'上传------------------成功！')
		else:
			log.red('资源版本'+version+'上传------------------失败！')

#####################上传文件到存储桶end#####################