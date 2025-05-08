<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController  extends Controller
{

    public function index()
    {
        return view('dashboard.index');
    }
    public function settings()
    {
        $projects = collect(config('projects'))->filter(function ($project) {
            return empty($project['hidden']);
        });

        return view('dashboard.dashboard', compact('projects'));
    }
}
