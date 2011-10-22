SMarkUp.conf.css = {
	markup: [
		SMarkUp.addons.searchAndReplace,
		{separator: true},
		{
			name: 'class-name',
			title: 'Class Name',
			prepend: "\n",
			open: "{class} {\n",
			wrapSelection: "   {selection}\n",
			wrapMultiline: true,
			close: "}",
			attributes: [
				{
					type: 'text',
					name: 'class',
					label: 'Class Name'
				}
			]
		},
		{separator: true},
		{
			name: 'strong',
			title: 'Bold',
			open: 'font-weight: ',
			placeholder: 'bold',
			close: ';'
		},
		{
			name: 'em',
			title: 'Italic',
			open: 'font-style: ',
			placeholder: 'italic',
			close: ';'
		},
		{
			name: 'del',
			title: 'Strike Through',
			open: 'text-decoration: ',
			placeholder: 'line-through',
			close: ';'
		},
		{separator: true},
		{
			name: 'font',
			title: 'Font',
			open: 'font: ',
			placeholder: '{weight} {style} {size} {family}',
			close: ';',
			attributes: [
				{
					name: 'weight',
					type: 'list',
					label: 'Font Weight',
					list: {'': '', bold: 'bold', bolder: 'bolder'}
				},
				{
					name: 'style',
					type: 'list',
					label: 'Font Style',
					list: {normal: 'normal', italic: 'italic'}
				},
				{
					name: 'size',
					type: 'text',
					label: 'Font Size'
				},
				{
					name: 'family',
					type: 'text',
					label: 'Font Family'
				}
			]
		},
		{
			name: 'uppercase',
			title: 'Upper Case',
			open: 'text-transform: ',
			placeholder: 'uppercase',
			close: ';'
		},
		{
			name: 'lowercase',
			title: 'Lower Case',
			open: 'text-transform: ',
			placeholder: 'lowercase',
			close: ';'
		},
		{separator: true},
		{
			name: 'text-indent',
			title: 'Text Indent',
			open: 'text-indent: ',
			placeholder: '5px',
			close: ';'
		},
		{
			name: 'letter-spacing',
			title: 'Letter Spacing',
			open: 'letter-spacing: ',
			placeholder: '5px',
			close: ';'
		},
		{
			name: 'line-height',
			title: 'Line Height',
			open: 'line-height: ',
			placeholder: '1.5em',
			close: ';'
		},
		{separator: true},
		{
			name: 'aligns',
			title: 'Alignments',
			dropDownMenu: [
				{
					name: 'align-center',
					title: 'Center',
					open: 'text-align: ',
					placeholder: 'center',
					close: ';'
				},
				{
					name: 'align-justify',
					title: 'Justify',
					open: 'text-align: ',
					placeholder: 'justify',
					close: ';'
				},
				{
					name: 'align-left',
					title: 'Left',
					open: 'text-align: ',
					placeholder: 'left',
					close: ';'
				},
				{
					name: 'align-right',
					title: 'Right',
					open: 'text-align: ',
					placeholder: 'right',
					close: ';'
				}
			]
		},
		{
			name: 'paddings',
			title: 'Paddings &amp; Margins',
			dropDownMenu: [
				{
					name: 'padding-top',
					title: 'Padding Top',
					open: 'padding-top: ',
					placeholder: '4px',
					close: ';',
					alt: {
						open: 'margin-top'
					}
				},
				{
					name: 'padding-right',
					title: 'Padding Right',
					open: 'padding-right: ',
					placeholder: '4px',
					close: ';',
					alt: {
						open: 'margin-right'
					}
				},
				{
					name: 'padding-bottom',
					title: 'Padding Bottom',
					open: 'padding-bottom: ',
					placeholder: '4px',
					close: ';',
					alt: {
						open: 'margin-bottom'
					}
				},
				{
					name: 'padding-left',
					title: 'Padding Left',
					open: 'padding-left: ',
					placeholder: '4px',
					close: ';',
					alt: {
						open: 'margin-left'
					}
				}
			]
		},
		{separator: true},
		{
			name: 'background',
			title: 'Background Image',
			open: 'background: ',
			placeholder: '{color} url({url}) {repeat} {top} {left}',
			close: ';',
			attributes: [
				{
					type: 'text',
					name: 'color',
					label: 'Background Color'
				},
				{
					type: 'text',
					name: 'url',
					label: 'Image URL'
				},
				{
					type: 'list',
					name: 'repeat',
					label: 'Repeat',
					list: {'repeat': 'Repeat', 'no-repeat': 'No Repeat', 'repeat-x': 'Repeat X', 'repeat-y': 'Repeat Y'}
				},
				{
					type: 'text',
					name: 'top',
					label: 'Position Top'
				},
				{
					type: 'text',
					name: 'left',
					label: 'Position Left'
				}
			]
		},
		SMarkUp.addons.colorPicker
	]
};