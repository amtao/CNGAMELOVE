
var onSuccess=function(token){
	//$('.body-content').removeClass('overlay');
	//$('.bar').removeClass('bar');
	//$('#loading').removeClass('loading').contents().remove();
	//$('#notice-show').html('success. please back to game.');
	//alert('success');
	//window.history.back();
	android.onLoginSuccess();
};

var onClose=function(){
	//alert('fail');
	//window.history.back();
	android.onPaySuccess();
};
