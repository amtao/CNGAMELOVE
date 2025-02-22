call env.bat

cd %TOOLS%
::call make_optpng.bat

cd %PRJ_PATH%

::rd /s /q build

CocosCreator.exe --path . --build "platform=android;apiLevel=android-28;encryptJs=true;xxteaKey=cbf4545c-3764-4a;zipCompressJs=true;"
::xcopy D:\project\apk_icons build\jsb-link\frameworks\runtime-src\proj.android-studio\app\res /e/y
CocosCreator.exe --path . --compile "platform=android;apiLevel=android-28;"
echo "end build"