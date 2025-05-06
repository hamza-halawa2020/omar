<!DOCTYPE html>
<html lang="en" data-theme="light">
<x-head />

<body>
    <x-navbar />
    <div class="outer-container">
        <div class="container bg-primary-50 rounded">
            <div class="inner-grid">
                @foreach ($projects as $project)
                    <div class="inner-box project-card p-4">
                        <img src="{{ asset($project['image']) }}" alt="{{ $project['name'] }} Icon">
                        <div class="box-content">
                            <div class="title mt-3">{{ $project['name'] }}</div>
                            <p class="description">{{ $project['description'] }}</p>
                            <a href="{{ $project['url'] }}" class="mb-3">
                                TRY NOW <span>→</span>
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
        .outer-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .container {}

        .sidebar-toggle {
            display: none;
        }

        .rounded {
            border-radius: 20px !important;
        }

        .inner-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: auto;
            grid-auto-rows: minmax(150px, auto);
        }

        .inner-box {
            display: flex;
            align-items: center;
        }

        .project-card {
            border-bottom: 1px solid #2b5c9b;
            border-right: 1px solid #2b5c9b;
            background-color: transparent;
            padding: 20px;
            position: relative;
        }

        .project-card:nth-last-child(-n+4) {
            border-bottom: none;
        }

        .project-card:nth-child(4n) {
            border-right: none;
        }



        .inner-box img {
            width: 60px;
            height: 60px;
            margin: 0 15px;
            transition: transform 0.3s ease;
        }

        .inner-box:hover img {
            transform: scale(1.1);
        }

        .box-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2b5c9b;
            transition: color 0.3s ease;
        }

        .project-card:hover .title {
            color: #1a3e6f;
        }


        .box-content a {
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .box-content a span {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }

        .box-content a:hover {
            color: #0056b3;
        }

        .box-content a:hover span {
            transform: translateX(3px);
        }
    </style>
</body>

</html>
