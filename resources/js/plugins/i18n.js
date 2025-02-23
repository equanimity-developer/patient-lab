import { createI18n } from 'vue-i18n'
import pl from '@/lang/pl.json'
import en from '@/lang/en.json'

export default createI18n({
    legacy: false,
    locale: import.meta.env.VITE_APP_LOCALE || 'en',
    fallbackLocale: 'en',
    messages: {
        pl,
        en
    }
})
