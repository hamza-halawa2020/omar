<?php

namespace App\Services;

use App\Models\UserWhatsAppMessageTemplate;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileService
{
    public function __construct(private readonly FileService $fileService)
    {
    }

    public function update(array $validated, ?UploadedFile $profileImage): array
    {
        $user = Auth::user();

        try {
            if ($profileImage) {
                if ($user->profile_image) {
                    $this->fileService->deleteStorageFile($user->profile_image, 'public');
                }
                $validated['profile_image'] = $this->fileService->storeStorageFile($profileImage, 'profile_images', 'public');
            }

            if (! empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            if (! empty($validated['email'])) {
                $validated['email'] = Str::lower($validated['email']);
            }

            $whatsappTemplates = $validated['whatsapp_templates'] ?? [];
            unset($validated['whatsapp_templates']);

            $user->update($validated);
            $this->updateWhatsAppTemplates((int) $user->id, $whatsappTemplates);

            return [
                'status' => true,
                'message' => __('messages.profile_updated_successfully'),
                'profile_image' => $user->profile_image ?? null,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => __('messages.failed_to_update_profile') . ': ' . $e->getMessage(),
            ];
        }
    }

    private function updateWhatsAppTemplates(int $userId, array $templates): void
    {
        if ($templates === []) {
            return;
        }

        foreach ($templates as $key => $template) {
            if (! in_array($key, WhatsAppService::TEMPLATE_KEYS, true)) {
                continue;
            }

            UserWhatsAppMessageTemplate::query()->updateOrCreate(
                ['user_id' => $userId, 'key' => $key],
                ['template' => trim((string) $template)]
            );
        }

        app(WhatsAppService::class)->clearTemplatesCache($userId);
    }
}
