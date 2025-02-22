#coding:utf8
import os
from FileUtils import FileUtils
import sys
reload(sys)
sys.setdefaultencoding('utf8')


if __name__ == '__main__':
	scriptpath = "../assets/scripts/app"
	listscript = []
	FileUtils.getAllFilePath(scriptpath,listscript);
	for ll in listscript:
		tmpstr = ""
		lines = FileUtils.readCommonFile(ll);
		for tt in lines:
			if tt.find("require(") != -1:
				if tt.find("//") != -1:
					hh = tt.split("//")
					if hh[0].find("require(") != -1:
						pp = tt.split("/");
						length = len(pp)
						if length > 1:
							tmpstr += (pp[0].replace(".","") + pp[length-1].replace(".",""))
						else:
							tmpstr += tt
					else:
						tmpstr += tt;
				else:
					pp = tt.split("/");
					length = len(pp)
					if length > 1:
						tmpstr += (pp[0].replace(".","") + pp[length-1].replace(".",""))
					else:
						tmpstr += tt
			else:
				tmpstr += tt
		FileUtils.saveCommonFile(tmpstr,ll,'utf-8')