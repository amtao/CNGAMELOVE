import os
import sys
from FileUtils import FileUtils 

if __name__ == '__main__':
	path = '../assets/resources/gb/res/ui'
	listdirpath = []
	FileUtils.getAllDirPath(path,listdirpath)
	for ll in listdirpath:
		FileUtils.renameDirFiles(ll)