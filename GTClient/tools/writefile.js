const fs = require('fs')
const path = require('path')

const write = function(code, url, filename){
  const newCode = code;
  // 将新代码写入文件
  fs.writeFileSync(path.join(url,filename),newCode)
}

module.exports = write;