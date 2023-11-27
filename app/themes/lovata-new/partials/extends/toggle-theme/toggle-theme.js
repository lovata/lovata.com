export default new class ToggleTheme {
  constructor () {
    this.toggleTheme = '.js-toggle-theme'
    this.handler()
  }

  setActiveTheme (activeTheme) {
    let body = document.querySelector('body')
    body.classList.remove(activeTheme == 'dark' ? 'light' : 'dark')
    body.classList.add(activeTheme)
    localStorage.setItem('theme', activeTheme)
    document.querySelector('.js-active-theme').dataset.activetheme = activeTheme
  }

  // setAnimateClass (theme) {
  //   const elemActive = document.querySelector('.js-active-theme')
  //   if (theme == 'dark') {
  //     elemActive.classList.add('a-moon')
  //     elemActive.classList.remove('a-light')
  //
  //   } else {
  //     elemActive.classList.add('a-light')
  //     elemActive.classList.remove('a-moon')
  //     localStorage.setItem('theme', 'light')
  //   }
  // }

  handler () {
    document.addEventListener('DOMContentLoaded', () => {
      const toggles = document.querySelectorAll(this.toggleTheme)
      if (localStorage.getItem('theme')) this.setActiveTheme(localStorage.getItem('theme'))
      toggles.forEach(button => {
        button.addEventListener('click', (el) => {
          const theme = button.dataset.theme
          if (!document.querySelector('body').classList.contains(theme)) {
            this.setActiveTheme(button.dataset.theme)
          }
        })
      })
    })
  }
}()
