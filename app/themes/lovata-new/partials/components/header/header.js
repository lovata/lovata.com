export default new class Header {
    constructor () {
        this.menu = null
        this.handler()
        this.timeout = null
        this.screen = null
    }

    showMenuforMobile (event) {
        console.log('showMenuforMobile click')
        const elem = event.target.parentNode.parentNode
        if (elem.classList.contains('hide')) {
            const heightSubMenu = elem.querySelector('ul').clientHeight + 'px'
            elem.classList.remove('hide')
            elem.classList.add('show')
        } else {
            elem.classList.remove('show')
            elem.classList.add('hide')
        }
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
                    submenu.forEach(menu => {
                        menu.classList.remove('show')
                        menu.classList.add('hide')
                    })
                }
            } else this.addEventMobile()
            this.screen = screenWidth
        }
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
            this.burgerMenu()
            window.addEventListener('resize', () => {
                this.bindEvent()
            })
        })
    }
}()
