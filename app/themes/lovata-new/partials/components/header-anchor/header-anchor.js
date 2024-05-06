export default new class headerAnchor {
  constructor () {
    this.anchors = null;
    this.init();
  }

  init () {
    document.addEventListener("DOMContentLoaded", () => {
      this.setAnchors();
      this.setInitClick();
    });
  }

  setInitClick () {
    for (let anchor of this.anchors) {
      anchor.addEventListener("click", (e) => {
        e.preventDefault();
        this.setEventScroll(anchor);

      });
    }
  }

  setEventScroll (anchor) {
    const blockId = anchor.getAttribute("href").replace("#", "");
    if (document.getElementById(blockId) == null) {
      console.log(`not find block with id '${blockId}'`);
      return false;
    }
    document.getElementById(blockId).scrollIntoView({
      behavior: "smooth",
      block: "start"
    });
  }

  setAnchors () {
    this.anchors = document.querySelectorAll("a[href*=\"#\"]");
  }
};
