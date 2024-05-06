import Swiper from "swiper";
import { Pagination } from "swiper/modules";

export default new class portfolioSlider {
  constructor () {
    this.screenSlide = 1024;
    this.screen = null;
    this.slider = null;
    this.init();
  }

  init () {
    document.addEventListener("DOMContentLoaded", () => {
      this.setScreen();
      this.eventResize();
    });
  }

  eventResize () {
    addEventListener("resize", (event) => {
      this.setScreen();
    });
  }

  setScreen () {
    let screenWidth = window.innerWidth >= this.screenSlide ? "desktop" : "mobile";

    if (screenWidth !== this.screen) {
      this.screen = screenWidth;
      this.changeSlider();

    }
  }

  changeSlider () {
    if (this.screen == "mobile") {
      this.slider = this.initSlider();
    } else if (this.slider !== null) {
      this.slider.destroy(true, true);
    }
  }

  initSlider () {
    return new Swiper(document.querySelector(".js-slider-port"), {
      modules: [Pagination],
      spaceBetween: 16,
      slidesPerView: 2.5,
      breakpoints: {
        580: {
          slidesPerView: 2,
        },
        0: {
          slidesPerView: 1
        }
      },
      pagination: {
        el: document.querySelector('.js-slider-portfolio-bullets'),
        type: 'bullets',
        bulletClass: 'slider-bullet__bullet',
        bulletActiveClass: 'slider-bullet--active',
        clickable: true,
        renderBullet: function (index, className) {
          return '<span class="' + className + '" ><span class="slider-bullet__dot">' + "</span></span>";
        },
      },
    });
  }
}();
