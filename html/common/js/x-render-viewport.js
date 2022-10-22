  /** ---------- ---------- ---------- ---------- ---------- Viewport Render Settings
   *
   * Get the value of the current viewport by using the global variable
   * Set the consistent and correct sizing for Viewport units with a
   * CSS custom variable '--vh'
   * @param {HTMLELement} element=document.documentElement
   * @param window.innerHeight
   * @param vh='--vh'
   */

    // Set property vh
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);

    window.addEventListener('resize', () => {

      let vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);

    });

    // Request Full Screen
    function fullScreen() {

      if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
      } else if (document.documentElement.mozRequestFullScreen) {
        document.documentElement.mozRequestFullScreen();
      } else if (document.documentElement.webkitRequestFullscreen) {
        document.documentElement.webkitRequestFullscreen();
      } else if (document.documentElement.msRequestFullscreen) {
        document.documentElement.msRequestFullscreen();
      }

    }

    // Exit Full Screen
    function smallScreen() {

      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }

    }

    // Lock Screen Orientation
    function lockScreen(orientation) {

      fullScreen();
      screen.orientation.lock(orientation);

    }
