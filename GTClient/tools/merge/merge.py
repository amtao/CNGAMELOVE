#-*- coding:utf-8 -*-

import os,sys
sys.path.append("../../tools")
import re
import chardet
from FileUtils import FileUtils

reload (sys)
sys.setdefaultencoding('utf-8')

#准备清理即将要合并的分支路径，包括revert以及多余文件删除
def ClearUrlPath(path):
    revertcmd = 'svn revert -R %s'%path
    svnstcmd = 'svn st %s '%path
    #先revert本地变更
    os.system(revertcmd)
    infotmp = os.popen(svnstcmd).read()
    infolist = infotmp.split('\n')
    for info in infolist:
        regex = re.compile('\?\s+')
        res = re.match(regex,info)
        if res:
            regex = re.compile('[^\?\s+].*')
            pathinfo = re.findall(regex,info)
            pathlist = pathinfo[0]
            #print(u'准备清除工程目录下的本地目录:%s'.decode('utf-8').encode('gbk') %pathlist)
            print(u'准备清除工程目录下的本地目录:%s' %pathlist)
            #清除本地多余目录和文件
            DeletePath(pathlist)

#删除文件或文件夹操作函数
def DeletePath(pathlist):
    # 首先要检查这个path是文件还是文件夹，不同类型的要分开处理
    cmd_rm = 'rd /s /q' if sys.platform.startswith('win32') else 'rm -rf'
    if os.path.isdir(pathlist):
        os.system('{0} {1}'.format(cmd_rm, pathlist))
    elif os.path.isfile(pathlist):
        os.remove(pathlist)


#生成提交日志
def CommitLog(trunk_url,version):
    logcmd = 'svn log %s -r%s'%(trunk_url,version)
    loginfotmp = os.popen(logcmd).readlines()
    if len(loginfotmp) <= 4:
    	return None;
    versioninfo = loginfotmp[3].strip('\n')
    authorinfo = loginfotmp[1].split('|')[1]
    loginfo = 'reversion:'+ version + ' author:' + authorinfo + versioninfo

    return loginfo

#判断本地路径中是否有冲突文件,如有冲突，返回冲突文件列表
def CheckExistConflictFile(path):
    svnstcmd = 'svn st %s ' % path
    infotmp = os.popen(svnstcmd).read()
    infolist = infotmp.split('\n')
    #增加冲突标记
    conftag = False
    conffilelist = []

    for info in infolist:
        regex = re.compile(r'C\s+|\s+C\s+')
        res = re.match(regex, info)
        if res:
            regex = re.compile(r'[^(C\s+)|(\s+)(C\s+)].*') #正则表达式，匹配除C开头和！   C开头的其他字符
            pathinfo = re.findall(regex, info)
            pathlist = pathinfo[0]
            conffilelist.append(pathlist)
            conftag = True #将标记置为true

    if conftag == False:
        return False
    else:
        print(u'----------冲突警告----------')
        for file in conffilelist:
            fileencoding=chardet.detect(file)
            if fileencoding['encoding']=='utf-8':
                print(u'冲突文件:%s' %r)
            elif fileencoding['encoding']=='gb2312':
                r = file.decode('gb2312')
                print(u'冲突文件:%s' %r)
        return conffilelist

def CreateConflictFile(filename,commitlog,conffilelist):
    f = open(filename,'a+') #用追加模式写入
    confline = '-----------------' + commitlog + '-----------------\n'
    f.write(confline)
    for conffile in conffilelist:
        f.write(conffile + '\n')
    f.write('\n')
    f.close()

def Merge(trunk_url,branch_path,filename):
    f = open(filename,'r')
    versioninfos = f.read().splitlines()
    f.close()
    svnupcmd = 'svn up %s' % branch_path
    #tmplist = versioninfos[:] #拷贝个list，用作合并处理，原列表用来重写文件
    tmplist = []
    for ll in versioninfos:
    	strArr = ll.split("-")
    	print(strArr)
    	if len(strArr) > 1:
    		for ii in range(int(strArr[0]),int(strArr[1])+1):
    			tmplist.append(str(ii))
    	else:
    		tmplist.append(ll)

    for version in tmplist:
        commitlog = CommitLog(trunk_url,version)
        if commitlog == None:
        	continue
        os.system(svnupcmd)
        if not version.isdigit(): # 判断version是否为数字
            continue
        mergecmd = 'svn merge -c%s %s %s'%(version,trunk_url,branch_path)
        #commitcmd = u'svn ci -m"task:22 合并 %s"' %commitlog
        #commitcmd =  """svn ci -m"task:22 这是一次自动合并"  """
        #print(u'\n-------准备合并:%s ------'.encode('gbk') %commitlog)

        mergelog = os.popen(mergecmd).readlines() #显示日志，不用system来调用shell指令，因为冲突的时候会出现选项，程序对暂停...
        for log in mergelog:
            print(log.strip('\n'))
        confresult = CheckExistConflictFile(branch_path)
        hasconf = False
        if confresult != False:
            hasconf = True
            print(u'Reversion：%s的合并有冲突，解决冲突后输入[C]继续！' % version)
            conf = raw_input(u'Input:')
            #写入冲突文件
            #CreateConflictFile(Globalconfig.conflictFile,commitlog,confresult)
            while True:
                if conf == 'C' or conf == 'c':
                    confresult = CheckExistConflictFile(branch_path)
                    if confresult != False:
                        print(u'依旧有冲突没有解决，请解决后再输入[C]继续执行！')
                        conf = raw_input(u'Input:')
                        continue
                    break
                else:
                    print(u'输入错误！如果想继续请输入[C]')
                    conf = raw_input(u'Input:')

        print(u'%s合并成功，准备提交！'%version)
        os.chdir(branch_path)
        if hasconf:
            os.system('svn ci .\\ -m"Merged [C] {}"'.format(commitlog))
        else:
            os.system('svn ci .\\ -m"Merged {}"'.format(commitlog))

        #versioninfos.remove(version)

    #重写filename文件
    #FileUtils.WriteFile(filename,data=versioninfos)

if __name__ == '__main__':
	mergeListPath = "./mergeList.txt"
	fromMergeUrl = "https://192.168.1.91:8443/svn/fhwy/trunk/03_client/GTClient/assets"
	curNeedMergePath = "../../assets"
	curNeedMergePath = os.path.abspath(curNeedMergePath)
	print(curNeedMergePath)
	svnupdatecmd = 'svn update %s' %curNeedMergePath
	os.system(svnupdatecmd)
	print(u'更新目录')
	Merge(fromMergeUrl,curNeedMergePath,mergeListPath)

	