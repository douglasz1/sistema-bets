process.env.DISABLE_NOTIFIER = true

const elixir = require('laravel-elixir')

require('laravel-elixir-env').config({ path: '.env.permissions' })
require('laravel-elixir-vue-2')

elixir(mix => {
  mix.sass('app.scss')

    .styles('plugins.css', 'public/css/plugins.css')

    .scripts(['./node_modules/fastclick/lib/fastclick.js', 'utils.js'], 'public/js/theme.js')
    .scripts('plugins.js', 'public/js/plugins.js')

    .copy(['node_modules/bootstrap-sass/assets/fonts', 'node_modules/font-awesome/fonts'], 'public/build/fonts')

    .webpack('app.js')

  if (elixir.config.production) {
    mix.version([
      'css/app.css', 'css/plugins.css',
      'js/app.js', 'js/plugins.js', 'js/theme.js'
    ])
  } else {
    mix.browserSync({
      proxy: 'http://localhost',
      open: false,
      notify: false
    })
  }
})
