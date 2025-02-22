
import os
import json
import codecs
from subprocess import Popen
from FileUtils import FileUtils 
import sys
reload(sys)
sys.setdefaultencoding('utf8')

def getAllFilePath(dirpath,listpath):
	file_list = os.listdir(dirpath)
	for file in file_list:
		curpath = os.path.join(dirpath,file)
		if os.path.isdir(curpath):
			getAllFilePath(curpath,listpath);
		else:
			if os.path.splitext(file)[1] != '.meta' and os.path.splitext(file)[1] != '.pac':
				listpath.append(curpath)
	return listpath

def readFileTextKey(path,dicdata):
	fw = open(path)
	content = fw.read();
	fw.close()
	data = json.loads(content);
	# for ll in data:
	# 	if ll.has_key('_N$textKey') and ll['_N$textKey'] != None and ll['_N$textKey'] != "":
	# 		dicdata[ll['_N$textKey']] = True
	tmp = {}
	for ll in data:
		if ll.has_key("__type__") and ll['__type__'] == "cc.Button":
			tmp[ll["node"]["__id__"]] = True
	# mm = {}
	# for ll in data:
	# 	if ll.has_key("__type__") and ll["__type__"] == "cc.Node":
	# 		if ll.has_key("_parent") and ll["_parent"] != None:
	# 			cid = ll["_parent"]["__id__"] + 1;
	# 			if tmp.has_key(cid) and tmp[cid] != None:
	# 				mm[cid] = True
	# dicc = {}
	# for ll in data:
	# 	if ll.has_key("_N$textKey") and ll['_N$textKey'] != "":
	# 		cid = ll["node"]["__id__"];
	# 		if tmp.has_key(cid-1) and tmp[cid-1] == True:
	# 			dicc[cid] = ll["_N$textKey"]
	# for ll in data:
	# 	if ll.has_key("__type__") and ll["__type__"] == "cc.Node":
	# 		if ll.has_key("_parent") and ll["_parent"] != None:
	# 			cid = ll["_parent"]["__id__"];
	# 			if dicc.has_key(cid+1) and dicc[cid+1] != None and mm.has_key(cid) and  mm[cid] == True:
	# 				dicdata[dicc[cid+1]] = True
	for ll in data:
		if ll.has_key("_N$textKey") and ll['_N$textKey'] != "":
			cid = ll["node"]["__id__"];
			if tmp.has_key(cid-1) and tmp[cid-1] == True:
				dicdata[ll["_N$textKey"]] = True

if __name__ == '__main__':
	prefabpath = '../assets/resources/gb/prefabs'
	scenePath = '../assets/scenes'
	listprefab = []
	listscene = []
	FileUtils.getAllFilePath(prefabpath,listprefab)
	FileUtils.getAllFilePath(scenePath,listscene)
	for ff_ in listscene:
		listprefab.append(ff_);
	dickeys = {};
	for mm in listprefab:
		readFileTextKey(mm,dickeys)
	zhfilepath = '../assets/scripts/app/i18n/zh.js'
	lines = FileUtils.readCommonFile(zhfilepath);
	txtdata = []
	if lines:
		for line in lines:
			if line.find(': ') != -1:
				cc = line.split(': ')
				key = cc[0].replace(" ","");
				if dickeys.has_key(key) and dickeys[key] == True:
					txtdata.append(cc[1])
				else:
					print(key)
	txtdata.append("1234567890kK")
	txtfilepath = './bmfont/zh.txt';
	FileUtils.saveFileByEncoding(txtdata,txtfilepath,"utf_8_sig")
	FileUtils.saveFile(dickeys,"./output/textKey.json")
	mydir = os.getcwd();
	print(mydir);
	mydir = os.path.join(mydir,'bmfont')
	runthis = Popen(os.path.join(mydir,'create.bat'),cwd=mydir)
	# stdout, stderr = runthis.communicate()
	# fw = open('./textkey.json','w')
	# json.dump(dickeys,fw,ensure_ascii=False,indent=4)
