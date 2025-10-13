<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Education Dashboard</title>

    {{-- 
        We'll use Tailwind CSS, which is linked in the default Laravel setup.
        If you ran 'npm run dev' earlier, your compiled CSS is in 'public/build/assets'.
        This link assumes your app is set up with Vite or Laravel Mix.
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- OPTIONAL: Add Figtree font for Laravel look --}}
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
</head>

<body class="antialiased font-figtree">

    {{-- MAIN CONTAINER: White background, full screen, responsive paddi --}}
    <div class="min-h-screen bg-white">
        <div class="text-xl font-bold text-gray-800">LOGO</div>
        {{-- Navigation Bar --}}
        <header class="">

            <nav class="navbar">
                <ul>
                    <li class="active">
                        <a href="#">
                            <i class='bx bx-home-alt-2 icon'></i>
                            <i class='bx bxs-home-alt-2 activeIcon'></i>
                        </a>

                    </li>
                    <li>
                        <a href="#">
                            <i class='bx bx-user icon' ></i>
                            <i class='bx bxs-user activeIcon'></i>
                        </a>

                    </li>
                    <li >
                        <a href="#">
                            <i class='bx bx-lock-alt icon'></i>
                            <i class='bx bxs-lock-alt activeIcon' ></i>
                        </a>

                    </li>
                    <div class="indicator"></div>
                </ul>
            </nav>
        </header>

        {{-- HERO SECTION: Split layout (Text on left, Image/Graphic on right) --}}
        <main class="max-w-7xl mx-auto px-6 lg:px-8 pt-16 pb-24 md:flex md:items-center md:space-x-12">

            {{-- Left Side: Text and Button --}}
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-6xl font-extrabold text-blue-600 leading-tight">
                    Online <br> Education
                </h1>

                <p class="mt-6 text-lg text-gray-600 leading-relaxed max-w-md">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum varius, turpis vitae auctor
                    congue, nibh ipsum tristique tellus, ut elementum magna ipsum eu dui.
                </p>

                <a href="#"
                    class="mt-8 inline-block px-8 py-3 text-white bg-blue-600 rounded-lg text-lg font-semibold shadow-lg hover:bg-blue-700 transition duration-200">
                    Get Started
                </a>
            </div>

            {{-- Right Side: Placeholder for Graphic --}}
            <div class="md:w-1/2 flex justify-center">
                {{-- 
                    Since we can't embed the actual image, we'll use a descriptive placeholder 
                    to represent the monitor, people, and books from your image. 
                --}}
                <div
                    class="bg-gray-100 p-8 rounded-xl shadow-xl w-full max-w-lg h-96 flex items-center justify-center border-4 border-gray-200">
                    <span class="text-gray-500 text-center text-xl font-medium">
                        [Placeholder for Online Education Graphic]
                    </span>
                </div>
            </div>

        </main>

    </div>
   
</body>

</html>
