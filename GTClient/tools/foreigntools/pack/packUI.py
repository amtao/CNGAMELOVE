#!/usr/bin/python
import threading
import json
import sys
import os
import shutil
import importlib
import collections
# import tkinter as tk
# import tkinter.messagebox as tkmsg
import utils
import webbrowser
# importlib.reload(sys)
path = os.path.dirname(os.path.realpath(__file__))
sys.path.append(path+'/pack')
import packHot,packApk
from packEnum import SysEnum,ChannelEnum
class PackUI:
	packBtn = None

	# def __init__(self,frame):
	# 	self.wframe = frame
	############################ UI ##############################
	def createUI(self,state,channel=ChannelEnum.Develop,area="hk"):
		# for widget in self.wframe.winfo_children():
		# 	widget.destroy()

		self.state = state

		self.channel = channel
		# 区域代码
		self.area = area

		title = ""
		#服务器
		serverList = ""
		#平台
		pf = ""
		#是否使用sdk登录
		login_by_sdk = ""
		#apiPath
		apiPath = ""
		#版本
		version = ""

		config = utils.getConfig(path+'/../')

		if state == SysEnum.web:
			title = "网页测试包(预览地址：http://118.89.163.69:84/hkymg/)"
			dirPath = config['trunk_path']
			serverList = config['webtest_path'] + "serverlist.php"
			pf = "local"
			login_by_sdk = "0"
			self.lBysdk = 'login_by_sdk: 0,'
			apiPath = config['webtest_path']+"version.php"
			version = "1.0.0"

			self.lVersion = 'version: "'+version+'",'
		elif state == SysEnum.hot:
			dirPath = config['android_path']
			pf = "xianyu"
			login_by_sdk = "!0"
			self.lBysdk = 'login_by_sdk: !0,'
			if channel == ChannelEnum.Develop:
				title = self.area+"发布服热更包"
				serverList = config[self.area]['develop_path']+"serverlist.php"
				apiPath = config[self.area]['develop_path']+"version.php"
			elif channel == ChannelEnum.Online:
				title = self.area+"正式服热更包"
				serverList = config[self.area]['online_path']+"serverlist.php"
				apiPath = config[self.area]['online_path']+"version.php"
			version = "1.0.0"
		elif state == SysEnum.pack:
			dirPath = config['android_path']
			pf = "xianyu"
			login_by_sdk = "!0"
			self.lBysdk = 'login_by_sdk: !0,'
			if channel == ChannelEnum.Develop:
				title = self.area+"发布服apk包"
				serverList = config[self.area]['develop_path']+"serverlist.php"
				apiPath = config[self.area]['develop_path']+"version.php"
			elif channel == ChannelEnum.Online:
				title = self.area+"正式服apk包"
				serverList = config[self.area]['online_path']+"serverlist.php"
				apiPath = config[self.area]['online_path']+"version.php"
			version = "1.0.0"
		elif state == SysEnum.packios:
			dirPath = config['android_path']
			pf = "xianyu"
			login_by_sdk = "!0"
			self.lBysdk = 'login_by_sdk: !0,'
			title = self.area+"正式服ios包"
			serverList = config[self.area]['ios_online_path']+"serverlist.php"
			apiPath = config[self.area]['ios_online_path']+"version.php"
			version = "1.0.0"


		# textLabel = tk.Label(self.wframe,text = title,font = ('Arial 20 bold'))
		# textLabel.pack(fill = tk.X)

		# frame1 = tk.Frame(self.wframe,relief='raised')
		# frame1.pack(padx=5, pady=5)

		# #工程路径
		# dirLabel1 = tk.Label(frame1,text = "工程路径(assets的父路径):",font =('Arial 10'))
		# dirLabel1.grid(row=0,column=0)
		# dirLabel2 = tk.Label(frame1,text = dirPath,font = ('Arial 10 bold'))
		# dirLabel2.grid(row=0,column=1)

		# #服务器地址
		# serverLabel1 = tk.Label(frame1,text = "服务器地址:",font =('Arial 10'))
		# serverLabel1.grid(row=1,column=0)
		# serverLabel2 = tk.Label(frame1,text = serverList,font = ('Arial 10 bold'))
		# serverLabel2.grid(row=1,column=1)

		# #平台
		# pfLabel1 = tk.Label(frame1,text = "平台:",font =('Arial 10'))
		# pfLabel1.grid(row=2,column=0)
		# pfLabel2 = tk.Label(frame1,text = pf,font = ('Arial 10 bold'))
		# pfLabel2.grid(row=2,column=1)

		# sdkLabel1 = tk.Label(frame1,text = "使用sdk登录:",font =('Arial 10'))
		# sdkLabel1.grid(row=3,column=0)
		# sdkLabel2 = tk.Label(frame1,text = login_by_sdk,font = ('Arial 10 bold'))
		# sdkLabel2.grid(row=3,column=1)

		# apiLabel1 = tk.Label(frame1,text = "apiPath地址:",font =('Arial 10'))
		# apiLabel1.grid(row=4,column=0)
		# apiLabel2 = tk.Label(frame1,text = apiPath,font = ('Arial 10 bold'))
		# apiLabel2.grid(row=4,column=1)

		# if state == SysEnum.web:
		# 	verLabel1 = tk.Label(frame1,text = "版本号(格式:1.0.0):",font =('Arial 10'))
		# 	verLabel1.grid(row=5,column=0)
		# 	verLabel2 = tk.Label(frame1,text = version,font = ('Arial 10 bold'))
		# 	verLabel2.grid(row=5,column=1)
		# elif state == SysEnum.hot:
		# 	txtBanben = ""
		# 	if channel == ChannelEnum.Develop:
		# 		txtBanben = "版本号(格式:1.0.0.xxx):"
		# 	elif channel == ChannelEnum.Online:
		# 		txtBanben = "版本号(格式:1.0.6.xxx):"
		# 	verLabel1 = tk.Label(frame1,text = txtBanben,font =('Arial 10'))
		# 	verLabel1.grid(row=5,column=0)
		# 	self.verLabel2 = tk.Entry(frame1,font = ('Arial 10 bold'))
		# 	self.verLabel2.grid(row=5,column=1)

		# 	cosLabel1 = tk.Label(frame1,text = "是否上传COS:",font =('Arial 10'))
		# 	cosLabel1.grid(row=6,column=0)

		# 	self.cosType = tk.IntVar()
		# 	check = tk.Checkbutton(frame1, text="", variable=self.cosType)
		# 	check.deselect()
		# 	check.grid(row=6,column=1)

		# elif state == SysEnum.packios:
		# 	txtBanben = ""
		# 	if channel == ChannelEnum.Develop:
		# 		txtBanben = "版本号(格式:1.0.0.xxx):"
		# 	elif channel == ChannelEnum.Online:
		# 		txtBanben = "版本号(格式:1.0.6.xxx):"
		# 	verLabel1 = tk.Label(frame1,text = txtBanben,font =('Arial 10'))
		# 	verLabel1.grid(row=5,column=0)
		# 	self.verLabel2 = tk.Entry(frame1,font = ('Arial 10 bold'))
		# 	self.verLabel2.grid(row=5,column=1)

		# 	apkVerLabel1 = tk.Label(frame1,text = "apk versionCode(格式：数字):",font =('Arial 10'))
		# 	apkVerLabel1.grid(row=6,column=0)
		# 	self.apkVerLabel2 = tk.Entry(frame1,font = ('Arial 10 bold'))
		# 	self.apkVerLabel2.grid(row=6,column=1)

		# elif state == SysEnum.pack:
		# 	txtBanben = ""
		# 	if channel == ChannelEnum.Develop:
		# 		txtBanben = "版本号(格式:1.0.0.xxx):"
		# 	elif channel == ChannelEnum.Online:
		# 		txtBanben = "版本号(格式:1.0.6.xxx):"
		# 	verLabel1 = tk.Label(frame1,text = txtBanben,font =('Arial 10'))
		# 	verLabel1.grid(row=5,column=0)
		# 	self.verLabel2 = tk.Entry(frame1,font = ('Arial 10 bold'))
		# 	self.verLabel2.grid(row=5,column=1)

		# 	qudaoLabel1 = tk.Label(frame1,text = "打包渠道(格式：数字):",font =('Arial 10'))
		# 	qudaoLabel1.grid(row=7,column=0)

		# 	self.qudaoType = tk.IntVar()
		# 	tk.Radiobutton(frame1, text="google", variable=self.qudaoType, value=1).grid(row=7,column=1)
		# 	tk.Radiobutton(frame1, text="onestore", variable=self.qudaoType, value=2).grid(row=7,column=2)

		# 	apkVerLabel1 = tk.Label(frame1,text = "apk versionCode(格式：数字):",font =('Arial 10'))
		# 	apkVerLabel1.grid(row=8,column=0)
		# 	self.apkVerLabel2 = tk.Entry(frame1,font = ('Arial 10 bold'))
		# 	self.apkVerLabel2.grid(row=8,column=1)

		# 	apkType = tk.Label(frame1,text = "打包类型:",font =('Arial 10'))
		# 	apkType.grid(row=9,column=0)

		# 	self.apkType2 = tk.IntVar()
		# 	# self.apkType2.set(2)
		# 	tk.Radiobutton(frame1, text="apk热更包", variable=self.apkType2, value=1,command=self.onClickRadio).grid(row=9,column=1)
		# 	tk.Radiobutton(frame1, text="apk整包", variable=self.apkType2, value=2,command=self.onClickRadio).grid(row=9,column=2)
		# 	tk.Radiobutton(frame1, text="apk(obb包)", variable=self.apkType2, value=3,command=self.onClickRadio).grid(row=9,column=3)


		self.lServerlist = 'serverList: "'+serverList+'",'
		self.lPf = 'pf: "'+pf+'",'
		self.lApipath = 'apiPath: "'+apiPath+'",'

		packApk._packApk(self.lServerlist,self.lPf,self.lBysdk,self.lApipath,"1.0.1",1,"1.0.1",3)
		# frame2 = tk.Frame(self.wframe)
		# frame2.pack(padx=5, pady=50)
		# #构建按钮
		# if state == SysEnum.web:
		# 	self.packBtn = tk.Button(frame2, text='构建',relief='raised', width=12, height=2,command = self.packAndroidTest)
		# 	self.packBtn.grid(row=0,column=0)

		# 	tk.Label(frame2,text='       ').grid(row=0,column=1)

		# 	self.goBtn = tk.Button(frame2,text='预览',relief='raised',width=12,height=2,command = self.goWatch)
		# 	self.goBtn.grid(row=0,column=2)
		# elif state == SysEnum.hot or state == SysEnum.packios:
		# 	self.packBtn = tk.Button(frame2, text='构建',relief='raised', width=12, height=2,command = self.packHot)
		# 	self.packBtn.grid(row=0,column=0)
		# elif state == SysEnum.pack:
		# 	self.packBtn = tk.Button(frame2, text='构建',relief='raised', width=12, height=2,command = self.packApk)
		# 	self.packBtn.grid(row=0,column=0)

	def onClickRadio(self):
		# print(self.apkType2.get())
		pass
	############################ 回调 #############################
	def packHot2(self):
		self.lVersion = 'version: "'+self.verLabel2.get()+'",'
		if self.state == SysEnum.hot:
			packHot._packHot(self.lServerlist,self.lPf,self.lBysdk,self.lApipath,self.verLabel2.get(),self.channel,self.cosType.get(),self.area)
		else:
			packHot._packIosBegin(self.lServerlist,self.lPf,self.lBysdk,self.lApipath,self.verLabel2.get(),self.apkVerLabel2.get())
	def packHot(self):
		self.packBtn['state'] = 'disabled'
		self.packBtn['text'] = '构建中'
		th = threading.Thread(target=self.packHot2,args=())
		th.setDaemon(True)
		th.start()
	####打包
	def packApk2(self):
		self.lVersion = 'version: "'+self.verLabel2.get()+'",'
		qudaoType = self.qudaoType.get()
		if self.area == "sgp":
			qudaoType = 3
		elif self.area == "hk":
			qudaoType = 4
			
		packApk._packApk(self.lServerlist,self.lPf,self.lBysdk,self.lApipath,self.verLabel2.get(),qudaoType,self.apkVerLabel2.get(),self.apkType2.get())

	def packApk(self):
		self.packBtn['state'] = 'disabled'
		self.packBtn['text'] = '构建中'
		th = threading.Thread(target=self.packApk2,args=())
		th.setDaemon(True)
		th.start()

	def packAndroidTest2(self):
		packAndTest._packNewWeiDuan(self.lServerlist,self.lPf,self.lBysdk,self.lApipath,self.lVersion,'/data/zjfh/zjfh_client/hkymg/')
		self.packBtn['state'] = 'normal'
		self.packBtn['text'] = '构建'

		result = tkmsg.showinfo(title='提示',message='构建完成，可点击预览查看！')
		if result == 'yes':
			webbrowser.open("http://118.89.163.69:84/hkymg/", new=0)

	def packAndroidTest(self):
		self.packBtn['state'] = 'disabled'
		self.packBtn['text'] = '构建中'
		th = threading.Thread(target=self.packAndroidTest2, args=())
		th.setDaemon(True)
		th.start()

	def goWatch(self):
		webbrowser.open("http://118.89.163.69:84/hkymg/",new=0)

	def __del__(self):
		print(u'del')

if __name__ == '__main__':
	PackUI().createUI(SysEnum.pack,ChannelEnum.Online,"hk")	
	# pack.createUI(SysEnum.pack,ChannelEnum.Online,"hk")

