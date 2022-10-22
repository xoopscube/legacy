/**
 * UI Options
 * Commands, customize toolbar, contextmenu, theme
 */

var xelfinderUiOptions = {
	ui : ['toolbar', 'places', 'tree', 'path', 'stat'],
	uiOptions : {
		// toolbar configuration
		toolbar : [
			['back', 'forward'],
			// ['reload'],
			// ['home', 'up'],
			//['mkdir', 'mkfile', 'upload'],
			['open', 'download', 'getfile'],
			['info'],
			['quicklook'],
			['copy', 'cut', 'paste'],
			//['rm'],
			['duplicate', 'rename', 'edit', 'resize'],
			['extract', 'archive'],
			['search'],
			['view'],
			['help']
		],
		toolbarExtra : {
			// also displays the text label on the button (true / false / 'none')
			displayTextLabel: 'none',
			// show Preference button into contextmenu of the toolbar (true / false)
			preferenceInContextmenu: false
		},
		contextmenu: {
			navbar: ['open', '|', 'copy', '|', 'rm', '|', 'info'],
			cwd: ['reload', 'back', '|', 'open', 'copy', '|', 'info'],
			files: [
				'open', 'quicklook', 'sharefolder', '|', 'download', '|', 'copy', 'cut', 'paste', '|', 'info'
			]
		},
		// directories tree options
		tree : {
			// expand current root on init
			openRootOnLoad : true,
			// auto load current dir parents
			syncTree : true
		},
		cwd : {
			listView : {
				columns : ['perm', 'date', 'size', 'kind', 'owner'],
			}
		}
	},

	commandsOptions : {
		edit : {
			dialogWidth: '80%',
			mkfileHideMimes: ['image/x-sketch', 'image/x-adobe-dng', 'image/x-portable-pixmap', 'image/vnd.adobe.photoshop', 'image/x-pixlr-data', 'image/webp', 'image/x-ms-bmp']
		},
		help : {
			// Tabs to show
			view : ['about', 'help', 'integrations'],
			// HTML source URL of the heip tab
			helpSource : ''
		},
		preference : {
			// dialog width
			width: 600,
			// dialog height
			height: 400,
			// tabs setting see preference.js : build()
			categories: {
				language: ['language'],
				theme : ['theme'],
			},
			// preference setting see preference.js : build()
			prefs: null,
			// language setting  see preference.js : build()
			langs: ['en', 'fr', 'ja'],
			// Command list of action when select file
			// Array value are 'Command Name' or 'Command Name1/CommandName2...'
			selectActions : ['open', 'edit/download', 'resize/edit/download', 'download', 'quicklook'],
			all: false
		},
	},
	themes : {
		'Dark Slim'     : 'themes/dark-slim/css/theme.css',
		// 'material'      : 'https://nao-pon.github.io/elfinder-theme-manifests/material-default.json',
		// 'material-gray' : 'https://nao-pon.github.io/elfinder-theme-manifests/material-gray.json',
		// 'material-light': 'https://nao-pon.github.io/elfinder-theme-manifests/material-light.json',
		// 'bootstrap'     : 'https://nao-pon.github.io/elfinder-theme-manifests/bootstrap.json',
		// 'moono'         : 'https://nao-pon.github.io/elfinder-theme-manifests/moono.json',
		// 'win10'         : 'https://nao-pon.github.io/elfinder-theme-manifests/win10.json'
	},
	theme : 'dark-slim'
};
