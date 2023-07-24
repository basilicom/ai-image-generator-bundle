const storage = function () {
    return pimcore.settings.AiImageGeneratorBundle || {};
}

class ConfigStorage {
    set(key, value) {
        storage()[key] = value;
    }

    get(key, defaultValue = null) {
        return storage()[key] || defaultValue;
    }
}

export default new ConfigStorage();
