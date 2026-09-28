<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Services\WhatsAppService;
use Illuminate\Routing\Controller as BaseController;

class ProfileController extends BaseController
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function index(WhatsAppService $whatsAppService)
    {
        return view('dashboard.profile.index', [
            'whatsappTemplates' => $whatsAppService->templatesForUser(auth()->user()),
            'whatsappTemplateKeys' => WhatsAppService::TEMPLATE_KEYS,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $result = $this->profileService->update($request->validated(), $request->file('profile_image'));
        if ($result['status']) {
            return response()->json(['message' => $result['message'],'profile_image' => $result['profile_image']]);
        }

        return response()->json(['message' => $result['message']], 500);
    }
}
