<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js Test</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>

    <div x-data="{ items: ['Apple', 'Banana', 'Cherry'] }">
        <template x-for="item in items" :key="item">
            <p x-text="item"></p>
        </template>
    </div>

    <div x-data="{ open: false }">
        <button @click="open = !open">Toggle</button>
        <div x-show="open" class="p-4 border mt-2">Hello, I'm visible!</div>
    </div>

    <div x-data="{ name: '' }">
        <input type="text" x-model="name" placeholder="Enter your name" class="border p-2">
        <p>Hello, <span x-text="name"></span>!</p>
    </div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="p-2 bg-blue-500 text-white">Menu</button>
        <div x-show="open" @click.outside="open = false" class="absolute bg-white shadow-md mt-2 p-4">
            <p>Dropdown Content</p>
        </div>
    </div>


    <div x-data="{ dark: false }" :class="dark ? 'bg-gray-900 text-white' : 'bg-white text-black'" class="p-4">
        <button @click="dark = !dark" class="p-2 bg-blue-500 text-white">Toggle Dark Mode</button>
        <p class="mt-2">This is a themed section.</p>
    </div>


    <div x-data="{ count: 0 }">
        <button @click="count--" class="p-2 bg-red-500 text-white">-</button>
        <span x-text="count" class="mx-4 text-xl"></span>
        <button @click="count++" class="p-2 bg-green-500 text-white">+</button>
    </div>

    <div x-data="{ users: [] }" x-init="fetch('https://jsonplaceholder.typicode.com/users')
    .then(response => response.json())
    .then(data => users = data)">

        <template x-for="user in users" :key="user.id">
            <p x-text="user.name" class="p-2 border"></p>
        </template>
    </div>

    <div x-data="{ tab: 'home' }">
        <div class="flex">
            <button @click="tab = 'home'" :class="tab === 'home' ? 'font-bold' : ''">Home</button>
            <button @click="tab = 'about'" :class="tab === 'about' ? 'font-bold' : ''">About</button>
        </div>

        <div x-show="tab === 'home'">Welcome to Home!</div>
        <div x-show="tab === 'about'">This is the About section.</div>
    </div>
    

</body>
</html>
