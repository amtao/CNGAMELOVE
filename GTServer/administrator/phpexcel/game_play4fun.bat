@echo off
title 一键解析提交

::rem 更新源配置
::set RES_DIR=%~d0/king/auto/config/
::TortoiseProc.exe /command:update /path:"%RES_DIR%" /closeonend:1
::echo 更新源配置完成!!!!

::xcopy /y "..\..\..\01_design\conf_updata\*" "..\..\..\04_server\game_korea\"
::cd %~d0/king/backend_jpxl_test/administrator/phpexcel
::cd /d %~dp0
"..\..\..\..\..\trunk\07_other\tools\php\php.exe" play4fun.php 1
echo 解析为PHP配置完成!!!!

::rem 提交
::set PRJ_DIR=%~d0/king/backend_jpxl_test/
::echo #############更新资源svn#####################
::TortoiseProc.exe /command:commit /path:"%PRJ_DIR%" /closeonend:1
::echo done!!!
pause
