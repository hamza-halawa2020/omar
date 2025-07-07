<!DOCTYPE html>
<html lang="en" data-theme="light">
<x-head />

<body>
    <x-navbar />
    <div class="outer-container bg-gradient-to-br from-blue-50 to-indigo-100 py-16">
        <div class="container mx-auto px-4">
            <h4 class="text-5xl font-extrabold text-gray-900 text-center mb-4 leading-tight">
                Choose Your <span class="text-blue-700">Service</span>
            </h4>
            <p class="text-xl text-gray-600 text-center mb-16 max-w-2xl mx-auto">
                Click a service below to get started.
            </p>

            <div class="inner-grid">
                @foreach ($projects as $project)
                    <a href="{{ $project['url'] }}" class="service-card group bg-white rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-2 hover:scale-[1.01] border border-gray-100 flex flex-col items-center justify-center text-center p-8 cursor-pointer">
                        <div class="icon-wrapper w-28 h-28 flex items-center justify-center mb-6 rounded-full bg-blue-50 group-hover:bg-blue-100 transition-colors duration-300">
                            <img src="{{ asset($project['image']) }}" alt="{{ $project['name'] }} Icon" class="w-20 h-20 object-contain">
                        </div>
                        <h5 class="text-3xl mt-3 font-bold text-gray-800 mb-2 group-hover:text-blue-700 transition-colors duration-300 leading-tight">
                            {{ $project['name'] }}
                        </h5>
                        <p class="description text-gray-600 text-base leading-relaxed">
                            {{ $project['description'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <x-script />
    <x-footer />

<style>
    .sidebar-toggle, #GENERAL_LINK {
        display: none !important;
    }

    .outer-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(135deg, #f9fafb, #e0ecff);
    }

    .container {
        max-width: 1200px;
        margin: auto;
    }

    .inner-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        padding: 1rem;
    }

    .service-card {
        background: #ffffff;
        border-radius: 1.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid #eaeaea;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .service-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 30px rgba(0, 0, 0, 0.08);
        border-color: #d0e2ff;
    }

    .icon-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 9999px;
        background-color: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        transition: background-color 0.3s;
        margin-top: 2rem;
    }

    .service-card:hover .icon-wrapper {
        background-color: #dbeafe;
    }

    .icon-wrapper img {
        width: 60px;
        height: 60px;
        object-fit: contain;
    }

    .service-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .service-card:hover h2 {
        color: #2563eb;
    }

    .description {
        font-size: 0.95rem;
        color: #6b7280;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        min-height: 2.5rem;
    }

    @media (max-width: 768px) {
        .inner-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

</body>
</html>