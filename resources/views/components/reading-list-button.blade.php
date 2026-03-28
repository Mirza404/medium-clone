@props(['post', 'readingLists' => []])

@auth
    <div class="mt-8 relative w-full md:w-auto"
        x-data="{
            open: false,
            busy: null,
            postId: {{ $post->id }},
            lists: @js($readingLists),
            toggleDropdown() {
                this.open = !this.open
            },
            closeDropdown() {
                this.open = false
            },
            attachedCount() {
                return this.lists.filter(list => list.attached).length
            },
            toggle(listId) {
                if (this.busy === listId) {
                    return
                }

                this.busy = listId

                axios.post(`/reading-lists/${listId}/posts/${this.postId}`)
                    .then(({ data }) => {
                        const index = this.lists.findIndex(list => list.id === data.readingListId)
                        if (index !== -1) {
                            this.lists[index].attached = data.attached
                        }
                    })
                    .catch(() => {
                        alert('Unable to update your reading list right now. Please try again.')
                    })
                    .finally(() => {
                        this.busy = null
                    })
            }
        }"
        @keydown.escape.window="closeDropdown()">
        <button type="button"
            class="mt-6 inline-flex items-center gap-2 px-4 py-2 border rounded-2xl text-sm font-medium transition"
            :class="attachedCount() > 0 ? 'border-emerald-500 text-emerald-700 bg-emerald-50' : 'border-gray-300 text-gray-700 bg-white'"
            @click="toggleDropdown()">
            <template x-if="attachedCount() > 0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                    class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                    <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2" />
                </svg>
            </template>
            <template x-if="attachedCount() === 0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                    class="bi bi-bookmark" viewBox="0 0 16 16">
                    <path
                        d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1z" />
                </svg>
            </template>
            <span
                x-text="attachedCount() === 0 ? 'Save to reading list' : `Saved in ${attachedCount()} list${attachedCount() === 1 ? '' : 's'}`"></span>
        </button>

        <div x-show="open" x-transition.origin.top.left x-cloak
            class="absolute z-20 mt-2 w-72 rounded-2xl border border-gray-200 bg-white shadow-xl"
            @click.outside="closeDropdown()">
            <div class="p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Your reading lists</h3>

                <template x-if="lists.length === 0">
                    <div class="text-sm text-gray-500">
                        <p>You haven't created a reading list yet.</p>
                        <a href="{{ route('reading-lists.create') }}"
                            class="mt-2 inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700">
                            <span>Create your first list</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </template>

                <template x-for="list in lists" :key="list.id">
                    <button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm"
                        :class="list.attached ? 'bg-emerald-50 text-emerald-700' : 'hover:bg-gray-100 text-gray-700'"
                        @click="toggle(list.id)">
                        <span x-text="list.title"></span>
                        <span class="flex items-center gap-1 text-xs" :class="list.attached ? 'text-emerald-700' : 'text-gray-500'">
                            <template x-if="busy === list.id">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </template>
                            <template x-if="busy !== list.id">
                                <span x-text="list.attached ? 'Saved' : 'Save'"></span>
                            </template>
                        </span>
                    </button>
                </template>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">
                <a href="{{ route('reading-lists.create') }}"
                    class="flex items-center gap-2 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Create a new reading list</span>
                </a>
            </div>
        </div>
    </div>
@endauth
