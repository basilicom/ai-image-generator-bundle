# AI Image Generator Bundle (PoC)

This is currently a Proof Of Concept for Image Generation in the Pimcore Backend.
Key features will be:
* generate images in image-editables and asset-fields directly in the backend
* be able to use a local Automatic1111, DreamStudio or other APIs to generate images
* context aware image creation
    * use aspect ratio created by width and height of image-container
    * based on document/object
    * based on homepage itself 
    * based on user prompts
    * configuration of context to use when generating prompts
    * (use LLMs for prompt generation)

## Installation
```
composer update basilicom/ai-image-generator-bundle
```

Make sure to also install the bundle via `BundleSetupSubscriber` or console.

## Configuration
```
basilicom_ai_images:
  stable-diffusion-api:
    baseUrl: "http://host.docker.internal:7860"
    model: "realisticVisionV20_v20"
    steps: 10
```

## Testing
Open http://your-local-pimcore-instance:8080/ai-images

## Using Automatic1111's Stable Diffusion API

When running Automatic1111 locally, you can define `http://host.docker.internal:7860` as your local API-url.  

Additionally, make sure you started Automatic1111 with `--api`:
```
  ./webui.sh --api # windows
  ./webui.bat --api # linux/mac
```

If you want to know which models you have, call the [Models-Endpoint](http://localhost:7860/sdapi/v1/sd-models ) and copy the name of a model of your choice.

### Authors

Alexander Heidrich
