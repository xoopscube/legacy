var xelfinderUiOptions = {
	ui : ['toolbar', 'places', 'tree', 'path', 'stat'],
	uiOptions : {
		cwd : {
			listView : {
				columns : ['perm', 'date', 'size', 'kind', 'owner'],
			}
		}
	},
	commandsOptions : {
		edit : {
			dialogWidth: '80%'
		}
	},
	themes : {
        'Dark Slim'     : 'themes/dark-slim/css/theme.min.css',
        'Material'      : 'themes/Material/css/theme.min.css',
        'Material-Grey' : 'themes/Material-Grey/css/theme.min.css',
        'Material-Light' : 'themes/Material-Grey/css/theme.min.css',
        'Moono'         : 'themes/moono/css/theme.min.css',
        'Windows-10'    : 'themes/windows-10/css/theme.min.css',
		// 'dark-slim'     : 'https://nao-pon.github.io/elfinder-theme-manifests/dark-slim.json',
		// 'material'      : 'https://nao-pon.github.io/elfinder-theme-manifests/material-default.json',
		// 'material-gray' : 'https://nao-pon.github.io/elfinder-theme-manifests/material-gray.json',
		// 'material-light': 'https://nao-pon.github.io/elfinder-theme-manifests/material-light.json',
		// 'bootstrap'     : 'https://nao-pon.github.io/elfinder-theme-manifests/bootstrap.json',
		// 'moono'         : 'https://nao-pon.github.io/elfinder-theme-manifests/moono.json',
		// 'win10'         : 'https://nao-pon.github.io/elfinder-theme-manifests/win10.json'
	},
	theme : 'default'
};
