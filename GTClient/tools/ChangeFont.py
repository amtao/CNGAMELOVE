#coding:utf8
import os
from FileUtils import FileUtils
import sys
reload(sys)
sys.setdefaultencoding('utf8')

if __name__ == '__main__':
    project_path = "../assets"
    extensions = [".prefab", ".fire"]
    file_list = FileUtils.getExtensionFilePath(project_path, extensions)
    for file_path in file_list:
        lines = FileUtils.readCommonFile(file_path)
        strList = []
        bNext = False
        intput_str = ""
        bChange = False
        for index in range(len(lines)):
            str_line = lines[index]
            if bNext:
                bNext = False
                continue
            elif "\"_N$file\":" in str_line:
                next_line = lines[index + 1]
                if ("\"_isSystemFontUsed\":" in next_line) and ("null" in str_line):
                    intput_str += "   \"_N$file\": {\n"
                    intput_str += "      \"__uuid__\": \"fca501e4-2991-441e-ba3e-db7b13e9a4d1\"\n"
                    intput_str += "    },\n"
                    intput_str += "   \"_isSystemFontUsed\": false,\n"
                    bChange = True
                    bNext = True
                else:
                    intput_str += str_line
            elif ("\"_N$font\":" in str_line) and ("null" in str_line):
                intput_str += "   \"_N$font\": {\n"
                intput_str += "      \"__uuid__\": \"fca501e4-2991-441e-ba3e-db7b13e9a4d1\"\n"
                intput_str += "    },\n"
                bChange = True
            elif "\"_isSystemFontUsed\":" in str_line:
                intput_str += "   \"_isSystemFontUsed\": false,\n"
                bChange = True
            else:
                intput_str += str_line
        if bChange:
            FileUtils.saveCommonFile(intput_str, file_path, 'utf-8')
        print("change font: " + file_path)
    print("change font all done.")


