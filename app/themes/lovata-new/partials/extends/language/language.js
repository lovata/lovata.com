export default new class Dropdowns {
  constructor () {
    this.handler()
  }

  showMenu (elem) {
    const parent = elem.parentNode
    if (parent.classList.contains('js-dropdown')) {
      const conteiner = parent.querySelector('.js-dropdown-container')
      const classMenu = conteiner.classList.contains('show') ? 'hide' : 'show'
      conteiner.classList.remove(...['hide', 'show'])
      conteiner.classList.add(classMenu)
    }
  }

  handler () {
    document.addEventListener('DOMContentLoaded', () => {
      const dropdowns = document.querySelectorAll('.js-dropdowns-btn')
      dropdowns.forEach(elem => {
        elem.addEventListener('click', (event) => this.showMenu(elem))
      })
      document.addEventListener('click', (event) => {
        const menu = document.querySelector('.js-dropdown-container')
        const isClickedOutsideMenu = event.target.closest('.js-dropdown') === null
        if (isClickedOutsideMenu && menu.classList.contains('show')) {
          menu.classList.remove('show')
          menu.classList.add('hide')
        }
      })
    })
  }
}
