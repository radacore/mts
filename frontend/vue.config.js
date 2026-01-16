const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  chainWebpack: config => {
    config
        .plugin('html')
        .tap(args => {
            args[0].title = "e-ipa mtsn1 kota makassar";
            return args;
        })
},
pwa: {
  name: "e-ipa",
  themeColor: "#FFFFFF"
},
  transpileDependencies: [
    'quasar'
  ],

  pluginOptions: {
    quasar: {
      importStrategy: 'kebab',
      rtlSupport: false
    }
  }
})
