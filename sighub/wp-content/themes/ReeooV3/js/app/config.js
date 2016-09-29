require.config({
	baseUrl: 'resource/js/app',
	paths: {
		'css': '../lib/css.min',
		'jquery': '../lib/jquery-1.11.1.min',
		'jquery.hammer': '../lib/jquery.hammer-full.min',
		'angular': '../lib/angular.min',
		'bootstrap': '../lib/bootstrap.min',
		'underscore': '../lib/underscore-min',
		'iscroll': '../lib/iscroll-lite',
		'moment': '../lib/moment',
		'filestyle': '../lib/bootstrap-filestyle.min',
		'daterangepicker': '../../components/daterangepicker/daterangepicker',
		'datetimepicker': '../../components/datetimepicker/bootstrap-datetimepicker.min',
		'map': 'http://api.map.baidu.com/getscript?v=2.0&ak=F51571495f717ff1194de02366bb8da9&services=&t=20140530104353',
		'editor': '../../components/tinymce/tinymce.min',
		'kindeditor':'../../components/kindeditor/lang/zh_CN',
		'kindeditor.main':'../../components/kindeditor/kindeditor-min',
		'WeixinApi': '../lib/WeixinApi'
	},
	shim:{
		'jquery.hammer': {
			exports: "$",
			deps: ['jquery']
		},
		'angular': {
			exports: 'angular',
			deps: ['jquery']
		},
		'bootstrap': {
			exports: "$",
			deps: ['jquery']
		},
		'iscroll': {
			exports: "IScroll"
		},
		'filestyle': {
			exports: '$',
			deps: ['bootstrap']
		},
		'daterangepicker': {
			exports: '$',
			deps: ['bootstrap', 'moment', 'css!../../components/daterangepicker/daterangepicker.css']
		},
		'datetimepicker': {
			exports: '$',
			deps: ['bootstrap', 'css!../../components/datetimepicker/bootstrap-datetimepicker.min.css']
		},
		'map': {
			exports: 'BMap'
		},
		'kindeditor': {
			deps: ['kindeditor.main', 'css!../../components/kindeditor/themes/default/default.css']
		}
	}
});