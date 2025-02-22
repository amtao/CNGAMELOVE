const ff = require("./findfiles");
const write = require('./writefile');
const path = require('path');
const fs = require('fs');

// 查找
var FILE_DIR_LIST = [
    // 'assets/resources',
    // 'assets/scripts',
    // '../assets/resources/gb/res/spine/'
    '../assets/resources/gb/res/'
]

// 忽略
var IGNOR_LIST = [
    '.svn',
    '.atlas',
    '.png',
    '.json',    
    '.DS_Store',
    '.ts',
    '.meta',
    '.anim',
    '.mp3',
    '.spine',
    '.plist',
    '.jpg',
    '.fnt',
    '.TTF'
]

// 指定
var SPECIFIC_LIST = [
    'png.meta'
]

function core(url, fileName) {
    let destJsPath = path.join(url, fileName);    
    fs.readFile(destJsPath, (err, data) => {
        let code = data.toString();
        console.log(destJsPath)
        let json = JSON.parse(code);
        
        var add = {
          "android": {
            "formats": [
              {
                "name": "etc2",
                "quality": "fast"
              }
            ]
          },
          "ios": {
            "formats": [
              {
                "name": "etc2",
                "quality": "fast"
              }
            ]
          }
        };
        json.platformSettings = add;
        console.log("destJsPath:"+url+" fileName:"+fileName);
        write(JSON.stringify(json), url, fileName);
    });
}

ff((path1, path2)=>{
    core(path1, path2);
}, FILE_DIR_LIST, IGNOR_LIST, SPECIFIC_LIST);


