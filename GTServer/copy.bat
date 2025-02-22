
xcopy .\*.* ..\release\ /s /e /c /y /h /r /EXCLUDE:exclude.txt
rd /s /q ..\release\config\xianyu
rd /s /q ..\release\config\server
rd /s /q ..\release\install
rd /s /q ..\release\协议
rd /s /q ..\release\public\servers
rd /s /q ..\release\doc
del /f /s /q /a ..\release\报错信息
del /f /s /q /a ..\release\config.php
del /f /s /q /a ..\release\config.xml
del /f /s /q /a ..\release\t.sh
del /f /s /q /a ..\release\public\version.php
del /f /s /q /a ..\release\copy.bat
del /f /s /q /a ..\release\exclude.txt
del /f /q /a ..\release\administrator\config\*.php
del /f /q /a ..\release\config\*.php
pause