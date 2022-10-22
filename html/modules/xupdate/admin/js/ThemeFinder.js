/**
 * @constructor
 */
var Legacy_ThemeFinder = function() {
};

Legacy_ThemeFinder.prototype = {

	/**
	 * @enum
	 */
	outlet: {
		canvas: '#themeFinder',
		iframe: '#themeFinderIframe',
		form:   '#themeFinderForm'
	},

	/**
	 * メイン処理
	 * @public
	 */
	run: function() {
		this._showIframe();
		this._submitForm();
	},

	/**
	 * <iframe />を表示する
	 * @protected
	 */
	_showIframe: function() {
		$(this.outlet.iframe).show();
	},

	/**
	 * サイトの情報をテーマファインダにPOSTする
	 * @protected
	 */
	_submitForm: function() {
		$(this.outlet.form).submit();
		$(this.outlet.form).remove();
	}
};
