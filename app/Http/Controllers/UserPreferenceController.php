<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceUpdateRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="API Endpoints for managing user preferences"
 * )
 */
class UserPreferenceController extends Controller
{
    /**
     * List the current user's preferences.
     *
     * @OA\Get(
     *     path="/api/user/preferences",
     *     summary="Get the current user's preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of preferences",
     *
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/UserPreferenceResource")),
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->preferences()->with('preferable')->get();

        return UserPreferenceResource::collection($preferences);
    }

    /**
     * Update user preferences (remove old ones not in selection, add new ones).
     *
     * @OA\Put(
     *     path="/api/user/preferences",
     *     summary="Update the user's preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"preferences"},
     *
     *             @OA\Property(
     *                 property="preferences",
     *                 type="array",
     *                 minItems=1,
     *
     *                 @OA\Items(
     *                     type="object",
     *                     required={"preferable_id", "preferable_type"},
     *
     *                     @OA\Property(property="preferable_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="preferable_type",
     *                         type="string",
     *                         enum={"category", "source", "author"},
     *                         example="category"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserPreferenceResource"))
     *         )
     *     ),
     *
     *     @OA\Response(response=400, description="Invalid data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */
    public function update(UserPreferenceUpdateRequest $request)
    {
        $user = Auth::user();
        $newPreferences = collect($request->preferences);

        // Get current preferences (including soft-deleted ones)
        $currentPreferences = $user->preferences()->withTrashed()->get();

        // Find preferences to delete (not in new selection)
        $preferencesToDelete = $currentPreferences->filter(function ($preference) use ($newPreferences) {
            return ! $newPreferences->contains(function ($newPref) use ($preference) {
                return $newPref['preferable_id'] == $preference->preferable_id &&
                    $newPref['preferable_type'] == $preference->preferable_type;
            });
        });

        // Soft delete preferences that are no longer selected
        if ($preferencesToDelete->isNotEmpty()) {
            UserPreference::whereIn('id', $preferencesToDelete->pluck('id'))->delete();
        }

        // Find preferences that should be restored
        $preferencesToRestore = $currentPreferences->filter(function ($preference) use ($newPreferences) {
            return $preference->trashed() && $newPreferences->contains(function ($newPref) use ($preference) {
                return $newPref['preferable_id'] == $preference->preferable_id &&
                    $newPref['preferable_type'] == $preference->preferable_type;
            });
        });

        // Restore soft-deleted preferences
        if ($preferencesToRestore->isNotEmpty()) {
            UserPreference::whereIn('id', $preferencesToRestore->pluck('id'))->restore();
        }

        // Find preferences to add (not already existing)
        $preferencesToAdd = $newPreferences->filter(function ($newPref) use ($currentPreferences) {
            return ! $currentPreferences->contains(function ($existingPref) use ($newPref) {
                return $newPref['preferable_id'] == $existingPref->preferable_id &&
                    $newPref['preferable_type'] == $existingPref->preferable_type;
            });
        });

        // Insert new preferences
        if ($preferencesToAdd->isNotEmpty()) {
            $insertData = $preferencesToAdd->map(fn ($preference) => [
                'user_id' => $user->id,
                'preferable_id' => $preference['preferable_id'],
                'preferable_type' => $preference['preferable_type'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            UserPreference::insert($insertData);
        }

        return response()->json([
            'message' => 'Preferences updated successfully',
            'data' => UserPreferenceResource::collection($user->preferences()->with('preferable')->get()),
        ]);
    }
}
