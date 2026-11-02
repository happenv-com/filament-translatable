// Drives the locale tabs of the Translations component. Filament's own
// tabsSchemaComponent is tied to the markup of Filament's tabs view, which
// this package does not render, so the package ships its own component.
export default function translationsTabsComponent({
    activeTab,
    isTabPersistedInQueryString,
    livewireId,
    tab,
    tabQueryStringKey,
}) {
    return {
        tab,
        unsubscribeLivewireHook: null,

        init() {
            const tabs = this.getTabs()
            const queryString = new URLSearchParams(window.location.search)

            if (
                isTabPersistedInQueryString &&
                queryString.has(tabQueryStringKey) &&
                tabs.includes(queryString.get(tabQueryStringKey))
            ) {
                this.tab = queryString.get(tabQueryStringKey)
            }

            if (!this.tab || !tabs.includes(this.tab)) {
                this.tab = tabs[activeTab - 1] ?? tabs[0] ?? null
            }

            this.$watch('tab', () => {
                this.updateQueryString()
                this.autofocusFields()
            })

            this.autofocusFields(true)

            const syncTabAfterUpdate = (component) => {
                this.$nextTick(() => {
                    if (component.id !== livewireId) {
                        return
                    }

                    const tabs = this.getTabs()

                    if (!tabs.includes(this.tab)) {
                        this.tab = tabs[activeTab - 1] ?? this.tab
                    }
                })
            }

            // Livewire 4 (Filament 5) has interceptMessage; Livewire 3 (Filament 4) only the commit hook.
            this.unsubscribeLivewireHook =
                typeof Livewire.interceptMessage === 'function'
                    ? Livewire.interceptMessage(({ message, onSuccess }) => {
                          onSuccess(() => syncTabAfterUpdate(message.component))
                      })
                    : Livewire.hook('commit', ({ component, succeed }) => {
                          succeed(() => syncTabAfterUpdate(component))
                      })
        },

        getTabs() {
            return this.$refs.tabsData
                ? JSON.parse(this.$refs.tabsData.value)
                : []
        },

        updateQueryString() {
            if (!isTabPersistedInQueryString) {
                return
            }

            const url = new URL(window.location.href)
            url.searchParams.set(tabQueryStringKey, this.tab)

            history.replaceState(null, document.title, url.toString())
        },

        autofocusFields(respectCurrentFocus = false) {
            this.$nextTick(() => {
                if (
                    respectCurrentFocus &&
                    document.activeElement &&
                    document.activeElement !== document.body &&
                    this.$el.compareDocumentPosition(document.activeElement) &
                        Node.DOCUMENT_POSITION_PRECEDING
                ) {
                    return
                }

                const fields = this.$el.querySelectorAll(
                    '.fi-sc-tabs-tab.fi-active [autofocus]',
                )

                for (const field of fields) {
                    field.focus()

                    if (document.activeElement === field) {
                        break
                    }
                }
            })
        },

        destroy() {
            this.unsubscribeLivewireHook?.()
        },
    }
}
