/**
 * Theme Changer
 * XCL version 2.3.1
 * How To Use
 *
 * 1) Add the script to the head of your theme.html
 *
 *    <script type="module" src="<{$xoops_url}>/common/js/ThemeManager.js"></script>
 *
 * 2) Add [data-theme="dark"] after :root { } in your theme style.css file
 *
 *      :root[data-theme="dark"] {
            --ui-color: #e7e7e7;
            --ui-link-color: #face74;
        }
 *
 * 3) Add a <button id="themeToggle"> to your theme.html
 *
        <button id="themeToggle">(icon or text defined in line 74)</button>

        <script type="module">
            import {ThemeManager} from '<{$xoops_url}>/common/js/ThemeManager.js';
            new ThemeManager(document.getElementById('themeToggle'));
        </script>

    ⚠️ USE CSS.escape if IDs are numbers or special characters
    https://developer.mozilla.org/en-US/docs/Web/API/CSS/escape

        const theId = "1";
        const el = document.querySelector(`#${CSS.escape(theId)}`);
*/
export class ThemeManager {
    'use-strict';
    /**
     * Constructs object of class ThemeManager
     * @param {string} themeToggle - the html element to change the theme mode
     * @param {string} theme - initial theme mode light and vice versa for dark
     */
    constructor(themeToggle, theme = 'light') {
        //get the theme toggle DOM node
        if (!themeToggle) {
            console.error(`A valid DOM element must be passed as the themeToggle. You passed ${themeToggle}`);
            return;
        }
        this.themeToggle = document.querySelector('#themeToggle'); /* ⚠️ USE CSS.escape if number or special character */
        this.themeToggle.addEventListener('click', () => this.switchTheme());

        //get the initial theme and apply the color-scheme
        this.theme = theme;
        if (localStorage.getItem('data-theme')) {
            if (localStorage.getItem('data-theme') === (theme === 'light' ? 'dark' : 'light')) {
                this.theme = (theme === 'light' ? 'dark' : 'light');
            }
        }
        else if (window.matchMedia(`(prefers-color-scheme: ${(theme === 'light' ? 'dark' : 'light')})`).matches) {
            this.theme = (theme === 'light' ? 'dark' : 'light');
        }
        this._applyTheme();

        //add the listener to change the theme color-scheme on O.S. change
        window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (e) => {
            this.theme = (e.matches ? 'light' : 'dark');
            this._applyTheme();
        });

    }

    /**
     * Private _applyTheme sets documentElement and localStorage 'data-theme' attribute
     * Icon Moon ? : &#127767;
     * Icon Sun ☼ : &#x263C; or &#9728; : ☀
     */
    _applyTheme = () => {
        this.themeToggle.innerHTML = (this.theme === 'light' ? '<i class="i-dark"></i>' : '<i class="i-light"></i>');
        document.documentElement.setAttribute('data-theme', this.theme);
        localStorage.setItem('data-theme', this.theme);
    }

    /**
     * switchTheme toggles the theme color-scheme on themeToggle event: 'click'
     */
    switchTheme = () => {
        this.theme = (this.theme === 'light' ? 'dark' : 'light');
        this._applyTheme();
    }
}
