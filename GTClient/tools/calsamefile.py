#coding:utf8
import os
from subprocess import Popen
from FileUtils import FileUtils
import sys
reload(sys)
sys.setdefaultencoding('utf8')

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
	path = '../assets/resources/gb/res/ui'
	listui = []
	FileUtils.getAllFilePath(path,listui)
	md5filedic = {}

	for ll in listui:
		md5str = FileUtils.getFileMd5(ll);
		if not md5filedic.has_key(md5str):
			md5filedic[md5str] = {}
			md5filedic[md5str]['num'] = 1;
			md5filedic[md5str]['cpath'] = [];
			md5filedic[md5str]['cpath'].append(ll)
		else:
			md5filedic[md5str]['num'] += 1;
			md5filedic[md5str]['cpath'].append(ll)

	temp = []
	for k,v in md5filedic.items():
		if v['num'] > 1:
			cc = {};
			cc['md5ky'] = k;
			cc['val'] = v
			temp.append(cc)

	FileUtils.saveFile(temp,'./output/samepic.json')

	scenePath = '../assets/scenes'
	prefabpath = '../assets/resources/gb/prefabs'
	listprefab = []
	listscene = []
	FileUtils.getAllFilePath(prefabpath,listprefab)
	FileUtils.getAllFilePath(scenePath,listscene)
	for ff_ in listscene:
		listprefab.append(ff_);

	recorddic = {}
	for ll in listprefab:
		FileUtils.getPrefabAllUUid(ll,recorddic)

	ppdic = {}
	for cc_ in listui:
		uuid = FileUtils.readFileContentUUID(cc_+'.meta');
		for k,v in recorddic.items():
			if v.has_key(uuid):
				if not ppdic.has_key(cc_):
					ppdic[cc_] = 1
				else:
					ppdic[cc_] +=1;

	listreference = []
	for k,v in ppdic.items():
		ll = {}
		ll["path"] = k;
		ll["num"] = v;
		listreference.append(ll)

	listreference.sort(lambda x,y:cmp(x['num'],y['num']),reverse=True)

	FileUtils.saveFile(listreference,'./output/referencecountpic.json')

	hhpp = {}
	for k,v in md5filedic.items():
		if len(v['cpath']) > 1:
			hhpp[k] = {}
			for ll in v['cpath']:
				if ppdic.has_key(ll):
					if not hhpp[k].has_key(ll):
						hhpp[k][ll] = ppdic[ll]

	FileUtils.saveFile(hhpp,'./output/samepicreferencecountpic.json')

	rrtt = {}
	for k,v in hhpp.items():
		keys = list(v.keys())
		if len(keys) > 1:
			tkey = "";
			tnum = 0;
			for s,j in v.items():
				if s.find("res/ui/common") != -1:
					tnum = j;
					tkey = s;
					break;
				if j > tnum:
					tnum = j;
					tkey = s;
			uuid = FileUtils.readFileContentUUID(tkey+'.meta');
			for s,j in v.items():
				if s != tkey:
					uuid2 = FileUtils.readFileContentUUID(s+'.meta');
					rrtt[uuid2] = uuid;

	for ll in listprefab:
		FileUtils.modifyPrefabFixUUID(ll,rrtt);

	mydir = os.getcwd();
	curpath = os.path.join(mydir,'删除没有引用的图片和spine.bat'.decode('utf-8').encode('cp936'))
	runthis = Popen(curpath,cwd=mydir)




