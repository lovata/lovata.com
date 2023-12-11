const lightStyles = document.querySelectorAll('link[rel=stylesheet][media*=prefers-color-scheme][media*=light]')
const darkStyles = document.querySelectorAll('link[rel=stylesheet][media*=prefers-color-scheme][media*=dark]')

function setupSwitcher () {
  getSavedScheme();
  const switcherButton = document.querySelectorAll('.js-toggle-theme')

  console.log("switcherButton")
  console.log(switcherButton)
  switcherButton.forEach((button) => {

    button.addEventListener('click', () => {
      const theme = button.dataset.theme
      setScheme(theme)
      removeThemeTailwind(theme)
      setThemeTailwind(theme)
    })
  })
}

function setupScheme () {
  const savedScheme = getSavedScheme()
  const systemScheme = getSystemScheme()

  if (savedScheme === null) return
  if (savedScheme !== systemScheme) {
    setScheme(savedScheme)
  }
}

function setScheme (scheme) {
  switchMedia(scheme);
  scheme === 'auto' ? clearScheme() : saveScheme(scheme)
}

function switchMedia (scheme) {
  let lightMedia
  let darkMedia

  if (scheme === 'auto') {
    lightMedia = '(prefers-color-scheme: light)'
    darkMedia = '(prefers-color-scheme: dark)'
  } else {
    lightMedia = (scheme === 'light') ? 'all' : 'not all'
    darkMedia = (scheme === 'dark') ? 'all' : 'not all'
  }

  [...lightStyles].forEach((link) => {
    link.media = lightMedia
  });

  [...darkStyles].forEach((link) => {
    link.media = darkMedia
  })
}

function getSystemScheme () {
  const darkScheme = matchMedia('(prefers-color-scheme: dark)').matches
  return darkScheme ? 'dark' : 'light'
}

function getSavedScheme () {
  return localStorage.getItem('color-scheme')
}

function saveScheme (scheme) {
  localStorage.setItem('color-scheme', scheme)
}

function clearScheme () {
  localStorage.removeItem('color-scheme')
}

document.addEventListener('DOMContentLoaded', () => {
  setupSwitcher()
  setThemeTailwind(localStorage.getItem('color-scheme'))
})

function setThemeTailwind (scheme) {
  document.querySelector('body').classList.add(scheme)
  document.querySelector('.js-active-theme').dataset.activetheme = scheme
}
function removeThemeTailwind (scheme) {
  document.querySelector('body').classList.remove(scheme === 'dark' ? 'light' : 'dark')
}
setupScheme()
