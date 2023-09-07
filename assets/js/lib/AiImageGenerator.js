const GET = async function (url = '', data = {}) {
    const params = new URLSearchParams(data);
    const response = await fetch(url + '?' + params.toString(), {method: 'GET'});

    return response.json();
}

const POST = async function (url = '', data = {}) {
    const response = await fetch(url, {method: 'POST', body: JSON.stringify(data)});

    return response.json();
}

class AiImageGenerator {
    generateAiImageByContext(payload, onRequest, onSuccess, onError, onDone) {
        const url = Routing.generate('ai_image_by_element_context', payload);
        onRequest();
        POST(url, payload)
            .then(jsonData => {
                if (jsonData.success === true) {
                    onSuccess(jsonData);
                } else {
                    onError(jsonData);
                }
            })
            .finally(() => {
                onDone();
            });
    }

    upscaleImage(payload, onRequest, onSuccess, onError, onDone) {
        const url = Routing.generate('ai_image_upscale', payload);
        onRequest();
        POST(url, payload)
            .then(jsonData => {
                if (jsonData.success === true) {
                    onSuccess(jsonData);
                } else {
                    onError(jsonData);
                }
            })
            .finally(() => {
                onDone();
            });
    }

    varyImage(payload, onRequest, onSuccess, onError, onDone) {
        const url = Routing.generate('ai_image_vary', payload);
        onRequest();
        POST(url, payload)
            .then(jsonData => {
                if (jsonData.success === true) {
                    onSuccess(jsonData);
                } else {
                    onError(jsonData);
                }
            })
            .finally(() => {
                onDone();
            });
    }

    inpaintBackground(payload, onRequest, onSuccess, onError, onDone) {
        const url = Routing.generate('ai_image_inpaint_background', payload);
        onRequest();
        POST(url, payload)
            .then(jsonData => {
                if (jsonData.success === true) {
                    onSuccess(jsonData);
                } else {
                    onError(jsonData);
                }
            })
            .finally(() => {
                onDone();
            });
    }
}

export default new AiImageGenerator();
