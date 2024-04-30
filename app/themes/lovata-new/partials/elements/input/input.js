class FieldInput extends HTMLElement {

  constructor() {
    super();
    this.label = this.querySelector('label')
    this.input = this.querySelector('input')
    this.initInput();

  }
  closeEventBody () {
    this.input.addEventListener('focusout', (event) => {

      if(!this.input.value) this.lowerLabel()
      // const isClickedOutsideMenu = event.target.closest('.js-field-input') === null
      // if (isClickedOutsideMenu && this.input.value ) {
      //   this.lowerLabel()
      // }
    })
  }

  lowerLabel() { // опустить
    this.label.classList.remove("high")
  }
  raiseLabel() { // поднять
    this.label.classList.add("high")
  }
  toggleInput() {
    this.addEventListener("click", () => {
      this.raiseLabel()
    })
  }
  setInput() {
    this.addEventListener("input", () => {
      this.raiseLabel()
    })
  }
  initInput() {
    document.addEventListener('DOMContentLoaded', (event) => {
      this.setInput()
      this.toggleInput()
      this.closeEventBody()
    })
  }
}

customElements.define('field-text', FieldInput);
