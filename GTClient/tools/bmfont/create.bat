
bmfont64.exe -t zh.txt -c "stzhong.bmfc" -o "btnlabeltitle.fnt"
xcopy  .\btnlabeltitle.fnt ..\..\.\assets\resources\gb\res\ui\common  
xcopy  .\btnlabeltitle_0.png ..\..\.\assets\resources\gb\res\ui\common
python .././resovebtntitle.py
pause
