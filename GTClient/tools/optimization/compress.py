# -*- coding: utf-8 -*-

import os
import os.path
import shutil
import sys

# 压缩结果是否覆盖源文件
SaveToOriginalDir=True

SelfPath = sys.path[0]
# 压缩工具
PngquantExe=SelfPath+"\pngquant\pngquant.exe"


# 工程根目录
PathWorkspaceRoot = os.path.abspath(
  os.path.join(os.path.dirname(__file__), ".."))
print u"当前工作目录: "+PathWorkspaceRoot
# 压缩资源目录
PngSrcRoot=PathWorkspaceRoot+"../../assets/resources"
# 压缩后存放的目录
SaveRoot=PathWorkspaceRoot+"/../../assets/resources"
# 压缩过的图片列表
CompressFilesRecord=PngSrcRoot+'/compress_record.txt'

# 黑名单（不需要压缩的图片）
Backlits=[
  'NetworkTips_atlas0.png',
  'Common_atlas0.png',
  'BldgUpgrade_atlas0.png'
   ]

# 文件后缀名
file_end='.png'
file_temp_end='-fs8.png'

# 压缩品质范围
compress_quality='75-80'

# 文件列表
file_list=[]

# 清理旧文件
def initDir():
  global SaveRoot
  if(SaveToOriginalDir):
    if os.path.exists(CompressFilesRecord):
      print u"图片已经压缩过了！"
      return
    SaveRoot = PngSrcRoot
  else:
    if os.path.exists(SaveRoot):
      print u"压缩文件存放目录清空"
      shutil.rmtree(SaveRoot)
    print u"创建压缩文件存放目录："+SaveRoot
    os.makedirs(SaveRoot)

# 获取文件列表
def _getFileFromRootDir(dir,ext=None):
	allfiles = []
	needExtFilter = (ext != None)
	for root,dirs,files in os.walk(dir):        
            for name in files:
                if(root.find("spine") == -1):
                    filename = os.path.join(root,name)
                    extension = os.path.splitext(filename)[1][1:]
                    if needExtFilter and extension == ext in ext:
                        allfiles.append(filename)
	return allfiles


# 获取文件列表
def getImages(root):
  print u"========= 开始遍历图片"
  global file_list
  files = os.listdir(root)
  for file in files:
    # 过滤出 png 图片
    if os.path.isdir(os.path.realpath(file)):
      print u"过滤掉文件目录："+file
      getImages(os.path.realpath(file))
    else:
      endStr = os.path.splitext(file)[1]
      if endStr == file_end:
        if isBack(file):
          print u"过滤掉黑名单中的文件："+file
        else:
          file_list.append(file)
          # print u"文件 " + file + u" 添加到压缩列表"

# 开始图片压缩任务
def startCompress():
  print u"========= 开始压缩图片"
  for file in _getFileFromRootDir(PngSrcRoot,'png'):
    print u"压缩图片："+file
    compress(file)
    # record_file.write(file+'\n')
#   record_file.close()

def main():
#   initDir()
#   getImages(PngSrcRoot)
  startCompress()
  print u"========= 图片压缩完成"
  
# 判断是否在黑名单中
def isBack(filePath):
  for i in Backlits:
    if(filePath.find(i) != -1):
      return True
  return False

# 压缩一个图片
def compress(fileName):
  srcPath = fileName
  outPath = fileName
  if SaveToOriginalDir:  # 使用 .png 后缀，且通过 -f 覆盖源文件
    cmd = PngquantExe + " -f --ext "+ file_end + " " + srcPath + " --quality " + compress_quality
    os.system(cmd)
    return
  else:          # 默认压缩到当前目录下，并加上 '-fs8.png' 后缀
    cmd = PngquantExe + " --ext "+ file_temp_end + " " + srcPath + " --quality " + compress_quality
    os.system(cmd)
  # 复制到文件夹
  fileOriginalName = os.path.splitext(fileName)[0]
  compressed_srcpath = PngSrcRoot + '/'+fileOriginalName + file_temp_end
  if os.path.exists(compressed_srcpath):
    if os.path.exists(outPath):
      os.remove(outPath)
    shutil.move(compressed_srcpath, outPath)     #移动文件

if __name__ == '__main__':
  main()