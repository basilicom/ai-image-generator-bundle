# AI Image Generator Bundle

This is bundle utalizes common APIs for generative image AIs to generate images in the Pimcore Backend.

## Installation

```
composer update basilicom/ai-image-generator-bundle
```

Make sure to also install the bundle via `BundleSetupSubscriber` or console.

## Support

| Parameter   | Text-To-Image | Variations | Upscaling | Inpainting | Background Inpainting |
|-------------|:-------------:|:----------:|:---------:|:----------:|:---------------------:|
| ClipDrop    |       X       |     X      |     X     |     -      |           X           | 
| A1111       |       X       |     X      |     X     |     X      |           X           | 
| DreamStudio |       X       |     X      |     X     |     X      |           ~           | 
| OpenAI      |       X       |     X      |     -     |     X      |           X           | 

## Configuration

```
ai_image_generator:
   brand:
    colors:
      - "#0062FF"
      - "#B34197"
      - "#FF444A"

  prompt_enhancement:
    service:        ~|ollama|basilicom|open_ai

    services:
      ollama:
        baseUrl:    "http://localhost:11434/"
        model:      "llama2"

      basilicom:
        baseUrl:    "http://localhost:8080/"

      open_ai:
        baseUrl:    "https://api.openai.com/v1"
        apiKey:     "%env(OPEN_AI_API_KEY)%"

  feature_services:
    txt2img:            open_ai | stable_diffusion_api | dream_studio | clip_drop
    image_variations:   open_ai | stable_diffusion_api | dream_studio | clip_drop
    upscale:            -       | stable_diffusion_api | dream_studio | clip_drop
    inpaint:            open_ai | stable_diffusion_api | dream_studio | -
    inpaint_background: open_ai | stable_diffusion_api | -            | clip_drop

  services:
    stable_diffusion_api:
      baseUrl:        "http://host.docker.internal:7860"
      model:          "JuggernautXL"
      inpaint_model:  "JuggernautXL"
      steps:          30
      upscaler:       "ESRGAN_4x"
    
    dream_studio:
      baseUrl:        "https://api.stability.ai"
      model:          "stable-diffusion-xl-beta-v2-2-2"
      inpaint_model:  "stable-diffusion-xl-1024-v1-0"
      steps:          10 
      apiKey:         "%env(DREAM_STUDIO_API_KEY)%"
      upscaler:       "esrgan-v1-x2plus"
      
    open_ai:
      baseUrl:        "https://api.openai.com/v1"
      apiKey:         "%env(OPEN_AI_API_KEY)%"
      
    clip_drop:
      baseUrl:        "https://clipdrop-api.co"
      apiKey:         "%env(CLIP_DROP_API_KEY)%"
```

## Usage

### Generating images in documents

If no prompt is given, the prompt will be generated (and not translated!) from

* document SEO title
* document SEO description
* h1-Elements
* h2-Elements
* h3- and h4-elements if the previous mentioned sources are empty

![Image editables will get a button to generate an image](./docs/img/ai-image-in-documents.png)

### Generating images in DataObjects

If no prompt is given, the prompt will be generated (and not translated!) by trying to access the following properties:

* key
* title
* name
* productName
* description

![Image and ImageGallery fields will get a context-menu-item to generate an image](./docs/img/ai-image-in-objects.png)

## API

#### `(POST) /admin/ai-images/generate/{context}-{id}`

Generate an image based on a document or object context. If the prompt is empty, the budle-logic for prompting will take
effect.

| Parameter   | Type   | Example         |
|-------------|--------|-----------------|
| **context** | string | document/object |
| **id**      | int    | 123             |
| prompt      | string | a towel         |
| aspectRatio | string | 16:9            |

#### `(POST) /admin/ai-images/upscale/{id}`

Upscale image, while the target upscaling size is AI-Service specific

| Parameter | Type | Default | Example |
|-----------|------|---------|---------|
| **id**    | int  | 123     |         |

#### `(POST) /admin/ai-images/vary/{id}`

Inpaint backgrounds where the background logic differ for provided AI-Services.

| Parameter | Type   | Default | Example |
|-----------|--------|---------|---------|
| **id**    | int    | 123     |         |
| prompt    | string |         | a towel |

### Responses

Based on the `Accept`-header, you can say if you want to have a JSON-response or the image itself.

#### `Accept: application/json`

```
{
  success: true,
  id: Pimcore-Asset-ID,
  image: "base64-decoded Image",   
}
```

```
{
  success: false,
  message: "..."
}
```

#### `Accept: image/jpeg`

```
// the base64 decoded image
```

## Using Stable Diffusion API

When running Automatic1111 locally, you can define `http://host.docker.internal:7860` as your local API-url.

Additionally, make sure you started Automatic1111 with `--api`:

```
  ./webui.sh --api # windows
  ./webui.bat --api # linux/mac
```

If you want to know which models you have, call the [Models-Endpoint](http://localhost:7860/sdapi/v1/sd-models ) and
copy the name of a model of your choice.

### Plugins used

* ControlNet with `canny` and `ip2p`
* _SD Upscaler_ Post Processor Script

## Using LLM-driven prompt enhancing

In order to enhance prompts, we use local images of LLMs.
There are three supported prompt enhancement services:

- `open_ai` (ChatGPT)
- `basilicom` (a simple LLM implementation, see [Docker Hub](https://hub.docker.com/r/basilicom/llm-api))
- `ollama` (see [Github](https://hub.docker.com/r/ollama/ollama))

## Limitations

* the DreamStudio REST API does currently not support variations, I'll look forward to use the gRPC API
* [ClipDrops text-to-image API can only create 1:1 images](https://clipdrop.co/apis/docs/text-to-image#text-to-image-api)

## Additional ideas

* Prompting
    * enhance prompts, especially for background inpainting, like
      ```
      background = "a creepy forest at night"
      image_type = "a haunted castle background"
      characters = "medieval warriors"
      action = "fighting for the honor"
      prompt = f"{image_type} in {background} with {characters} {action}"
      ```
* generate prompt in lightbox before sending?
* background-inpainting for other service by using masks
* CLIP interrogate in order to optimize variation prompting
    * allow variants by img2img and CLIP
* run IMG2IMG with low denoise on background-inpainting
* LCM for super fast preview generation => midjourney-like/inpainting-like image selection before upscaling, etc.
* outpainting via Thumbnail
* better error handling (warnings and fallbacks if credits exceeded)
* ComfyUI + Nodes to Python as fixed presets
    * allow docker images with presets
* InvokeAI

### Authors

Alexander Heidrich
