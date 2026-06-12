<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Any authenticated user may post an ad. Owner checks live in AdPolicy.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'category_id'    => ['required', 'integer', Rule::exists('categories', 'id')],
            'district_id'    => ['required', 'integer', Rule::exists('districts', 'id')],
            'city_id'        => ['required', 'integer', Rule::exists('cities', 'id')],
            'title'          => ['required', 'string', 'min:5', 'max:255'],
            'description'    => ['required', 'string', 'min:20', 'max:5000'],
            'condition'      => ['nullable', Rule::in(['new', 'used'])],
            'price'          => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'is_negotiable'  => ['sometimes', 'boolean'],
            'contact_name'   => ['required', 'string', 'max:255'],
            'contact_phone'  => ['required', 'string', 'regex:/^0\d{9}$/'],

            // Up to 8 images, 5MB each.
            'images'         => ['nullable', 'array', 'max:8'],
            'images.*'       => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],

            // Dynamic category attributes (vehicles / electronics / property, etc.)
            'attributes'     => ['nullable', 'array'],
            'attributes.*'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id'   => 'ප්‍රවර්ගය',
            'district_id'   => 'දිස්ත්‍රික්කය',
            'city_id'       => 'නගරය',
            'title'         => 'සිරැසිය',
            'description'   => 'විස්තරය',
            'price'         => 'මිල',
            'contact_name'  => 'සම්බන්ධතා නම',
            'contact_phone' => 'දුරකථන අංකය',
            'images'        => 'පින්තූර',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'කරුණාකර ප්‍රවර්ගයක් තෝරන්න.',
            'category_id.exists'   => 'තෝරාගත් ප්‍රවර්ගය වලංගු නැත.',
            'district_id.required' => 'කරුණාකර දිස්ත්‍රික්කයක් තෝරන්න.',
            'district_id.exists'   => 'තෝරාගත් දිස්ත්‍රික්කය වලංගු නැත.',
            'city_id.required'     => 'කරුණාකර නගරයක් තෝරන්න.',
            'city_id.exists'       => 'තෝරාගත් නගරය වලංගු නැත.',

            'title.required'       => 'කරුණාකර දැන්වීමේ සිරැසිය ඇතුළත් කරන්න.',
            'title.min'            => 'සිරැසිය අවම වශයෙන් අක්ෂර :min ක් විය යුතුය.',
            'title.max'            => 'සිරැසිය අක්ෂර :max ට වඩා වැඩි විය නොහැක.',

            'description.required' => 'කරුණාකර විස්තරයක් ඇතුළත් කරන්න.',
            'description.min'      => 'විස්තරය අවම වශයෙන් අක්ෂර :min ක් විය යුතුය.',
            'description.max'      => 'විස්තරය අක්ෂර :max ට වඩා වැඩි විය නොහැක.',

            'condition.in'         => 'තත්ත්වය \'new\' හෝ \'used\' විය යුතුය.',

            'price.numeric'        => 'මිල වලංගු සංඛ්‍යාවක් විය යුතුය.',
            'price.min'            => 'මිල :min ට වඩා අඩු විය නොහැක.',

            'contact_name.required'  => 'කරුණාකර සම්බන්ධතා නම ඇතුළත් කරන්න.',
            'contact_phone.required' => 'කරුණාකර දුරකථන අංකය ඇතුළත් කරන්න.',
            'contact_phone.regex'    => 'දුරකථන අංකය 0 කින් ආරම්භ වන ඉලක්කම් 10 ක් විය යුතුය (උදා: 0712345678).',

            'images.max'           => 'ඔබට උපරිම පින්තූර :max ක් පමණක් උඩුගත කළ හැක.',
            'images.*.image'       => 'උඩුගත කරන සෑම ගොනුවක්ම පින්තූරයක් විය යුතුය.',
            'images.*.mimes'       => 'පින්තූර jpeg, jpg, png හෝ webp ආකෘතියෙන් විය යුතුය.',
            'images.*.max'         => 'එක් පින්තූරයක උපරිම ප්‍රමාණය 5MB වේ.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_negotiable' => $this->boolean('is_negotiable'),
        ]);
    }
}
