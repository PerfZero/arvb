(function () {
  var DESIGN_WIDTH = 1920;
  var MOBILE_DESIGN_WIDTH = 390;
  var MOBILE_BREAKPOINT = 768;

  function scalePage() {
    var vw = window.innerWidth;
    var zoom;
    if (vw > MOBILE_BREAKPOINT) {
      zoom = vw / DESIGN_WIDTH;
    } else {
      zoom = vw / MOBILE_DESIGN_WIDTH;
    }
    document.documentElement.style.zoom = zoom;
    document.documentElement.style.setProperty("--page-zoom", zoom);
  }

  scalePage();
  window.addEventListener("resize", scalePage);
})();
