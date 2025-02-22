const ff = require("./findfiles");
const write = require('./writefile');
const path = require('path');
const fs = require('fs');

// 查找
var FILE_DIR_LIST = [
    // 'assets/resources',
    // 'assets/scripts',
    '../assets/resources/gb/res/spine/'
]

// 忽略
var IGNOR_LIST = [
    '.svn',
    '.atlas',
    '.png',
    '.json',    
    '.DS_Store',
    '.ts',
]

// 指定
var SPECIFIC_LIST = [
    '.meta'
]

function core(url, fileName) {
    let destJsPath = path.join(url, fileName);
    // console.log("destJsPath:"+destJsPath);
    fs.readFile(destJsPath, (err, data) => {
        let code = data.toString();
        let json = JSON.parse(code);
        if(json.premultiplyAlpha != null) {
            console.log("destJsPath:"+destJsPath);
            json.premultiplyAlpha = false;
            write(JSON.stringify(json), url, fileName);
        }
    });
}

ff((path1, path2)=>{
    core(path1, path2);
}, FILE_DIR_LIST, IGNOR_LIST, SPECIFIC_LIST);


