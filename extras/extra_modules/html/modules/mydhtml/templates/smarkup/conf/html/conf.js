SMarkUp.conf.html = {
	onCtrlEnter: {open: "\n<br/>"},
	onShiftEnter: {open: "\n<p>", close: "</p>"},
	preview: {
		template: '~/templates/preview.html',
		autoRefresh: false
	},
	markup: [
		SMarkUp.addons.searchAndReplace,
		SMarkUp.addons.preview,
		{separator: true},
		{
			name: 'h1',
			title: 'Heading 1',
			open: '<h1>',
			close: '</h1>',
			prepend: "\n",
			placeholder: 'Heading 1'
		},
		{
			name: 'h2',
			title: 'Heading 2',
			open: '<h2>',
			close: '</h2>',
			prepend: "\n"
		},
		{
			name: 'h3',
			title: 'Heading 3',
			open: '<h3>',
			close: '</h3>',
			prepend: "\n"
		},
		{
			name: 'h4',
			title: 'Heading 4',
			open: '<h4>',
			close: '</h4>',
			prepend: "\n"
		},
		{
			name: 'h5',
			title: 'Heading 5',
			open: '<h5>',
			close: '</h5>',
			prepend: "\n"
		},
		{
			name: 'h6',
			title: 'Heading 6',
			open: '<h6>',
			close: '</h6>',
			prepend: "\n"
		},
		{
			name: 'p',
			title: 'Paragraph',
			open: '<p>',
			close: '</p>',
			prepend: "\n",
			wrapSelection: "\n{selection}\n"
		},
		{
			name: 'blockquote',
			title: 'Blockquote',
			open: '<blockquote{attributes}>',
			close: '</blockquote>',
			prepend: "\n",
			wrapSelection: "\n{selection}\n",
			attributes: [
				{
					type: 'text',
					name: 'cite',
					label: 'Cite'
				}
			]
		},
		{
			separator: true
		},
		{
			name: 'strong',
			title: 'Bold',
			open: '<strong>',
			close: '</strong>',
			key: 'B',
			alt: {
				open: '<b>',
				close: '</b>'
			}
		},
		{
			name: 'em',
			title: 'Italic',
			key: 'I',
			open: '<em>',
			close: '</em>'
		},
		{
			name: 'del',
			title: 'Strike Through',
			open: '<del>',
			close: '</del>'
		},
		{
			separator: true	
		},
		{
			name: 'ul',
			title: 'Unordered List',
			open: '<ul>',
			close: '</ul>',
			prepend: "\n",
			wrapSelection: "\n   <li>{selection}</li>\n",
			wrapMultiline: true
		},
		{
			name: 'ol',
			title: 'Ordered List',
			open: '<ol>',
			close: '</ol>',
			prepend: "\n",
			wrapSelection: "\n   <li>{selection}</li>\n",
			wrapMultiline: true
		},
		{
			name: 'li',
			title: 'List Item',
			open: '<li>',
			close: '</li>',
			prepend: "\n   ",
			wrapMultiline: true
		},
		{
			separator: true	
		},
		{
			open: '<img{attributes}/>',
			name: 'img',
			title: 'Image',
			attributes: [
				{
					type: 'text',
					name: 'src',
					label: 'Image URL'
				},
				{
					type: 'text',
					name: 'width',
					label: 'Width'
				},
				{
					type: 'text',
					name: 'height',
					label: 'Height'
				},
				{
					type: 'text',
					name: 'alt',
					label: 'Alt'
				},
				{
					type: 'text',
					name: 'title',
					label: 'Title'
				},
				{
					type: 'list',
					name: 'align',
					label: 'Align',
					list: {left: 'Left', middle: 'Middle', right: 'Right'}
				}
			]
		},
		{
			open: '<a{attributes}>',
			close: '</a>',
			name: 'a',
			title: 'Link',
			attributes: [
				{
					name: 'href',
					type: 'text',
					label: 'Link URL'
				},
				{
					name: 'target',
					type: 'list',
					label: 'Target',
					list: {'': '', '_self': '_self', '_parent': '_parent', '_blank': '_blank'}
				},
				{
					name: 'title',
					type: 'text',
					label: 'Title'
				},
				{
					name: 'class',
					type: 'text',
					label: 'Class'
				}			
			]
		},
		{separator: true},
		SMarkUp.addons.help
	]
};