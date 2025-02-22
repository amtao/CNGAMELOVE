var fs = require('fs');
var path = require('path');

  function main(cb, FILE_DIR_LIST, IGNOR_LIST, SPECIFIC_LIST) {
    FILE_DIR_LIST.forEach(function(dir) {
      ignore_findPathFiles(__dirname+"/"+dir, cb, IGNOR_LIST);
      specific_findPathFiles(__dirname+"/"+dir, cb, SPECIFIC_LIST);      
    });
  }

  function ignore_findPathFiles($path, cb, IGNOR_LIST) {
    fs.readdirSync($path).forEach(function(f){
      let file_path = path.join($path,f);
      let state = fs.lstatSync(file_path);
      let flag = true;
      for(var i=0; i<IGNOR_LIST.length; i++) {
        if(file_path.indexOf(IGNOR_LIST[i]) != -1) {
          // console.log(file_path);
          flag = false;
          break;
        }
      }      
      if(flag) {
        if(state.isDirectory()){         
          ignore_findPathFiles(file_path, cb, IGNOR_LIST);
        } else if(state.isFile()){
          // console.log($path, f);
          cb && cb($path, f);          
        }
      }
    });
  }

  function specific_findPathFiles($path, cb, SPECIFIC_LIST) {
    fs.readdirSync($path).forEach(function(f){
      let file_path = path.join($path,f);
      let state = fs.lstatSync(file_path);            
      if(state.isDirectory()){         
        specific_findPathFiles(file_path, cb, SPECIFIC_LIST);
      } else if(state.isFile()){
        let flag = false;
        for(var i=0; i<SPECIFIC_LIST.length; i++) {
          if(file_path.indexOf(SPECIFIC_LIST[i]) != -1) {
            // console.log(file_path);
            flag = true;
            break;
          }
        }   
        if(flag) {   
          // console.log($path, f);
          cb && cb($path, f);          
        }
      }
    });
  }
  
  module.exports = main;
