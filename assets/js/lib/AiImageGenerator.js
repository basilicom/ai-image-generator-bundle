const GET = async function (url = '', data = {}) {
    const params = new URLSearchParams(data);
    const response = await fetch(url + '?' + params.toString(), {method: 'GET'});

    return response.json();
}

const POST = async function (url = '', data = {}) {
    const response = await fetch(url, {method: 'POST', body: JSON.stringify(data)});

    return response.json();
}

export class AiImageGenerator {
    generateAiImage(payload, onRequest, onSuccess, onError, onDone) {
        onRequest();
        GET('/ai-images/generate', payload)
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
        onRequest();
        POST('/ai-images/upscale', payload)
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
