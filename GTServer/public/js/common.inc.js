
var screenClass = function() {
    /// 解锁
    this.unlock = function()
    {
        var divLock = document.getElementById("divLock");
        if(divLock == null) return;
        document.body.removeChild(divLock);
    };
    
    /// 锁屏
    this.lock = function()
    {
        var sWidth,sHeight;
        var imgPath = "/images/loading.gif";
        sWidth  = screen.width;
        sHeight = screen.height;
        var bgObj=document.createElement("div");
        bgObj.setAttribute("id","divLock");
        bgObj.style.position="absolute";
        bgObj.style.top="0";
        bgObj.style.background="#cccccc";
        bgObj.style.filter="progid:DXImageTransform.Microsoft.Alpha(style=3,opacity=25,finishOpacity=75";
        bgObj.style.opacity="0.6";
        bgObj.style.left="0";
        bgObj.style.width=sWidth + "px";
        bgObj.style.height=sHeight + "px";
        bgObj.style.zIndex = "100";
        document.body.appendChild(bgObj);
        var html = "<table border='0' width='"+sWidth+"' height='"+sHeight+
        "'><tr><td valign='middle' align='center'>正在加载，请稍候片刻...<image src='"+
        imgPath+"'></td></tr></table>";
        bgObj.innerHTML = html;
        // 解锁
        bgObj.onclick = function()
        {
        	if ( window.confirm('是否要中断加载?') ) {
        		new screenClass().unlock();
        	}
        }
    };
}

function ajaxRequest(){
    jQuery.ajax({
        url: '',   // 提交的页面
        data: $('#searchForm').serialize(), // 从表单中获取数据
        type: 'POST', // 设置请求类型为"POST"，默认为"GET"
        beforeSend: function() // 设置表单提交前方法
        {
            new screenClass().lock();
        },
        error: function(request) { // 设置表单提交出错
            new screenClass().unlock();
            alert('加载失败，请尝试缩小搜索范围');
        },
        success: function(data) {
            new screenClass().unlock(); // 设置表单提交完成使用方法
            data.execCommand('SaveAs');
        }
    });
    return false;
}