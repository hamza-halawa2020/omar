<!DOCTYPE html>
<html lang="en" data-theme="light">
<x-head />

<body>
    <x-navbar />
    <div class="outer-container">
        <div class="container bg-primary-50 rounded-lg shadow-lg mx-auto p-6">
            <div class="inner-grid">
                @foreach ($projects as $project)
                    <div class="inner-box project-card p-5 bg-white rounded-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                        <img src="{{ asset($project['image']) }}" alt="{{ $project['name'] }} Icon" class="w-16 h-16 object-contain transition-transform duration-300 hover:scale-110">
                        <div class="box-content">
                            <div class="title mt-4 text-xl font-bold text-blue-900 transition-colors duration-300 hover:text-blue-700">{{ $project['name'] }}</div>
                            <p class="description text-gray-600 text-sm leading-relaxed mt-2">{{ $project['description'] }}</p>
                            <a href="{{ $project['url'] }}" class="mt-4 inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition-all duration-300 group">
                                TRY NOW
                                <span class="ml-2 inline-block transition-transform duration-300 group-hover:translate-x-2">→</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <x-script />
    <x-footer />

    <style>
        .sidebar-toggle{
            display: none !important;
        }
        .outer-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px 0;
        }

        .container {
            max-width: 1200px;
        }

        .rounded-lg {
            border-radius: 1.5rem !important;
        }

        .inner-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 1.5rem;
            padding: 1rem;
        }

        @media (max-width: 1024px) {
            .inner-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .inner-grid {
                grid-template-columns: 1fr;
            }
        }

  
        .project-card {
            border: 1px solid #e0e7ff;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .inner-box img {
            flex-shrink: 0;
            transition: transform 0.3s ease-in-out;
        }

        .box-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .title {
            font-size: 1.25rem;
            line-height: 1.5;
            color: #2b5c9b;
            transition: color 0.3s ease-in-out;
        }

        .project-card:hover .title {
            color: #1a3e6f;
        }

        .description {
            color: #4b5563;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            max-height: 3rem;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .box-content a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease-in-out;
        }

        .box-content a:hover {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .box-content a span {
            margin-left: 0.5rem;
            transition: transform 0.3s ease-in-out;
        }

        .box-content a:hover span {
            transform: translateX(0.5rem);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .hover:shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .hover:-translate-y-2 {
            transform: translateY(-0.5rem);
        }
    </style>
</body>

</html>