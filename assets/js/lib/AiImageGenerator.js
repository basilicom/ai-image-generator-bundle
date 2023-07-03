export class AiImageGenerator {
    generateAiImage(payload, onRequest, onSuccess, onError, onDone) {
        const params = new URLSearchParams(payload);

        fetch("/ai-images?" + params.toString())
            .then(response => response.json())
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
