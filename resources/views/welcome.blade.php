<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/preline/dist/preline.js"></script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body>
    <header
        class="flex flex-wrap sm:justify-start sm:flex-nowrap w-full py-3 bg-white dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
        <nav class="max-w-[85rem] w-full mx-auto px-4 flex flex-wrap basis-full items-center justify-between">
            <a class="flex-none text-xl font-semibold text-foreground focus:outline-hidden focus:opacity-80" href="#"
                aria-label="Brand">
                <span
                    class="inline-flex items-center gap-x-2 text-xl font-semibold text-foreground justify-center lg:justify-start">
                    <div class="flex flex-wrap">
                        <img src="{{ asset('images/onboarding-bp.png') }}" alt="Logo"
                            class="h-12 sm:h-14 md:h-16 w-auto object-contain">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo"
                            class="h-12 sm:h-14 md:h-16 w-auto object-contain">
                    </div>
                    <div class="text-header m-3">
                        <div class="text-4xl font-garamond text-[#009748]">BSWM FertRight Map</div>
                        <div class="text-xs leading-none my-1">
                            Republic of the Philippines<br>
                            Department of Agriculture<br>
                            Bureau of Soils and Water Management
                        </div>
                    </div>
                </span>
            </a>
            {{-- <div class="sm:order-3 flex items-center gap-x-2">
                <button type="button"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-[#006837] dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-white dark:text-white shadow-2xs hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-hidden focus:bg-gray-50 dark:focus:bg-neutral-700 disabled:opacity-50 disabled:pointer-events-none">
                    Button
                </button>
            </div> --}}
        </nav>
    </header>



    

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-16">

            <!-- Top -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">

                <!-- Logo -->
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-40">
                    </div>
                </div>

                <!-- Preline -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">About Us</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" class="hover:text-gray-900">Why BSWM?</a></li>
                        <li><a href="#" class="hover:text-gray-900">What is BSWM?</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Legal</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" type="button" class="hover:text-gray-900"  data-hs-overlay="#hs-export-modal">Data
                                Use Disclaimer</a></li>
                        <li><a href="#" class="hover:text-gray-900" data-hs-overlay="#hs-basic-modal">Data Export Disclaimer</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Apps</h4>
                    <ul class="space-y-3 text-gray-600">
                        <li><a href="#" class="hover:text-gray-900">▶️ <span>Google Play</span></a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom -->
            <div class="mt-16 text-sm text-gray-500">
                © 2026 BSWM FertRight Map. All rights reserved.
            </div>

        </div>
    </footer>
    <div id="hs-export-modal" class="hs-overlay hidden fixed inset-0 z-50
         bg-black/50 backdrop-blur-[1px]
         opacity-0 transition-all
         pointer-events-none
         hs-overlay-open:opacity-100
         hs-overlay-open:pointer-events-auto" role="dialog" tabindex="-1" aria-labelledby="hs-export-modal-label">

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-lg bg-white border shadow-xl rounded-xl pointer-events-auto">

                <!-- Header -->
                <div class="flex justify-between items-center py-3 px-4 border-b">
                    <h3 id="hs-export-modal-label" class="font-semibold text-gray-900">
                        DA-BSWM FertRight Map Data Use Disclaimer
                    </h3>
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center rounded-full hover:bg-gray-100"
                        aria-label="Close" data-hs-overlay="#hs-export-modal">
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4 text-sm leading-relaxed text-gray-700">
                    The BSWm FertRight Map is developede and manage by the DA-Bureau of Soils and Water Management (DA-BSWM).<br><br>
                    The DA-BSWM declares that no information is collected through this website. All information inputs only to serve for the functionalities offere by the application. By using this website, you acknowledge and aggree to use your information as described in this notice. <br><br>
                    The DA-BSWM reserves the right to update this disclaimer as bnecessatyry to reflect changesin our privacy policies or applicable laws and regulations. Please review this discaimer periodically for any updates
                </div>
                <!-- Footer -->
                <div class="flex justify-end gap-2 py-3 px-4 border-t mt-3">
                    <button data-hs-overlay="#hs-export-modal" class="px-3 py-2 text-sm rounded-lg bg-green-700 text-white hover:bg-green-800">
                        Close
                    </button>
                    {{-- <button class="px-3 py-2 text-sm rounded-lg bg-green-700 text-white hover:bg-green-800">
                        Save changes
                    </button> --}}
                </div>

            </div>
        </div>
    </div>

    <div id="hs-basic-modal" class="hs-overlay hidden fixed inset-0 z-50
         bg-black/50 backdrop-blur-[1px]
         opacity-0 transition-all
         pointer-events-none
         hs-overlay-open:opacity-100
         hs-overlay-open:pointer-events-auto" role="dialog" tabindex="-1" aria-labelledby="hs-basic-modal-label">

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-lg bg-white border shadow-xl rounded-xl pointer-events-auto">

                <!-- Header -->
                <div class="flex justify-between items-center py-3 px-4 border-b">
                    <h3 id="hs-basic-modal-label" class="font-semibold text-gray-900">
                        DA-BSWM FertRight Map Data Export Disclaimer
                    </h3>
                    <button type="button"
                        class="size-8 inline-flex justify-center items-center rounded-full hover:bg-gray-100"
                        aria-label="Close" data-hs-overlay="#hs-basic-modal">
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="p-4 text-sm leading-relaxed text-gray-700">
                    The BSWM FertRight Map has an option to export generated data as a download PDF file. This feature allows user to store and print 
                    the file for convenience. Please be advised that DA-BSWM shall not be held responsible for any damages, losses or liabilities
                    arising from the use or misuse of the exported data. As such, users are encouraged to secure the handling and storage of the file<br><br>
                    Thank you for using the BSWM FertRight Map! We hope to actively contribute to sustaining and maintaining a sound agricultural community throughout the country.

                </div>
                <!-- Footer -->
                <div class="flex justify-end gap-2 py-3 px-4 border-t mt-3">
                    <button data-hs-overlay="#hs-basic-modal" class="px-3 py-2 text-sm rounded-lg bg-green-700 text-white hover:bg-green-800">
                        Close
                    </button>
                    {{-- <button class="px-3 py-2 text-sm rounded-lg bg-green-700 text-white hover:bg-green-800">
                        Save changes
                    </button> --}}
                </div>

            </div>
        </div>
    </div>
    </body>
</html>