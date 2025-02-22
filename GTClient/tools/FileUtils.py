
import os
import json
import re
import codecs
import shutil
import sys
import hashlib

class FileUtils:
	@staticmethod
	def getAllFilePath(dirpath,listpath):
		file_list = os.listdir(dirpath)
		for file in file_list:
			curpath = os.path.join(dirpath,file)
			if os.path.isdir(curpath):
				FileUtils.getAllFilePath(curpath,listpath)
			else:
				if os.path.splitext(file)[1] != '.meta' and os.path.splitext(file)[1] != '.pac':
					cc_ = curpath.replace("\\","/")
					listpath.append(cc_)
		return listpath
	
	@staticmethod
	def getExtensionFilePath(dirpath, extensions):
		listpath = FileUtils.getAllFilePath(dirpath, [])
		result = []
		for file in listpath:
			for extension in extensions:
				if extension not in file:
					continue
				else:
					resultPath = file.replace("\\","/")
					result.append(resultPath)
		return result

	@staticmethod
	def readFile(path):
		fw = open(path)
		content = fw.read();
		fw.close()		
		try:
			data = json.loads(content);
		except Exception as e:
			print(path)
		return data;

	@staticmethod
	def removeSingleFile(path):
		os.remove(path)

	@staticmethod
	def removeDir(path):
		shutil.rmtree(path)

	@staticmethod
	def readFileContentUUID(path):
		fp = open(path)
		content = fp.read()
		fp.close()
		ls = json.loads(content)
		subMetas = ls['subMetas'];
		for k,v in subMetas.items():
			if v['uuid'] != None:
				return v['uuid']
		return ""

	@staticmethod
	def getAllUUID(data,uuiddic):
		for k,v in data.items():
			if k == '__uuid__':
				uuiddic[v] = True
			else:
				if isinstance(v,dict):
					FileUtils.getAllUUID(v,uuiddic);

	@staticmethod
	def saveFile(jsondata,path):
		fw = open(path,"w")
		json.dump(jsondata,fw,ensure_ascii=False,indent=4)
		fw.close()

	@staticmethod
	def getAllEmptyFiledirPath(dirpath,listpath):
		file_list = os.listdir(dirpath)
		if len(file_list) == 0:
			listpath.append(dirpath);
		else:
			if len(file_list) == 2:
				for file in file_list:
					oo = os.path.splitext(file)
					curpath = os.path.join(dirpath,file)
					if oo[1] == '.pac':
						listpath.append(dirpath)
					if os.path.isdir(curpath):
						FileUtils.getAllEmptyFiledirPath(curpath,listpath)
			for file in file_list:
				curpath = os.path.join(dirpath,file)
				if os.path.isdir(curpath):
					FileUtils.getAllEmptyFiledirPath(curpath,listpath);
		return listpath

	@staticmethod
	def getPathAllUUID(path,listdata):
		data = FileUtils.readFile(path)
		for cc in data:
			if isinstance(cc,dict):
				FileUtils.getAllUUID(cc,listdata);

	@staticmethod
	def IsContainNeedPic(path):
		notneeddeletlist = ["res/ui/dressshop/fzsc_sale","res/ui/dispatch","res/ui/lingqi","res/ui/child/lianyindk0","res/ui/wife/qi","res/ui/wife/shu","res/ui/child/girl","res/ui/servant/se_icon0","res/ui/common/btnlabeltitle","res/ui/create/xiyue","res/ui/create/beishang","res/ui/create/jianyi","res/ui/servant/btn_zizhi0","/res/ui/look/","res/ui/email/weizhankai_yidu","/res/ui/pinz0","/res/ui/servantsp/pinz","/res/ui/cardQuality2","/res/ui/cardFrame2","res/ui/card","res/ui/activity/","/res/ui/zhongyuan/guwu","res/ui/purchase","res/ui/activitygrid/xnhd_point_0","res/ui/activitygrid/xnhd_0","res/ui/activitygrid/xnhd_touzi_0","res/ui/rank","res/ui/guoli/img_qd","res/ui/servant/pro_b","res/ui/spring/baozhu_","res/ui/jiban_word_","res/ui/clothe/clothe_pro_","res/ui/clothe_pro_","res/ui/qixi/qian","res/ui/chat/","/res/ui/unpack/partnerzone_bg"]
		for gg in notneeddeletlist:
			if path.find(gg) != -1:
				return True;
		return False;

	@staticmethod
	def IsUtfEncoding(path):
		data = FileUtils.readFile(path)
		ss = chardet.detect(data)
		if ss['encoding'] != 'utf-8':
			return True
		return False

	@staticmethod
	def ReadSpineFileContentUUID(path):
		ls = FileUtils.readFile(path)
		uuid = ls["uuid"];
		if uuid != None:
			return uuid;
		return ""

	@staticmethod
	def getAllSpineJsonFilePath(dirpath,listpath):
		file_list = os.listdir(dirpath)
		for file in file_list:
			curpath = os.path.join(dirpath,file)
			if os.path.isdir(curpath):
				FileUtils.getAllSpineJsonFilePath(curpath,listpath);
			else:
				kk_ = os.path.splitext(file)
				if kk_[1] == '.json':
					listpath.append(curpath)
		return listpath

	@staticmethod
	def getAllContainSpineuuid(path,spinedic):
		data = FileUtils.readFile(path);
		for cc in data:
			if cc['__type__'] == 'sp.Skeleton':
				if (cc.has_key('_N$skeletonData') == True):
						if (cc['_N$skeletonData'] != None and cc['_N$skeletonData'].has_key('__uuid__') == True):
							spinedic[cc['_N$skeletonData']['__uuid__']] = True

	@staticmethod
	def readCommonFile(path):
		fw = open(path);
		lines = fw.readlines(1000000);
		fw.close();
		return lines;

	@staticmethod
	def RemoveSpineFile(path):
		tp = os.path.splitext(path);
		fname = tp[0]
		os.remove(path)
		os.remove(path + '.meta')
		os.remove(fname + '.png')
		os.remove(fname + '.png.meta')
		os.remove(fname + '.atlas')
		os.remove(fname + '.atlas.meta')

	@staticmethod
	def saveFileByEncoding(data,path,encoding):
		fw = codecs.open(path,"w",encoding);
		json.dump(data,fw,ensure_ascii=False,indent=4)
		fw.close()

	@staticmethod
	def isExistFile(path):
		if os.path.exists(path):
			return True;
		return False;

	@staticmethod
	def getAllDirPath(dirpath,listdir):
		listdir.append(dirpath)
		file_list = os.listdir(dirpath)
		for file in file_list:
			curpath = os.path.join(dirpath,file)
			if os.path.isdir(curpath):
				FileUtils.getAllDirPath(curpath,listdir)
		return listdir

	@staticmethod
	def renameDirFiles(dirpath):
		cc = dirpath.replace("\\","/")
		pp = cc.split("/")
		dirname = pp[len(pp)-1]
		file_list = os.listdir(dirpath)
		currentpath = os.getcwd()
		os.chdir(dirpath)
		for file in file_list:
			curpath = os.path.join(dirpath,file)
			if not os.path.isdir(curpath):
				ff_ = os.path.splitext(file)
				if ff_[1] != 'pac' and ff_[0].find('.pac') == -1 and file.find(".png") != -1 and file.find("btnlabeltitle_") == -1:
					hh = file.replace(dirname + "_","")
					os.rename(file,dirname + "_" + hh)

		os.chdir(currentpath)
		sys.stdin.flush()

	@staticmethod
	def getFileMd5(path):
		ff = os.path.splitext(path)
		if ff[1] == ".png":
			fw = open(path,"rb");
			data = fw.read();
			fw.close();
			file_md5 = hashlib.md5(data).hexdigest();
			return file_md5
		else:
			fw = open(path)
			data = fw.read();
			fw.close();
			file_md5 = hashlib.md5(data).hexdigest();
			return file_md5;

	@staticmethod
	def getPrefabAllUUid(path,dic):
		if not dic.has_key(path):
			dic[path] = {}
		data = FileUtils.readFile(path)
		for cc in data:
			if isinstance(cc,dict):
				FileUtils.getAllUUID(cc,dic[path]);


	@staticmethod
	def modifyUUID(data,dic,tlist):
		for k,v in data.items():
			if k == '__uuid__':
				if dic.has_key(v):
					data[k] = dic[v]
					tlist.append(v);
			else:
				if isinstance(v,dict):
					FileUtils.modifyUUID(v,dic,tlist);

	@staticmethod
	def modifyPrefabFixUUID(path,dic):
		data = FileUtils.readFile(path);
		tlist = [];
		for cc in data:
			if isinstance(cc,dict):
				FileUtils.modifyUUID(cc,dic,tlist)
		if len(tlist) > 0:
			FileUtils.saveFileByEncoding(data,path,'utf-8')

	@staticmethod
	def saveCommonFile(strdata,path,encoding):
		fw = codecs.open(path,"w",encoding);
		fw.write(strdata)
		fw.close()

	@staticmethod
	def systemCommand(path):
		print(path)
		os.system(path)

	@staticmethod
	def WriteFile(filename,data):
		f = open(filename,'w')
		for each in data:
			each = each + '\n'
			f.writelines(each)
		f.close()


