<?php

namespace Basilicom\AiImageGeneratorBundle\Service\Prompt;

use Basilicom\AiImageGeneratorBundle\Model\Prompt;

class PromptPreset
{
    public const DEFAULT_NEGATIVE_PROMPT = '(worst quality, low quality, normal quality, lowres, low details, oversaturated, undersaturated, overexposed, underexposed, grayscale, bw, bad photo, bad photography, bad art:1.4), (watermark, signature, text font, username, error, logo, words, letters, digits, autograph, trademark, name:1.2), (blur, blurry, grainy), morbid, ugly, asymmetrical, mutated malformed, mutilated, poorly lit, bad shadow, draft, cropped, out of frame, cut off, censored, jpeg artifacts, out of focus, glitch, duplicate, (airbrushed, cartoon, anime, semi-realistic, cgi, render, blender, digital art, manga, amateur:1.3), (3D ,3D Game, 3D Game Scene, 3D Character:1.1), (bad hands, bad anatomy, bad body, bad face, bad teeth, bad arms, bad legs, deformities:1.3)';
    public const SFW_NEGATIVE_PROMPT = 'NSFW, Cleavage, Pubic Hair, Nudity, Naked, Au naturel, Watermark, Text, censored, deformed, bad anatomy, disfigured, poorly drawn face, mutated, extra limb, ugly, poorly drawn hands, missing limb, floating limbs, disconnected limbs, disconnected head, malformed hands, long neck, mutated hands and fingers, bad hands, missing fingers, cropped, worst quality, low quality, mutation, poorly drawn, huge calf, bad hands, fused hand, missing hand, disappearing arms, disappearing thigh, disappearing calf, disappearing legs, missing fingers, fused fingers, abnormal eye proportion, Abnormal hands, abnormal legs, abnormal feet,  abnormal fingers';

    public const ENHANCE = 'ENHANCE';
    public const PHOTOGRAPH = 'photograph';
    public const CINEMATIC = 'cinematic';
    public const DIGITAL_ART = 'digital-art';

    public const ADVERTISEMENT_POSTER = 'advertisement';
    public const ADVERTISEMENT_AUTOMOTIVE = 'automotive-advertisement';
    public const ADVERTISEMENT_CORPORATE = 'corporate-advertisement';
    public const ADVERTISEMENT_FASHION = 'fashion-advertisement';
    public const ADVERTISEMENT_LUXURY = 'luxury-advertisement';
    public const ADVERTISEMENT_REAL_ESTATE = 'real-estate-advertisement';
    public const ADVERTISEMENT_FOOD = 'food-advertisement';
    public const ADVERTISEMENT_GOURMET_FOOD = 'gourmet-food-advertisement';
    public const ADVERTISEMENT_RETAIL = 'retail-advertisement';

    /** kudos to Fooocus */
    public const PRESETS = [
        self::ENHANCE => [
            'positive' => 'breathtaking {prompt} . award-winning, professional, highly detailed',
            'negative' => 'ugly, deformed, noisy, blurry, distorted, grainy'
        ],
        self::PHOTOGRAPH => [
            'positive' => 'photograph {prompt}, 50mm . cinematic 4k epic detailed 4k epic detailed photograph shot on kodak detailed cinematic hbo dark moody, 35mm photo, grainy, vignette, vintage, Kodachrome, Lomography, stained, highly detailed, found footage',
            'negative' => 'bokeh, depth of field, blurry, cropped, regular face, saturated, contrast, deformed iris, deformed pupils, semi-realistic, cgi, 3d, render, sketch, cartoon, drawing, anime, text, cropped, out of frame, worst quality, low quality, jpeg artifacts, ugly, duplicate, morbid, mutilated, extra fingers, mutated hands, poorly drawn hands, poorly drawn face, mutation, deformed, dehydrated, bad anatomy, bad proportions, extra limbs, cloned face, disfigured, gross proportions, malformed limbs, missing arms, missing legs, extra arms, extra legs, fused fingers, too many fingers, long neck'
        ],
        self::CINEMATIC => [
            'positive' => 'cinematic still {prompt} . emotional, harmonious, vignette, highly detailed, high budget, bokeh, cinemascope, moody, epic, gorgeous, film grain, grainy',
            'negative' => 'anime, cartoon, graphic, text, painting, crayon, graphite, abstract, glitch, deformed, mutated, ugly, disfigured'
        ],
        self::DIGITAL_ART => [
            'positive' => 'concept art {prompt} . digital artwork, illustrative, painterly, matte painting, highly detailed',
            'negative' => 'photo, photorealistic, realism, ugly'
        ],

        self::ADVERTISEMENT_POSTER => [
            'positive' => 'advertising poster style {prompt} . Professional, modern, product-focused, commercial, eye-catching, highly detailed',
            'negative' => 'noisy, blurry, amateurish, sloppy, unattractive'
        ],
        self::ADVERTISEMENT_AUTOMOTIVE => [
            'positive' => 'automotive advertisement style {prompt} . sleek, dynamic, professional, commercial, vehicle-focused, high-resolution, highly detailed',
            'negative' => 'noisy, blurry, unattractive, sloppy, unprofessional'
        ],
        self::ADVERTISEMENT_CORPORATE => [
            'positive' => 'corporate branding style {prompt} . professional, clean, modern, sleek, minimalist, business-oriented, highly detailed',
            'negative' => 'noisy, blurry, grungy, sloppy, cluttered, disorganized'
        ],
        self::ADVERTISEMENT_FASHION => [
            'positive' => 'fashion editorial style {prompt} . high fashion, trendy, stylish, editorial, magazine style, professional, highly detailed',
            'negative' => 'outdated, blurry, noisy, unattractive, sloppy'
        ],
        //self::ADVERTISEMENT_FOOD => [
        //    'positive' => 'food photography style {prompt} . appetizing, professional, culinary, high-resolution, commercial, highly detailed"',
        //    'negative' => 'unappetizing, sloppy, unprofessional, noisy, blurry'
        //],
        //self::ADVERTISEMENT_GOURMET_FOOD => [
        //    'positive' => 'gourmet food photo of {prompt} . soft natural lighting, macro details, vibrant colors, fresh ingredients, glistening textures, bokeh background, styled plating, wooden tabletop, garnished, tantalizing, editorial quality"',
        //    'negative' => 'cartoon, anime, sketch, grayscale, dull, overexposed, cluttered, messy plate, deformed'
        //],
        self::ADVERTISEMENT_LUXURY => [
            'positive' => 'luxury product style {prompt} . elegant, sophisticated, high-end, luxurious, professional, highly detailed"',
            'negative' => 'cheap, noisy, blurry, unattractive, amateurish'
        ],
        self::ADVERTISEMENT_REAL_ESTATE => [
            'positive' => 'real estate photography style {prompt} . professional, inviting, well-lit, high-resolution, property-focused, commercial, highly detailed"',
            'negative' => 'dark, blurry, unappealing, noisy, unprofessional'
        ],
        //self::ADVERTISEMENT_RETAIL => [
        //    'positive' => 'retail packaging style {prompt} . vibrant, enticing, commercial, product-focused, eye-catching, professional, highly detailed"',
        //    'negative' => 'noisy, blurry, amateurish, sloppy, unattractive'
        //],
    ];

    public function getPositivePrompt(Prompt $prompt, string $style): string
    {
        return (string)str_replace(
            '{prompt}',
            $prompt->getPositive(),
            self::PRESETS[$style]['positive']
        );
    }

    public function getNegativePrompt(Prompt $prompt, string $style): string
    {
        return (string)str_replace(
            '{prompt}',
            $prompt->getNegative() . ',' . self::SFW_NEGATIVE_PROMPT,
            self::PRESETS[$style]['negative']
        );
    }
}
