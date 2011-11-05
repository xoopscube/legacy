/**
* MediaQuery MultiDevice Template ver 0.1β
* @Author: funnythingz
* @Url:    http://hiropo.co.uk
* 
*jquery-jcflick.js
*/

(function($){
/**============================================================
* global
*/
$.fn.jcflick = function(config)
{
	//引数の初期値を設定（カンマ区切り）
	var defaults = {
		flickContainer: 'flickContainer',
		flickWrapper: 'flickWrapper',
		flickCol: 'flickCol',
		
		autoTimer: 5000
	}
	var options = $.extend( defaults, config );
	
	//一致した要素上で繰り返す
	return this.each(function(i){
		var self = $(this);
		//init
		init( options, self );
	});
}

var init = function( args, self ){
	
	/**------------------------------------------------------------
	* プロパティ
	*/
	var f = (typeof(f) == 'undefined' || !f)? {}: undefined;
	f.p = {
		//フリックするコンテンツの現在のページ値
		//0～
		nowPage: 0,
		
		//フリックエリアトータル横幅
		wrapperWidth: 0,
		
		//フリックする1つあたりのコンテンツ横幅
		colWidth: 0,
		
		//フリックするコンテンツ最大数
		totalLength: 0,
		
		//タッチした瞬間のX座標
		touchPositionX: 0,
		
		//タッチした瞬間のY座標
		touchPositionY: 0,
		
		//実際に移動する距離
		movePosition: 0,
		
		//現在地
		nowPosition: 0,
		
		//進むか戻るかの判定(X軸)
		positionIntX: 0,
		
		//進むか戻るかの判定(Y軸)
		positionIntY: 0,
		
		//フリックイベント判定
		flag: false,
		
		//自動で切り替わるフラグ
		autoChangeFlag: true,
		
		//Timer変数
		Timer: '',
		
		//Android用フリック調整Timer変数
		AndroidTimer: ''
	}
	
	/**------------------------------------------------------------
	* 汎用関数
	*/
	f.singleCol = function(className, self){
		var elm = $('.'+ className, self);
		return elm;
	}
	
	//ua
	f.ua = {
		Android: navigator.userAgent.indexOf('Linux; U; Android ')!=-1,
		Honeycomb: navigator.userAgent.indexOf('HONEYCOMB')!=-1,
		GalaxyTab: navigator.userAgent.indexOf('SC-01C')!=-1,
		iPod: navigator.userAgent.indexOf('iPod')!=-1,
		iPhone: navigator.userAgent.indexOf('iPhone')!=-1,
		iPad: navigator.userAgent.indexOf('iPad')!=-1
	}
	
	/**------------------------------------------------------------
	* args
	*/
	var flickContainer = f.singleCol(args.flickContainer, self);
	var flickWrapper = f.singleCol(args.flickWrapper, self);
	var flickCol = f.singleCol(args.flickCol, self);
	
	flickWrapper.css({ width: flickContainer.width() + 'px' });
	flickCol.css({ width: flickContainer.width() + 'px' });
	
	f.p.nowPage = (typeof(args.flickCur) != 'undefined')? args.flickCur: 0;
	
	/*------------------------------------------------------------
	* Module
	*/
	//iOS
	var flickWrapperCssTranslate3d = function(pos){
		flickWrapper.css({
			webkitTransition: 'all 0.6s',
			webkitTransform: 'translate3d('+ pos +'px, 0, 0)'
		});
	}
	//Android
	var flickWrapperCssTranslate = function(pos){
		flickWrapper.css({
			webkitTransition: 'all 0.6s',
			webkitTransform: 'translate('+ pos +'px, 0)'
		});
	}
	//PC
	var flickWrapperCssPostion = function(pos){
		flickWrapper.stop().animate({
			left: f.p.nowPosition +'px'
		}, 500);
	}
	
	//All
	var flickWrapperMoveAll = function(pos){
		//iPhone, iPad, iPod
		if( f.ua.iPhone || f.ua.iPad || f.ua.iPod ){
			flickWrapperCssTranslate3d(pos);
		}
		//Android
		else if( f.ua.Android ){
			flickWrapperCssTranslate(pos);
		}
		//PC
		else{
			flickWrapperCssPostion(pos);
		}
	}
	
	/**------------------------------------------------------------
	* 初期化
	*/
	f.p.wrapperWidth = (f.p.totalLength = flickCol.length)*(f.p.colWidth = flickCol.width());
	f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
	
	flickWrapperMoveAll(f.p.nowPosition);
	
	//lamp
	f.flickLamp = function(count){
		var span = $('<span>');
		span.addClass('lamp_def lamp_'+ count);
		span.html('');
		return span
	}
	f.flickLampCol = [];
	if( typeof(args.flickLamp) != 'undefined' ){
		
		//lamp position center
		if( f.ua.iPad ){
			var flickLamp = f.singleCol(args.flickLamp, self).html('').css({
				textAlign: 'center',
				width: flickContainer.width() + 'px'
			});
		}
		else{
			var flickLamp = f.singleCol(args.flickLamp, self).html('').css({
				textAlign: 'center'
			});
		}
		
		//lamp append
		for( var i = 0; i < f.p.totalLength; i++ ){
			f.flickLampCol[i] = new f.flickLamp(i);
			if( i === f.p.nowPage ){
				f.flickLampCol[i].addClass('lamp_cur');
			}
			flickLamp.append( f.flickLampCol[i] );
		}
	}
	
	//option
	var autoChange = false;
	autoChange = (args.autoChange)? args.autoChange: false;
	
	//Timer
	var mintime = args.autoTimer;
	var autoChangeFunc = function(){
		if( f.p.autoChangeFlag ){
			f.p.nowPosition = f.p.movePosition;
			
			if( typeof(args.flickBtn) != 'undefined' ){
				f.flickBtnNext.removeClass('btnFalse').addClass('pointer');
				f.flickBtnBack.removeClass('btnFalse').addClass('pointer');
			}
			
			//進む
			if( f.p.nowPage < f.p.totalLength ){
				if( f.p.nowPage < (f.p.totalLength - 1) ){
					f.p.nowPage++;
					f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
					
					flickWrapperMoveAll(f.p.nowPosition);
					
					if( typeof(args.flickBtn) != 'undefined' && f.p.nowPage == (f.p.totalLength - 1) ){
						f.flickBtnNext.removeClass('pointer').addClass('btnFalse');
					}
				}
				else if( f.p.nowPage === (f.p.totalLength - 1) ){
					//最小値までフリックした場合、0地点に戻す
					f.p.nowPage = 0;
					f.p.nowPosition = 0;
					
					flickWrapperMoveAll(f.p.nowPosition);
					
					if( typeof(args.flickBtn) != 'undefined' ){
						f.flickBtnBack.removeClass('pointer').addClass('btnFalse');
					}
				}
			}
			if( typeof(args.flickLamp) != 'undefined' && typeof(f.flickLampCol[f.p.nowPage]) != 'undefined' ){
				//lamp削除
				for( var i = 0; i < f.p.totalLength; i++ ){
					f.flickLampCol[i].removeClass('lamp_cur');
				}
				//カレント追加
				f.flickLampCol[f.p.nowPage].addClass('lamp_cur');
			}
		}
	}
	
	var autoWidthChange = function(){
		if( f.p.autoChangeFlag ){
			flickWrapper.css({ width: flickContainer.width() + 'px' });
			flickCol.css({ width: flickContainer.width() + 'px' });
			if( typeof(args.flickBtn) != 'undefined' ){
				flickBtn.css({ width: flickContainer.width() + 'px' });
			}
			f.p.wrapperWidth = (f.p.totalLength = flickCol.length)*(f.p.colWidth = flickCol.width());
			flickWrapper.css({
				width: f.p.wrapperWidth
			});
			f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
			flickWrapperMoveAll(f.p.nowPosition);
		}
	}
	
	//flickBtn
	f.flickBtn = function(className, value){
		var span = $('<span>');
		span.addClass( 'flick_btn_' + className );
		span.html(value);
		return span;
	}
	//event 進む
	var autoChangeFlagTrue = function(){ f.p.autoChangeFlag = true; }
	
	if( typeof(args.flickBtn) != 'undefined' ){
		//new btn
		var flickBtn = f.singleCol(args.flickBtn, self).html('').css({ width: flickContainer.width() + 'px' });
		var flag = true;
		
		var fbn = (typeof(args.flickBtnNextName) != 'undefined')? args.flickBtnNextName: 'next';
		var fbb = (typeof(args.flickBtnBackName) != 'undefined')? args.flickBtnBackName: 'back';
		
		//append
		f.flickBtnNext = new f.flickBtn('next', fbn);
		f.flickBtnBack = new f.flickBtn('back', fbb);
		if( f.p.nowPage === 0 ){
			f.flickBtnNext.addClass('pointer');
			f.flickBtnBack.addClass('btnFalse');
		}
		else if( f.p.nowPage === (f.p.totalLength - 1) ){
			f.flickBtnNext.addClass('btnFalse');
			f.flickBtnBack.addClass('pointer');
		}
		else{
			f.flickBtnNext.addClass('pointer');
			f.flickBtnBack.addClass('pointer');
		}
		flickBtn.append( f.flickBtnNext );
		flickBtn.append( f.flickBtnBack );
		
		f.flickBtnNext.bind('click', function(){
			f.p.autoChangeFlag = false;
			clearInterval(f.p.Timer);
			clearInterval(f.p.AndroidTimer);
			
			flag = false;
			if( f.p.nowPage < (f.p.totalLength - 1) ){
				f.p.nowPage++;
				f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
				
				flickWrapperMoveAll(f.p.nowPosition);
				
				f.flickBtnBack.removeClass('btnFalse').addClass('pointer');
				if( f.p.nowPage == (f.p.totalLength - 1) ){
					f.flickBtnNext.removeClass('pointer').addClass('btnFalse');
				}
				if( typeof(args.flickLamp) != 'undefined' && typeof(f.flickLampCol[f.p.nowPage]) != 'undefined' ){
					//lamp削除
					for( var i = 0; i < f.p.totalLength; i++ ){
						f.flickLampCol[i].removeClass('lamp_cur');
					}
					//カレント追加
					f.flickLampCol[f.p.nowPage].addClass('lamp_cur');
				}
			}
			flag = true;
			autoChangeFlagTrue();
			if( autoChange ){
				f.p.Timer = setInterval(autoChangeFunc, mintime);
			}
			f.p.AndroidTimer = setInterval(autoWidthChange, 1000);
		});
		
		//event 戻る
		f.flickBtnBack.bind('click', function(){
			f.p.autoChangeFlag = false;
			clearInterval(f.p.Timer);
			clearInterval(f.p.AndroidTimer);
			
			flag = false;
			if( f.p.nowPage > 0 ){
				f.p.nowPage--;
				f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
				
				flickWrapperMoveAll(f.p.nowPosition);
				
				f.flickBtnNext.removeClass('btnFalse').addClass('pointer');
				if( f.p.nowPage === 0 ){
					f.flickBtnBack.removeClass('pointer').addClass('btnFalse');
				}
				if( typeof(args.flickLamp) != 'undefined' && typeof(f.flickLampCol[f.p.nowPage]) != 'undefined' ){
					//lamp削除
					for( var i = 0; i < f.p.totalLength; i++ ){
						f.flickLampCol[i].removeClass('lamp_cur');
					}
					//カレント追加
					f.flickLampCol[f.p.nowPage].addClass('lamp_cur');
				}
			}
			flag = true;
			autoChangeFlagTrue();
			if( autoChange ){
				f.p.Timer = setInterval(autoChangeFunc, mintime);
			}
			f.p.AndroidTimer = setInterval(autoWidthChange, 1000);
		});
	}
	var androidua = navigator.userAgent;
	function touchHandler(event){
		if( event.type === 'touchstart' ){
			f.p.autoChangeFlag = false;
			f.p.touchPositionX = event.originalEvent.touches[0].clientX;
			f.p.touchPositionY = event.originalEvent.touches[0].clientY;
			clearInterval(f.p.Timer);
			clearInterval(f.p.AndroidTimer);
		}
		if( event.type === 'touchmove' ){
			f.p.positionIntX = f.p.touchPositionX - event.originalEvent.touches[0].clientX;
			f.p.positionIntY = f.p.touchPositionY - event.originalEvent.touches[0].clientY;
			$('#a').html(f.p.positionIntX);
			if( Math.abs(f.p.positionIntX) > 5 ){
				event.preventDefault();
				f.p.flag = true;
				f.p.movePosition = f.p.nowPosition - f.p.positionIntX;
				
				flickWrapper.css({
					webkitTransition: 'none',
					webkitTransform: 'translate3d('+ f.p.movePosition +'px, 0, 0)'
				});
			}
		}
		if( event.type === 'touchend' ){
			if( f.p.flag ){
				f.p.nowPosition = f.p.movePosition;
				
				if( typeof(args.flickBtn) != 'undefined' ){
					f.flickBtnNext.removeClass('btnFalse').addClass('pointer');
					f.flickBtnBack.removeClass('btnFalse').addClass('pointer');
				}
				
				//進む
				if( f.p.positionIntX > 50 ){
					if( f.p.nowPage < (f.p.totalLength - 1) ){
						f.p.nowPage++;
						f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
						flickWrapperMoveAll(f.p.nowPosition);
						if( typeof(args.flickBtn) != 'undefined' && f.p.nowPage == (f.p.totalLength - 1) ){
							f.flickBtnNext.removeClass('pointer').addClass('btnFalse');
						}
					}
					else{
						//最大値までフリックした場合、最大値で止まる
						f.p.nowPosition = -(f.p.colWidth * (f.p.totalLength - 1));
						flickWrapperMoveAll(f.p.nowPosition);
						if( typeof(args.flickBtn) != 'undefined' && f.p.nowPage == (f.p.totalLength - 1) ){
							f.flickBtnNext.removeClass('pointer').addClass('btnFalse');
						}
					}
				}
				//戻る
				else if( f.p.positionIntX < -50 ){
					if( f.p.nowPage <= 0 ){
						//最小値までフリックした場合、0地点に戻す
						f.p.nowPage = 0;
						f.p.nowPosition = 0;
						flickWrapperMoveAll(f.p.nowPosition);
						if( typeof(args.flickBtn) != 'undefined' ){
							f.flickBtnBack.removeClass('pointer').addClass('btnFalse');
						}
					}
					else{
						f.p.nowPage--;
						f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
						flickWrapperMoveAll(f.p.nowPosition);
						if( typeof(args.flickBtn) != 'undefined' && f.p.nowPage === 0 ){
							f.flickBtnBack.removeClass('pointer').addClass('btnFalse');
						}
					}
				}
				else {
					f.p.nowPosition = -(f.p.colWidth * f.p.nowPage);
					flickWrapperMoveAll(f.p.nowPosition);
				}
				if( typeof(args.flickLamp) != 'undefined' && typeof(f.flickLampCol[f.p.nowPage]) != 'undefined' ){
					//lamp削除
					for( var i = 0; i < f.p.totalLength; i++ ){
						f.flickLampCol[i].removeClass('lamp_cur');
					}
					//カレント追加
					f.flickLampCol[f.p.nowPage].addClass('lamp_cur');
				}
				autoChangeFlagTrue();
				if( autoChange ){
					f.p.Timer = setInterval(autoChangeFunc, mintime);
				}
				f.p.AndroidTimer = setInterval(autoWidthChange, 1000);
			}
			f.p.flag = false;
		}
		if( event.type === 'touchcancel' ){
			autoChangeFlagTrue();
			if( autoChange ){
				f.p.Timer = setInterval(autoChangeFunc, mintime);
			}
			f.p.AndroidTimer = setInterval(autoWidthChange, 1000);
		}
	}
	if( !androidua.match(/Android 1\.5/) || !androidua.match(/Android 1\.6/) ){
		flickWrapper.bind('touchstart touchmove touchend touchcancel', touchHandler);
	}
	if( autoChange ){
		f.p.Timer = setInterval(autoChangeFunc, mintime);
	}
	//------------------------------------------------------------
	// どの横幅からでも1秒間1回横幅を取得して再計算させる。
	// これによりどの端末からでもリキッド状態のカルーセルフリックが可能となる。
	//
	f.p.AndroidTimer = setInterval(autoWidthChange, 1000);
}


//============================================================
})(jQuery);