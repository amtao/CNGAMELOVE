#!/usr/bin/python
import threading
import json
import sys
import os
import shutil
import importlib
import collections
import tkinter as tk
import utils
importlib.reload(sys)

path = os.path.dirname(os.path.realpath(__file__))
print(u"我的路径是:%s" %path )

sys.path.append(path+'/pack')
import packAndTest
from packUI import PackUI
from packEnum import SysEnum,ChannelEnum

class GuiTool:
	def __init__(self):
		self.window = tk.Tk()
		self.window.title('易梦阁国内版集成工具')
		width = 800
		height = 500
		screenwidth = (self.window.winfo_screenwidth()-width)/2
		screenheight = (self.window.winfo_screenheight()-height)/2
		self.window.geometry('%dx%d+%d+%d' % (width,height,screenwidth,screenheight))
		self.window.resizable(width=False, height=False)

		self.createFrame()
		self.createMenu()

		self.window.config(menu=self.menubar)
		self.window.mainloop()


	########################### Frame #############################
	def createFrame(self):
		self.wframe = tk.Frame(bd=1)
		self.wframe.pack(fill="x", padx=5, pady=5)


	########################### 菜单界面 #############################
	def createMenu(self):
		self.menubar = tk.Menu(self.window)

		self.menubar.add_command(label='网页测试包',command = self.buildWeb)

		hotmenu = tk.Menu(self.menubar,tearoff = False)
		self.menubar.add_cascade(label='热更',menu = hotmenu)
		hotmenu.add_command(label='发布服热更',command=self.buildHot1)
		hotmenu.add_command(label='正式服/灰度服热更',command=self.buildHot2)
		hotmenu.add_command(label='对外体验服热更',command=self.buildHot3)

		bigmenu = tk.Menu(self.menubar,tearoff = False)
		self.menubar.add_cascade(label='apk/ipa包',menu = bigmenu)
		androidmenu = tk.Menu(bigmenu)
		bigmenu.add_cascade(label='安卓',menu=androidmenu)
		androidmenu.add_command(label='安卓发布服',command=self.buildAndroid1)
		androidmenu.add_command(label='安卓正式服/灰度服',command=self.buildAndroid2)
		androidmenu.add_command(label='安卓对外体验服',command=self.buildAndroid3)

		iosmenu = tk.Menu(bigmenu)
		bigmenu.add_cascade(label='苹果',menu=iosmenu)
		iosmenu.add_command(label='苹果',command=self.buildIos)


		self.initUI()
			

	def initUI(self):
		self.packUI = PackUI(self.wframe)

	######web测试版
	def buildWeb(self):
		self.packUI.createUI(SysEnum.web)

	#####发布服热更
	def buildHot1(self):
		self.packUI.createUI(SysEnum.hot,ChannelEnum.Develop)

	#####正式服热更
	def buildHot2(self):
		self.packUI.createUI(SysEnum.hot,ChannelEnum.Online)

	#####对外体验服热更
	def buildHot3(self):
		self.packUI.createUI(SysEnum.hot,ChannelEnum.Test)

	def buildAndroid1(self):
		self.packUI.createUI(SysEnum.pack,ChannelEnum.Develop)

	def buildAndroid2(self):
		self.packUI.createUI(SysEnum.pack,ChannelEnum.Online)

	def buildAndroid3(self):
		self.packUI.createUI(SysEnum.pack,ChannelEnum.Test)

	def buildIos(self):
		self.packUI.createUI(SysEnum.packios,ChannelEnum.Online)


if __name__ == '__main__':
	guiTool = GuiTool()