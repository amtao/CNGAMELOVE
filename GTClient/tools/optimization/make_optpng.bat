call env.bat

cd %TOOLS%\

for /R %RES_PATH%\ %%i in (*.png) do (
	pngquant.exe --ext .png --force --quality=50 %%i
)