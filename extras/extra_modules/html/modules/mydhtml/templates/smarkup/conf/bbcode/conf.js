SMarkUp.conf.bbcode = {
	markup: [
		SMarkUp.addons.searchAndReplace,
		{separator: true},
		{
			name: 'bold',
			className: 'strong',
			title: 'Bold',
			open: '[b]',
			key: 'B',
			close: '[/b]'
		},
		{
			name: 'italic',
			className: 'em',
			title: 'Italic',
			open: '[i]',
			close: '[/i]'
		},
		{
			name: 'underline',
			title: 'Underline',
			open: '[u]',
			close: '[/u]'
		},
		{
			name: 'strikethrough',
			title: 'Strike Through',
			className: 'strikethrough',
			open: '[d]',
			close: '[/d]'
		},
		{separator: true},
		{
			name: 'img',
			title: 'Image',
			attributes: [
				{
					type: 'text',
					name: 'url',
					label: 'Image URL'
				}
			],
			dropDownMenu: [
				{
					name: 'none',
					title: '',
					open: '[img]',
					close: '[/img]'
				},
				{
					name: 'left',
					title: 'left',
					open: '[img align=left]',
					close: '[/img]'
				},
				{
					name: 'right',
					title: 'right',
					open: '[img align=right]',
					close: '[/img]'
				}
			]
		},
		{
			name: 'url',
			className: 'a',
			title: 'Link',
			open: '[url={url}]',
			close: '[/url]',
			attributes: [
				{
					type: 'text',
					name: 'url',
					label: 'Link URL'
				}
			]
		},
		{
			name: 'email',
			className: 'email',
			title: 'Email',
			open: '[email]',
			close: '[/email]'
		},
		{separator: true},
		{
			name: 'size',
			className: 'size',
			title: 'Size',
			dropDownMenu: [
				{
					name: 'xx-large',
					className: 'size',
					title: 'XX-Large',
					open: '[size=xx-large]',
					close: '[/size]'
				},
				{
					name: 'x-large',
					className: 'size',
					title: 'X-Large',
					open: '[size=x-large]',
					close: '[/size]'
				},
				{
					name: 'large',
					className: 'size',
					title: 'Large',
					open: '[size=large]',
					close: '[/size]'
				},
				{
					name: 'medium',
					className: 'size',
					title: 'Medium',
					open: '[size=medium]',
					close: '[/size]'
				},
				{
					name: 'small',
					className: 'size',
					title: 'Small',
					open: '[size=small]',
					close: '[/size]'
				},
				{
					name: 'x-small',
					className: 'size',
					title: 'X-Small',
					open: '[size=x-small]',
					close: '[/size]'
				},
				{
					name: 'xx-small',
					className: 'size',
					title: 'XX-Small',
					open: '[size=xx-small]',
					close: '[/size]'
				}
			]
		},
		{
			name: 'fonts',
			className: 'font',
			title: 'Fonts',
			dropDownMenu: [
				{
					name: 'Arial',
					className: 'font',
					title: 'Arial',
					open: '[font=Arial]',
					close: '[/font]'
				},
				{
					name: 'Courier',
					className: 'font',
					title: 'Courier',
					open: '[font=Courier]',
					close: '[/font]'
				},
				{
					name: 'Georgia',
					className: 'font',
					title: 'Georgia',
					open: '[font=Georgia]',
					close: '[/font]'
				},
				{
					name: 'Helvetica',
					className: 'font',
					title: 'Helvetica',
					open: '[font=Helvetica]',
					close: '[/font]'
				},
				{
					name: 'Impact',
					className: 'font',
					title: 'Impact',
					open: '[font=Impact]',
					close: '[/font]'
				},
				{
					name: 'Verdana',
					className: 'font',
					title: 'Verdana',
					open: '[font=Verdana]',
					close: '[/font]'
				},
			]
		},
		{separator: true},
		{
			name: 'quote',
			className: 'blockquote',
			open: '[quote]',
			close: '[/quote]'
		},
		{
			name: 'code',
			open: '[code]',
			close: '[/code]'
		}
	]
};