export default new class ToggleTheme {
  constructor () {
    this.toggleTheme = ".js-toggle-theme";
    this.handler();
  }
  setActiveTheme (activeTheme) {
    document.querySelector(".js-active-theme").dataset.activetheme = activeTheme;
    let html = document.querySelector("html");
    localStorage.setItem("theme", activeTheme);
    html.classList.remove(activeTheme == "dark" ? "light" : "dark");
    html.classList.add(activeTheme);
  }

  setAnimateClass (theme) {
    const elemActive = document.querySelector(".js-active-theme");
    if (theme == "dark") {
      elemActive.classList.add("a-moon");
      elemActive.classList.remove("a-light");

    } else {
      elemActive.classList.add("a-light");
      elemActive.classList.remove("a-moon");
    }
  }

  setEventActiveTheme (button) {
    const theme = button.dataset.theme;
    if (!document.querySelector("html").classList.contains(theme)) {
      this.setActiveTheme(button.dataset.theme);
    }
  }

  handler () {
    document.addEventListener("DOMContentLoaded", () => {
      const toggles = document.querySelectorAll(this.toggleTheme);
      if (localStorage.getItem("theme")) this.setActiveTheme(localStorage.getItem("theme"));
      toggles.forEach(button => {
        button.addEventListener("click", (el) => {
          this.setEventActiveTheme(button);
          this.setAnimateClass(button.dataset.theme);
        });
      });
    });
  }
}();
