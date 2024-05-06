export default new class Accordion {
  constructor () {
    this.init();
  }

  init () {
    document.addEventListener("DOMContentLoaded", () => {
      const accordions = document.querySelectorAll(".accordion");
      if (!accordions) return;
      this.initAccordion(accordions)
    });
  }
  initAccordion(accordions) {

    accordions.forEach((accordion) => {
      const intro = accordion.querySelector(".js-accordion-intro");
      const content = accordion.querySelector(".js-accordion-content");

      intro.onclick = () => {
        if (content.style.maxHeight) {
          this.closeAccordion(accordion);
        } else {
          accordions.forEach((accordion) => this.closeAccordion(accordion));
          this.openAccordion(accordion);
        }
      };
    });
  }
  openAccordion(accordion) {
    const content = accordion.querySelector(".js-accordion-content");
    accordion.classList.add("accordion__active");
    content.style.maxHeight = content.scrollHeight + "px";
  }

  closeAccordion (accordion) {
    const content = accordion.querySelector(".js-accordion-content");
    accordion.classList.remove("accordion__active");
    content.style.maxHeight = null;
  };
};
