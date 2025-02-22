
import os
import json
from FileUtils import FileUtils 


if __name__ == '__main__':
	path = '../assets/resources/gb/res/ui'
	prefabpath = '../assets/resources/gb/prefabs'
	scenePath = '../assets/scenes'
	spinepath = '../assets/resources/gb/res/spine';
	listui = []
	listprefab = []
	listscene = []
	FileUtils.getAllFilePath(path,listui)
	FileUtils.getAllFilePath(prefabpath,listprefab)
	FileUtils.getAllFilePath(scenePath,listscene)
	listspinepath = []
	FileUtils.getAllSpineJsonFilePath(spinepath,listspinepath)
	for ff_ in listscene:
		listprefab.append(ff_);
	list_delete = {}
	listUUIDDic = {}
	listspineuuiddic = {}
	list_spinedelete = {}
	for mm in listprefab:
		FileUtils.getPathAllUUID(mm,listUUIDDic)
		FileUtils.getAllContainSpineuuid(mm,listspineuuiddic)
	for cc_ in listui:
		uuid = FileUtils.readFileContentUUID(cc_+'.meta')
		Tag_ = False
		if listUUIDDic.has_key(uuid) and listUUIDDic[uuid] == True:
			Tag_ = True;
		if Tag_ == False:
			tmps = cc_.replace('\\','/')
			if FileUtils.IsContainNeedPic(tmps) == False :
				list_delete[uuid] = tmps;
				FileUtils.removeSingleFile(cc_);
				FileUtils.removeSingleFile(cc_ + ".meta");

	for ll in listspinepath:
		uuid = FileUtils.ReadSpineFileContentUUID(ll + '.meta')
		Tag_ = False
		if listspineuuiddic.has_key(uuid) and listspineuuiddic[uuid] == True:
			Tag_ = True;
		if Tag_ == False:
			tmps = ll.replace('\\','/')
			list_spinedelete[uuid] = tmps
			FileUtils.RemoveSpineFile(ll);
	
	deleteuipath = "./output/delete_ui.json";
	deletespinepath = "./output/delete_spine.json";
	deletedirpath = "./output/delete_dir.json";
	FileUtils.saveFile(list_delete,deleteuipath);
	FileUtils.saveFile(list_spinedelete,deletespinepath);

	dirpath = '../assets/resources/gb'
	listdir = []
	FileUtils.getAllEmptyFiledirPath(dirpath,listdir);
	FileUtils.saveFile(listdir,deletedirpath);
	for file in listdir:
		FileUtils.removeSingleFile(file+".meta")
		FileUtils.removeDir(file)
