import { createI18n } from 'vue-i18n'
import pl from '../lang/pl.json'
import en from '../lang/en.json'

export default createI18n({
    legacy: false,
    locale: 'pl',
    fallbackLocale: 'en',
    messages: {
        pl,
        en
    }
}) 