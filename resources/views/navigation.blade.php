<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <circle fill="none" stroke="#fff" stroke-width="1.1" cx="10" cy="10" r="9" />
        <rect x="9" y="4" width=".1" height="7" fill="none" stroke="#fff"/>
        <path fill="none" stroke="#fff" stroke-width="1.1" d="M13.018,14.197 L9.445,10.625" />
    </svg>
    <span class="sidebar-label">
        Novacron
    </span>
</h3>
<ul class="list-reset mb-8">
    <li class="leading-wide mb-4 text-sm">
        <router-link :to="{
            name: 'index',
            params: {
                resourceName: 'tasks'
            }
        }" class="text-white ml-8 no-underline dim">
            Tasks
        </router-link>
    </li>
</ul>