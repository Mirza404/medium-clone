@props(['user'])

<div {{ $attributes }} x-data="{
    following: {{ $user->isFollowedBy(auth()->user()) ? 'true' : 'false' }},
    followersCount: {{ $user->followers()->count() }},
    follow() {
        this.following = !this.following
        axios.post('/follow/{{ $user->username }}').then(res => {
                console.log(res.data)
                this.followersCount = res.data.followersCount
            })
            .catch(err => {
                console.error(err)
            })
    }
}"
    class="flex-direction:column items-center text-left me-20 border-l border-gray-200 pl-8">
    {{ $slot }}
</div>
