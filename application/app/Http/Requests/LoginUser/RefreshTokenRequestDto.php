<?php
namespace App\Http\Requests\LoginUser;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  schema="RefreshTokenRequestDto",
 *  type="object",
 *  required = {"refresh_token"})
 *  {
 *      @OA\Property(property="refresh_token", type="string", description="Refresh token"),
 *  }
 * )
 */
class RefreshTokenRequestDto extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => 'required|string'
        ];
    }
}
