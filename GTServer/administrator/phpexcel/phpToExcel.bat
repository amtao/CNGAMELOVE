@echo off
title һ�������ύ

::rem ����Դ����
::set RES_DIR=%~d0/king/auto/config/
::TortoiseProc.exe /command:update /path:"%RES_DIR%" /closeonend:1
::echo ����Դ�������!!!!

::xcopy /y "..\..\..\..\servers\conf\*" "..\..\..\..\servers\config\"
::cd %~d0/king/backend_jpxl_test/administrator/phpexcel
cd /d %~dp0
php phpToExcel.php
echo ����ΪPHP�������!!!!

::rem �ύ
::set PRJ_DIR=%~d0/king/backend_jpxl_test/
::echo #############������Դsvn#####################
::TortoiseProc.exe /command:commit /path:"%PRJ_DIR%" /closeonend:1
::echo done!!!
pause
