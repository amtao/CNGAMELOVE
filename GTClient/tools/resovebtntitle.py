import os
import json
import codecs
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

def modifyPrefab(path,uuid,uuiddic,modifylist):
	data = FileUtils.readFile(path);
	recordid = {}
	curFlag = False
	for ll in data:
		if ll.has_key("_N$textKey") and uuiddic.has_key(ll["_N$textKey"]):
			recordid[ll['node']["__id__"]] = True;
	for ll in data:
		if ll.has_key("__type__") and ll["__type__"] == "cc.Label":
			cid = ll["node"]["__id__"];
			if recordid.has_key(cid) and recordid[cid] == True:
				if ll.has_key("_N$file") and ll["_N$file"] != None and ll["_N$file"].has_key("__uuid__"):
					ll["_N$file"]["__uuid__"] = uuid;
					modifylist[path] = True
					curFlag = True;
	if curFlag:
		FileUtils.saveFile(data,path)

		

if __name__ == '__main__':
	prefabpath = '../assets/resources/gb/prefabs'
	scenePath = '../assets/scenes'
	fntpath = "../assets/resources/gb/res/ui/common/btnlabeltitle.fnt.meta";
	if not FileUtils.isExistFile(fntpath):
		print("=====error========== btnlabeltitle.fnt.meta is not exsit===============")
		sys.exit(0);
	listprefab = []
	listscene = []
	FileUtils.getAllFilePath(prefabpath,listprefab)
	FileUtils.getAllFilePath(scenePath,listscene)
	for ff_ in listscene:
		listprefab.append(ff_);
	data = FileUtils.readFile(fntpath)
	fntuuid = data['uuid'];
	print(fntuuid)

	bb = FileUtils.readFile('./output/textKey.json');

	modifylist = {}
	for ll in listprefab:
		modifyPrefab(ll,fntuuid,bb,modifylist);

	FileUtils.saveFile(modifylist,"./output/modifyfile.json");


