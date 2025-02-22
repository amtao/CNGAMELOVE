#!/bin/sh

# cocoscreator build 
/Applications/CocosCreator/Creator/2.3.4/CocosCreator.app/Contents/MacOS/CocosCreator --path ./ --build "platform=ios;encryptJs=true;xxteaKey=cbf4545c-3764-4a;zipCompressJs=true;"
# echo 'creator build success'
# xcodebuild clean

# xcodebuild build
# echo 'xcode build success'

# xcrun -sdk iphoneos packageApplication -v /Users/admin/Documents/jenkins/workspace/GT_CLIENT_BUILD_IOS/GTClient/build/jsb-link/frameworks/runtime-src/proj.ios_mac/build/Release-iphoneos/newzjfh-mobile.app -o /Users/admin/Documents/jenkins/workspace/GT_CLIENT_BUILD_IOS/GTClient/Packager/wyym.ipa


#项目路径，根据你的配置进行修改
projectDir="/Users/admin/Documents/jenkins/workspace/GT_CLIENT_BUILD_IOS/GTClient/build/jsb-link/frameworks/runtime-src/proj.ios_mac"

# 打包生成路径 需修改
ipaPath="/Users/admin/Documents/jenkins/workspace/GT_CLIENT_BUILD_IOS/GTClient/Packager"

# icon 目录
bundleSourcesPath="/Users/admin/Documents/jenkins/workspace/GT_CLIENT_BUILD_IOS/icons/ios/AppIcon.appiconset"

# Provisioning Profile 需修改 查看本地配置文件
PROVISIONING_PROFILE="ky"

# Project Name
projectName="wyym"

# 版本号
bundleVersion="1.0.0"

############# 重签名需要文件
# 以下文件需放在 ipaPath 路径下
# Entitlements=${ipaPath}/entitlements.plist

schemeName="newzjfh"

xcodebuild -project ${projectDir}/${schemeName}.xcodeproj 

#build 生成app
xcodebuild build -project ${projectDir}/${schemeName}.xcodeproj


# 将对应的 资源文件（icon，lauchscreen，等资源文件） 复制到需要修改的 app 的目录下
# cp -Rf ${bundleSourcesPath}/* ${projectDir}/build/Release-iphoneos/${schemeName}.app


# 生成 ipa
xcrun -sdk iphoneos packageApplication -v ${projectDir}/build/Release-iphoneos/${schemeName}.app -o ${ipaPath}/${projectName}.ipa
# xcrun -sdk iphoneos -v PackageApplication ${projectDir}/build/Release-iphoneos/${schemeName}-mobile.app -o ${ipaPath}/${projectName}.ipa
#xcodebuild -exportArchive -archivePath ${ipaPath}/Payload/${schemeName}.app -exportPath ${ipaPath}/$appDownloadName.ipa -exportOptionsPlist '/Users/Harvey/Downloads/entitlements.plist'