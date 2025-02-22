import os
import json
from FileUtils import FileUtils 

def getAllFilePath(dirpath,listpath):
	file_list = os.listdir(dirpath)
	for file in file_list:
		curpath = os.path.join(dirpath,file)
		if os.path.isdir(curpath):
			getAllFilePath(curpath,listpath)
		else:
			if os.path.splitext(file)[1] == '.meta' and (os.path.splitext(file)[0].find("png") != -1 or os.path.splitext(file)[0].find("jpg") != -1 or os.path.splitext(file)[0].find("jpeg") != -1):
				cc_ = curpath.replace("\\","/")
				listpath.append(cc_)
	return listpath


if __name__ == '__main__':
	path = '../assets/resources/gb/res'
	listImage = []
	getAllFilePath(path,listImage)
	for ll in listImage:
		print(ll)
		data = FileUtils.readFile(ll)
		if ll.find(".png.meta") != -1:
			if data.has_key("platformSettings"):
				data["platformSettings"] = {"web": {"formats": [{"name": "etc1","quality": "fast"}]},"android": {"formats": [{"name": "etc1","quality": "fast"}]},"ios": {"formats": [{"name": "pvrtc_4bits_rgb_a","quality": "normal"}]}}	
		elif ll.find(".jpg.meta") != -1 or ll.find(".jpeg.meta") != -1:
			if data.has_key("platformSettings"):
				data["platformSettings"] = {"web": {"formats": [{"name": "etc1_rgb","quality": "fast"}]},"android": {"formats": [{"name": "etc1_rgb","quality": "fast"}]},"ios": {"formats": [{"name": "pvrtc_4bits_rgb","quality": "normal"}]}}
		FileUtils.saveFile(data,ll)