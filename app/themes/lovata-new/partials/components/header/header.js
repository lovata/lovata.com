export default new class Header {
    constructor () {
        this.menu = null
        this.handler()
        this.timeout = null
        this.screen = null
    }

    showMenuforMobile (event) {
        const elem = event.target.parentNode.parentNode
        elem.classList.contains('hide') ? this.showDesctopMenu() : this.hideDesctopMenu(elem)

    }

    addEventMobile () {
        this.menu.forEach(elem => {
            elem.addEventListener('click', this.showMenuforMobile)
        })
    }

    bindEvent () {
        let screenWidth = window.innerWidth >= 1023 ? 'desktop' : 'mobile'
        if (this.screen != screenWidth) {
            if (screenWidth === 'desktop') {
                this.menu.forEach(elem => {
                    elem.removeEventListener('click', this.showMenuforMobile)
                })
                const submenu = document.querySelectorAll('.js-show-submenu.show')
                if (submenu) {
                    submenu.forEach(menu => this.hideDesctopMenu(menu))
                }
            } else this.addEventMobile()
            this.screen = screenWidth
        }
    }

    focusEvent() {
        const submenu = document.querySelectorAll('.js-show-submenu')
        if (submenu) {
            submenu.forEach(button => {

                button.addEventListener("focusin", (event) => {
                    this.showDesctopMenu(event.target.closest(".js-show-submenu"))
                });
                button.addEventListener("focusout", (event) => {
                    this.hideDesctopMenu(event.target.closest(".js-show-submenu"))
                });
            })
        }
    }

    hideDesctopMenu(btn) {
        btn.classList.remove('show')
        btn.classList.add('hide')
    }
    showDesctopMenu(btn) {
        btn.classList.add('show')
        btn.classList.remove('hide')
    }

    burgerMenu () {
        const menuButton = document.querySelectorAll('.js-mobile-menu')
        menuButton.forEach(elem => {
            elem.addEventListener('click', (event) => {
                const navigation = document.querySelector('.header__navigation')
                if (navigation.classList.contains('open')) {
                    navigation.classList.remove('open')
                    document.querySelector('body').classList.remove('overflow-hidden')
                } else {
                    navigation.classList.add('open')
                    document.querySelector('body').classList.add('overflow-hidden')
                }
            })
        })
    }

    handler () {
        document.addEventListener('DOMContentLoaded', () => {
            this.menu = document.querySelectorAll('.js-show-submenu')
            this.bindEvent()
            this.focusEvent()
            this.burgerMenu()
            window.addEventListener('resize', () => {
                this.bindEvent()
            })
        })
    }
}()
