<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController  extends Controller
{
    public function index()
    {
        $projects = collect(config('projects'))->filter(function ($project) {
            return empty($project['hidden']);
        });

        return view('dashboard.dashboard', compact('projects'));
    }
}
