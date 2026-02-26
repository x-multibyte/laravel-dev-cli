import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Laravel Dev CLI',
  description: 'CLI tool for Laravel development with AI Agent integration',
  lang: 'en-US',
  base: '/laravel-dev-cli/',

  head: [
    ['link', { rel: 'icon', href: '/logo.png' }]
  ],

  themeConfig: {
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Getting Started', link: '/guide/getting-started' },
      { text: 'Commands', link: '/reference/commands' },
      { text: 'AI Integration', link: '/guide/integration' },
      {
        text: 'GitHub',
        link: 'https://github.com/x-multibyte/laravel-dev-cli'
      }
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Guide',
          items: [
            { text: 'Getting Started', link: '/guide/getting-started' },
            { text: 'AI Platform Integration', link: '/guide/integration' },
            { text: 'Presets', link: '/guide/presets' },
            { text: 'Development', link: '/guide/development' }
          ]
        }
      ],
      '/reference/': [
        {
          text: 'Reference',
          items: [
            { text: 'Commands', link: '/reference/commands' },
            { text: 'Configuration', link: '/reference/configuration' }
          ]
        }
      ]
    },

    search: {
      provider: 'local'
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/x-multibyte/laravel-dev-cli' }
    ],

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2026 x-multibyte'
    }
  }
})